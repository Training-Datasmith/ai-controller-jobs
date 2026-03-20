<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Address;

/**
 * Address processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Supplier\Import\Csv\Processor\Iface
{
    /**
     * Saves the supplier related data to the storage
     *
     * @param \Aimeos\MShop\Supplier\Item\Iface $supplier Supplier item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Supplier\Item\Iface $supplier, array $data): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'supplier');
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $items = $supplier->get_address_items();
        foreach ($map as $list) {
            if ($this->check_entry($list) === false) {
                continue;
            }
            $key = $items->last_key();
            $item = $items->pop() ?? $manager->create_address_item();
            $item->from_array($list);
            $supplier->add_address_item($item, $key);
        }
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
        if ($this->val($list, 'supplier.address.languageid') === null) {
            return false;
        }
        if ($this->val($list, 'supplier.address.countryid') === null) {
            return false;
        }
        if ($this->val($list, 'supplier.address.city') === null) {
            return false;
        }
        return true;
    }
}