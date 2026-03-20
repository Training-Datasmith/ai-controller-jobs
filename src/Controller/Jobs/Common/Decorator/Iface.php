<?php

declare (strict_types=1);
/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Jobs
 */
namespace Aimeos\Controller\Jobs\Common\Decorator;

/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Iface extends \Aimeos\Controller\Jobs\Iface
{
    /**
     * Initializes a new controller decorator object.
     *
     * @param \Aimeos\Controller\Jobs\Iface $controller Controller object
     * @param \Aimeos\MShop\ContextIface $context Context object with required objects
     * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
     */
    public function __construct(\Aimeos\Controller\Jobs\Iface $controller, \Aimeos\M_Shop\Context_Iface $context, \Aimeos\Bootstrap $aimeos);
}