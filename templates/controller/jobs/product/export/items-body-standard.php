<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
$enc = $this->encoder();
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<products>
	<?php 
foreach ($this->get('exportItems', []) as $item) {
    ?>
		<productitem ref="<?php 
    echo $enc->attr($item->get_code());
    ?>">
			<product.type><![CDATA[<?php 
    echo $enc->xml($item->get_type());
    ?>]]></product.type>
			<product.code><![CDATA[<?php 
    echo $enc->xml($item->get_code());
    ?>]]></product.code>
			<product.label><![CDATA[<?php 
    echo $enc->xml($item->get_label());
    ?>]]></product.label>
			<product.boost><![CDATA[<?php 
    echo $enc->xml($item->boost());
    ?>]]></product.boost>
			<product.status><![CDATA[<?php 
    echo $enc->xml($item->get_status());
    ?>]]></product.status>
			<product.config><![CDATA[<?php 
    echo $enc->xml(json_encode($item->get_config()));
    ?>]]></product.config>
			<product.datestart><![CDATA[<?php 
    echo $enc->xml(str_replace(' ', 'T', $item->get_date_start() ?? ''));
    ?>]]></product.datestart>
			<product.dateend><![CDATA[<?php 
    echo $enc->xml(str_replace(' ', 'T', $item->get_date_end() ?? ''));
    ?>]]></product.dateend>
			<lists>
				<?php 
    foreach ($item->get_domains() as $domain) {
        ?>
					<?php 
        echo $this->partial('product/export/items-partial-' . str_replace('/', '', $domain) . '-standard', ['listItems' => $item->get_list_items($domain)]);
        ?>
				<?php 
    }
    ?>
			</lists>
			<property>
				<?php 
    foreach ($item->get_property_items() as $prop_item) {
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
		</productitem>
	<?php 
}
?>
</products>