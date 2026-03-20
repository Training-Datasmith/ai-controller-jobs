<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 */
/** Available data
 * - orderProductItem : Order product item
 * - orderAddressItem : Order address item
 * - voucher : Voucher code
 */
$enc = $this->encoder();
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$price_format = $pricefmt !== 'price:default' ? $pricefmt : $this->translate('controller/jobs', '%1$s %2$s');
echo wordwrap(strip_tags($this->get('intro', '')));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'Your voucher') . ': ' . $this->voucher));
?>


<?php 
$price = $this->order_product_item->get_price();
$price_currency = $this->translate('currency', $price->get_currency_id());
$value = sprintf($price_format, $this->number($price->get_value() + $price->get_rebate(), $price->get_precision()), $price_currency);
echo wordwrap(strip_tags(sprintf($this->translate('controller/jobs', 'The value of your voucher is %1$s', 'The value of your vouchers are %1$s', count((array) $this->voucher)), $value)));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'You can use your vouchers at any time in our online shop')));