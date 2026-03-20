<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Attribute;

/**
 * Attribute processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface
{
    /** controller/jobs/product/import/csv/processor/attribute/name
     * Name of the attribute processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Attribute\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2015.10
     */
    private \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Cache\Attribute\Standard $cache;
    private ?array $list_types = null;
    private array $types = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param array $mapping Associative list of field position in CSV as key and domain item key as value
     * @param \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface $object Decorated processor
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, array $mapping, ?\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface $object = null)
    {
        parent::__construct($context, $mapping, $object);
        $config = $context->config();
        /** controller/jobs/product/import/csv/attribute/listtypes
         * Names of the product list types for attributes that are updated or removed
         *
         * If you want to associate attribute items manually via the administration
         * interface to products and don't want these to be touched during the
         * import, you can specify the product list types for these attributes
         * that shouldn't be updated or removed.
         *
         * @param array|null List of product list type names or null for all
         * @since 2015.05
         * @see controller/jobs/product/import/csv/domains
         * @see controller/jobs/product/import/csv/separator
         * @see controller/jobs/product/import/csv/catalog/listtypes
         * @see controller/jobs/product/import/csv/media/listtypes
         * @see controller/jobs/product/import/csv/price/listtypes
         * @see controller/jobs/product/import/csv/product/listtypes
         * @see controller/jobs/product/import/csv/supplier/listtypes
         * @see controller/jobs/product/import/csv/text/listtypes
         */
        $default = $config->get('controller/jobs/product/import/csv/processor/attribute/listtypes');
        $this->list_types = $config->get('controller/jobs/product/import/csv/attribute/listtypes', $default);
        if ($this->list_types === null) {
            $this->list_types = [];
            $manager = \Aimeos\M_Shop::create($context, 'product/lists/type');
            $search = $manager->filter()->slice(0, 0x7fffffff);
            foreach ($manager->search($search) as $item) {
                $this->list_types[$item->get_code()] = $item->get_code();
            }
        } else {
            $this->list_types = array_combine($this->list_types, $this->list_types);
        }
        $manager = \Aimeos\M_Shop::create($context, 'attribute/type');
        $search = $manager->filter()->slice(0, 0x7fffffff);
        foreach ($manager->search($search) as $item) {
            $this->types[$item->get_code()] = $item->get_code();
        }
        $this->cache = $this->get_cache('attribute');
    }
    /**
     * Saves the attribute related data to the storage
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Product\Item\Iface $product, array $data): array
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'product');
        /** controller/jobs/product/import/csv/separator
         * Separator between multiple values in one CSV field
         *
         * In Aimeos, fields of some CSV columns can contain multiple values which
         * are split and imported as separate values. This setting configures the
         * character that is used for splitting the values and by default, a new
         * line character (\n) is used.
         *
         * @param string Unique character or characters in field values
         * @since 2015.05
         * @see controller/jobs/product/import/csv/domains
         * @see controller/jobs/product/import/csv/attribute/listtypes
         * @see controller/jobs/product/import/csv/media/listtypes
         * @see controller/jobs/product/import/csv/price/listtypes
         * @see controller/jobs/product/import/csv/product/listtypes
         * @see controller/jobs/product/import/csv/supplier/listtypes
         * @see controller/jobs/product/import/csv/text/listtypes
         */
        $separator = $context->config()->get('controller/jobs/product/import/csv/separator', "\n");
        $pos = 0;
        $list_map = [];
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $list_items = $product->get_list_items('attribute', $this->list_types, null, false);
        foreach ($list_items as $list_item) {
            if ($ref_item = $list_item->get_ref_item()) {
                $list_map[$ref_item->get_code()][$ref_item->get_type()][$list_item->get_type()] = $list_item;
            }
        }
        foreach ($map as $list) {
            if ($this->check_entry($list) === false) {
                continue;
            }
            $attr_type = trim($this->val($list, 'attribute.type', ''));
            $listtype = trim($this->val($list, 'product.lists.type', 'default'));
            $this->add_type('product/lists/type', 'attribute', $listtype);
            $list_config = $this->get_list_config(trim($this->val($list, 'product.lists.config', '')));
            unset($list['product.lists.config']);
            $codes = explode($separator, trim($this->val($list, 'attribute.code', '')));
            unset($list['attribute.code'], $list['product.lists.config']);
            foreach ($codes as $code) {
                $code = trim($code);
                $attr_item = $this->get_attribute_item($code, $attr_type);
                $attr_item = $attr_item->from_array($list)->set_code($code);
                $list_item = $list_map[$code][$attr_type][$listtype] ?? $manager->create_list_item();
                $list_item = $list_item->set_position($pos)->from_array($list)->set_config($list_config);
                $product->add_list_item('attribute', $list_item->set_type($listtype), $attr_item);
                unset($list_items[$list_item->get_id()]);
            }
        }
        $product->delete_list_items($list_items);
        return $this->object()->process($product, $data);
    }
    /**
     * Checks if the entry from the mapped data is valid
     *
     * @param array $list Associative list of key/value pairs from the mapped data
     * @return bool True if the entry is valid, false if not
     */
    protected function check_entry(array $list): bool
    {
        if ($this->val($list, 'attribute.code') === null) {
            return false;
        }
        if (($type = trim($this->val($list, 'product.lists.type', 'default'))) && !isset($this->list_types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'product list');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        if (($type = trim($this->val($list, 'attribute.type', ''))) && !isset($this->types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'attribute');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return true;
    }
    /**
     * Returns the attribute item for the given code and type
     *
     * @param string $code Attribute code
     * @param string $type Attribute type
     * @return \Aimeos\MShop\Attribute\Item\Iface Attribute item object
     */
    protected function get_attribute_item(string $code, string $type): \Aimeos\M_Shop\Attribute\Item\Iface
    {
        if (($item = $this->cache->get($code, $type)) === null) {
            $manager = \Aimeos\M_Shop::create($this->context(), 'attribute');
            $item = $manager->create();
            $item->set_type($type);
            $item->set_domain('product');
            $item->set_label($code);
            $item->set_code($code);
            $item->set_status(1);
            $item = $manager->save($item);
            $this->cache->set($item);
        }
        return $item;
    }
}