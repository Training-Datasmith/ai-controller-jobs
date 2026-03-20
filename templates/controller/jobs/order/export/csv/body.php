<?php

declare (strict_types=1);
$order_fcn = function (\Aimeos\M_Shop\Order\Item\Iface $item) {
    return ['invoice', $item->get_id(), $item->get_channel(), $item->get_date_payment(), $item->get_status_payment(), $item->get_date_delivery(), $item->get_status_delivery(), $item->get_related_id(), $item->get_customer_id(), $item->get_sitecode(), $item->locale()->get_language_id(), $item->locale()->get_currency_id(), $item->get_price()->get_value(), $item->get_price()->get_costs(), $item->get_price()->get_rebate(), $item->get_price()->get_taxvalue(), $item->get_price()->get_taxflag(), $item->get_comment()];
};
$address_fcn = function (\Aimeos\M_Shop\Order\Item\Address\Iface $item) {
    return ['address', $item->get_parent_id(), $item->get_type(), $item->get_salutation(), $item->get_company(), $item->get_vat_id(), $item->get_title(), $item->get_first_name(), $item->get_last_name(), $item->get_address1(), $item->get_address2(), $item->get_address3(), $item->get_postal(), $item->get_city(), $item->get_state(), $item->get_country_id(), $item->get_language_id(), $item->get_telephone(), $item->get_telefax(), $item->get_email(), $item->get_website(), $item->get_longitude(), $item->get_latitude()];
};
$service_fcn = function (\Aimeos\M_Shop\Order\Item\Service\Iface $item) {
    $list = ['service', $item->get_parent_id(), $item->get_type(), $item->get_code(), $item->get_name(), $item->get_media_url(), $item->get_price()->get_value(), $item->get_price()->get_costs(), $item->get_price()->get_rebate(), $item->get_price()->get_taxrate()];
    if ($attr = $item->get_attribute_items()->first()) {
        $list[] = $attr->get_type();
        $list[] = $attr->get_code();
        $list[] = $attr->get_name();
        $list[] = $attr->get_value();
    }
    return $list;
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
    echo '"' . join('","', $order_fcn($item)) . '"' . "\n";
    foreach ($item->get_address('payment') as $address) {
        echo '"' . join('","', $address_fcn($address)) . '"' . "\n";
    }
    foreach ($item->get_address('delivery') as $address) {
        echo '"' . join('","', $address_fcn($address)) . '"' . "\n";
    }
    foreach ($item->get_service('payment') as $service) {
        echo '"' . join('","', $service_fcn($service)) . '"' . "\n";
    }
    foreach ($item->get_service('delivery') as $service) {
        echo '"' . join('","', $service_fcn($service)) . '"' . "\n";
    }
    foreach ($item->get_coupons() as $code => $list) {
        echo '"coupon","' . $item->get_id() . '""' . str_replace('"', '\"', $code) . '"' . "\n";
    }
    foreach ($item->get_products() as $product) {
        echo '"' . join('","', $product_fcn($product)) . '"' . "\n";
        foreach ($product->get_products() as $sub_product) {
            echo '"' . join('","', $product_fcn($sub_product)) . '"' . "\n";
        }
    }
}