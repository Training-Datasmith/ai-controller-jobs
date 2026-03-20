<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
$enc = $this->encoder();
$detail_target = $this->config('client/html/catalog/detail/url/target');
$detail_controller = $this->config('client/html/catalog/detail/url/controller', 'catalog');
$detail_action = $this->config('client/html/catalog/detail/url/action', 'detail');
$detail_config = $this->config('client/html/catalog/detail/url/config', ['absoluteUri' => 1]);
$detail_filter = array_flip($this->config('client/html/catalog/detail/url/filter', ['d_prodid']));
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$price_format = $pricefmt !== 'price:default' ? $pricefmt : $this->translate('controller/jobs', '%1$s %2$s');
/// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
$cost_format = $this->translate('controller/jobs', '+ %1$s %2$s/item');
/// Rebate percent format with rebate percent value (%1$s)
$rebate_percent_format = '(' . $this->translate('controller/jobs', '-%1$s%%') . ')';
/// Tax rate format with tax rate in percent (%1$s)
$vat_format = $this->translate('controller/jobs', 'Incl. %1$s%% VAT');
echo wordwrap(strip_tags($this->get('intro', '')));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'One or more products you are watching have been updated.')));
?>



<?php 
echo strip_tags($this->translate('controller/jobs', 'Watched products'));
?>:
<?php 
foreach ($this->get('products') as $product) {
    ?>

<?php 
    echo strip_tags($product->get_name());
    ?>


<?php 
    $price = $product->get('price');
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
    $params = array_diff_key(array_merge($this->get('urlparams'), ['currency' => $price->get_currency_id(), 'd_name' => $product->get_name('url'), 'd_prodid' => $product->get_id(), 'd_pos' => '']), $detail_filter);
    echo $this->url($product->get_target() ?: $detail_target, $detail_controller, $detail_action, $params, [], $detail_config);
    ?>

<?php 
}
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'If you have any questions, please reply to this e-mail')));