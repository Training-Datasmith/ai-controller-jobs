<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Text;

/**
 * Text processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Iface
{
    /** controller/jobs/supplier/import/csv/processor/text/name
     * Name of the text processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Text\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2020.07
     */
    private ?array $list_types = null;
    private array $types = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param array $mapping Associative list of field position in CSV as key and domain item key as value
     * @param \Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Iface $object Decorated processor
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, array $mapping, ?\Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Iface $object = null)
    {
        parent::__construct($context, $mapping, $object);
        /** controller/jobs/supplier/import/csv/processor/text/listtypes
         * Names of the supplier list types for texts that are updated or removed
         *
         * If you want to associate text items manually via the administration
         * interface to suppliers and don't want these to be touched during the
         * import, you can specify the supplier list types for these texts
         * that shouldn't be updated or removed.
         *
         * @param array|null List of supplier list type names or null for all
         * @since 2020.07
         * @see controller/jobs/supplier/import/csv/domains
         * @see controller/jobs/supplier/import/csv/processor/attribute/listtypes
         * @see controller/jobs/supplier/import/csv/processor/supplier/listtypes
         * @see controller/jobs/supplier/import/csv/processor/media/listtypes
         * @see controller/jobs/supplier/import/csv/processor/price/listtypes
         * @see controller/jobs/supplier/import/csv/processor/supplier/listtypes
         */
        $key = 'controller/jobs/supplier/import/csv/processor/text/listtypes';
        $this->list_types = $context->config()->get($key);
        if ($this->list_types === null) {
            $this->list_types = [];
            $manager = \Aimeos\M_Shop::create($context, 'supplier/lists/type');
            $search = $manager->filter()->slice(0, 0x7fffffff);
            foreach ($manager->search($search) as $item) {
                $this->list_types[$item->get_code()] = $item->get_code();
            }
        } else {
            $this->list_types = array_combine($this->list_types, $this->list_types);
        }
        $manager = \Aimeos\M_Shop::create($context, 'text/type');
        $search = $manager->filter()->slice(0, 0x7fffffff);
        foreach ($manager->search($search) as $item) {
            $this->types[$item->get_code()] = $item->get_code();
        }
    }
    /**
     * Saves the supplier related data to the storage
     *
     * @param \Aimeos\MShop\Supplier\Item\Iface $supplier Supplier item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Supplier\Item\Iface $supplier, array $data): array
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'supplier');
        $ref_manager = \Aimeos\M_Shop::create($context, 'text');
        $list_map = [];
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $list_items = $supplier->get_list_items('text', $this->list_types);
        foreach ($list_items as $list_item) {
            if (($ref_item = $list_item->get_ref_item()) !== null) {
                $list_map[$ref_item->get_content()][$ref_item->get_type()][$list_item->get_type()] = $list_item;
            }
        }
        foreach ($map as $pos => $list) {
            if ($this->check_entry($list) === false) {
                continue;
            }
            $type = trim($this->val($list, 'text.type', 'name'));
            $listtype = trim($this->val($list, 'supplier.lists.type', 'default'));
            $content = trim($this->val($list, 'text.content', ''));
            if (isset($list_map[$content][$type][$listtype])) {
                $list_item = $list_map[$content][$type][$listtype];
                $ref_item = $list_item->get_ref_item();
                unset($list_items[$list_item->get_id()]);
            } else {
                $list_item = $manager->create_list_item()->set_type($listtype);
                $ref_item = $ref_manager->create()->set_type($type);
            }
            $list_item = $list_item->set_position($pos)->from_array($list);
            $label = mb_strcut(strip_tags($this->val($list, 'text.content', '')), 0, 255);
            $ref_item = $ref_item->set_label($label)->from_array($list);
            $supplier->add_list_item('text', $list_item, $ref_item);
        }
        $supplier->delete_list_items($list_items->to_array(), true);
        return $this->object()->process($supplier, $data);
    }
    /**
     * Checks if an entry can be used for updating a media item
     *
     * @param array $list Associative list of key/value pairs from the mapping
     * @return bool True if valid, false if not
     */
    protected function check_entry(array $list): bool
    {
        if ($this->val($list, 'text.content') === null) {
            return false;
        }
        if (($type = trim($this->val($list, 'supplier.lists.type', ''))) && !isset($this->list_types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'supplier list');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        if (($type = trim($this->val($list, 'text.type', ''))) && !isset($this->types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'text');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return true;
    }
}