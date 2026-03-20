<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Property;

/**
 * Property processor for XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    /** controller/jobs/common/import/xml/processor/property/name
     * Name of the property processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Property\Myname".
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
        \Aimeos\Utils::implements($item, \Aimeos\M_Shop\Common\Item\Property_Ref\Iface::class);
        $resource = $item->get_resource_type();
        $manager = \Aimeos\M_Shop::create($this->context(), $resource);
        $prop_items = $item->get_property_items(null, false);
        $map = [];
        foreach ($prop_items as $prop_item) {
            $map[$prop_item->get_type()][$prop_item->get_language_id()][$prop_item->get_value()] = $prop_item->get_id();
        }
        foreach ($node->child_nodes as $prop_node) {
            if ($prop_node->node_name !== 'propertyitem') {
                continue;
            }
            $list = [];
            foreach ($prop_node->child_nodes as $tag_node) {
                $list[$tag_node->node_name] = \Aimeos\Base\Str::decode($tag_node->node_value);
            }
            $prop_item = $manager->create_property_item()->from_array($list);
            if (isset($map[$prop_item->get_type()][$prop_item->get_language_id()][$prop_item->get_value()])) {
                $prop_items->remove($map[$prop_item->get_type()][$prop_item->get_language_id()][$prop_item->get_value()]);
            } else {
                $item->add_property_item($prop_item);
            }
            $this->add_type($resource . '/property/type', 'product', $prop_item->get_type());
        }
        return $item->delete_property_items($prop_items->to_array());
    }
}