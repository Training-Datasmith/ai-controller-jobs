<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2026
 */
/** controller/jobs/product/export/partials/attribute
 * Name of the partial used for exporting the attribute items into the product XML
 *
 * When exporting products into XML files, the assoicated attribute items are
 * added to the product XML node. This partial receives the list and attribute
 * items that associate the attributes to the product. Then, the partial creates
 * the XML tags for these items that will be inserted into the product XML.
 *
 * @param string Name of the product attribute partial
 * @since 2019.04
 * @category Developer
 */
$enc = $this->encoder();
?>
<attribute>
	<?php 
foreach ($this->list_items as $list_item) {
    ?>
		<?php 
    if ($ref_item = $list_item->get_ref_item()) {
        ?>
			<attributeitem ref="<?php 
        echo $enc->attr($ref_item->get_domain() . '|' . $ref_item->get_type() . '|' . $ref_item->get_code());
        ?>"
				lists.type="<?php 
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
        ?>" />
		<?php 
    }
    ?>
	<?php 
}
?>
</attribute>
