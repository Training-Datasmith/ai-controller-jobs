<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 * @package Controller
 * @subpackage Common
 */
namespace Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Stock;

/**
 * Product stock processor for CSV imports
 *
 * @package Controller
 * @subpackage Common
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Base implements \Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Iface
{
    /** controller/jobs/product/import/csv/processor/stock/name
     * Name of the stock processor implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Controller\Jobs\Common\Product\Import\Csv\Processor\Stock\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the processor class name
     * @since 2015.10
     */
    /**
     * Saves the product stock related data to the storage
     *
     * @param \Aimeos\MShop\Product\Item\Iface $product Product item with associated items
     * @param array $data List of CSV fields with position as key and data as value
     * @return array List of data which hasn't been imported
     */
    public function process(\Aimeos\M_Shop\Product\Item\Iface $product, array $data): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'stock');
        $manager->begin();
        try {
            $stock = 0;
            $map = $this->get_mapped_chunk($data, $this->get_mapping());
            $items = $manager->search($manager->filter()->add(['stock.productid' => $product->get_id()]));
            $items = $items->col(null, 'stock.type');
            foreach ($map as $list) {
                if (!array_key_exists('stock.stocklevel', $list)) {
                    continue;
                }
                $list['stock.productid'] = $product->get_id();
                $list['stock.type'] = $this->val($list, 'stock.type', 'default');
                $this->add_type('stock/type', 'stock', $list['stock.type']);
                $item = $items->pull($list['stock.type']) ?: $manager->create();
                $manager->save($item->from_array($list), false);
                if ($item->get_stock_level() === null || $item->get_stock_level() > 0) {
                    $stock = 1;
                }
            }
            $manager->delete($items);
            $product->set_in_stock($stock);
            $data = $this->object()->process($product, $data);
            $manager->commit();
        } catch (\Exception $e) {
            $manager->rollback();
            throw $e;
        }
        return $data;
    }
}