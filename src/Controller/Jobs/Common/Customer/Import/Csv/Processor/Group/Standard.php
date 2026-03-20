<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Group;

/**
 * Group processor for customer CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Iface
{
    /** controller/jobs/customer/import/csv/processor/group/name
     * Name of the group processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Group\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2025.10
     */
    private \Aimeos\M_Shop\Common\Manager\Iface $group_manager;
    private array $map = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param array $mapping Associative list of field position in CSV as key and domain item key as value
     * @param \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Iface $object Decorated processor
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, array $mapping, ?\Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Iface $object = null)
    {
        parent::__construct($context, $mapping, $object);
        $this->group_manager = \Aimeos\M_Shop::create($context, 'group');
        $filter = $this->group_manager->filter();
        $config = $context->config();
        if ($allowed = $config->get('controller/jobs/customer/import/csv/processor/group/allowed')) {
            $filter->add('group.code', '==', (array) $allowed);
        }
        if ($denied = $config->get('controller/jobs/customer/import/csv/processor/group/denied', ['admin', 'editor'])) {
            $filter->add('group.code', '!=', (array) $denied);
        }
        $cursor = $this->group_manager->cursor($filter);
        while ($items = $this->group_manager->iterate($cursor)) {
            foreach ($items as $item) {
                $this->map[$item->get_code()] = $item->get_id();
            }
        }
    }
    /**
     * Saves the customer property related data to the storage
     *
     * @param \Aimeos\MShop\Common\Item\Iface $customer Customer item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Common\Item\Iface $customer, array $data): array
    {
        $ids = [];
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        foreach ($map as $list) {
            if (($value = $this->val($list, 'customer.groups')) === null) {
                continue;
            }
            $groups = explode("\n", $value);
            foreach ($groups as $group) {
                if (isset($this->map[$group])) {
                    $ids[] = $this->map[$group];
                }
            }
        }
        $customer->set_groups($ids);
        return $this->object()->process($customer, $data);
    }
}