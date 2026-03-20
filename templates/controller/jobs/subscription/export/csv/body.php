<?php

declare (strict_types=1);
$subscription_fcn = function (\Aimeos\M_Shop\Subscription\Item\Iface $item) {
    return ['subscription', $item->get_id(), $item->get_interval(), $item->get_date_next(), $item->get_date_end(), $item->get_period(), $item->get_status(), $item->get_order_id()];
};
$address_fcn = function (\Aimeos\M_Shop\Order\Item\Address\Iface $item) {
    return ['address', $item->get_parent_id(), $item->get_type(), $item->get_salutation(), $item->get_company(), $item->get_vat_id(), $item->get_title(), $item->get_first_name(), $item->get_last_name(), $item->get_address1(), $item->get_address2(), $item->get_address3(), $item->get_postal(), $item->get_city(), $item->get_state(), $item->get_country_id(), $item->get_language_id(), $item->get_telephone(), $item->get_telefax(), $item->get_email(), $item->get_website(), $item->get_longitude(), $item->get_latitude()];
};
$product_fcn = function (\Aimeos\M_Shop\Order\Item\Product\Iface $item) {
    $list = ['product', $item->get_parent_id(), $item->get_type(), $item->get_stock_type(), $item->get_vendor(), $item->get_product_code(), $item->get_scale(), $item->get_quantity(), $item->get_quantity_open(), $item->get_name(), $item->get_description(), $item->get_media_url(), $item->get_price()->get_value(), $item->get_price()->get_costs(), $item->get_price()->get_rebate(), $item->get_price()->get_taxrate(), $item->get_price()->get_taxvalue(), $item->get_price()->get_taxflag(), $item->get_status_payment(), $item->get_status_delivery(), $item->get_timeframe(), $item->get_position(), $item->get_notes()];
    if ($attr = $item->get_attribute_items()->first()) {
        $list[] = $attr->get_type();
        $list[] = $attr->get_code();
        $list[] = $attr->get_name();
        $list[] = $attr->get_value();
    }
    return $list;
};
foreach ($this->get('items', []) as $item) {
    echo '"' . join('","', $subscription_fcn($item)) . '"' . "\n";
    if ($order_item = $item->get_order_item()) {
        foreach ($order_item->get_address('payment') as $address) {
            echo '"' . join('","', $address_fcn($address)) . '"' . "\n";
        }
        foreach ($order_item->get_address('delivery') as $address) {
            echo '"' . join('","', $address_fcn($address)) . '"' . "\n";
        }
        foreach ($order_item->get_products() as $product) {
            echo '"' . join('","', $product_fcn($product)) . '"' . "\n";
            foreach ($product->get_products() as $sub_product) {
                echo '"' . join('","', $product_fcn($sub_product)) . '"' . "\n";
            }
        }
    }
}