<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** Available data
 * - orderItem: Order item
 * - summaryBasket : Order item (basket) with addresses, services, products, etc.
 */
$total_qty = 0;
$enc = $this->encoder();
$pricetype = 'price:default';
$pricefmt = $this->translate('controller/jobs', $pricetype);
/// Price format with price value (%1$s) and currency (%2$s)
$pricefmt = $pricefmt === 'price:default' ? $this->translate('controller/jobs', '%1$s %2$s') : $pricefmt;
?>
<style>
	.basket .label { width: 45%; text-align: left }
	.basket .code { width: 20%; text-align: center }
	.basket .quantity { width: 15%; text-align: center }
	.basket .price { width: 20%; text-align: right }
	.basket .header .price { text-align: center }
	.basket .header { background-color: #f8f8f8; font-weight: bold }
	.basket .subtotal, .basket .total { background-color: #f8f8f8; font-weight: bold }
	.basket .product { border: 1px solid #f8f8f8 }
</style>
<table class="basket" cellpadding="5">
	<tr class="header">
		<th class="label"><?php 
echo $enc->html($this->translate('controller/jobs', 'Name'), $enc::TRUST);
?></th>
		<th class="code"><?php 
echo $enc->html($this->translate('controller/jobs', 'Article no.'), $enc::TRUST);
?></th>
		<th class="quantity"><?php 
echo $enc->html($this->translate('controller/jobs', 'Qty'), $enc::TRUST);
?></th>
		<th class="price"><?php 
echo $enc->html($this->translate('controller/jobs', 'Sum'), $enc::TRUST);
?></th>
	</tr>
	<?php 
foreach ($this->summary_basket->get_products() as $product) {
    $total_qty += $product->get_quantity();
    ?>
		<tr class="body product">
			<td class="label">
				<?php 
    echo $enc->html($product->get_name(), $enc::TRUST);
    ?>
				<?php 
    if (($desc = $product->get_description()) !== '') {
        ?>
					<p class="product-description"><?php 
        echo $enc->html($desc);
        ?></p>
				<?php 
    }
    ?>
				<?php 
    foreach (['variant', 'config', 'custom'] as $attr_type) {
        ?>
					<?php 
        if (!($attributes = $product->get_attribute_items($attr_type))->is_empty()) {
            ?>
						<ul class="attr-list attr-type-<?php 
            echo $enc->attr($attr_type);
            ?>">
							<?php 
            foreach ($attributes as $attribute) {
                ?>
								<li class="attr-item attr-code-<?php 
                echo $enc->attr($attribute->get_code());
                ?>">
									<span class="name"><?php 
                echo $enc->html($this->translate('controller/jobs', $attribute->get_code()));
                ?>:</span>
									<span class="value">
										<?php 
                if ($attribute->get_quantity() > 1) {
                    ?>
											<?php 
                    echo $enc->html($attribute->get_quantity());
                    ?>×
										<?php 
                }
                ?>
										<?php 
                echo $enc->html($attr_type !== 'custom' && $attribute->get_name() ? $attribute->get_name() : $attribute->get_value());
                ?>
									</span>
								</li>
							<?php 
            }
            ?>
						</ul>
					<?php 
        }
        ?>
				<?php 
    }
    ?>
			</td>
			<td class="code">
				<?php 
    echo $product->get_product_code();
    ?>
			</td>
			<td class="quantity">
				<?php 
    echo $enc->html($product->get_quantity());
    ?>
			</td>
			<td class="price">
				<?php 
    echo $enc->html(sprintf($pricefmt, $this->number($product->get_price()->get_value() * $product->get_quantity(), $product->get_price()->get_precision()), $this->translate('currency', $product->get_price()->get_currency_id())));
    ?>
			</td>
		</tr>
	<?php 
}
?>

	<?php 
foreach ($this->summary_basket->get_service('delivery') as $service) {
    ?>
		<?php 
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        ?>
			<tr class="body delivery">
				<td class="label"><?php 
        echo $enc->html($service->get_name());
        ?></td>
				<td class="code"></td>
				<td class="quantity">1</td>
				<td class="price"><?php 
        echo $enc->html(sprintf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
        ?></td>
			</tr>
		<?php 
    }
    ?>
	<?php 
}
?>

	<?php 
foreach ($this->summary_basket->get_service('payment') as $service) {
    ?>
		<?php 
    if ($service->get_price()->get_value() > 0) {
        $price_item = $service->get_price();
        ?>
			<tr class="body payment">
				<td class="label"><?php 
        echo $enc->html($service->get_name());
        ?></td>
				<td class="code"></td>
				<td class="quantity">1</td>
				<td class="price"><?php 
        echo $enc->html(sprintf($pricefmt, $this->number($price_item->get_value(), $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
        ?></td>
			</tr>
		<?php 
    }
    ?>
	<?php 
}
?>

	<?php 
if ($this->summary_basket->get_price()->get_costs() > 0 || $this->summary_basket->get_price()->get_tax_flag() === false) {
    ?>
		<tr class="footer subtotal">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Sub-total'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>

	<?php 
if (($costs = $this->summary_basket->get_costs()) > 0) {
    ?>
		<tr class="footer delivery">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', '+ Shipping'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>

	<?php 
if (($costs = $this->summary_basket->get_costs('payment')) > 0) {
    ?>
		<tr class="footer payment">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', '+ Payment costs'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($costs, $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>

	<?php 
if ($this->summary_basket->get_price()->get_tax_flag() === true) {
    ?>
		<tr class="footer total">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Total'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"><?php 
    echo $enc->html($total_qty);
    ?></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>

	<?php 
foreach ($this->summary_basket->get_taxes() as $tax_name => $map) {
    ?>
		<?php 
    foreach ($map as $tax_rate => $price_item) {
        ?>
			<?php 
        if (($tax_value = $price_item->get_tax_value()) > 0) {
            ?>
				<tr class="footer tax">
					<td class="label"><?php 
            echo $enc->html(sprintf($price_item->get_tax_flag() ? $this->translate('controller/jobs', 'Incl. %1$s%% %2$s') : $this->translate('controller/jobs', '+ %1$s%% %2$s'), $this->number($tax_rate), $this->translate('controller/jobs', $tax_name)));
            ?></td>
					<td class="code"></td>
					<td class="quantity"></td>
					<td class="price"><?php 
            echo $enc->html(sprintf($pricefmt, $this->number($tax_value, $price_item->get_precision()), $this->translate('currency', $price_item->get_currency_id())));
            ?></td>
				</tr>
			<?php 
        }
        ?>
		<?php 
    }
    ?>
	<?php 
}
?>

	<?php 
if ($this->summary_basket->get_price()->get_tax_flag() === false) {
    ?>
		<tr class="footer total">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Total'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"><?php 
    echo $enc->html($total_qty);
    ?></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_value() + $this->summary_basket->get_price()->get_costs() + $this->summary_basket->get_price()->get_tax_value(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>

	<?php 
if ($this->summary_basket->get_price()->get_rebate() > 0) {
    ?>
		<tr class="footer rebate">
			<td class="label"><?php 
    echo $enc->html($this->translate('controller/jobs', 'Included rebates'));
    ?></td>
			<td class="code"></td>
			<td class="quantity"></td>
			<td class="price"><?php 
    echo $enc->html(sprintf($pricefmt, $this->number($this->summary_basket->get_price()->get_rebate(), $this->summary_basket->get_price()->get_precision()), $this->translate('currency', $this->summary_basket->get_price()->get_currency_id())));
    ?></td>
		</tr>
	<?php 
}
?>
</table>
