<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */
$enc = $this->encoder();
$tree_target = $this->config('client/html/catalog/tree/url/target');
$tree_cntl = $this->config('client/html/catalog/tree/url/controller', 'catalog');
$tree_action = $this->config('client/html/catalog/tree/url/action', 'list');
$tree_filter = array_flip($this->config('client/html/catalog/tree/url/filter', []));
$tree_config = $this->config('client/html/catalog/tree/url/config', []);
$tree_config['absoluteUri'] = true;
$locales = $this->get('siteLocales', map());
$sites = $locales->group_by('locale.siteid');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($this->get('siteItems', []) as $id => $item) {
    $lang_ids = [];
    $date = str_replace(' ', 'T', $item->get_time_modified() ?? '') . date('P');
    foreach ($locales as $locale) {
        $lang_id = $locale->get_language_id();
        if (isset($lang_ids[$lang_id])) {
            continue;
        }
        $lang_ids[$lang_id] = true;
        $name = $item->get_name('url', $lang_id);
        $params = ['f_name' => \Aimeos\Base\Str::slug($name), 'f_catid' => $id];
        if (count($sites) > 1) {
            $params['site'] = $locale->get_site_code();
        }
        if (count($locales) > 1) {
            $params['locale'] = $lang_id;
        }
        $url = $this->url($item->get_target() ?: $tree_target, $tree_cntl, $tree_action, array_diff_key($params, $tree_filter), [], $tree_config);
        echo '<url><loc>' . $enc->xml($url) . '</loc><lastmod>' . $date . "</lastmod></url>\n";
    }
}
echo "</urlset>\n";