<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Address;

/**
 * Address processor for XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    /** controller/jobs/common/import/xml/processor/address/name
     * Name of the address processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Address\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2019.04
     */
    /**
     * Updates the given item using the data from the DOM node
     *
     * @param \Aimeos\MShop\Common\Item\Iface $item Item which should be updated
     * @param \DOMNode $node XML document node containing a list of nodes to process
     * @return \Aimeos\MShop\Common\Item\Iface Updated item
     */
    public function process(\Aimeos\M_Shop\Common\Item\Iface $item, \Dom_Node $node): \Aimeos\M_Shop\Common\Item\Iface
    {
        \Aimeos\Utils::implements($item, \Aimeos\M_Shop\Common\Item\Address_Ref\Iface::class);
        $manager = \Aimeos\M_Shop::create($this->context(), $item->get_resource_type());
        $addr_items = $item->get_address_items()->reverse();
        foreach ($node->child_nodes as $addr_node) {
            if ($addr_node->node_name !== 'addressitem') {
                continue;
            }
            $list = [];
            foreach ($addr_node->child_nodes as $tag_node) {
                $list[$tag_node->node_name] = \Aimeos\Base\Str::decode($tag_node->node_value);
            }
            if (($addr_item = $addr_items->pop()) !== null) {
                $addr_items->remove($addr_item->get_id());
            } else {
                $addr_item = $manager->create_address_item();
            }
            $item = $item->add_address_item($addr_item->from_array($list), $addr_item->get_id());
        }
        return $item->delete_address_items($addr_items->to_array());
    }
}