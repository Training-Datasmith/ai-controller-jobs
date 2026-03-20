<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Cache;

/**
 * Attribute cache for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Base
{
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
     * Returns the context object
     *
     * @return \Aimeos\MShop\ContextIface Context object
     */
    protected function context(): \Aimeos\M_Shop\Context_Iface
    {
        return $this->context;
    }
}