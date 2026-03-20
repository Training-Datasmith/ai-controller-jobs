<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Customer\Import\Xml\Processor\Group;

/**
 * Group processor for customer XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    /** controller/jobs/customer/import/xml/processor/group/name
     * Name of the group processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Customer\Import\Xml\Processor\Group\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2019.04
     */
    private \Aimeos\M_Shop\Common\Manager\Iface $group_manager;
    private array $map = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        parent::__construct($context);
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
     * Updates the given item using the data from the DOM node
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item which should be updated
     * @param \DOMNode $node XML document node containing a list of nodes to process
     * @return \Aimeos\MShop\Common\Item\Iface Updated item
     */
    public function process(\Aimeos\M_Shop\Common\Item\Iface $item, \Dom_Node $node): \Aimeos\M_Shop\Common\Item\Iface
    {
        \Aimeos\Utils::implements($item, \Aimeos\M_Shop\Customer\Item\Iface::class);
        $ids = [];
        foreach ($node->child_nodes as $node) {
            if ($node->node_name === 'groupitem' && ($refattr = $node->attributes->get_named_item('ref')) !== null) {
                $code = $refattr->node_value;
                if (!isset($this->map[$code])) {
                    $this->map[$code] = $this->group_manager->find($code)->get_id();
                }
                $ids[] = $this->map[$code];
            }
        }
        return $item->set_groups($ids);
    }
}