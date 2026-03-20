<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
$enc = $this->encoder();
$detail_target = $this->config('client/html/catalog/detail/url/target');
$detail_cntl = $this->config('client/html/catalog/detail/url/controller', 'catalog');
$detail_action = $this->config('client/html/catalog/detail/url/action', 'detail');
$detail_filter = array_flip($this->config('client/html/catalog/detail/url/filter', ['d_prodid']));
$detail_config = $this->config('client/html/catalog/detail/url/config', []);
$detail_config['absoluteUri'] = true;
$locales = $this->get('siteLocales', map());
$sites = $locales->group_by('locale.siteid');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($this->get('siteItems', []) as $id => $item) {
    $lang_ids = [];
    $slug = $item->get_name('url');
    $date = str_replace(' ', 'T', $item->get_time_modified() ?? '') . date('P');
    foreach ($locales as $locale) {
        $lang_id = $locale->get_language_id();
        if (isset($lang_ids[$lang_id])) {
            continue;
        }
        $lang_ids[$lang_id] = true;
        $name = \Aimeos\Base\Str::slug($item->get_name('url', $lang_id));
        $params = ['path' => $name, 'd_name' => $name, 'd_prodid' => $id, 'd_pos' => '', 'site' => $locale->get_site_code()];
        if (count($locales) > 1) {
            $params['locale'] = $lang_id;
            $params['currency'] = $locale->get_currency_id();
        }
        $url = $this->url($item->get_target() ?: $detail_target, $detail_cntl, $detail_action, array_diff_key($params, $detail_filter), [], $detail_config);
        echo '<url><loc>' . $enc->xml($url) . '</loc><lastmod>' . $date . "</lastmod></url>\n";
    }
}
echo "</urlset>\n";