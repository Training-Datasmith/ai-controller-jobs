<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022-2026
 */
/** Available data
 * - orderProductItem : Order product item
 * - orderAddressItem : Order address item
 * - voucher : Voucher code
 */
$enc = $this->encoder();
$logo = $this->get('logodata');
$this->pdf->set_margins(15, 30, 15);
$this->pdf->set_auto_page_break(true, 30);
$this->pdf->set_title(sprintf($this->translate('controller/jobs', 'Voucher %1$s'), $this->get('voucher')));
$this->pdf->set_font('dejavusans', '', 10);
$vmargin = ['h1' => [
    // HTML tag
    0 => ['h' => 1.5, 'n' => 0],
    // space before = h * n
    1 => ['h' => 1.5, 'n' => 3],
], 'h2' => [0 => ['h' => 1.5, 'n' => 10], 1 => ['h' => 1.5, 'n' => 5]], 'ul' => [0 => ['h' => 0, 'n' => 0], 1 => ['h' => 0, 'n' => 0]]];
$this->pdf->set_html_v_space($vmargin);
$this->pdf->set_list_indent_width(4);
$this->pdf->set_header_function(function ($pdf) use ($logo) {
    /* Add background image
       $margin = $pdf->getBreakMargin();
       $pdf->setAutoPageBreak( false, 0 );
       $pdf->image( __DIR__ . '/pdf-background.png', 0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'PNG' );
       $pdf->setAutoPageBreak( true, $margin );
       $pdf->setPageMark();
       */
    $pdf->write_html_cell(210, 20, 0, 0, '
		<div style="background-color: #103050; color: #ffffff; text-align: center; font-weight: bold">
			<div style="font-size: 0px"> </div>
			<img src="' . ($logo ? '@' . base64_encode($logo) : '') . '" height="30">
			Example company
			<div style="font-size: 0px"> </div>
		</div>
	');
});
$this->pdf->set_footer_function(function ($pdf) {
    $pdf->write_html_cell(180, 22.5, 15, -22.5, '
		<table cellpadding="0.5" style="font-size: 8px">
			<tr>
				<td style="font-weight: bold">Example company</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>Example address, 12345 Example city</td>
				<td>District court: </td>
				<td>Bank: </td>
			</tr>
			<tr>
				<td>Telephone: </td>
				<td>Managing director: </td>
				<td>IBAN: </td>
			</tr>
			<tr>
				<td>E-Mail: </td>
				<td>VAT ID: </td>
				<td>BIC: </td>
			</tr>
		</table>
	');
    $pdf->write_html_cell(210, 5, 0, -5, '
		<div style="background-color: #103050; color: #ffffff; text-align: center; font-weight: bold; font-size: 10px">
			example.com
		</div>
	');
});
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$price_format = $pricefmt !== 'price:default' ? $pricefmt : $this->translate('controller/jobs', '%1$s %2$s');
?>
<h1><?php 
echo $enc->html($this->get('intro'));
?></h1>

<h2><?php 
echo nl2br($enc->html($this->translate('controller/jobs', 'Your voucher') . ': ' . $this->voucher, $enc::TRUST));
?></h2>

<p>
	<?php 
$price_currency = $this->translate('currency', $this->order_product_item->get_price()->get_currency_id());
?>
	<?php 
$value = sprintf($price_format, $this->number($this->order_product_item->get_price()->get_value() + $this->order_product_item->get_price()->get_rebate(), $this->order_product_item->get_price()->get_precision()), $price_currency);
?>
	<?php 
echo nl2br($enc->html(sprintf($this->translate('controller/jobs', 'The value of your voucher is %1$s', 'The value of your vouchers are %1$s', count((array) $this->voucher)), $value), $enc::TRUST));
?>
</p>

<p><?php 
echo nl2br($enc->html($this->translate('controller/jobs', 'You can use your vouchers at any time in our online shop'), $enc::TRUST));
?></p>
