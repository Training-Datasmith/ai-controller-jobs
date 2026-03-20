<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Group;

/**
 * Customer group processor for XML imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Base implements \Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Iface
{
    use \Aimeos\Controller\Jobs\Common\Import\Xml\Traits;
    /** controller/jobs/common/import/xml/processor/group/name
     * Name of the group processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Import\Xml\Processor\Group\Myname".
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
        \Aimeos\Utils::implements($item, \Aimeos\M_Shop\Customer\Item\Iface::class);
        $map = $this->get_items($node->child_nodes);
        $list = [];
        foreach ($node->child_nodes as $node) {
            if ($node->node_name !== 'groupitem') {
                continue;
            }
            if (($attr = $node->attributes->get_named_item('ref')) === null) {
                continue;
            }
            $attr_value = \Aimeos\Base\Str::decode($attr->node_value);
            if (!isset($map[$attr_value])) {
                continue;
            }
            $list[] = $map[$attr_value]->get_id();
        }
        return $item->set_groups($list);
    }
    /**
     * Returns the attribute items for the given nodes
     *
     * @param \DomNodeList $nodes List of XML attribute item nodes
     * @return \Aimeos\MShop\Customer\Item\Group\Iface[] Associative list of customer group items with codes as keys
     */
    protected function get_items(\Dom_Node_List $nodes): array
    {
        $keys = $map = [];
        $manager = \Aimeos\M_Shop::create($this->context(), 'group');
        foreach ($nodes as $node) {
            if ($node->node_name === 'groupitem' && ($attr = $node->attributes->get_named_item('ref')) !== null) {
                $keys[\Aimeos\Base\Str::decode($attr->node_value)] = null;
            }
        }
        $search = $manager->filter()->slice(0, count($keys));
        $search->set_conditions($search->compare('==', 'group.code', array_keys($keys)));
        foreach ($manager->search($search, []) as $item) {
            $map[$item->get_code()] = $item;
        }
        return $map;
    }
}