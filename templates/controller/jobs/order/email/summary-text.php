<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
/** Available data
 * - orderItem: Order Item
 * - summaryBasket : Order item (basket) with addresses, services, products, etc.
 */
$enc = $this->encoder();
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$pricefmt = $pricefmt === 'price:default' ? $this->translate('controller/jobs', '%1$s %2$s') : $pricefmt;
// for xgettext to extract used tax names
$this->translate('controller/jobs', 'tax');
$this->translate('controller/jobs', 'taxpst');
$this->translate('controller/jobs', 'taxgst');
echo strip_tags($this->translate('controller/jobs', 'Billing address'));
?>:

<?php 
foreach ($this->summary_basket->get_address('payment') as $addr) {
    echo preg_replace(["/\n+/m", '/ +/'], ["\n", ' '], trim($enc->html(sprintf(
        /// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
        /// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
        /// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
        /// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
        $this->translate('controller/jobs', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'),
        $addr->get_company(),
        $this->translate('mshop/code', $addr->get_salutation()),
        $addr->get_title(),
        $addr->get_first_name(),
        $addr->get_last_name(),
        $addr->get_address1(),
        $addr->get_address2(),
        $addr->get_address3(),
        $addr->get_postal(),
        $addr->get_city(),
        $addr->get_state(),
        $this->translate('country', $addr->get_country_id()),
        $this->translate('language', $addr->get_language_id()),
        $addr->get_email(),
        $addr->get_telephone(),
        $addr->get_telefax(),
        $addr->get_website(),
        $addr->get_vat_id()
    ))));
}
?>



<?php 
echo strip_tags($this->translate('controller/jobs', 'Delivery address'));
?>:

<?php 
if (!empty($addr_items = $this->summary_basket->get_address('delivery'))) {
    foreach ($addr_items as $addr) {
        echo preg_replace(["/\n+/m", '/ +/'], ["\n", ' '], trim($enc->html(sprintf(
            /// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
            /// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
            /// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
            /// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
            $this->translate('controller/jobs', '%1$s
%2$s %3$s %4$s %5$s
%6$s %7$s
%8$s
%9$s %10$s
%11$s
%12$s
%13$s
%14$s
%15$s
%16$s
%17$s
%18$s
'),
            $addr->get_company(),
            $this->translate('mshop/code', $addr->get_salutation()),
            $addr->get_title(),
            $addr->get_first_name(),
            $addr->get_last_name(),
            $addr->get_address1(),
            $addr->get_address2(),
            $addr->get_address3(),
            $addr->get_postal(),
            $addr->get_city(),
            $addr->get_state(),
            $this->translate('country', $addr->get_country_id()),
            $this->translate('language', $addr->get_language_id()),
            $addr->get_email(),
            $addr->get_telephone(),
            $addr->get_telefax(),
            $addr->get_website(),
            $addr->get_vat_id()
        ))));
    }
} else {
    echo $this->translate('controller/jobs', 'like billing address');
}
?>



<?php 
if (($services = $this->summary_basket->get_service('delivery')) !== []) {
    echo strip_tags($this->translate('controller/jobs', 'delivery'));
    ?>:
<?php 
    foreach ($services as $service) {
        ?>

<?php 
        echo strip_tags($service->get_name());
        ?>

<?php 
        foreach ($service->get_attribute_items() as $attribute) {
            $name = $attribute->get_name() != '' ? $attribute->get_name() : $this->translate('controller/jobs', $attribute->get_code());
            switch ($attribute->get_value()) {
                case 'array':
                case 'object':
                    $value = join(', ', (array) $attribute->get_value());
                    break;
                default:
                    $value = $attribute->get_value();
            }
            echo '- ' . strip_tags($name) . ': ' . strip_tags($value) . "\n";
        }
    }
}
?>


<?php 
if (($services = $this->summary_basket->get_service('payment')) !== []) {
    echo strip_tags($this->translate('controller/jobs', 'payment'));
    ?>:
<?php 
    foreach ($services as $service) {
        ?>

<?php 
        echo strip_tags($service->get_name());
        ?>

<?php 
        foreach ($service->get_attribute_items() as $attribute) {
            if (in_array($attribute->get_type(), ['', 'hidden', 'tx'])) {
                $name = $attribute->get_name() != '' ? $attribute->get_name() : $this->translate('controller/jobs', $attribute->get_code());
                switch ($attribute->get_value()) {
                    case 'array':
                    case 'object':
                        $value = join(', ', (array) $attribute->get_value());
                        break;
                    default:
                        $value = $attribute->get_value();
                }
                echo '- ' . strip_tags($name) . ': ' . strip_tags($value) . "\n";
            }
        }
    }
}
?>


<?php 
if (!($coupons = $this->summary_basket->get_coupons())->is_empty()) {
    echo strip_tags($this->translate('controller/jobs', 'Coupons'));
    ?>:
<?php 
    foreach ($coupons as $code => $products) {
        echo '- ' . $code . "\n";
    }
    ?>

<?php 
}
if ($this->summary_basket->get_customer_reference() != '') {
    echo strip_tags($this->translate('controller/jobs', 'Your reference number'));
    ?>:
<?php 
    echo strip_tags($this->summary_basket->get_customer_reference()) . "\n";
    ?>

<?php 
}
if ($this->summary_basket->get_comment() != '') {
    echo strip_tags($this->translate('controller/jobs', 'Your comment'));
    ?>:
<?php 
    echo strip_tags($this->summary_basket->get_comment()) . "\n";
    ?>

<?php 
}
?>


<?php 
echo strip_tags($this->translate('controller/jobs', 'Order details'));
?>:
<?php 
foreach ($this->summary_basket->get_products() as $product) {
    $price_item = $product->get_price();
    ?>

<?php 
    echo strip_tags($product->get_name());
    ?> (<?php 
    echo $product->get_product_code();
    ?>)
<?php 
    foreach (['variant', 'config', 'custom'] as $attr_type) {
        foreach ($product->get_attribute_items($attr_type) as $attribute) {
            ?>
- <?php 
            echo strip_tags($this->translate('controller/jobs', $attribute->get_code()));
            ?>: <?php 
            echo $attribute->get_quantity() > 1 ? $attribute->get_quantity() . '× ' : '';
            echo strip_tags($attribute->get_name() != '' ? $attribute->get_name() : $attribute->get_value());
            ?>

<?php 
        }
    }
    if ($this->order_item->get_status_payment() >= \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED && ($product->get_status_payment() < 0 || $product->get_status_payment() >= \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED) && $attribute = $product->get_attribute_item('download', 'hidden')) {
        ?>
- <?php 
        echo strip_tags($attribute->get_name());
        ?>: <?php 
        echo $this->link('client/html/account/download/url', ['dl_id' => $attribute->get_id()], ['absoluteUri' => true]);
        ?>

<?php 
    }
    echo strip_tags($this->translate('controller/jobs', 'Quantity'));
    ?>: <?php 
    echo $product->get_quantity();
    ?>

<?php 
    echo strip_tags($this->translate('controller/jobs', 'Price'));
    ?>: <?php 
    printf($pricefmt, $this->number($price_item->get_value() * $product->get_quantity(), $price_item->get_precision()), $price_item->get_currency_id());
    ?>

<?php 
    if (($status = $product->get_status_delivery()) >= 0) {
        $key = 'stat:' . $status;
        echo strip_tags($this->translate('controller/jobs', 'Status'));
        ?>: <?php 
        echo strip_tags($this->translate('mshop/code', $key));
    }
}
?>

<?php 
foreach ($this->summary_basket->get_service('delivery') as $service) {
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        echo strip_tags($service->get_name());
        ?>

<?php 
        echo strip_tags($this->translate('controller/jobs', 'Price'));
        ?>: <?php 
        printf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $price_item->get_currency_id());
        ?>

<?php 
    }
}
foreach ($this->summary_basket->get_service('payment') as $service) {
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        echo strip_tags($service->get_name());
        ?>

<?php 
        echo strip_tags($this->translate('controller/jobs', 'Price'));
        ?>: <?php 
        printf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $price_item->get_currency_id());
        ?>

<?php 
    }
}
?>

<?php 
echo strip_tags($this->translate('controller/jobs', 'Sub-total'));
?>: <?php 
printf($pricefmt, $this->number($this->summary_basket->get_price()->get_value(), $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
?>

<?php 
if (($costs = $this->summary_basket->get_costs()) > 0) {
    echo strip_tags($this->translate('controller/jobs', '+ Shipping'));
    ?>: <?php 
    printf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
    ?>

<?php 
}
if (($costs = $this->summary_basket->get_costs('payment')) > 0) {
    echo strip_tags($this->translate('controller/jobs', '+ Payment costs'));
    ?>: <?php 
    printf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
    ?>

<?php 
}
if ($this->summary_basket->get_price()->get_tax_flag() === true) {
    echo strip_tags($this->translate('controller/jobs', 'Total'));
    ?>: <?php 
    printf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs(), $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
    ?>

<?php 
}
foreach ($this->summary_basket->get_taxes() as $tax_name => $map) {
    foreach ($map as $tax_rate => $price_item) {
        if (($tax_value = $price_item->get_tax_value()) > 0) {
            $tax_format = $price_item->get_tax_flag() ? $this->translate('controller/jobs', 'Incl. %1$s%% %2$s') : $this->translate('controller/jobs', '+ %1$s%% %2$s');
            echo strip_tags(sprintf($tax_format, $this->number($tax_rate), $this->translate('controller/jobs', $tax_name)));
            ?>: <?php 
            printf($pricefmt, $this->number($tax_value, $price_item->get_precision()), $price_item->get_currency_id());
            ?>

<?php 
        }
    }
}
if ($this->summary_basket->get_price()->get_tax_flag() === false) {
    echo strip_tags($this->translate('controller/jobs', 'Total'));
    ?>: <?php 
    printf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs() + $this->summary_basket->get_price()->get_tax_value(), $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
    ?>

<?php 
}
if ($this->summary_basket->get_price()->get_rebate() > 0) {
    echo strip_tags($this->translate('controller/jobs', 'Included rebates'));
    ?>: <?php 
    printf($pricefmt, $this->number($this->summary_basket->get_price()->get_rebate(), $this->summary_basket->get_price()->get_precision()), $this->summary_basket->get_price()->get_currency_id());
    ?>

<?php 
}