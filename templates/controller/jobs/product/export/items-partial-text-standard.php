<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** controller/jobs/product/export/partials/text
 * Name of the partial used for exporting the text items into the product XML
 *
 * When exporting products into XML files, the assoicated text items are
 * added to the product XML node. This partial receives the list and text
 * items that associate the texts to the product. Then, the partial creates
 * the XML tags for these items that will be inserted into the product XML.
 *
 * @param string Name of the product text partial
 * @since 2019.04
 * @category Developer
 */
$enc = $this->encoder();
?>
<text>
	<?php 
foreach ($this->list_items as $list_item) {
    ?>
		<?php 
    if ($ref_item = $list_item->get_ref_item()) {
        ?>
			<textitem lists.type="<?php 
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
				<text.type><![CDATA[<?php 
        echo $enc->xml($ref_item->get_type());
        ?>]]></text.type>
				<text.languageid><![CDATA[<?php 
        echo $enc->xml($ref_item->get_language_id());
        ?>]]></text.languageid>
				<text.label><![CDATA[<?php 
        echo $enc->xml($ref_item->get_label());
        ?>]]></text.label>
				<text.content><![CDATA[<?php 
        echo $enc->xml($ref_item->get_content());
        ?>]]></text.content>
				<text.status><![CDATA[<?php 
        echo $enc->xml($ref_item->get_status());
        ?>]]></text.status>
			</textitem>
		<?php 
    }
    ?>
	<?php 
}
?>
</text>
