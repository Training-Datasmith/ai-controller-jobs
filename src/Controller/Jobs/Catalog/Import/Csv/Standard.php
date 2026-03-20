<?php

declare (strict_types=1);
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2026
 * @package Controller
 * @subpackage Jobs
 */
namespace Aimeos\Controller\Jobs\Catalog\Import\Csv;

use Aimeos\Base\Logger\Base as Log;
/**
 * Job controller for CSV catalog imports.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Standard extends \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Base implements \Aimeos\Controller\Jobs\Iface
{
    /** controller/jobs/catalog/import/csv/name
     * Class name of the used catalog CSV importer implementation
     *
     * Each default job controller can be replace by an alternative imlementation.
     * To use this implementation, you have to set the last part of the class
     * name as configuration value so the controller factory knows which class it
     * has to instantiate.
     *
     * For example, if the name of the default class is
     *
     *  \Aimeos\Controller\Jobs\Catalog\Import\Csv\Standard
     *
     * and you want to replace it with your own version named
     *
     *  \Aimeos\Controller\Jobs\Catalog\Import\Csv\Mycsv
     *
     * then you have to set the this configuration option:
     *
     *  controller/jobs/catalog/import/csv/name = Mycsv
     *
     * The value is the last part of your own class name and it's case sensitive,
     * so take care that the configuration value is exactly named like the last
     * part of the class name.
     *
     * The allowed characters of the class name are A-Z, a-z and 0-9. No other
     * characters are possible! You should always start the last part of the class
     * name with an upper case character and continue only with lower case characters
     * or numbers. Avoid chamel case names like "MyCsv"!
     *
     * @param string Last part of the class name
     * @since 2018.04
     */
    /** controller/jobs/catalog/import/csv/decorators/excludes
     * Excludes decorators added by the "common" option from the catalog import CSV job controller
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to remove a decorator added via
     * "controller/jobs/common/decorators/default" before they are wrapped
     * around the job controller.
     *
     *  controller/jobs/catalog/import/csv/decorators/excludes = array( 'decorator1' )
     *
     * This would remove the decorator named "decorator1" from the list of
     * common decorators ("\Aimeos\Controller\Jobs\Common\Decorator\*") added via
     * "controller/jobs/common/decorators/default" to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/catalog/import/csv/decorators/global
     * @see controller/jobs/catalog/import/csv/decorators/local
     */
    /** controller/jobs/catalog/import/csv/decorators/global
     * Adds a list of globally available decorators only to the catalog import CSV job controller
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap global decorators
     * ("\Aimeos\Controller\Jobs\Common\Decorator\*") around the job controller.
     *
     *  controller/jobs/catalog/import/csv/decorators/global = array( 'decorator1' )
     *
     * This would add the decorator named "decorator1" defined by
     * "\Aimeos\Controller\Jobs\Common\Decorator\Decorator1" only to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/catalog/import/csv/decorators/excludes
     * @see controller/jobs/catalog/import/csv/decorators/local
     */
    /** controller/jobs/catalog/import/csv/decorators/local
     * Adds a list of local decorators only to the catalog import CSV job controller
     *
     * Decorators extend the functionality of a class by adding new aspects
     * (e.g. log what is currently done), executing the methods of the underlying
     * class only in certain conditions (e.g. only for logged in users) or
     * modify what is returned to the caller.
     *
     * This option allows you to wrap local decorators
     * ("\Aimeos\Controller\Jobs\Catalog\Import\Csv\Decorator\*") around the job
     * controller.
     *
     *  controller/jobs/catalog/import/csv/decorators/local = array( 'decorator2' )
     *
     * This would add the decorator named "decorator2" defined by
     * "\Aimeos\Controller\Jobs\Catalog\Import\Csv\Decorator\Decorator2"
     * only to the job controller.
     *
     * @param array List of decorator names
     * @since 2018.04
     * @see controller/jobs/common/decorators/default
     * @see controller/jobs/catalog/import/csv/decorators/excludes
     * @see controller/jobs/catalog/import/csv/decorators/global
     */
    /**
     * Returns the localized name of the job.
     *
     * @return string Name of the job
     */
    public function get_name(): string
    {
        return $this->context()->translate('controller/jobs', 'Catalog import CSV');
    }
    /**
     * Returns the localized description of the job.
     *
     * @return string Description of the job
     */
    public function get_description(): string
    {
        return $this->context()->translate('controller/jobs', 'Imports new and updates existing categories from CSV files');
    }
    /**
     * Executes the job.
     *
     * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
     */
    public function run(): void
    {
        $context = $this->context();
        $logger = $context->logger();
        try {
            $errors = 0;
            $fs = $context->fs('fs-import');
            $site = $context->locale()->get_site_item()->get_code();
            $location = $this->location() . '/' . $site;
            if ($fs->is_dir($location) === false) {
                return;
            }
            $logger->info(sprintf('Started catalog import from "%1$s"', $location), 'import/csv/catalog');
            foreach (map($fs->scan($location))->sort() as $filename) {
                $path = $location . '/' . $filename;
                if ($filename[0] === '.') {
                    continue;
                }
                if ($fs instanceof \Aimeos\Base\Filesystem\Dir_Iface && $fs->is_dir($path)) {
                    continue;
                }
                $errors = $this->import($path);
            }
            if ($errors > 0) {
                $this->mail('Catalog CSV import', sprintf('Invalid catalog lines during import: %1$d', $errors));
            }
            $logger->info(sprintf('Finished catalog import from "%1$s"', $location), 'import/csv/catalog');
        } catch (\Exception $e) {
            $logger->error('Catalog import error: ' . $e->get_message() . "\n" . $e->get_trace_as_string(), 'import/csv/catalog');
            $this->mail('Catalog CSV import error', $e->get_message());
            throw new \Aimeos\Controller\Jobs\Exception($e->get_message());
        }
    }
    /**
     * Returns the directory for storing imported files
     *
     * @return string Directory for storing imported files
     */
    protected function backup(): string
    {
        /** controller/jobs/catalog/import/csv/backup
         * Name of the backup for sucessfully imported files
         *
         * After a CSV file was imported successfully, you can move it to another
         * location, so it won't be imported again and isn't overwritten by the
         * next file that is stored at the same location in the file system.
         *
         * You should use an absolute path to be sure but can be relative path
         * if you absolutely know from where the job will be executed from. The
         * name of the new backup location can contain placeholders understood
         * by the PHP DateTime::format() method (with percent signs prefix) to
         * create dynamic paths, e.g. "backup/%Y-%m-%d" which would create
         * "backup/2000-01-01". For more information about the date() placeholders,
         * please have a look  into the PHP documentation of the
         * {@link https://www.php.net/manual/en/datetime.format.php format() method}.
         *
         * **Note:** If no backup name is configured, the file will be removed!
         *
         * @param integer Name of the backup file, optionally with date/time placeholders
         * @since 2018.04
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/mapping
         * @see controller/jobs/catalog/import/csv/max-size
         * @see controller/jobs/catalog/import/csv/skip-lines
         */
        $backup = $this->context()->config()->get('controller/jobs/catalog/import/csv/backup');
        return \Aimeos\Base\Str::strtime((string) $backup);
    }
    /**
     * Returns the list of domain names that should be retrieved along with the attribute items
     *
     * @return array List of domain names
     */
    protected function domains(): array
    {
        /** controller/jobs/catalog/import/csv/domains
         * List of item domain names that should be retrieved along with the catalog items
         *
         * For efficient processing, the items associated to the catalogs can be
         * fetched to, minimizing the number of database queries required. To be
         * most effective, the list of item domain names should be used in the
         * mapping configuration too, so the retrieved items will be used during
         * the import.
         *
         * @param array Associative list of MShop item domain names
         * @since 2018.04
         * @see controller/jobs/catalog/import/csv/backup
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/mapping
         * @see controller/jobs/catalog/import/csv/max-size
         * @see controller/jobs/catalog/import/csv/skip-lines
         */
        return $this->context()->config()->get('controller/jobs/catalog/import/csv/domains', ['media', 'text']);
    }
    /**
     * Returns the position of the "catalog.code" column from the catalog item mapping
     *
     * @param array $mapping Mapping of the "item" columns with position as key and code as value
     * @return int Position of the "catalog.code" column
     * @throws \Aimeos\Controller\Jobs\Exception If no mapping for "catalog.code" is found
     */
    protected function get_code_position(array $mapping): int
    {
        foreach ($mapping as $pos => $key) {
            if ($key === 'catalog.code') {
                return $pos;
            }
        }
        throw new \Aimeos\Controller\Jobs\Exception(sprintf('No "catalog.code" column in CSV mapping found'));
    }
    /**
     * Returns the catalog items building the tree as list
     *
     * @param array $codes List of catalog item codes
     * @param array $domains List of domain names whose items should be fetched too
     * @return array Associative list of catalog codes as keys and items implementing \Aimeos\MShop\Catalog\Item\Iface as values
     */
    protected function get_categories(array $codes, array $domains): array
    {
        $manager = \Aimeos\M_Shop::create($this->context(), 'catalog');
        $search = $manager->filter()->add(['catalog.code' => $codes])->slice(0, count($codes));
        $map = [];
        foreach ($manager->search($search, $domains) as $item) {
            $map[$item->get_code()] = $item;
        }
        return $map;
    }
    /**
     * Returns the parent ID of the catalog node for the given code
     *
     * @param array $catalogItems Associative list of catalog items with codes as keys and items implementing \Aimeos\MShop\Catalog\Item\Iface as values
     * @param array $map Associative list of catalog item key/value pairs
     * @param string $code Catalog item code of the parent category
     * @return string|null ID of the parent category or null for top level nodes
     */
    protected function get_parent_id(array $catalog_items, array $map, string $code): ?string
    {
        if (!isset($map['catalog.parent'])) {
            $msg = sprintf('Required column "%1$s" not found for code "%2$s"', 'catalog.parent', $code);
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        $parent = trim($map['catalog.parent']);
        if ($parent != '' && !isset($catalog_items[$parent])) {
            $msg = sprintf('Parent node for code "%1$s" not found', $parent);
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return $parent != '' ? $catalog_items[$parent]->get_id() : null;
    }
    /**
     * Imports the CSV file from the given path
     *
     * @param string $path Relative path to the CSV file
     * @return int Number of lines which couldn't be imported
     */
    protected function import(string $path): int
    {
        $context = $this->context();
        $logger = $context->logger();
        $logger->info(sprintf('Started catalog import from "%1$s"', $path), 'import/csv/catalog');
        $maxcnt = $this->max();
        $skiplines = $this->skip();
        $domains = $this->domains();
        $mappings = $this->mapping();
        $processor = $this->get_processors($mappings);
        $code_pos = $this->get_code_position($mappings['item']);
        $fs = $context->fs('fs-import');
        $fh = $fs->reads($path);
        $total = $errors = 0;
        for ($i = 0; $i < $skiplines; $i++) {
            fgetcsv($fh, null, ',', '"', '');
        }
        while (($data = $this->get_data($fh, $maxcnt, $code_pos)) !== []) {
            $catalog_items = $this->get_categories(array_keys($data), $domains);
            $errors += $this->import_categories($catalog_items, $data, $mappings['item'], $processor);
            $total += count($data);
            unset($catalog_items, $data);
        }
        $processor->finish();
        fclose($fh);
        if (!empty($backup = $this->backup())) {
            $fs->move($path, $backup);
        } else {
            $fs->rm($path);
        }
        $str = sprintf('Finished catalog import from "%1$s" (%2$d/%3$d)', $path, $errors, $total);
        $logger->info($str, 'import/csv/catalog');
        return $errors;
    }
    /**
     * Imports the CSV data and creates new categories or updates existing ones
     *
     * @param array $catalogItems Associative list of catalog items with codes as keys and items implementing \Aimeos\MShop\Catalog\Item\Iface as values
     * @param array $data Associative list of import data as index/value pairs
     * @param array $mapping Associative list of positions and domain item keys
     * @param \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Iface $processor Processor object
     * @return int Number of catalogs that couldn't be imported
     * @throws \Aimeos\Controller\Jobs\Exception
     */
    protected function import_categories(array $catalog_items, array $data, array $mapping, \Aimeos\Controller\Jobs\Common\Catalog\Import\Csv\Processor\Iface $processor): int
    {
        $errors = 0;
        $context = $this->context();
        $manager = \Aimeos\M_Shop::create($context, 'catalog');
        foreach ($data as $code => $list) {
            $manager->begin();
            try {
                $code = trim($code);
                $item = $catalog_items[$code] ?? $manager->create();
                $map = current($this->get_mapped_chunk($list, $mapping));
                // there can only be one chunk for the base catalog data
                if ($map) {
                    $map['catalog.config'] = json_decode($map['catalog.config'] ?? '[]', true) ?: [];
                    $parentid = $this->get_parent_id($catalog_items, $map, $code);
                    $item->from_array($map, true);
                    if (isset($catalog_items[$code])) {
                        $manager->move($item->get_id(), $item->get_parent_id(), $parentid);
                        $item = $manager->save($item);
                    } else {
                        $item = $manager->insert($item, $parentid);
                    }
                    $processor->process($item, $list);
                    $catalog_items[$code] = $item;
                    $manager->save($item);
                }
                $manager->commit();
            } catch (\Exception $e) {
                $manager->rollback();
                $msg = sprintf('Unable to import catalog with code "%1$s": %2$s', $code, $e->get_message());
                $context->logger()->error($msg, 'import/csv/catalog');
                $errors++;
            }
        }
        return $errors;
    }
    /**
     * Returns the path to the directory with the CSV file
     *
     * @return string Path to the directory with the CSV file
     */
    protected function location(): string
    {
        /** controller/jobs/catalog/import/csv/location
         * Directory where the CSV files are stored which should be imported
         *
         * It's the relative path inside the "fs-import" virtual file system
         * configuration. The default location of the "fs-import" file system is:
         *
         * * Laravel: ./storage/import/
         * * TYPO3: /uploads/tx_aimeos/.secure/import/
         *
         * @param string Relative path to the CSV files
         * @since 2015.08
         * @see controller/jobs/catalog/import/csv/backup
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/mapping
         * @see controller/jobs/catalog/import/csv/max-size
         * @see controller/jobs/catalog/import/csv/skip-lines
         */
        return (string) $this->context()->config()->get('controller/jobs/catalog/import/csv/location', 'catalog');
    }
    /**
     * Returns the CSV column mapping
     *
     * @return array CSV column mapping
     */
    protected function mapping(): array
    {
        /** controller/jobs/catalog/import/csv/mapping
         * List of mappings between the position in the CSV file and item keys
         *
         * The importer have to know which data is at which position in the CSV
         * file. Therefore, you need to specify a mapping between each position
         * and the MShop domain item key (e.g. "catalog.code") it represents.
         *
         * You can use all domain item keys which are used in the fromArray()
         * methods of the item classes.
         *
         * These mappings are grouped together by their processor names, which
         * are responsible for importing the data, e.g. all mappings in "item"
         * will be processed by the base catalog importer while the mappings in
         * "text" will be imported by the text processor.
         *
         * @param array Associative list of processor names and lists of key/position pairs
         * @since 2018.04
         * @see controller/jobs/catalog/import/csv/backup
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/max-size
         * @see controller/jobs/catalog/import/csv/skip-lines
         */
        $map = (array) $this->context()->config()->get('controller/jobs/catalog/import/csv/mapping', $this->get_default_mapping());
        if (!isset($map['item']) || !is_array($map['item'])) {
            $msg = sprintf('Required mapping key "%1$s" is missing or contains no array', 'item');
            throw new \Aimeos\Controller\Jobs\Exception($msg);
        }
        return $map;
    }
    /**
     * Returns the maximum number of CSV rows to import at once
     *
     * @return int Maximum number of CSV rows to import at once
     */
    protected function max(): int
    {
        /** controller/jobs/catalog/import/csv/max-size
         * Maximum number of CSV rows to import at once
         *
         * It's more efficient to read and import more than one row at a time
         * to speed up the import. Usually, the bigger the chunk that is imported
         * at once, the less time the importer will need. The downside is that
         * the amount of memory required by the import process will increase as
         * well. Therefore, it's a trade-off between memory consumption and
         * import speed.
         *
         * @param integer Number of rows
         * @since 2018.04
         * @see controller/jobs/catalog/import/csv/backup
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/mapping
         * @see controller/jobs/catalog/import/csv/skip-lines
         */
        return (int) $this->context()->config()->get('controller/jobs/catalog/import/csv/max-size', 1000);
    }
    /**
     * Returns the number of rows skipped in front of each CSV files
     *
     * @return int Number of rows skipped in front of each CSV files
     */
    protected function skip(): int
    {
        /** controller/jobs/catalog/import/csv/skip-lines
         * Number of rows skipped in front of each CSV files
         *
         * Some CSV files contain header information describing the content of
         * the column values. These data is for informational purpose only and
         * can't be imported into the database. Using this option, you can
         * define the number of lines that should be left out before the import
         * begins.
         *
         * @param integer Number of rows
         * @since 2015.08
         * @see controller/jobs/catalog/import/csv/backup
         * @see controller/jobs/catalog/import/csv/converter
         * @see controller/jobs/catalog/import/csv/domains
         * @see controller/jobs/catalog/import/csv/location
         * @see controller/jobs/catalog/import/csv/mapping
         * @see controller/jobs/catalog/import/csv/max-size
         */
        return (int) $this->context()->config()->get('controller/jobs/catalog/import/csv/skip-lines', 0);
    }
}