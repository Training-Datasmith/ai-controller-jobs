<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Lists\Attribute;

/**
 * Attribute list processor for XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    use \Aimeos\Controller\Jobs\Common\Import\Xml\Traits;
    /** controller/jobs/common/import/xml/processor/lists/attribute/name
     * Name of the lists processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Lists\Attribute\Myname".
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
        $context = $this->context();
        $resource = $item->get_resource_type();
        $list_items = $item->get_list_items('attribute', null, null, false);
        $manager = \Aimeos\M_Shop::create($context, $resource);
        $map = $this->get_items($node->child_nodes);
        foreach ($node->child_nodes as $node) {
            $attributes = $node->attributes;
            if ($node->node_name !== 'attributeitem') {
                continue;
            }
            if (($attr = $attributes->get_named_item('ref')) === null) {
                continue;
            }
            $attr_value = \Aimeos\Base\Str::decode($attr->node_value);
            if (!isset($map[$attr_value])) {
                continue;
            }
            $list = [];
            $ref_item = $map[$attr_value];
            $type = ($attr = $attributes->get_named_item('lists.type')) !== null ? $attr->node_value : 'default';
            if (($list_item = $item->get_list_item('attribute', $type, $ref_item->get_id())) === null) {
                $list_item = $manager->create_list_item();
            } else {
                unset($list_items[$list_item->get_id()]);
            }
            foreach ($attributes as $attr_name => $attr_node) {
                $list[$resource . '.' . $attr_name] = \Aimeos\Base\Str::decode($attr_node->node_value);
            }
            $name = $resource . '.lists.config';
            $list[$name] = isset($list[$name]) ? (array) json_decode($list[$name]) : [];
            $list[$resource . '.lists.type'] = $type;
            $this->add_type($resource . '/lists/type', 'attribute', $type);
            $list_item = $list_item->from_array($list);
            $item->add_list_item('attribute', $list_item, $ref_item);
        }
        return $item->delete_list_items($list_items->to_array());
    }
    /**
     * Returns the attribute items for the given nodes
     *
     * @param \DomNodeList $nodes List of XML attribute item nodes
     * @return \Aimeos\MShop\Attribute\Item\Iface[] Associative list of attribute items with codes as keys
     */
    protected function get_items(\Dom_Node_List $nodes): array
    {
        $keys = $map = [];
        $manager = \Aimeos\M_Shop::create($this->context(), 'attribute');
        foreach ($nodes as $node) {
            if ($node->node_name === 'attributeitem' && ($attr = $node->attributes->get_named_item('ref')) !== null) {
                $keys[\Aimeos\Base\Str::decode($attr->node_value)] = null;
            }
        }
        $search = $manager->filter()->slice(0, count($keys));
        $search->set_conditions($search->compare('==', 'attribute.key', array_keys($keys)));
        foreach ($manager->search($search, []) as $item) {
            $map[$item->get_key()] = $item;
        }
        return $map;
    }
}