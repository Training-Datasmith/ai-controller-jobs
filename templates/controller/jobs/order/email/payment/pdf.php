<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** Available data
 * - orderItem: Order item
 * - addressItem: Billing address item
 * - summaryBasket : Order item (basket) with addresses, services, products, etc.
 */
$enc = $this->encoder();
$logo = $this->get('logodata');
$this->pdf->set_margins(15, 30, 15);
$this->pdf->set_auto_page_break(true, 30);
$this->pdf->set_title(sprintf($this->translate('controller/jobs', 'Invoice %1$s'), $this->order_item->get_invoice_number()));
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
// SEPA payment QR-Code
$total = $this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs();
$data = [
    'BCD',
    // required
    '002',
    // Version 2 (required, 1=UTF-8, 2=ISO 8859-1, 3=ISO 8859-2, 4=ISO 8859-4, 5=ISO 8859-5, 6=ISO 8859-7, 7=ISO 8859-10, 8=ISO 8859-15)
    1,
    // UTF-8 (required)
    'SCT',
    // SEPA Credit Transfer (required)
    '',
    // BIC (optional)
    '',
    // Name of recipient (required, name of your company)
    '',
    // IBAN (required)
    $this->summary_basket->get_price()->get_currency_id() . $total,
    // Currency and value (required)
    '',
    // Purpose (optional, 4 char code, https://wiki.windata.de/index.php?title=Purpose-SEPA-Codes)
    '',
    // ISO 11649 RF Creditor Reference (optional, 35 characters structured code)
    $this->translate('controller/jobs', 'Invoice') . ' ' . $this->order_item->get_invoice_number(),
    // Reference of order and other data (optional, max. 140 characters)
    $this->summary_basket->get_customer_reference(),
];
$barcode = new Tcpdf2d_Barcode(join("\n", $data), 'QRCODE,M');
?>
<style>
	.address-self { font-size: 7px; font-weight: normal }
	.address-self .company { font-weight: bold }
	.meta { font-size: 8.5px }
</style>
<h1 class="address-self">
	<span class="company">Example company</span> · Example address · 12345 Example city
</h1>
<table>
	<tr>
		<td class="address" style="width: 66%">
			<?php 
if ($addr = current($this->summary_basket->get_address('payment'))) {
    ?>
				<?php 
    echo preg_replace(['/^[ ]+/', '/\n+/m', '/ +/'], ['', '<br/>', ' '], trim($enc->html(sprintf(
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
    ?>
			<?php 
}
?>
		</td>
		<td style="width: 33%">
			<table class="meta">
				<tr>
					<td><?php 
echo $enc->html($this->translate('controller/jobs', 'Order date'));
?>:</td>
					<td><?php 
echo $enc->html(date_create($this->order_item->get_time_created())->format($this->translate('controller/jobs', 'Y-m-d')));
?></td>
				</tr>
				<?php 
if ($this->summary_basket->get_customer_reference()) {
    ?>
					<tr>
						<td><?php 
    echo $enc->html($this->translate('controller/jobs', 'Reference'));
    ?>:</td>
						<td><?php 
    echo $enc->html($this->summary_basket->get_customer_reference());
    ?></td>
					</tr>
				<?php 
}
?>
				<tr>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="2">
						<img style="padding: 5px" src="@<?php 
echo base64_encode($barcode->get_barcode_png_data(3, 3, [0, 0, 0]));
?>" />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<h2><?php 
echo $enc->html($this->translate('controller/jobs', 'Invoice'));
?>: <?php 
echo $enc->html($this->order_item->get_invoice_number());
?></h2>
<?php 
echo $this->partial(
    /** controller/jobs/order/email/payment/pdf-partial
     * Location of the address partial template for the text e-mails
     *
     * To configure an alternative template for the address partial, you
     * have to configure its path relative to the template directory
     * (usually templates/controller/jobs/). It's then used to display the
     * payment or delivery address block in the text e-mails.
     *
     * @param string Relative path to the address partial
     * @since 2020.07
     */
    $this->config('controller/jobs/order/email/payment/pdf-partial', 'order/email/summary-pdf'),
    ['orderItem' => $this->order_item, 'summaryBasket' => $this->summary_basket]
);