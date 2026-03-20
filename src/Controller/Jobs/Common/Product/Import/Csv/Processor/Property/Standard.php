<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Property;

/**
 * Product property processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface
{
    /** controller/jobs/product/import/csv/processor/property/name
     * Name of the property processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Property\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2015.10
     */
    /**
     * Saves the product property related data to the storage
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Product\Item\Iface $product, array $data): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'product');
        $prop_map = [];
        $items = $product->get_property_items(null, false);
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        foreach ($items as $item) {
            $prop_map[$item->get_value()][$item->get_type()] = $item;
        }
        foreach ($map as $list) {
            if (($value = $this->val($list, 'product.property.value')) === null) {
                continue;
            }
            $type = $this->val($list, 'product.property.type');
            $this->add_type('product/property/type', 'product', $type);
            if (isset($prop_map[$value][$type])) {
                $item = $prop_map[$value][$type];
                $items->remove($item->get_id());
            } else {
                $item = $manager->create_property_item()->set_type($type);
            }
            $product->add_property_item($item->from_array($list));
        }
        $product->delete_property_items($items->to_array());
        return $this->object()->process($product, $data);
    }
}