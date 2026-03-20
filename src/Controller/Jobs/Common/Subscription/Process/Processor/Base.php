<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Subscription\Process\Processor;

/**
 * Abstract class with common methods for all subscription processors
 *
 * @package Controller
 * @subpackage Common
 */
class Base
{
    use \Aimeos\Macro\Macroable;
    private \Aimeos\M_Shop\Context_Iface $context;
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context)
    {
        $this->context = $context;
    }
    /**
     * Returns the context item
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    protected function context(): \Aimeos\M_Shop\Context_Iface
    {
        return $this->context;
    }
    /**
     * Processes the initial subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function begin(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order)
    {
    }
    /**
     * Executed before the subscription renewal
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function renew_before(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order)
    {
    }
    /**
     * Executed after the subscription renewal
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $order Order item
     */
    public function renew_after(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order)
    {
    }
    /**
     * Processes the end of the subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function end(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order)
    {
    }
}