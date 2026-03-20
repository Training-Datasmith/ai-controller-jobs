<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Text;

/**
 * Text processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Iface
{
    /** controller/jobs/catalog/import/csv/processor/text/name
     * Name of the text processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Text\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2018.04
     */
    private ?array $list_types = null;
    private array $types = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param array $mapping Associative list of field position in CSV as key and domain item key as value
     * @param \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Iface $object Decorated processor
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, array $mapping, ?\Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Iface $object = null)
    {
        parent::__construct($context, $mapping, $object);
        /** controller/jobs/catalog/import/csv/processor/text/listtypes
         * Names of the catalog list types for texts that are updated or removed
         *
         * If you want to associate text items manually via the administration
         * interface to catalogs and don't want these to be touched during the
         * import, you can specify the catalog list types for these texts
         * that shouldn't be updated or removed.
         *
         * @param array|null List of catalog list type names or null for all
         * @since 2018.04
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/processor/attribute/listtypes
         * @see controller/jobs/catalog/import/csv/processor/catalog/listtypes
         * @see controller/jobs/catalog/import/csv/processor/media/listtypes
         * @see controller/jobs/catalog/import/csv/processor/price/listtypes
         * @see controller/jobs/catalog/import/csv/processor/catalog/listtypes
         */
        $key = 'controller/jobs/catalog/import/csv/processor/text/listtypes';
        $this->list_types = $context->config()->get($key);
        if ($this->list_types === null) {
            $this->list_types = [];
            $manager = \Aimeos\M_Shop::create($context, 'catalog/lists/type');
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
     * Saves the catalog related data to the storage
     *
     * @param \Aimeos\MShop\Catalog\Item\Iface $catalog Catalog item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Catalog\Item\Iface $catalog, array $data): array
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'catalog');
        $ref_manager = \Aimeos\M_Shop::create($context, 'text');
        $list_map = [];
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $list_items = $catalog->get_list_items('text', $this->list_types);
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
            $content = trim($this->val($list, 'text.content', ''));
            $listtype = trim($this->val($list, 'catalog.lists.type', 'default'));
            $list_config = $this->get_list_config(trim($this->val($list, 'catalog.lists.config', '')));
            unset($list['catalog.lists.config']);
            $this->add_type('catalog/lists/type', 'text', $listtype);
            $this->add_type('text/type', 'product', $type);
            if (isset($list_map[$content][$type][$listtype])) {
                $list_item = $list_map[$content][$type][$listtype];
                $ref_item = $list_item->get_ref_item();
                unset($list_items[$list_item->get_id()]);
            } else {
                $list_item = $manager->create_list_item()->set_type($listtype);
                $ref_item = $ref_manager->create()->set_type($type);
            }
            $list_item = $list_item->set_position($pos)->from_array($list)->set_config($list_config);
            $label = mb_strcut(strip_tags($this->val($list, 'text.content', '')), 0, 255);
            $ref_item = $ref_item->set_label($label)->from_array($list);
            $catalog->add_list_item('text', $list_item, $ref_item);
        }
        $catalog->delete_list_items($list_items->to_array(), true);
        return $this->object()->process($catalog, $data);
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
        if (($type = trim($this->val($list, 'catalog.lists.type', 'default'))) && !isset($this->list_types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'catalog list');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        if (($type = trim($this->val($list, 'text.type', 'name'))) && !isset($this->types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'text');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return true;
    }
}