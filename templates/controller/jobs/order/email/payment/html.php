<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** Available data
 * - orderItem: Order Item
 * - addressItem: Billing address item
 * - summaryBasket : Order item (basket) with addresses, services, products, etc.
 */
$enc = $this->encoder();
$key = 'pay:' . $this->order_item->get_status_payment();
$order_status = $this->translate('mshop/code', $key);
$order_date = date_create($this->order_item->get_time_created())->format($this->translate('controller/jobs', 'Y-m-d'));
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$pricefmt = $pricefmt === 'price:default' ? $this->translate('controller/jobs', '%1$s %2$s') : $pricefmt;
?>
<!doctype html><html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office"><head><title> <?php 
echo $enc->html(sprintf($this->translate('controller/jobs', 'Your order %1$s'), $this->order_item->get_invoice_number()));
?> </title><!--[if !mso]><!-- --><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]--><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><style type="text/css">#outlook a { padding:0; }
          .ReadMsgBody { width:100%; }
          .ExternalClass { width:100%; }
          .ExternalClass * { line-height:100%; }
          body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
          table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
          img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
          p { display:block;margin:13px 0; }</style><!--[if !mso]><!--><style type="text/css">@media only screen and (max-width:480px) {
            @-ms-viewport { width:320px; }
            @viewport { width:320px; }
          }</style><!--<![endif]--><!--[if mso]>
        <xml>
        <o:OfficeDocumentSettings>
          <o:AllowPNG/>
          <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
        </xml>
        <![endif]--><!--[if lte mso 11]>
        <style type="text/css">
          .outlook-group-fix { width:100% !important; }
        </style>
        <![endif]--><!--[if !mso]><!--><link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700" rel="stylesheet" type="text/css"><style type="text/css">@import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);</style><!--<![endif]--><style type="text/css">@media only screen and (min-width:480px) {
        .mj-column-per-100 { width:100% !important; max-width: 100%; }
.mj-column-per-50 { width:50% !important; max-width: 50%; }
.mj-column-per-33 { width:33.333333333333336% !important; max-width: 33.333333333333336%; }
      }</style><style type="text/css">@media only screen and (max-width:480px) {
      table.full-width-mobile { width: 100% !important; }
      td.full-width-mobile { width: auto !important; }
    }</style><style type="text/css"><?php 
echo $this->get('css');
?></style></head><body><div class="aimeos"><!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="center" class="logo" style="font-size:0px;padding:10px 25px;word-break:break-word;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"><tbody><tr><td style="width:550px;"><img height="auto" src="<?php 
echo $this->get('logo');
?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;" width="550"></td></tr></tbody></table></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" class="email-common-salutation" style="font-size:0px;padding:10px 25px;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"> <?php 
switch ($this->address_item->get_salutation()) {
    case 'mr':
        ?> <?php 
        echo $enc->html(sprintf($this->translate('controller/jobs', 'Dear Mr %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name()));
        ?> <?php 
        break;
    case 'ms':
        ?> <?php 
        echo $enc->html(sprintf($this->translate('controller/jobs', 'Dear Ms %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name()));
        ?> <?php 
        break;
    default:
        ?> <?php 
        echo $enc->html(sprintf($this->translate('controller/jobs', 'Dear %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name()));
        ?> <?php 
}
?> </div></td></tr><tr><td align="left" class="email-common-intro" style="font-size:0px;padding:10px 25px;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"> <?php 
switch ($this->order_item->get_status_payment()) {
    case 3:
        /// Payment e-mail intro with order ID (%1$s) and order date (%2$s)
        ?> <?php 
        echo sprintf($this->translate('controller/jobs', 'The payment for your order %1$s from %2$s has been refunded.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        ?> <?php 
        break;
    case 4:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        ?> <?php 
        echo sprintf($this->translate('controller/jobs', 'The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        ?> <?php 
        break;
    case 5:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        ?> <?php 
        echo sprintf($this->translate('controller/jobs', 'Thank you for your order %1$s from %2$s.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        ?> <?php 
        break;
    case 6:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        ?> <?php 
        echo sprintf($this->translate('controller/jobs', 'We have received your payment, and will take care of your order immediately.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        ?> <?php 
        break;
    default:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        ?> <?php 
        echo sprintf($this->translate('controller/jobs', 'The payment status of your order %1$s from %2$s has been changed to "%3$s".'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        ?> <?php 
}
?> </div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="common-summary-outlook common-summary-address-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="common-summary common-summary-address" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="item-outlook payment-outlook" style="vertical-align:top;width:300px;" ><![endif]--><div class="mj-column-per-50 outlook-group-fix item payment" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'Billing address'), $enc::TRUST);
?></h3> <?php 
foreach ($this->summary_basket->get_address('payment') as $addr) {
    ?> <div class="content"> <?php 
    echo preg_replace(["/\n+/m", '/ +/'], ['<br/>', ' '], trim($enc->html(sprintf(
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
    ?> </div> <?php 
}
?> </div></td></tr></table></div><!--[if mso | IE]></td><td class="item-outlook delivery-outlook" style="vertical-align:top;width:300px;" ><![endif]--><div class="mj-column-per-50 outlook-group-fix item delivery" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'Delivery address'), $enc::TRUST);
?></h3> <?php 
if (($addresses = $this->summary_basket->get_address('delivery')) !== []) {
    ?> <?php 
    foreach ($addresses as $addr) {
        ?> <div class="content"> <?php 
        echo preg_replace(["/\n+/m", '/ +/'], ['<br/>', ' '], trim($enc->html(sprintf(
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
        ?> </div> <?php 
    }
    ?> <?php 
} else {
    ?> <div class="content"> <?php 
    echo $enc->html($this->translate('controller/jobs', 'like billing address'), $enc::TRUST);
    ?> </div> <?php 
}
?> </div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="common-summary-outlook common-summary-service-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="common-summary common-summary-service" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="item-outlook payment-outlook" style="vertical-align:top;width:300px;" ><![endif]--><div class="mj-column-per-50 outlook-group-fix item payment" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'payment'), $enc::TRUST);
?></h3> <?php 
foreach ($this->summary_basket->get_service('payment') as $service) {
    ?> <div class="content"><h4><?php 
    echo $enc->html($service->get_name());
    ?></h4> <?php 
    if (!($attributes = $service->get_attribute_items())->is_empty()) {
        ?> <ul class="attr-list"> <?php 
        foreach ($attributes as $attribute) {
            ?> <?php 
            if (in_array($attribute->get_type(), ['', 'hidden', 'tx'])) {
                ?> <li class="<?php 
                echo $enc->attr('payment-' . $attribute->get_code());
                ?>"><span class="name"><?php 
                echo $enc->html($attribute->get_name() ?: $this->translate('controller/jobs', $attribute->get_code()));
                ?>:</span> <?php 
                switch ($attribute->get_value()) {
                    case 'array':
                    case 'object':
                        ?> <?php 
                        foreach ((array) $attribute->get_value() as $value) {
                            ?> <span class="value"><?php 
                            echo $enc->html($value);
                            ?></span> <?php 
                        }
                        ?> <?php 
                        break;
                    // no break
                    default:
                        ?> <span class="value"><?php 
                        echo $enc->html($attribute->get_value());
                        ?></span> <?php 
                }
                ?> </li> <?php 
            }
            ?> <?php 
        }
        ?> </ul> <?php 
    }
    ?> </div> <?php 
}
?> </div></td></tr></table></div><!--[if mso | IE]></td><td class="item-outlook delivery-outlook" style="vertical-align:top;width:300px;" ><![endif]--><div class="mj-column-per-50 outlook-group-fix item delivery" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'delivery'), $enc::TRUST);
?></h3> <?php 
foreach ($this->summary_basket->get_service('delivery') as $service) {
    ?> <div class="content"><h4><?php 
    echo $enc->html($service->get_name());
    ?></h4> <?php 
    if (!($attributes = $service->get_attribute_items())->is_empty()) {
        ?> <ul class="attr-list"> <?php 
        foreach ($attributes as $attribute) {
            ?> <?php 
            if (strpos($attribute->get_type(), 'hidden') === false) {
                ?> <li class="<?php 
                echo $enc->attr('delivery-' . $attribute->get_code());
                ?>"><span class="name"><?php 
                echo $enc->html($attribute->get_name() ?: $this->translate('controller/jobs', $attribute->get_code()));
                ?>:</span> <?php 
                switch ($attribute->get_value()) {
                    case 'array':
                    case 'object':
                        ?> <?php 
                        foreach ((array) $attribute->get_value() as $value) {
                            ?> <span class="value"><?php 
                            echo $enc->html($value);
                            ?></span> <?php 
                        }
                        ?> <?php 
                        break;
                    // no break
                    default:
                        ?> <span class="value"><?php 
                        echo $enc->html($attribute->get_value());
                        ?></span> <?php 
                }
                ?> </li> <?php 
            }
            ?> <?php 
        }
        ?> </ul> <?php 
    }
    ?> </div> <?php 
}
?> </div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="common-summary-outlook common-summary-additional-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="common-summary common-summary-additional" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="item-outlook coupon-outlook" style="vertical-align:top;width:200px;" ><![endif]--><div class="mj-column-per-33 outlook-group-fix item coupon" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'Coupon codes'), $enc::TRUST);
?></h3><div class="content"> <?php 
if (!($coupons = $this->summary_basket->get_coupons())->is_empty()) {
    ?> <ul class="attr-list"> <?php 
    foreach ($coupons as $code => $products) {
        ?> <li class="attr-item"><?php 
        echo $enc->html($code);
        ?></li> <?php 
    }
    ?> </ul> <?php 
}
?> </div></div></td></tr></table></div><!--[if mso | IE]></td><td class="item-outlook customerref-outlook" style="vertical-align:top;width:200px;" ><![endif]--><div class="mj-column-per-33 outlook-group-fix item customerref" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'Your reference'), $enc::TRUST);
?></h3><div class="content"> <?php 
echo $enc->attr($this->summary_basket->get_customer_reference());
?> </div></div></td></tr></table></div><!--[if mso | IE]></td><td class="item-outlook comment-outlook" style="vertical-align:top;width:200px;" ><![endif]--><div class="mj-column-per-33 outlook-group-fix item comment" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:inherit;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"><h3><?php 
echo $enc->html($this->translate('controller/jobs', 'Your comment'), $enc::TRUST);
?></h3><div class="content"> <?php 
echo $enc->html($this->summary_basket->get_comment());
?> </div></div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="common-summary-outlook common-summary-detail-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="common-summary common-summary-detail" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" class="basket" style="font-size:0px;padding:10px 25px;word-break:break-word;"><table cellpadding="0" cellspacing="0" width="100%" border="0" style="cellspacing:0;color:#000000;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;"><tr class="header"><th class="status"></th><th class="label"><?php 
echo $enc->html($this->translate('controller/jobs', 'Name'), $enc::TRUST);
?></th><th class="quantity"><?php 
echo $enc->html($this->translate('controller/jobs', 'Qty'), $enc::TRUST);
?></th><th class="price"><?php 
echo $enc->html($this->translate('controller/jobs', 'Sum'), $enc::TRUST);
?></th></tr> <?php 
$total_qty = 0;
?> <?php 
foreach ($this->summary_basket->get_products() as $product) {
    $total_qty += $product->get_quantity();
    ?> <tr class="body product"><td class="status"> <?php 
    if (($status = $product->get_status_delivery()) >= 0) {
        $key = 'stat:' . $status;
        ?> <?php 
        echo $enc->html($this->translate('mshop/code', $key));
        ?> <?php 
    }
    ?> </td><td class="label"> <?php 
    $params = array_merge($this->param(), ['d_name' => $product->get_name('url'), 'd_prodid' => $product->get_parent_product_id() ?: $product->get_product_id(), 'd_pos' => '']);
    ?> <span class="product-name"><?php 
    echo $enc->html($product->get_name(), $enc::TRUST);
    ?></span><p class="code"><span class="name"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Article no.'), $enc::TRUST);
    ?>: </span><span class="value"><?php 
    echo $product->get_product_code();
    ?></span></p> <?php 
    if (($desc = $product->get_description()) !== '') {
        ?> <p class="product-description"><?php 
        echo $enc->html($desc);
        ?></p> <?php 
    }
    ?> <?php 
    foreach (['variant', 'config', 'custom'] as $attr_type) {
        ?> <?php 
        if (!($attributes = $product->get_attribute_items($attr_type))->is_empty()) {
            ?> <ul class="attr-list attr-type-<?php 
            echo $enc->attr($attr_type);
            ?>"> <?php 
            foreach ($attributes as $attribute) {
                ?> <li class="attr-item attr-code-<?php 
                echo $enc->attr($attribute->get_code());
                ?>"><span class="name"><?php 
                echo $enc->html($this->translate('controller/jobs', $attribute->get_code()));
                ?>:</span> <span class="value"> <?php 
                if ($attribute->get_quantity() > 1) {
                    ?> <?php 
                    echo $enc->html($attribute->get_quantity());
                    ?>× <?php 
                }
                ?> <?php 
                echo $enc->html($attr_type !== 'custom' && $attribute->get_name() ? $attribute->get_name() : $attribute->get_value());
                ?> </span></li> <?php 
            }
            ?> </ul> <?php 
        }
        ?> <?php 
    }
    ?> <?php 
    if ($this->order_item->get_status_payment() >= \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED && ($product->get_status_payment() < 0 || $product->get_status_payment() >= \Aimeos\M_Shop\Order\Item\Base::PAY_RECEIVED) && $attribute = $product->get_attribute_item('download', 'hidden')) {
        ?> <ul class="attr-list attr-list-hidden"><li class="attr-item attr-code-<?php 
        echo $enc->attr($attribute->get_code());
        ?>"><span class="name"><?php 
        echo $enc->html($this->translate('controller/jobs', $attribute->get_code()));
        ?></span><span class="value"><a href="<?php 
        echo $enc->attr($this->link('client/html/account/download/url', ['dl_id' => $attribute->get_id()], ['absoluteUri' => 1]));
        ?>"> <?php 
        echo $enc->html($attribute->get_name());
        ?> </a></span></li></ul> <?php 
    }
    ?> <?php 
    if (($timeframe = $product->get_timeframe()) !== '') {
        ?> <p class="timeframe"><span class="name"><?php 
        echo $enc->html($this->translate('controller/jobs', 'Delivery within'));
        ?>: </span><span class="value"><?php 
        echo $enc->html($timeframe);
        ?></span></p> <?php 
    }
    ?> </td><td class="quantity"> <?php 
    echo $enc->html($product->get_quantity());
    ?> </td><td class="price"> <?php 
    echo $enc->html(sprintf($pricefmt, $this->number($product->get_price()->get_value() * $product->get_quantity(), $product->get_price()->get_precision()), $this->translate('currency', $product->get_price()->get_currency_id())));
    ?> </td></tr> <?php 
}
?> <?php 
foreach ($this->summary_basket->get_service('delivery') as $service) {
    ?> <?php 
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        ?> <tr class="body delivery"><td class="status"></td><td class="label"><?php 
        echo $enc->html($service->get_name());
        ?></td><td class="quantity">1</td><td class="price"><?php 
        echo $enc->html(sprintf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
        ?></td></tr> <?php 
    }
    ?> <?php 
}
?> <?php 
foreach ($this->summary_basket->get_service('payment') as $service) {
    ?> <?php 
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        ?> <tr class="body payment"><td class="status"></td><td class="label"><?php 
        echo $enc->html($service->get_name());
        ?></td><td class="quantity">1</td><td class="price"><?php 
        echo $enc->html(sprintf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
        ?></td></tr> <?php 
    }
    ?> <?php 
}
?> <?php 
if ($this->summary_basket->get_price()->get_costs() > 0 || $this->summary_basket->get_price()->get_tax_flag() === false) {
    ?> <tr class="footer subtotal"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Sub-total'));
    ?></td><td class="quantity"></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> <?php 
if (($costs = $this->summary_basket->get_costs()) > 0) {
    ?> <tr class="footer delivery"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', '+ Shipping'));
    ?></td><td class="quantity"></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> <?php 
if (($costs = $this->summary_basket->get_costs('payment')) > 0) {
    ?> <tr class="footer payment"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', '+ Payment costs'));
    ?></td><td class="quantity"></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> <?php 
if ($this->summary_basket->get_price()->get_tax_flag() === true) {
    ?> <tr class="footer total"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Total'));
    ?></td><td class="quantity"><?php 
    echo $enc->html($total_qty);
    ?></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> <?php 
foreach ($this->summary_basket->get_taxes() as $tax_name => $map) {
    ?> <?php 
    foreach ($map as $tax_rate => $price_item) {
        ?> <?php 
        if (($tax_value = $price_item->get_tax_value()) > 0) {
            ?> <tr class="footer tax"><td class="status"></td><td class="label"><?php 
            echo $enc->html(sprintf($price_item->get_tax_flag() ? $this->translate('controller/jobs', 'Incl. %1$s%% %2$s') : $this->translate('controller/jobs', '+ %1$s%% %2$s'), $this->number($tax_rate), $this->translate('controller/jobs', $tax_name)));
            ?></td><td class="quantity"></td><td class="price"><?php 
            echo $enc->html(sprintf($pricefmt, $this->number($tax_value, $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
            ?></td></tr> <?php 
        }
        ?> <?php 
    }
    ?> <?php 
}
?> <?php 
if ($this->summary_basket->get_price()->get_tax_flag() === false) {
    ?> <tr class="footer total"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Total'));
    ?></td><td class="quantity"><?php 
    echo $enc->html($total_qty);
    ?></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs() + $this->summary_basket->get_price()->get_tax_value(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> <?php 
if ($this->summary_basket->get_price()->get_rebate() > 0) {
    ?> <tr class="footer rebate"><td class="status"></td><td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Included rebates'));
    ?></td><td class="quantity"></td><td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_rebate(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td></tr> <?php 
}
?> </table></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="email-common-outro-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="email-common-outro" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"> <?php 
echo $enc->html(nl2br($this->translate('controller/jobs', 'If you have any questions, please reply to this e-mail')));
?> </div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="email-common-legal-outlook" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]--><div class="email-common-legal" style="Margin:0px auto;max-width:600px;"><table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;vertical-align:top;"><!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]--><div class="mj-column-per-100 outlook-group-fix" style="font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"><table border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"><tr><td align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"><div style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#000000;"> <?php 
echo nl2br($enc->html($this->translate('controller/jobs', 'All orders are subject to our terms and conditions.')));
?> </div></td></tr></table></div><!--[if mso | IE]></td></tr></table><![endif]--></td></tr></tbody></table></div><!--[if mso | IE]></td></tr></table><![endif]--></div></body></html>