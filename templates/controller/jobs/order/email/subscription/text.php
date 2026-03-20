<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 */
$enc = $this->encoder();
$target = $this->config('client/html/catalog/detail/url/target');
$cntl = $this->config('client/html/catalog/detail/url/controller', 'catalog');
$action = $this->config('client/html/catalog/detail/url/action', 'detail');
$config = $this->config('client/html/catalog/detail/url/config', ['absoluteUri' => 1]);
$filter = array_flip($this->config('client/html/catalog/detail/url/filter', ['d_prodid']));
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$price_format = $pricefmt !== 'price:default' ? $pricefmt : $this->translate('controller/jobs', '%1$s %2$s');
/// Price quantity format with quantity (%1$s)
$quantity_format = $this->translate('controller/jobs', 'from %1$s');
/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
$cost_format = $this->translate('controller/jobs', '+ %1$s %2$s/item');
/// Rebate format with rebate value (%1$s) and currency (%2$s)
$rebate_format = $this->translate('controller/jobs', '%1$s %2$s off');
/// Rebate percent format with rebate percent value (%1$s)
$rebate_percent_format = '(' . $this->translate('controller/jobs', '-%1$s%%') . ')';
/// Tax rate format with tax rate in percent (%1$s)
$vat_format = $this->translate('controller/jobs', 'Incl. %1$s%% VAT');
echo wordwrap(strip_tags($this->get('intro', '')));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'The subscription for the product has ended')));
?>:

<?php 
switch ($this->subscription_item->get_reason()) {
    case -1:
        ?>
	<?php 
        echo wordwrap(strip_tags($this->translate('controller/jobs', 'The payment couldn\'t be renewed')));
        break;
    case 1:
        ?>
	<?php 
        echo wordwrap(strip_tags($this->translate('controller/jobs', 'You\'ve cancelled the subscription')));
}
?>



<?php 
echo strip_tags($this->translate('controller/jobs', 'Subscription product'));
?>:
<?php 
echo strip_tags($this->order_product_item->get_name());
?>


<?php 
$price = $this->order_product_item->get_price();
$price_currency = $this->translate('currency', $price->get_currency_id());
printf($price_format, $this->number($price->get_value(), $price->get_precision()), $price_currency);
?> <?php 
$price->get_rebate() > '0.00' ? printf($rebate_percent_format, $this->number(round($price->get_rebate() * 100 / ($price->get_value() + $price->get_rebate())), 0)) : '';
if ($price->get_costs() > 0) {
    echo ' ' . strip_tags(sprintf($cost_format, $this->number($price->get_costs(), $price->get_precision()), $price_currency));
}
if ($price->get_taxrate() > 0) {
    echo ', ' . strip_tags(sprintf($vat_format, $this->number($price->get_taxrate())));
}
?>

<?php 
$params = array_diff_key(array_merge($this->get('urlparams'), ['currency' => $this->order_product_item->get_price()->get_currency_id(), 'd_name' => $this->order_product_item->get_name('url'), 'd_prodid' => $this->order_product_item->get_parent_product_id() ?: $this->order_product_item->get_product_id(), 'd_pos' => '']), $filter);
echo $this->url($this->order_product_item->get_target() ?: $target, $cntl, $action, $params, [], $config);
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'If you have any questions, please reply to this e-mail')));