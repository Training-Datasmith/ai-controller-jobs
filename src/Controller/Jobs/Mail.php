<?php

declare (strict_types=1);
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 * @package Controller
 * @subpackage Jobs
 */
namespace Aimeos\Controller\Jobs;

/**
 * Mail trait for job controllers
 *
 * @package Controller
 * @subpackage Jobs
 */
trait Mail
{
    /**
     * Returns the context object
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    abstract protected function context(): \Aimeos\M_Shop\Context_Iface;
    /**
     * Returns the CSS rules for the given theme
     *
     * @param string|null $theme Theme name
     * @return string|null CSS rules
     */
    protected function mail_css(?string $theme): ?string
    {
        $theme = $theme ?: 'default';
        $fs = $this->context()->fs('fs-theme');
        return $fs->has($theme . '/email.css') ? $fs->read($theme . '/email.css') : null;
    }
    /**
     * Returns the e-mail intro message
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address item object
     * @return string Intro message with salutation
     */
    protected function mail_intro(\Aimeos\M_Shop\Common\Item\Address\Iface $addr): string
    {
        $msg = match ($addr->get_salutation()) {
            /// E-mail intro with first name (%1$s) and last name (%2$s)
            '' => $this->context()->translate('controller/jobs', 'Dear %1$s %2$s'),
            /// E-mail intro with first name (%1$s) and last name (%2$s)
            'mr' => $this->context()->translate('controller/jobs', 'Dear Mr %1$s %2$s'),
            /// E-mail intro with first name (%1$s) and last name (%2$s)
            'ms' => $this->context()->translate('controller/jobs', 'Dear Ms %1$s %2$s'),
            default => $this->context()->translate('controller/jobs', 'Dear customer'),
        };
        return sprintf($msg, $addr->get_first_name(), $addr->get_last_name());
    }
    /**
     * Returns the logo for the given path
     *
     * @param string|null $path Logo path relative to fs-media file system
     * @return string Binary logo data
     */
    protected function mail_logo(?string $path): string
    {
        $fs = $this->context()->fs('fs-media');
        return $path && $fs->has($path) ? $fs->read($path) : '';
    }
    /**
     * Prepares and returns a new mail message
     *
     * @param \Aimeos\MShop\Common\Item\Address\Iface $addr Address item object
     * @return \Aimeos\Base\Mail\Message\Iface Prepared mail message
     */
    protected function mail_to(\Aimeos\M_Shop\Common\Item\Address\Iface $addr): \Aimeos\Base\Mail\Message\Iface
    {
        $context = $this->context();
        $config = $context->config();
        $mail = $context->mail()->create()->header('X-MailGenerator', 'Aimeos')->from($config->get('resource/email/from-email'), $config->get('resource/email/from-name'))->to($addr->get_e_mail(), $addr->get_first_name() . ' ' . $addr->get_last_name())->bcc($config->get('resource/email/bcc-email'));
        if ($reply_to = $config->get('resource/email/reply-email')) {
            $mail->reply_to($reply_to);
        }
        return $mail;
    }
    /**
     * Returns the view for generating the mail message content
     *
     * @param string|null $langId Language ID the content should be generated for
     * @return \Aimeos\Base\View\Iface View object
     */
    protected function mail_view(?string $lang_id = null): \Aimeos\Base\View\Iface
    {
        $view = $this->context()->view();
        $helper = new \Aimeos\Base\View\Helper\Number\Locale($view, $lang_id);
        $view->add_helper('number', $helper);
        $helper = new \Aimeos\Base\View\Helper\Translate\Standard($view, $this->context()->i18n($lang_id ?: 'en'));
        $view->add_helper('translate', $helper);
        return $view;
    }
}