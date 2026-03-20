<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Cgroup;

/**
 * Customer group processor for subscriptions
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Base implements \Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Iface
{
    /** controller/jobs/subscription/process/processor/cgroup/name
     * Name of the customer group processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Cgroup\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2018.04
     */
    private array $group_ids;
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        parent::__construct($context);
        $config = $context->config();
        /** controller/jobs/subscription/process/processor/cgroup/groupids
         * List of group IDs that should be added to the customer account
         *
         * After customers bought a subscription, the list of group IDs will be
         * added to their accounts. When the subscription period ends, they will
         * be removed from the customer accounts again.
         *
         * @param array List of customer group IDs
         * @since 2018.04
         */
        $this->group_ids = (array) $config->get('controller/jobs/subscription/process/processor/cgroup/groupids', []);
    }
    /**
     * Processes the initial subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function begin(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order): void
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'customer');
        $product_manager = \Aimeos\M_Shop::create($context, 'order/product');
        $product_item = $product_manager->get($subscription->get_order_product_id());
        $item = $manager->get($subscription->get_order_item()->get_customer_id(), ['group']);
        if (($group_ids = (array) $product_item->get_attribute('group', 'hidden')) === []) {
            $group_ids = $this->group_ids;
        }
        $item->set_groups(array_unique(array_merge($item->get_groups(), $group_ids)));
        $manager->save($item);
    }
    /**
     * Processes the end of the subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function end(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order): void
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'customer');
        $product_manager = \Aimeos\M_Shop::create($context, 'order/product');
        $product_item = $product_manager->get($subscription->get_order_product_id());
        $item = $manager->get($subscription->get_order_item()->get_customer_id(), ['group']);
        if (($group_ids = (array) $product_item->get_attribute('group', 'hidden')) === []) {
            $group_ids = $this->group_ids;
        }
        $item->set_groups(array_diff($item->get_groups(), $group_ids));
        $manager->save($item);
    }
}