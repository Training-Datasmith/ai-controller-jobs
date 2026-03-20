<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Address;

/**
 * Address processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Iface
{
    /** controller/jobs/customer/import/csv/processor/address/name
     * Name of the address processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Customer\Import\Csv\Processor\Address\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2025.10
     */
    /**
     * Saves the customer related data to the storage
     *
     * @param \Aimeos\MShop\Customer\Item\Iface $customer Customer item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Customer\Item\Iface $customer, array $data): array
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'customer');
        $pos = 0;
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $addresses = $customer->get_address_items();
        foreach ($map as $entry) {
            $key = $addresses->first_key();
            $address = $addresses->pull($key) ?? $manager->create_address_item();
            $address->set_position($pos++)->from_array($entry);
            $customer->add_address_item($address, $key);
        }
        $customer->delete_address_items($addresses);
        return $this->object()->process($customer, $data);
    }
}