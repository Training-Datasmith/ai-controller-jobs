<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Media;

/**
 * Media processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface
{
    /** controller/jobs/product/import/csv/processor/media/name
     * Name of the media processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Media\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2015.10
     */
    private ?array $list_types = null;
    private array $types = [];
    private array $mimes = [];
    /**
     * Initializes the object
     *
     * @param \Aimeos\MShop\ContextIface $context Context object
     * @param array $mapping Associative list of field position in CSV as key and domain item key as value
     * @param \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface $object Decorated processor
     */
    public function __construct(\Aimeos\M_Shop\Context_Iface $context, array $mapping, ?\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface $object = null)
    {
        parent::__construct($context, $mapping, $object);
        $config = $context->config();
        $this->mimes = array_flip($config->get('mshop/media/manager/extensions', []));
        /** controller/jobs/product/import/csv/media/listtypes
         * Names of the product list types for media that are updated or removed
         *
         * If you want to associate media items manually via the administration
         * interface to products and don't want these to be touched during the
         * import, you can specify the product list types for these media
         * that shouldn't be updated or removed.
         *
         * @param array|null List of product list type names or null for all
         * @since 2015.05
         * @see controller/jobs/product/import/csv/domains
         * @see controller/jobs/product/import/csv/separator
         * @see controller/jobs/product/import/csv/attribute/listtypes
         * @see controller/jobs/product/import/csv/catalog/listtypes
         * @see controller/jobs/product/import/csv/product/listtypes
         * @see controller/jobs/product/import/csv/price/listtypes
         * @see controller/jobs/product/import/csv/supplier/listtypes
         * @see controller/jobs/product/import/csv/text/listtypes
         */
        $default = $config->get('controller/jobs/product/import/csv/processor/media/listtypes');
        $this->list_types = $config->get('controller/jobs/product/import/csv/media/listtypes', $default);
        if ($this->list_types === null) {
            $this->list_types = [];
            $manager = \Aimeos\M_Shop::create($context, 'product/lists/type');
            $search = $manager->filter()->slice(0, 0x7fffffff);
            foreach ($manager->search($search) as $item) {
                $this->list_types[$item->get_code()] = $item->get_code();
            }
        } else {
            $this->list_types = array_combine($this->list_types, $this->list_types);
        }
        $manager = \Aimeos\M_Shop::create($context, 'media/type');
        $search = $manager->filter()->slice(0, 0x7fffffff);
        foreach ($manager->search($search) as $item) {
            $this->types[$item->get_code()] = $item->get_code();
        }
    }
    /**
     * Saves the product related data to the storage
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Product\Item\Iface $product, array $data): array
    {
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'product');
        $ref_manager = \Aimeos\M_Shop::create($context, 'media');
        $separator = $context->config()->get('controller/jobs/product/import/csv/separator', "\n");
        $pos = 0;
        $list_map = [];
        $map = $this->get_mapped_chunk($data, $this->get_mapping());
        $list_items = $product->get_list_items('media', $this->list_types, null, false);
        foreach ($list_items as $list_item) {
            if (($ref_item = $list_item->get_ref_item()) !== null) {
                $list_map[$ref_item->get_url()][$ref_item->get_type()][$ref_item->get_language_id()][$list_item->get_type()] = $list_item;
            }
        }
        foreach ($map as $list) {
            if ($this->check_entry($list) === false) {
                continue;
            }
            $type = trim($this->val($list, 'media.type', 'default'));
            $lang_id = trim($this->val($list, 'media.languageid', ''));
            $listtype = trim($this->val($list, 'product.lists.type', 'default'));
            $list_config = $this->get_list_config(trim($this->val($list, 'product.lists.config', '')));
            unset($list['product.lists.config']);
            $urls = explode($separator, trim($this->val($list, 'media.url', '')));
            unset($list['media.url']);
            $this->add_type('product/lists/type', 'media', $listtype);
            $this->add_type('media/type', 'product', $type);
            foreach ($urls as $url) {
                $url = trim($url);
                if (isset($list_map[$url][$type][$lang_id][$listtype])) {
                    $list_item = $list_map[$url][$type][$lang_id][$listtype];
                    $ref_item = $list_item->get_ref_item();
                    unset($list_items[$list_item->get_id()]);
                } else {
                    $list_item = $manager->create_list_item()->set_type($listtype);
                    $ref_item = $ref_manager->create()->set_type($type);
                }
                $ext = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                if (isset($this->mimes[$ext])) {
                    $ref_item->set_mime_type($this->mimes[$ext]);
                }
                $ref_item->set_domain('product');
                $ref_item = $this->update($ref_item, $list, $url);
                $list_item = $list_item->set_position($pos++)->from_array($list)->set_config($list_config);
                $product->add_list_item('media', $list_item, $ref_item);
            }
        }
        $product->delete_list_items($list_items->to_array(), true);
        return $this->object()->process($product, $data);
    }
    /**
     * Checks if an entry can be used for updating a media item
     *
     * @param array $list Associative list of key/value pairs from the mapping
     * @return bool True if valid, false if not
     */
    protected function check_entry(array $list): bool
    {
        if ($this->val($list, 'media.url') === null) {
            return false;
        }
        if (($type = trim($this->val($list, 'product.lists.type', 'default'))) && !isset($this->list_types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'product list');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        if (($type = trim($this->val($list, 'media.type', 'default'))) && !isset($this->types[$type])) {
            $msg = sprintf('Invalid type "%1$s" (%2$s)', $type, 'media');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return true;
    }
    /**
     * Updates the media item with the given key/value pairs
     *
     * @param \Aimeos\MShop\Media\Item\Iface $refItem Media item to update
     * @param array &$list Associative list of key/value pairs, matching pairs are removed
     * @return \Aimeos\MShop\Media\Item\Iface Updated media item
     */
    protected function update(\Aimeos\M_Shop\Media\Item\Iface $ref_item, array &$list, string $url): \Aimeos\M_Shop\Media\Item\Iface
    {
        try {
            if (isset($list['media.previews']) && ($map = json_decode($list['media.previews'], true)) !== null) {
                $ref_item->set_previews($map)->set_url($url);
            } elseif (isset($list['media.preview'])) {
                $ref_item->set_preview($list['media.preview'])->set_url($url);
            } elseif ($ref_item->get_url() !== $url) {
                $ref_item = \Aimeos\M_Shop::create($this->context(), 'media')->scale($ref_item->set_url($url), true);
            } else {
                $ref_item = \Aimeos\M_Shop::create($this->context(), 'media')->scale($ref_item->set_url($url));
            }
            unset($list['media.previews'], $list['media.preview']);
        } catch (\Exception $e) {
            $msg = sprintf('Scaling image "%1$s" failed: %2$s', $url, $e->get_message());
            $this->context()->logger()->error($msg, 'import/csv/product');
        }
        return $ref_item->from_array($list);
    }
}