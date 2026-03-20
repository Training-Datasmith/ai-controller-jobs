<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Lists\Price;

/**
 * Price list processor for XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    use \Aimeos\Controller\Jobs\Common\Import\Xml\Traits;
    /** controller/jobs/common/import/xml/processor/lists/price/name
     * Name of the lists processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Lists\Price\Myname".
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
        \Aimeos\Utils::implements($item, \Aimeos\M_Shop\Common\Item\Lists_Ref\Iface::class);
        $list_items = $item->get_list_items('price', null, null, false)->reverse();
        $resource = $item->get_resource_type();
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, $resource);
        $price_manager = \Aimeos\M_Shop::create($context, 'price');
        foreach ($node->child_nodes as $ref_node) {
            if ($ref_node->node_name !== 'priceitem') {
                continue;
            }
            if (($list_item = $list_items->pop()) === null) {
                $list_item = $manager->create_list_item();
            }
            if (($ref_item = $list_item->get_ref_item()) === null) {
                $ref_item = $price_manager->create();
            }
            $list = [];
            foreach ($ref_node->child_nodes as $tag) {
                if (in_array($tag->node_name, ['lists'])) {
                    $ref_item = $this->get_processor($tag->node_name)->process($ref_item, $tag);
                } else {
                    $list[$tag->node_name] = \Aimeos\Base\Str::decode($tag->node_value);
                }
            }
            $ref_item = $ref_item->from_array($list);
            foreach ($ref_node->attributes as $attr_name => $attr_node) {
                $list[$resource . '.' . $attr_name] = \Aimeos\Base\Str::decode($attr_node->node_value);
            }
            $name = $resource . '.lists.config';
            $list[$name] = isset($list[$name]) ? (array) json_decode($list[$name]) : [];
            $name = $resource . '.lists.type';
            $list[$name] ??= 'default';
            $this->add_type($resource . '/lists/type', 'price', $list[$resource . '.lists.type']);
            $list_item = $list_item->from_array($list);
            $item->add_list_item('price', $list_item, $ref_item);
        }
        return $item->delete_list_items($list_items->to_array());
    }
}