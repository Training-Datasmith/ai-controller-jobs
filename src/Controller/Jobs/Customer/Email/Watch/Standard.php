<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Customer
 */
namespace Aimeos\Controller\Jobs\Customer\Email\Watch;

/**
 * Product notification e-mail job controller.
 *
 * @package Controller
 * @subpackage Customer
 */
class Standard extends \Aimeos\Controller\Jobs\Base implements \Aimeos\Controller\Jobs\Iface
{
    /** controller/jobs/customer/email/watch/name
     * Class name of the used product notification e-mail scheduler controller implementation
     *
     * Each default job controller can be replace by an alternative imlementation.
     * To use this implementation, you have to set the last part of the class
     * name as configuration value so the controller factory knows which class it
     * has to instantiate.
     *
     * For example, if the name of the default class is
     *
     *  \Aimeos\Controller\Jobs\Customer\Email\Watch\Standard
     *
     * and you want to replace it with your own version named
     *
     *  \Aimeos\Controller\Jobs\Customer\Email\Watch\Mywatch
     *
     * then you have to set the this configuration option:
     *
     *  controller/jobs/customer/email/watch/name = Mywatch
     *
     * The value is the last part of your own class name and it's case sensitive,
     * so take care that the configuration value is exactly named like the last
     * part of the class name.
     *
     * The allowed characters of the class name are A-Z, a-z and 0-9. No other
     * characters are possible! You should always start the last part of the class
     * name with an upper case character and continue only with lower case characters
     * or numbers. Avoid chamel case names like "MyWatch"!
     *
     * @param string Last part of the class name
     * @since 2014.03
     */
    /** controller/jobs/customer/email/watch/decorators/excludes
     * Excludes decorators added by the "common" option from the customer email watch controllers
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to remove a decorator added via
     * "controller/jobs/common/decorators/default" before they are wrapped
     * around the job controller.
     *
     *  controller/jobs/customer/email/watch/decorators/excludes = array( 'decorator1' )
     *
     * This would remove the decorator named "decorator1" from the list of
     * common decorators ("\Aimeos\Controller\Jobs\Common\Decorator\*") added via
     * "controller/jobs/common/decorators/default" to this job controller.
     *
     * @param array List of decorator names
     * @since 2015.09
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/customer/email/watch/decorators/global
     * @see controller/jobs/customer/email/watch/decorators/local
     */
    /** controller/jobs/customer/email/watch/decorators/global
     * Adds a list of globally available decorators only to the customer email watch controllers
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap global decorators
     * ("\Aimeos\Controller\Jobs\Common\Decorator\*") around the job controller.
     *
     *  controller/jobs/customer/email/watch/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Controller\Jobs\Common\Decorator\Decorator1" only to this job controller.
     *
     * @param array List of decorator names
     * @since 2015.09
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/customer/email/watch/decorators/excludes
     * @see controller/jobs/customer/email/watch/decorators/local
     */
    /** controller/jobs/customer/email/watch/decorators/local
     * Adds a list of local decorators only to the customer email watch controllers
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Controller\Jobs\Customer\Email\Watch\Decorator\*") around this job controller.
     *
     *  controller/jobs/customer/email/watch/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Controller\Jobs\Customer\Email\Watch\Decorator\Decorator2" only to this job
     * controller.
     *
     * @param array List of decorator names
     * @since 2015.09
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/customer/email/watch/decorators/excludes
     * @see controller/jobs/customer/email/watch/decorators/global
     */
    use \Aimeos\Controller\Jobs\Mail;
    private array $sites = [];
    /**
     * Returns the localized name of the job.
     *
     * @return string Name of the job
     */
    public function get_name(): string
    {
        return $this->context()->translate('controller/jobs', 'Product notification e-mails');
    }
    /**
     * Returns the localized description of the job.
     *
     * @return string Description of the job
     */
    public function get_description(): string
    {
        return $this->context()->translate('controller/jobs', 'Sends e-mails for watched products');
    }
    /**
     * Executes the job.
     *
     * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
     */
    public function run(): void
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'customer');
        $filter = $manager->filter(true);
        $func = $filter->make('customer:has', ['product', 'watch']);
        $filter->add($filter->is($func, '!=', null))->order('customer.id');
        $cursor = $manager->cursor($filter);
        while ($items = $manager->iterate($cursor, ['product' => ['watch'], 'price'])) {
            $manager->save($this->notify($items));
        }
    }
    /**
     * Sends product notifications for the given customers in their language
     *
     * @param \Aimeos\Map $customers List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
     * @return \Aimeos\Map List of customer items implementing \Aimeos\MShop\Customer\Item\Iface
     */
    protected function notify(\Aimeos\Map $customers): \Aimeos\Map
    {
        $date = date('Y-m-d H:i:s');
        $context = $this->context();
        foreach ($customers as $customer) {
            $list_items = $customer->get_list_items('product', null, null, false);
            $products = $this->products($list_items);
            try {
                if (!empty($products)) {
                    $sites = $this->sites($customer->get_site_id());
                    $context->locale()->set_language_id($customer->get_payment_address()->get_language_id());
                    $view = $this->view($customer->get_payment_address(), $sites->get_theme()->filter()->last());
                    $view->products = $products;
                    $this->send($view, $customer->get_payment_address(), $sites->get_logo()->filter()->last());
                }
                $str = sprintf('Sent product notification e-mail to "%1$s"', $customer->get_payment_address()->get_email());
                $context->logger()->debug($str, 'email/customer/watch');
            } catch (\Exception $e) {
                $str = 'Error while trying to send product notification e-mail for customer ID "%1$s": %2$s';
                $msg = sprintf($str, $customer->get_id(), $e->get_message()) . PHP_EOL . $e->get_trace_as_string();
                $context->logger()->error($msg, 'email/customer/watch');
            }
            $remove = $list_items->diff_keys($products)->filter(fn($list_item) => $list_item->get_date_end() < $date);
            $customer->delete_list_items($remove);
        }
        return $customers;
    }
    /**
     * Returns a filtered list of products for which a notification should be sent
     *
     * @param \Aimeos\Map $listItems List of customer list items
     * @return array Associative list of list IDs as key and product items values
     */
    protected function products(\Aimeos\Map $list_items): array
    {
        $result = [];
        $price_manager = \Aimeos\M_Shop::create($this->context(), 'price');
        foreach ($list_items as $id => $list_item) {
            try {
                if ($product = $list_item->get_ref_item()) {
                    $config = $list_item->get_config();
                    $prices = $product->get_ref_items('price', 'default', 'default');
                    $price = $price_manager->get_lowest_price($prices, 1, $config['currency'] ?? null);
                    if (($config['stock'] ?? null) && $product->in_stock() || ($config['price'] ?? null) && ($config['pricevalue'] ?? 0) > $price->get_value()) {
                        $result[$id] = $product->set('price', $price);
                    }
                }
            } catch (\Exception) {
            }
            // no price available
        }
        return $result;
    }
    /**
     * Sends the notification e-mail for the given customer address and products
     *
     * @param \Aimeos\Base\View\Iface $view View object
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Address item
     * @param string|null $logoPath Path to the logo
     */
    protected function send(\Aimeos\Base\View\Iface $view, \Aimeos\M_Shop\Common\Item\Address\Iface $address, ?string $logo_path = null)
    {
        /** controller/jobs/customer/email/watch/template-html
         * Relative path to the template for the HTML part of the watch emails.
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
         * @see controller/jobs/customer/email/watch/template-text
         */
        /** controller/jobs/customer/email/watch/template-text
         * Relative path to the template for the text part of the watch emails.
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
         * @see controller/jobs/customer/email/watch/template-html
         */
        $context = $this->context();
        $config = $context->config();
        $msg = $this->call('mailTo', $address);
        $view->logo = $msg->embed($this->call('mailLogo', $logo_path), basename((string) $logo_path));
        $msg->subject($context->translate('client', 'Your watched products'))->html($view->render($config->get('controller/jobs/customer/email/watch/template-html', 'customer/email/watch/html')))->text($view->render($config->get('controller/jobs/customer/email/watch/template-text', 'customer/email/watch/text')))->send();
    }
    /**
     * Returns the list of site items from the given site ID up to the root site
     *
     * @param string|null $siteId Site ID like "1.2.4."
     * @return \Aimeos\Map List of site items
     */
    protected function sites(?string $site_id = null): \Aimeos\Map
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'locale/site');
        if (!$site_id && !isset($this->sites[''])) {
            $default = $this->context()->config()->get('mshop/locale/site', 'default');
            $this->sites[''] = map($manager->find($default));
        }
        if (!isset($this->sites[(string) $site_id])) {
            $manager = \Aimeos\M_Shop::create($this->context(), 'locale/site');
            $site_ids = explode('.', trim((string) $site_id, '.'));
            $this->sites[$site_id] = $manager->get_path(end($site_ids));
        }
        return $this->sites[$site_id];
    }
    /**
     * Returns the view populated with common data
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $address Address item
     * @param string|null $theme Theme name
     * @return \Aimeos\Base\View\Iface View object
     */
    protected function view(\Aimeos\M_Shop\Common\Item\Address\Iface $address, ?string $theme = null): \Aimeos\Base\View\Iface
    {
        $view = $this->call('mailView', $address->get_language_id());
        $view->intro = $this->call('mailIntro', $address);
        $view->css = $this->call('mailCss', $theme);
        $view->urlparams = ['site' => $this->context()->locale()->get_site_item()->get_code(), 'locale' => $address->get_language_id()];
        return $view;
    }
}