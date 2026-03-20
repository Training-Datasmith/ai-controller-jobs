<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Email;

/**
 * Customer group processor for subscriptions
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Base implements \Aimeos\Controller\Jobs\Common\Subscription\Process\Processor\Iface
{
    use \Aimeos\Controller\Jobs\Mail;
    /**
     * Executed after the subscription renewal
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function renew_after(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order): void
    {
        if ($subscription->get_reason() === \Aimeos\M_Shop\Subscription\Item\Iface::REASON_PAYMENT) {
            $this->notify($subscription, $order);
        }
    }
    /**
     * Processes the end of the subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    public function end(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order): void
    {
        $this->notify($subscription, $order);
    }
    /**
     * Sends e-mails for the given subscription
     *
     * @param \Aimeos\MShop\Subscription\Item\Iface $subscription Subscription item object
     * @param \Aimeos\MShop\Order\Item\Iface $subscription Order item
     */
    protected function notify(\Aimeos\M_Shop\Subscription\Item\Iface $subscription, \Aimeos\M_Shop\Order\Item\Iface $order)
    {
        $address = current($order->get_address('payment'));
        $site_ids = explode('.', trim($order->get_site_id(), '.'));
        $sites = \Aimeos\M_Shop::create($this->context(), 'locale/site')->get_path(end($site_ids));
        $view = $this->view($order, $sites->get_theme()->filter()->last());
        $view->subscription_item = $subscription;
        $view->address_item = $address;
        foreach ($order->get_products() as $order_product) {
            if ($order_product->get_id() == $subscription->get_order_product_id()) {
                $this->send($view->set('orderProductItem', $order_product), $address, $sites->get_logo()->filter()->last());
            }
        }
    }
    /**
     * Sends the subscription e-mail to the customer
     *
     * @param \Aimeos\Base\View\Iface $view View object
     * @param \Aimeos\MShop\Order\Item\Address\Iface $address Address item
     * @param string|null $logoPath Path to the logo
     */
    protected function send(\Aimeos\Base\View\Iface $view, \Aimeos\M_Shop\Order\Item\Address\Iface $address, ?string $logo_path = null)
    {
        /** controller/jobs/order/email/subscription/template-html
         * Relative path to the template for the HTML part of the subscription emails.
         *
         * The template file contains the HTML code and processing instructions
         * to generate the result shown in the body of the frontend. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/controller/jobs).
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates.
         *
         * @param string Relative path to the template
         * @since 2022.04
         * @see controller/jobs/order/email/subscription/template-text
         */
        /** controller/jobs/order/email/subscription/template-text
         * Relative path to the template for the text part of the subscription emails.
         *
         * The template file contains the text and processing instructions
         * to generate the result shown in the body of the frontend. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/controller/jobs).
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates.
         *
         * @param string Relative path to the template
         * @since 2022.04
         * @see controller/jobs/order/email/subscription/template-html
         */
        $context = $this->context();
        $config = $context->config();
        $msg = $this->call('mailTo', $address);
        $view->logo = $msg->embed($this->call('mailLogo', $logo_path), basename((string) $logo_path));
        $msg->subject($context->translate('client', 'Your subscription'))->html($view->render($config->get('controller/jobs/order/email/subscription/template-html', 'order/email/subscription/html')))->text($view->render($config->get('controller/jobs/order/email/subscription/template-text', 'order/email/subscription/text')))->send();
    }
    /**
     * Returns the view populated with common data
     *
     * @param \Aimeos\MShop\Order\Item\Iface $base Basket including addresses
     * @param string|null $theme Theme name
     * @return \Aimeos\Base\View\Iface View object
     */
    protected function view(\Aimeos\M_Shop\Order\Item\Iface $base, ?string $theme = null): \Aimeos\Base\View\Iface
    {
        $address = current($base->get_address('payment'));
        $lang_id = $address->get_language_id() ?: $base->locale()->get_language_id();
        $view = $this->call('mailView', $lang_id);
        $view->intro = $this->call('mailIntro', $address);
        $view->css = $this->call('mailCss', $theme);
        $view->urlparams = ['currency' => $base->get_price()->get_currency_id(), 'site' => $base->get_site_code(), 'locale' => $lang_id];
        return $view;
    }
}