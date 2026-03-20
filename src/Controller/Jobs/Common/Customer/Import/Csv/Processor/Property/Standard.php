<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Property;

/**
 * Customer property processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Iface
{
    /** controller/jobs/customer/import/csv/processor/property/name
     * Name of the property processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Property\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2025.10
     */
    /**
     * Saves the customer property related data to the storage
     *
     * @param \Aimeos\MShop\Customer\Item\Iface $customer Customer item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Customer\Item\Iface $customer, array $data): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'customer');
        $prop_map = [];
        $items = $customer->get_property_items(null, false);
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        foreach ($items as $item) {
            $prop_map[$item->get_value()][$item->get_type()] = $item;
        }
        foreach ($map as $list) {
            if (($value = $this->val($list, 'customer.property.value')) === null) {
                continue;
            }
            $type = $this->val($list, 'customer.property.type');
            $this->add_type('customer/property/type', 'customer', $type);
            if (isset($prop_map[$value][$type])) {
                $item = $prop_map[$value][$type];
                $items->remove($item->get_id());
            } else {
                $item = $manager->create_property_item()->set_type($type);
            }
            $customer->add_property_item($item->from_array($list));
        }
        $customer->delete_property_items($items);
        return $this->object()->process($customer, $data);
    }
}