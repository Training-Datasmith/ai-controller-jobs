<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
/** Available data
 * - orderItem: Order item
 * - addressItem: Billing address item
 * - summaryBasket : Order item (basket) with addresses, services, products, etc.
 */
$key = 'pay:' . $this->order_item->get_status_payment();
$order_status = $this->translate('mshop/code', $key);
$order_date = date_create($this->order_item->get_time_created())->format($this->translate('controller/jobs', 'Y-m-d'));
switch ($this->address_item->get_salutation()) {
    case 'mr':
        echo sprintf($this->translate('controller/jobs', 'Dear Mr %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name());
        break;
    case 'ms':
        echo sprintf($this->translate('controller/jobs', 'Dear Ms %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name());
        break;
    default:
        echo sprintf($this->translate('controller/jobs', 'Dear %1$s %2$s'), $this->address_item->get_first_name(), $this->address_item->get_last_name());
}
?>


<?php 
switch ($this->order_item->get_status_payment()) {
    case 3:
        /// Payment e-mail intro with order ID (%1$s) and order date (%2$s)
        echo sprintf($this->translate('controller/jobs', 'The payment for your order %1$s from %2$s has been refunded.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        break;
    case 4:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        echo sprintf($this->translate('controller/jobs', 'The order is pending until we receive the final payment. If you\'ve chosen to pay in advance, please transfer the money to our bank account with the order ID %1$s as reference.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        break;
    case 5:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        echo sprintf($this->translate('controller/jobs', 'Thank you for your order %1$s from %2$s.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        break;
    case 6:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        echo sprintf($this->translate('controller/jobs', 'We have received your payment, and will take care of your order immediately.'), $this->order_item->get_invoice_number(), $order_date, $order_status);
        break;
    default:
        /// Payment e-mail intro with order ID (%1$s), order date (%2$s) and payment status (%3$s)
        echo sprintf($this->translate('controller/jobs', 'The payment status of your order %1$s from %2$s has been changed to "%3$s".'), $this->order_item->get_invoice_number(), $order_date, $order_status);
}
?>


<?php 
echo $this->partial('order/email/summary-text', ['orderItem' => $this->order_item, 'summaryBasket' => $this->summary_basket]);
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'If you have any questions, please reply to this e-mail')));
?>


<?php 
echo wordwrap(strip_tags($this->translate('controller/jobs', 'All orders are subject to our terms and conditions.')));