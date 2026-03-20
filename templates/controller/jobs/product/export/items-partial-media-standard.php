<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** controller/jobs/product/export/partials/media
 * Name of the partial used for exporting the media items into the product XML
 *
 * When exporting products into XML files, the assoicated media items are
 * added to the product XML node. This partial receives the list and media
 * items that associate the media to the product. Then, the partial creates
 * the XML tags for these items that will be inserted into the product XML.
 *
 * @param string Name of the product media partial
 * @since 2019.04
 * @category Developer
 */
$enc = $this->encoder();
?>
<media>
	<?php 
foreach ($this->list_items as $list_item) {
    ?>
		<?php 
    if ($ref_item = $list_item->get_ref_item()) {
        ?>
			<mediaitem lists.type="<?php 
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
				<media.type><![CDATA[<?php 
        echo $enc->xml($ref_item->get_type());
        ?>]]></media.type>
				<media.languageid><![CDATA[<?php 
        echo $enc->xml($ref_item->get_language_id());
        ?>]]></media.languageid>
				<media.label><![CDATA[<?php 
        echo $enc->xml($ref_item->get_label());
        ?>]]></media.label>
				<media.url><![CDATA[<?php 
        echo $enc->xml($ref_item->get_url());
        ?>]]></media.url>
				<media.preview><![CDATA[<?php 
        echo $enc->xml($ref_item->get_preview());
        ?>]]></media.preview>
				<media.mimetype><![CDATA[<?php 
        echo $enc->xml($ref_item->get_mimetype());
        ?>]]></media.mimetype>
				<media.status><![CDATA[<?php 
        echo $enc->xml($ref_item->get_status());
        ?>]]></media.status>
				<property>
					<?php 
        foreach ($ref_item->get_property_items() as $prop_item) {
            ?>
						<propertyitem>
							<product.property.type><![CDATA[<?php 
            echo $enc->xml($prop_item->get_type());
            ?>]]></product.property.type>
							<product.property.languageid><![CDATA[<?php 
            echo $enc->xml($prop_item->get_language_id());
            ?>]]></product.property.languageid>
							<product.property.value><![CDATA[<?php 
            echo $enc->xml($prop_item->get_value());
            ?>]]></product.property.value>
						</propertyitem>
					<?php 
        }
        ?>
				</property>
			</mediaitem>
		<?php 
    }
    ?>
	<?php 
}
?>
</media>
