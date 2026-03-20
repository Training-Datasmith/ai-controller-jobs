<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Jobs
 */
namespace Aimeos\Controller\Jobs\Subscription\Process\Renew;

/**
 * Job controller for subscription processs renew.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Standard extends \Aimeos\Controller\Jobs\Subscription\Process\Base implements \Aimeos\Controller\Jobs\Iface
{
    /** controller/jobs/subscription/process/renew/name
     * Class name of the used subscription suggestions scheduler controller implementation
     *
     * Each default job controller can be replace by an alternative imlementation.
     * To use this implementation, you have to set the last part of the class
     * name as configuration value so the controller factory knows which class it
     * has to instantiate.
     *
     * For example, if the name of the default class is
     *
     *  \Aimeos\Controller\Jobs\Subscription\Process\Renew\Standard
     *
     * and you want to replace it with your own version named
     *
     *  \Aimeos\Controller\Jobs\Subscription\Process\Renew\Myrenew
     *
     * then you have to set the this configuration option:
     *
     *  controller/jobs/subscription/process/renew/name = Myrenew
     *
     * The value is the last part of your own class name and it's case sensitive,
     * so take care that the configuration value is exactly named like the last
     * part of the class name.
     *
     * The allowed characters of the class name are A-Z, a-z and 0-9. No other
     * characters are possible! You should always start the last part of the class
     * name with an upper case character and continue only with lower case characters
     * or numbers. Avoid chamel case names like "MyRenew"!
     *
     * @param string Last part of the class name
     * @since 2018.04
     */
    /** controller/jobs/subscription/process/renew/decorators/excludes
     * Excludes decorators added by the "common" option from the subscription process CSV job controller
     *
     * Decorators extrenew the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to remove a decorator added via
     * "controller/jobs/common/decorators/default" before they are wrapped
     * around the job controller.
     *
     *  controller/jobs/subscription/process/renew/decorators/excludes = array( 'decorator1' )
     *
     * This would remove the decorator named "decorator1" from the list of
     * common decorators ("\Aimeos\Controller\Jobs\Common\Decorator\*") added via
     * "controller/jobs/common/decorators/default" to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/subscription/process/renew/decorators/global
     * @see controller/jobs/subscription/process/renew/decorators/local
     */
    /** controller/jobs/subscription/process/renew/decorators/global
     * Adds a list of globally available decorators only to the subscription process CSV job controller
     *
     * Decorators extrenew the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap global decorators
     * ("\Aimeos\Controller\Jobs\Common\Decorator\*") around the job controller.
     *
     *  controller/jobs/subscription/process/renew/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Controller\Jobs\Common\Decorator\Decorator1" only to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/subscription/process/renew/decorators/excludes
     * @see controller/jobs/subscription/process/renew/decorators/local
     */
    /** controller/jobs/subscription/process/renew/decorators/local
     * Adds a list of local decorators only to the subscription process CSV job controller
     *
     * Decorators extrenew the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Controller\Jobs\Subscription\Process\Renew\Decorator\*") around the job
     * controller.
     *
     *  controller/jobs/subscription/process/renew/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Controller\Jobs\Subscription\Process\Renew\Decorator\Decorator2"
     * only to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/subscription/process/renew/decorators/excludes
     * @see controller/jobs/subscription/process/renew/decorators/global
     */
    /**
     * Returns the localized name of the job.
     *
     * @return string Name of the job
     */
    public function get_name(): string
    {
        return $this->context()->translate('controller/jobs', 'Subscription process renew');
    }
    /**
     * Returns the localized description of the job.
     *
     * @return string Description of the job
     */
    public function get_description(): string
    {
        return $this->context()->translate('controller/jobs', 'Renews subscriptions at next date');
    }
    /**
     * Executes the job.
     *
     * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
     */
    public function run(): void
    {
        $date = date('Y-m-d H:i:s');
        $context = $this->context();
        $domains = $this->domains();
        $processors = $this->get_processors($this->names());
        $manager = \Aimeos\M_Shop::create($context, 'subscription');
        $search = $manager->filter(true)->add('subscription.datenext', '<=', $date)->slice(0, $this->max());
        $search->add($search->or([$search->compare('==', 'subscription.dateend', null), $search->compare('>', 'subscription.dateend', $date)]));
        $cursor = $manager->cursor($search);
        while ($items = $manager->iterate($cursor, $domains)) {
            foreach ($items as $item) {
                $manager->begin();
                try {
                    $manager->save($this->process($item, $processors));
                    $manager->commit();
                } catch (\Exception $e) {
                    $manager->rollback();
                    $str = 'Unable to renew subscription with ID "%1$s": %2$s';
                    $msg = sprintf($str, $item->get_id(), $e->get_message() . "\n" . $e->get_trace_as_string());
                    $context->logger()->error($msg, 'subscription/process/renew');
                }
            }
        }
    }
    /**
     * Adds the given addresses to the order
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Order\Item\Iface $newOrder Order object to add the addresses to
     * @param \Aimeos\Map $addresses List of type as key and address object implementing \Aimeos\MShop\Order\Item\Address\Iface as value
     * @return \Aimeos\MShop\Order\Item\Iface Order with addresses added
     */
    protected function add_basket_addresses(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Order\Item\Iface $new_order, \Aimeos\Map $addresses): \Aimeos\M_Shop\Order\Item\Iface
    {
        foreach ($addresses as $type => $order_addresses) {
            $idx = 0;
            foreach ($order_addresses as $order_address) {
                $new_order->add_address((clone $order_address)->set_id(null), $type, $idx);
            }
        }
        if (!$new_order->get_customer_id()) {
            return $new_order;
        }
        try {
            $customer = \Aimeos\M_Shop::create($context, 'customer')->get($new_order->get_customer_id());
            $address = \Aimeos\M_Shop::create($context, 'order')->create_address();
            $type = \Aimeos\M_Shop\Order\Item\Address\Base::TYPE_PAYMENT;
            $new_order->add_address($address->copy_from($customer->get_payment_address()), $type, 0);
        } catch (\Exception) {
            $msg = sprintf('Unable to add current address for customer with ID "%1$s"', $new_order->get_customer_id());
            $context->logger()->info($msg, 'subscription/process/renew');
        }
        return $new_order;
    }
    /**
     * Adds the given coupon codes to the order if enabled
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Order\Item\Iface $newOrder Order including product and addresses
     * @param \Aimeos\Map $codes List of coupon codes that should be added to the given order
     * @return \Aimeos\MShop\Order\Item\Iface Basket, maybe with coupons added
     */
    protected function add_basket_coupons(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Order\Item\Iface $new_order, \Aimeos\Map $codes): \Aimeos\M_Shop\Order\Item\Iface
    {
        /** controller/jobs/subscription/process/renew/use-coupons
         * Applies the coupons of the previous order also to the new one
         *
         * Reuse coupon codes added to the order by the customer the first time
         * again in new subscription orders. If they have any effect depends on
         * the codes still being active (status, time frame and count) and the
         * decorators added to the coupon providers in the admin interface.
         *
         * @param boolean True to reuse coupon codes, false to remove coupons
         * @since 2018.10
         */
        if ($context->config()->get('controller/jobs/subscription/process/renew/use-coupons', false)) {
            foreach ($codes as $code) {
                try {
                    $new_order->add_coupon($code);
                } catch (\Aimeos\M_Shop\Plugin\Provider\Exception|\Aimeos\M_Shop\Coupon\Exception) {
                    $new_order->delete_coupon($code);
                }
            }
        }
        return $new_order;
    }
    /**
     * Adds the given products to the order
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Order\Item\Iface $order Order to add the products to
     * @param \Aimeos\Map $orderProducts List of product items Implementing \Aimeos\MShop\Order\Item\Product\Iface
     * @param string $orderProductId Unique ID of the ordered subscription product
     * @return \Aimeos\MShop\Order\Item\Iface Order with products added
     */
    protected function add_basket_products(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Order\Item\Iface $new_order, \Aimeos\Map $order_products, $order_product_id): \Aimeos\M_Shop\Order\Item\Iface
    {
        foreach ($order_products as $order_product) {
            if ($order_product->get_id() == $order_product_id) {
                $order_product = clone $order_product;
                $order_product->get_attribute_items()->set_id(null);
                $new_order->add_product($order_product->set_id(null));
            }
        }
        return $new_order;
    }
    /**
     * Adds a matching delivery and payment service to the order
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Order\Item\Iface $order Order to add the services to
     * @param \Aimeos\Map $services Associative list of type as key and list of service objects implementing \Aimeos\MShop\Order\Item\Service\Iface as values
     * @return \Aimeos\MShop\Order\Item\Iface Order with delivery and payment service added
     */
    protected function add_basket_services(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Order\Item\Iface $new_order, \Aimeos\Map $services): \Aimeos\M_Shop\Order\Item\Iface
    {
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT;
        if (isset($services[$type])) {
            $idx = 0;
            foreach ($services[$type] as $order_service) {
                $order_service = clone $order_service;
                $order_service->get_attribute_items()->set_id(null);
                $new_order->add_service($order_service->set_id(null), $type, $idx++);
            }
        }
        $idx = 0;
        $type = \Aimeos\M_Shop\Order\Item\Service\Base::TYPE_DELIVERY;
        $service_manager = \Aimeos\M_Shop::create($context, 'service');
        $order_manager = \Aimeos\M_Shop::create($context, 'order');
        $search = $service_manager->filter(true);
        $search->set_sortations([$search->sort('+', 'service.position')]);
        $search->set_conditions($search->compare('==', 'service.type', $type));
        foreach ($service_manager->search($search, ['media', 'price', 'text']) as $item) {
            $provider = $service_manager->get_provider($item, $item->get_type());
            if ($provider->is_available($new_order) === true) {
                $order_service_item = $order_manager->create_service()->copy_from($item);
                return $new_order->add_service($order_service_item, $type, $idx++);
            }
        }
        return $new_order;
    }
    /**
     * Creates a new context based on the order and the customer the subscription belongs to
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $order Subscription item with associated order
     * @return \Aimeos\MShop\ContextIface New context object
     */
    protected function create_context(\Aimeos\M_Shop\Subscription\Item\Iface $subscription): \Aimeos\M_Shop\Context_Iface
    {
        $context = clone $this->context();
        $level = \Aimeos\M_Shop\Locale\Manager\Base::SITE_ALL;
        $order = $subscription->get_order_item();
        $sitecode = $order->get_site_code();
        $locale = $order->locale();
        $manager = \Aimeos\M_Shop::create($context, 'locale');
        $locale = $manager->bootstrap($sitecode, $locale->get_language_id(), $locale->get_currency_id(), false, $level);
        $context->set_locale($locale);
        try {
            $manager = \Aimeos\M_Shop::create($context, 'customer');
            $customer_item = $manager->get($order->get_customer_id(), ['group']);
            $context->set_user($customer_item);
            $manager = \Aimeos\M_Shop::create($context, 'group');
            $filter = $manager->filter(true)->add(['group.id' => $customer_item->get_groups()]);
            $group_items = $manager->search($filter->slice(0, count($customer_item->get_groups())))->all();
            $context->set_groups($group_items);
        } catch (\Exception) {
        }
        // Subscription without account
        return $context;
    }
    /**
     * Creates and stores a new order from the given subscription
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item with associated order
     * @return \Aimeos\MShop\Order\Item\Iface New order item including addresses, coupons, products and services
     */
    protected function create_order(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Subscription\Item\Iface $subscription): \Aimeos\M_Shop\Order\Item\Iface
    {
        $order = $subscription->get_order_item();
        $manager = \Aimeos\M_Shop::create($context, 'order');
        $new_order = $manager->create()->set_customer_id($order->get_customer_id())->set_channel('subscription');
        $new_order = $this->add_basket_addresses($context, $new_order, $order->get_addresses());
        $new_order = $this->add_basket_products($context, $new_order, $order->get_products(), $subscription->get_order_product_id());
        $new_order = $this->add_basket_services($context, $new_order, $order->get_services());
        $new_order = $this->add_basket_coupons($context, $new_order, $order->get_coupons()->keys());
        return $new_order->check();
    }
    /**
     * Creates a new payment for the given order and invoice
     *
     * @param \Aimeos\MShop\ContextIface Context object
     * @param \Aimeos\MShop\Order\Item\Iface $order Complete order with product, addresses and services
     * @return \Aimeos\MShop\Order\Item\Iface Updated order item
     */
    protected function create_payment(\Aimeos\M_Shop\Context_Iface $context, \Aimeos\M_Shop\Order\Item\Iface $order): \Aimeos\M_Shop\Order\Item\Iface
    {
        $manager = \Aimeos\M_Shop::create($context, 'service');
        foreach ($order->get_service(\Aimeos\M_Shop\Order\Item\Service\Base::TYPE_PAYMENT) as $service) {
            $manager->get_provider($manager->get($service->get_service_id()), 'payment')->repay($order);
        }
        return $order;
    }
    /**
     * Returns the domains that should be fetched together with the order data
     *
     * @return array List of domain names
     */
    protected function domains(): array
    {
        /** controller/jobs/subscription/process/domains
         * Associated items that should be available too in the subscription
         *
         * Orders consist of address, coupons, products and services. They can be
         * fetched together with the subscription items and passed to the processor.
         * Available domains for those items are:
         *
         * - order
         * - order/address
         * - order/coupon
         * - order/product
         * - order/service
         *
         * @param array Referenced domain names
         * @since 2022.04
         * @see controller/jobs/subscription/process/processors
         * @see controller/jobs/subscription/process/payment-days
         * @see controller/jobs/subscription/process/payment-status
         */
        $domains = ['order', 'order/address', 'order/coupon', 'order/product', 'order/service'];
        return $this->context()->config()->get('controller/jobs/subscription/process/domains', $domains);
    }
    /**
     * Returns if subscriptions should end if payment couldn't be captured
     *
     * @return bool TRUE if subscription should end, FALSE if not
     */
    protected function ends(): bool
    {
        /** controller/jobs/subscription/process/payment-ends
         * Subscriptions ends if payment couldn't be captured
         *
         * By default, a subscription ends automatically if the next payment couldn't
         * be captured. When setting this configuration to FALSE, the subscription job
         * controller will try to capture the payment at the next run again until the
         * subscription is deactivated manually.
         *
         * @param bool TRUE if payment failures ends the subscriptions, FALSE if not
         * @since 2019.10
         * @see controller/jobs/subscription/process/processors
         * @see controller/jobs/subscription/process/payment-days
         * @see controller/jobs/subscription/process/payment-status
         */
        return (bool) $this->context()->config()->get('controller/jobs/subscription/process/payment-ends', true);
    }
    /**
     * Returns the maximum number of orders processed at once
     *
     * @return int Maximum number of items
     */
    protected function max(): int
    {
        /** controller/jobs/subscription/process/batch-max
         * Maximum number of subscriptions processed at once by the subscription process job
         *
         * This setting configures the maximum number of subscriptions including
         * orders that will be processed at once. Bigger batches an improve the
         * performance but requires more memory.
         *
         * @param integer Number of subscriptions
         * @since 2023.04
         * @see controller/jobs/subscription/process/domains
         * @see controller/jobs/subscription/process/names
         * @see controller/jobs/subscription/process/payment-days
         * @see controller/jobs/subscription/process/payment-status
         */
        return $this->context()->config()->get('controller/jobs/subscription/process/batch-max', 100);
    }
    /**
     * Returns the names of the subscription processors
     *
     * @return array List of processor names
     */
    protected function names(): array
    {
        /** controller/jobs/subscription/process/processors
         * List of processor names that should be executed for subscriptions
         *
         * For each subscription a number of processors for different tasks can be executed.
         * They can for example add a group to the customers' account during the customer
         * has an active subscribtion.
         *
         * @param array List of processor names
         * @since 2018.04
         * @see controller/jobs/subscription/process/domains
         * @see controller/jobs/subscription/process/max
         * @see controller/jobs/subscription/process/payment-days
         * @see controller/jobs/subscription/process/payment-status
         */
        return (array) $this->context()->config()->get('controller/jobs/subscription/process/processors', []);
    }
    /**
     * Runs the subscription processors for the passed item
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $item Subscription item
     * @param iterable $processors List of processor objects to run on the item
     * @return \Aimeos\MShop\Subscription\Item\Iface Updated subscription item
     */
    protected function process(\Aimeos\M_Shop\Subscription\Item\Iface $item, iterable $processors): \Aimeos\M_Shop\Subscription\Item\Iface
    {
        $context = $this->context();
        $order_manager = \Aimeos\M_Shop::create($context, 'order');
        $context = $this->create_context($item);
        $new_order = $this->create_order($context, $item);
        foreach ($processors as $processor) {
            $processor->renew_before($item, $new_order);
        }
        $new_order = $order_manager->save($new_order->check());
        try {
            $new_order = $order_manager->save($this->create_payment($context, $new_order));
            $interval = new \DateInterval($item->get_interval());
            $date = date_create((string) $item->get_date_next())->add($interval)->format('Y-m-d H:i:s');
            $item->set_date_next($date)->set_period($item->get_period() + 1)->set_reason(null);
        } catch (\Exception $e) {
            if ($e->get_code() < 1) {
                // not a soft error
                $item->set_reason(\Aimeos\M_Shop\Subscription\Item\Iface::REASON_PAYMENT);
                if ($this->ends()) {
                    $item->set_date_end(date_create()->format('Y-m-d H:i:s'));
                }
            }
            throw $e;
        } finally {
            // will be always executed, even if exception is rethrown in catch()
            foreach ($processors as $processor) {
                $processor->renew_after($item, $new_order);
            }
        }
        return $item;
    }
}