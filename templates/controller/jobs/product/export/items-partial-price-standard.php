<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** controller/jobs/product/export/partials/price
 * Name of the partial used for exporting the price items into the product XML
 *
 * When exporting products into XML files, the assoicated price items are
 * added to the product XML node. This partial receives the list and price
 * items that associate the prices to the product. Then, the partial creates
 * the XML tags for these items that will be inserted into the product XML.
 *
 * @param string Name of the product price partial
 * @since 2019.04
 * @category Developer
 */
$enc = $this->encoder();
?>
<price>
	<?php 
foreach ($this->list_items as $list_item) {
    ?>
		<?php 
    if ($ref_item = $list_item->get_ref_item()) {
        ?>
			<priceitem lists.type="<?php 
        echo $enc->attr($list_item->get_type());
        ?>" lists.config="<?php 
        echo $enc->attr(json_encode($list_item->get_config()));
        ?>"
				lists.datestart="<?php 
        echo $enc->attr(str_replace(' ', 'T', $list_item->get_date_start() ?? ''));
        ?>" lists.dateend="<?php 
        echo $enc->attr(str_replace(' ', 'T', $list_item->get_date_end() ?? ''));
        ?>"
				lists.position="<?php 
        echo $enc->attr($list_item->get_position());
        ?>" lists.status="<?php 
        echo $enc->attr($list_item->get_status());
        ?>">
				<price.type><![CDATA[<?php 
        echo $enc->xml($ref_item->get_type());
        ?>]]></price.type>
				<price.currencyid><![CDATA[<?php 
        echo $enc->xml($ref_item->get_currency_id());
        ?>]]></price.currencyid>
				<price.taxrate><![CDATA[<?php 
        echo $enc->xml($ref_item->get_taxrate());
        ?>]]></price.taxrate>
				<price.quantity><![CDATA[<?php 
        echo $enc->xml($ref_item->get_quantity());
        ?>]]></price.quantity>
				<price.value><![CDATA[<?php 
        echo $enc->xml($ref_item->get_value());
        ?>]]></price.value>
				<price.costs><![CDATA[<?php 
        echo $enc->xml($ref_item->get_costs());
        ?>]]></price.costs>
				<price.rebate><![CDATA[<?php 
        echo $enc->xml($ref_item->get_rebate());
        ?>]]></price.rebate>
				<price.label><![CDATA[<?php 
        echo $enc->xml($ref_item->get_label());
        ?>]]></price.label>
				<price.status><![CDATA[<?php 
        echo $enc->xml($ref_item->get_status());
        ?>]]></price.status>
			</priceitem>
		<?php 
    }
    ?>
	<?php 
}
?>
</price>
