<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * This is an object model class used to manage module data
 */
class ElegantalTinyPngImageCompressClass extends ElegantalTinyPngImageCompressObjectModel
{

    public static $STATUS_ANALYZING = 1;
    public static $STATUS_COMPRESSING = 2;
    public static $STATUS_COMPLETED = 3;
    public $tableName = 'elegantaltinypngimagecompress';
    public static $definition = array(
        'table' => 'elegantaltinypngimagecompress',
        'primary' => 'id_elegantaltinypngimagecompress',
        'fields' => array(
            'image_group' => array('type' => self::TYPE_STRING, 'size' => 255, 'validate' => 'isGenericName', 'required' => true),
            'custom_dir' => array('type' => self::TYPE_STRING, 'size' => 255, 'validate' => 'isString'), // This is serialized array because we have Custom directory to compress by CRON setting
            'images_count' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'images_size_before' => array('type' => self::TYPE_NOTHING), // This is bigint type. It will store filesize in bytes
            'images_size_after' => array('type' => self::TYPE_NOTHING), // This is bigint type. It will store filesize in bytes
            'status' => array('type' => self::TYPE_BOOL, 'validate' => 'isUnsignedInt'),
            'created_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'updated_at' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        ),
    );

    /**
     * Types of images that can be compressed
     * @var array
     */
    public static $imageGroups = array(
        'product',
        'category',
        'manufacturer',
        'supplier',
        'store',
        'modules',
        'theme',
        'other',
        'custom',
    );

    /**
     * Allowed images
     * @var array
     */
    private $allowedImages = array('png', 'jpg', 'jpeg');

    /**
     * Stores supported image types like cart_default, small_default, large_default, etc.
     * @var array
     */
    private $imageTypes = null;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);

        if (!$id || empty($this->created_at) || $this->created_at == '0000-00-00 00:00:00') {
            $this->created_at = date('Y-m-d H:i:s');
        }
        if (!$id || empty($this->updated_at) || $this->updated_at == '0000-00-00 00:00:00') {
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function update($null_values = false)
    {
        $this->updated_at = date('Y-m-d H:i:s');
        return parent::update($null_values);
    }

    public static function findAllWithImages($offset = null, $limit = null, $orderBy = 'id_elegantaltinypngimagecompress', $orderType = 'desc')
    {
        Db::getInstance()->execute("SET SQL_BIG_SELECTS=1");

        $sql = "SELECT t.*, n.`not_compressed`, c.`compressed`, f.`failed`, (CAST(t.`images_size_before` as SIGNED) - CAST(t.`images_size_after` as SIGNED)) as `disk_space_saved` 
            FROM `" . _DB_PREFIX_ . "elegantaltinypngimagecompress` t 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `not_compressed` 
            	FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) n ON n.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `compressed` 
                FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_COMPRESSED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) c ON c.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `failed` 
                FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_FAILED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) f ON f.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            ORDER BY " . pSQL($orderBy) . " " . pSQL($orderType) . " ";

        if (!is_null($offset) && !is_null($limit)) {
            $sql .= " LIMIT " . (int) $offset . "," . (int) $limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public static function findByPkWithImages($id)
    {
        Db::getInstance()->execute("SET SQL_BIG_SELECTS=1");

        $sql = "SELECT t.*, n.`not_compressed`, c.`compressed`, f.`failed`, (CAST(t.`images_size_before` as SIGNED) - CAST(t.`images_size_after` as SIGNED)) as `disk_space_saved` 
            FROM `" . _DB_PREFIX_ . "elegantaltinypngimagecompress` t 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `not_compressed` 
            	FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) n ON n.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `compressed` 
                FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_COMPRESSED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) c ON c.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            LEFT JOIN (
            	SELECT `id_elegantaltinypngimagecompress`, COUNT(*) AS `failed` 
                FROM  `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
            	WHERE `status` = " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_FAILED . " AND `id_elegantaltinypngimagecompress` > 0 
                GROUP BY `id_elegantaltinypngimagecompress` 
            ) f ON f.`id_elegantaltinypngimagecompress` = t.`id_elegantaltinypngimagecompress` 
            WHERE t.`id_elegantaltinypngimagecompress` = " . (int) $id;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Scans directories and creates image model for each image
     * @return boolean
     */
    public function collectImages($offset = null, $limit = null)
    {
        if (in_array($this->image_group, array('product', 'category', 'manufacturer', 'supplier', 'store'))) {
            return $this->collectImagesByIds($offset, $limit);
        } else {
            return $this->collectImagesByPaths();
        }
    }

    /**
     * Collects all images by paths
     * @return boolean
     */
    protected function collectImagesByPaths()
    {
        $images = array();

        $paths = $this->getImageDirPaths();
        foreach ($paths as $path) {
            if ($path && is_dir($path)) {
                if (realpath(_PS_IMG_DIR_) == $path) {
                    $images = array_merge($images, $this->getImagesFromDirectory($path));
                } else {
                    $images = array_merge($images, $this->getImagesFromDirectoryRecursively($path));
                }
            } elseif ($path && is_file($path)) {
                $images = array_merge($images, array($path));
            }
        }

        return $this->createImagesForCompression($images);
    }

    /**
     * Collects images by IDs
     * @param int $offset
     * @param int $limit
     * @return boolean
     */
    protected function collectImagesByIds($offset, $limit)
    {
        $settings = $this->getModuleSettings();
        $ids = $this->getImageIdsByImageGroup($offset, $limit);
        $image_types = $this->getImageTypes();
        $image_formats_to_compress = $settings['image_formats_to_compress'];
        $is_high_resolution_on = (bool) Configuration::get('PS_HIGHT_DPI');
        $images = array();

        foreach ($ids as $id) {
            switch ($this->image_group) {
                case 'product':
                    // Get image by id
                    $image = new Image($id);
                    if ($image && $image->id) {
                        // Original image
                        if ($settings['compress_original_images']) {
                            $images[] = $image->image_dir . $image->getExistingImgPath() . '.' . $image->image_format;
                        }
                        // Generated images
                        if ($settings['compress_generated_images']) {
                            foreach ($image_types as $image_type) {
                                if (isset($image_type['products']) && $image_type['products']) {
                                    if (empty($image_formats_to_compress) || in_array('all', $image_formats_to_compress) || in_array($image_type['name'], $image_formats_to_compress)) {
                                        $images[] = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '.' . $image->image_format;
                                        $image2x = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '2x.' . $image->image_format;
                                        if ($is_high_resolution_on && is_file($image2x)) {
                                            $images[] = $image2x;
                                        }
                                        if (Configuration::get('WATERMARK_HASH')) {
                                            $images[] = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '-' . Configuration::get('WATERMARK_HASH') . '.' . $image->image_format;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'category':
                    $images = array_merge($images, $this->getObjectImages($id, 'Category', 'categories'));
                    break;
                case 'manufacturer':
                    $images = array_merge($images, $this->getObjectImages($id, 'Manufacturer', 'manufacturers'));
                    break;
                case 'supplier':
                    $images = array_merge($images, $this->getObjectImages($id, 'Supplier', 'suppliers'));
                    break;
                case 'store':
                    $images = array_merge($images, $this->getObjectImages($id, 'Store', 'stores'));
                    break;
                default:
                    break;
            }
        }

        return $this->createImagesForCompression($images);
    }

    /**
     * Collects product images
     * @param int $id_product
     * @return boolean
     */
    public function collectProductImages($id_product)
    {
        $settings = $this->getModuleSettings();
        $ids = $this->getProductImageIds($id_product);
        $image_types = $this->getImageTypes();
        $image_formats_to_compress = $settings['image_formats_to_compress'];
        $is_high_resolution_on = (bool) Configuration::get('PS_HIGHT_DPI');
        $images = array();

        foreach ($ids as $id) {
            // Get image by id
            $image = new Image($id);
            if ($image && $image->id) {
                // Original image
                if ($settings['compress_original_images']) {
                    $images[] = $image->image_dir . $image->getExistingImgPath() . '.' . $image->image_format;
                }
                // Generated images
                if ($settings['compress_generated_images']) {
                    foreach ($image_types as $image_type) {
                        if (isset($image_type['products']) && $image_type['products']) {
                            if (empty($image_formats_to_compress) || in_array('all', $image_formats_to_compress) || in_array($image_type['name'], $image_formats_to_compress)) {
                                $images[] = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '.' . $image->image_format;
                                $image2x = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '2x.' . $image->image_format;
                                if ($is_high_resolution_on && is_file($image2x)) {
                                    $images[] = $image2x;
                                }
                                if (Configuration::get('WATERMARK_HASH')) {
                                    $images[] = $image->image_dir . $image->getExistingImgPath() . '-' . $image_type['name'] . '-' . Configuration::get('WATERMARK_HASH') . '.' . $image->image_format;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $this->createImagesForCompression($images);
    }

    /**
     * Creates ElegantalTinyPngImageCompressImagesClass record for each image path given
     * @param array $images
     * @return boolean
     */
    protected function createImagesForCompression($images)
    {
        $settings = $this->getModuleSettings();
        $exclude_paths = (is_array($settings['exclude_paths']) && count($settings['exclude_paths']) > 0) ? $settings['exclude_paths'] : false;
        $date_images_created_from = $settings['date_images_created_from'] ? strtotime($settings['date_images_created_from']) : false;
        $date_images_created_to = $settings['date_images_created_to'] ? strtotime($settings['date_images_created_to']) : false;
        $date_images_created_applied_groups = ($settings['date_images_created_applied_groups'] && is_array($settings['date_images_created_applied_groups']) && count($settings['date_images_created_applied_groups']) > 0) ? $settings['date_images_created_applied_groups'] : false;

        foreach ($images as $image) {
            if (!is_file($image) || !filesize($image)) {
                continue;
            }
            if ($exclude_paths) {
                foreach ($exclude_paths as $exclude_path) {
                    $exclude_path = trim($exclude_path);
                    if (empty($exclude_path)) {
                        continue;
                    }
                    // If relative path, make it absolute
                    if (Tools::substr($exclude_path, 0, 1) != '/') {
                        $exclude_path = _PS_ROOT_DIR_ . '/' . $exclude_path;
                    }
                    // Add / to the end of the path if it does not exist
                    if (Tools::substr($exclude_path, -1) != '/') {
                        $exclude_path .= '/';
                    }
                    if (stripos($image, $exclude_path) === 0) {
                        continue 2;
                    }
                }
            }
            if ($date_images_created_from &&
                $date_images_created_applied_groups &&
                in_array($this->image_group, $date_images_created_applied_groups) &&
                $date_images_created_from > filectime($image)) {
                continue;
            }
            if ($date_images_created_to &&
                $date_images_created_applied_groups &&
                in_array($this->image_group, $date_images_created_applied_groups) &&
                $date_images_created_to < filectime($image)) {
                continue;
            }
            if ($settings['image_types_to_compress'] == 'jpg' || $settings['image_types_to_compress'] == 'png') {
                $mime = ElegantalTinyPngImageCompressTools::getMimeType($image);
                if (!$mime || !in_array($mime, array('image/jpeg', 'image/png')) ||
                    ($settings['image_types_to_compress'] == 'jpg' && $mime != 'image/jpeg') ||
                    ($settings['image_types_to_compress'] == 'png' && $mime != 'image/png')) {
                    continue;
                }
            }
            if ($settings['minimum_image_filesize_for_compression'] > 0) {
                $min_filesize = $settings['minimum_image_filesize_for_compression'] * 1024;
                if (filesize($image) < $min_filesize) {
                    continue;
                }
            }

            $imageModel = new ElegantalTinyPngImageCompressImagesClass();
            $imageSize = filesize($image);
            $imageModel->id_elegantaltinypngimagecompress = $this->id;
            $imageModel->image_path = realpath($image);
            $imageModel->image_size_before = $imageSize;
            $imageModel->image_size_after = $imageSize;
            $imageModel->modified_at = date('Y-m-d H:i:s', filemtime($image));

            if ($imageModel->add()) {
                $this->images_count++;
                $this->images_size_before += $imageSize;
                $this->images_size_after += $imageSize;
            }
        }

        return $this->update();
    }

    /**
     * Returns list of images paths of an object
     * @param int $id
     * @param string $class
     * @return array
     */
    protected function getObjectImages($id, $class, $imageTypeOption)
    {
        $settings = $this->getModuleSettings();
        $is_high_resolution_on = (bool) Configuration::get('PS_HIGHT_DPI');
        $images = array();
        $image_types = $this->getImageTypes();

        $objectModel = new $class($id);

        if ($objectModel && $objectModel->id) {
            // Original image
            if ($settings['compress_original_images']) {
                $images[] = $objectModel->image_dir . $objectModel->id . '.' . $objectModel->image_format;
            }
            // Generated images
            if ($settings['compress_generated_images']) {
                foreach ($image_types as $image_type) {
                    if (isset($image_type[$imageTypeOption]) && $image_type[$imageTypeOption]) {
                        $images[] = $objectModel->image_dir . $objectModel->id . '-' . Tools::stripslashes($image_type['name']) . '.' . $objectModel->image_format;
                        $image2x = $objectModel->image_dir . $objectModel->id . '-' . Tools::stripslashes($image_type['name']) . '2x.' . $objectModel->image_format;
                        if ($is_high_resolution_on && is_file($image2x)) {
                            $images[] = $image2x;
                        }
                    }
                }
            }
        }

        return $images;
    }

    /**
     * Returns list of IDs of images from particular image group.
     * Limit and offset are used when analyzing images in portions.
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getImageIdsByImageGroup($offset = null, $limit = null)
    {
        $settings = $this->getModuleSettings();
        $imageIdsFromDb = array();
        $sql = null;

        switch ($this->image_group) {
            case "product":
                $sql = "SELECT i.`id_image` as `id` FROM `" . _DB_PREFIX_ . "image` i 
                    INNER JOIN `" . _DB_PREFIX_ . "product_shop` psh ON psh.`id_product` = i.`id_product` 
                    WHERE i.`id_image` > 0 ";
                if ($settings['compress_only_active_object_images']) {
                    $sql .= "AND psh.`active` = 1 ";
                }
                if ($settings['compress_only_visible_product_images']) {
                    $sql .= "AND psh.`visibility` != 'none' ";
                }
                $sql .= " GROUP BY i.`id_image` ";
                break;
            case "category":
                $sql = "SELECT c.`id_category` as `id` FROM `" . _DB_PREFIX_ . "category` c";
                break;
            case "manufacturer":
                $sql = "SELECT m.`id_manufacturer` as `id` FROM `" . _DB_PREFIX_ . "manufacturer` m ";
                if ($settings['compress_only_active_object_images']) {
                    $sql .= "WHERE m.`active` = 1";
                }
                break;
            case "supplier":
                $sql = "SELECT s.`id_supplier` as `id` FROM `" . _DB_PREFIX_ . "supplier` s ";
                if ($settings['compress_only_active_object_images']) {
                    $sql .= "WHERE s.`active` = 1";
                }
                break;
            case "store":
                $sql = "SELECT s.`id_store` as `id` FROM `" . _DB_PREFIX_ . "store` s";
                break;
            default:
                break;
        }

        if ($sql) {
            $sql .= " ORDER BY `id`";

            if (!is_null($limit) && !is_null($offset)) {
                $sql .= " LIMIT " . (int) $offset . "," . (int) $limit;
            }

            $dbImages = Db::getInstance()->executeS($sql, true, false);

            foreach ($dbImages as $dbImage) {
                $imageIdsFromDb[] = $dbImage["id"];
            }
        }

        return $imageIdsFromDb;
    }

    /**
     * Returns image IDs by product
     * @param type $id_product
     * @return int
     */
    public function getProductImageIds($id_product)
    {
        $settings = $this->getModuleSettings();
        $imageIdsFromDb = array();

        $sql = "SELECT i.`id_image` as `id` 
            FROM `" . _DB_PREFIX_ . "image` i 
            INNER JOIN `" . _DB_PREFIX_ . "product_shop` psh ON psh.`id_product` = i.`id_product` 
            WHERE psh.`id_product` = " . (int) $id_product;
        if ($settings['compress_only_active_object_images']) {
            $sql .= " AND psh.`active` = 1 ";
        }
        if ($settings['compress_only_visible_product_images']) {
            $sql .= " AND psh.`visibility` != 'none' ";
        }
        $sql .= " GROUP BY i.`id_image` ";

        $dbImages = Db::getInstance()->executeS($sql, true, false);

        foreach ($dbImages as $dbImage) {
            $imageIdsFromDb[] = $dbImage["id"];
        }

        return $imageIdsFromDb;
    }

    /**
     * Returns list of paths to directories where images should be searched for compression
     * @return array
     */
    public function getImageDirPaths()
    {
        $paths = array();
        $dirs = array(
            'product' => realpath(_PS_PROD_IMG_DIR_),
            'category' => realpath(_PS_CAT_IMG_DIR_),
            'manufacturer' => realpath(_PS_MANU_IMG_DIR_),
            'supplier' => realpath(_PS_SUPP_IMG_DIR_),
            'store' => realpath(_PS_STORE_IMG_DIR_),
            'modules' => realpath(_PS_MODULE_DIR_),
            'theme' => realpath(_PS_THEME_DIR_),
        );

        switch ($this->image_group) {
            case 'product':
                $paths[] = $dirs['product'];
                break;
            case 'category':
                $paths[] = $dirs['category'];
                break;
            case 'manufacturer':
                $paths[] = $dirs['manufacturer'];
                break;
            case 'supplier':
                $paths[] = $dirs['supplier'];
                break;
            case 'store':
                $paths[] = $dirs['store'];
                break;
            case 'modules':
                $paths[] = $dirs['modules'];
                break;
            case 'theme':
                $paths[] = $dirs['theme'];
                break;
            case 'custom':
                if ($this->custom_dir) {
                    $custom_dirs = ElegantalTinyPngImageCompressTools::unserialize($this->custom_dir);
                    if ($custom_dirs && is_array($custom_dirs)) {
                        foreach ($custom_dirs as $custom_dir) {
                            $custom_dir = (Tools::substr($custom_dir, 0, 1) == '/') ? realpath($custom_dir) : realpath(_PS_ROOT_DIR_ . '/' . $custom_dir);
                            if ($custom_dir) {
                                $paths[] = $custom_dir;
                            }
                        }
                    }
                }
                break;
            case 'other':
                $others = scandir(_PS_IMG_DIR_);
                foreach ($others as $other) {
                    if ($other != '..' && $other != 'tmp') {
                        $otherDir = realpath(_PS_IMG_DIR_ . $other);
                        if (is_dir($otherDir) && !in_array($otherDir, $dirs)) {
                            $paths[] = $otherDir;
                        }
                    }
                }
                break;
            default:
                break;
        }

        return $paths;
    }

    /**
     * Returns list of images found in the directory including subdirectories
     * @param string $dir
     * @return array
     */
    protected function getImagesFromDirectoryRecursively($dir)
    {
        $imagesFromDir = array();

        $path = realpath($dir);
        if (is_dir($dir) && $path) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $file) {
                if ($this->isFileCompressableImage($file)) {
                    $imagesFromDir[] = $file;
                }
            }
        }

        return $imagesFromDir;
    }

    /**
     * Returns list of images found in the directory excluding subdirectories
     * @param string $dir
     * @return array
     */
    protected function getImagesFromDirectory($dir)
    {
        $imagesFromDir = array();

        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                $file = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . $file;
                if ($this->isFileCompressableImage($file)) {
                    $imagesFromDir[] = $file;
                }
            }
        }

        return $imagesFromDir;
    }

    /**
     * Checks if file is an image that can be compressed
     * @param string $file
     * @return boolean
     */
    protected function isFileCompressableImage($file)
    {
        $settings = $this->getModuleSettings();
        if (!is_file($file)) {
            return false;
        }
        if (!in_array(Tools::strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->allowedImages)) {
            return false;
        }
        if (!$settings['compress_generated_images'] && $this->isGeneratedImage($file)) {
            return false;
        }
        if (in_array($this->image_group, array('product', 'category', 'manufacturer', 'supplier', 'store')) && !$settings['compress_original_images'] && !$this->isGeneratedImage($file)) {
            return false;
        }
        return true;
    }

    /**
     * Checks if file is generated image by Prestashop
     * @param string $file
     * @return boolean
     */
    protected function isGeneratedImage($file)
    {
        $filename = basename($file);
        $imageTypes = $this->getImageTypes();
        foreach ($imageTypes as $imageType) {
            if (preg_match("/([A-Za-z0-9-]+-)([A-Za-z_\d]+)(\.)([A-Za-z]+)/", $filename, $match) && isset($match[2]) && strpos($match[2], $imageType['name']) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns list of image types used by Prestashop to generate thumbnails
     * @return array
     */
    protected function getImageTypes()
    {
        if (!$this->imageTypes) {
            $this->imageTypes = ImageType::getImagesTypes();
        }
        return $this->imageTypes;
    }

    /**
     * Deletes images of the model
     * @return boolean
     */
    public function deleteImages()
    {
        $sql = "DELETE FROM `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
                WHERE `id_elegantaltinypngimagecompress` = " . (int) $this->id;
        return Db::getInstance()->execute($sql);
    }
}
