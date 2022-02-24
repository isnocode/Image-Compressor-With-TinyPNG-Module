<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

require_once('initialize.php');

/**
 * Main class of the module
 */
class ElegantalTinyPngImageCompress extends ElegantalTinyPngImageCompressModule
{

    /**
     * ID of this module as product on addons
     * @var int
     */
    protected $productIdOnAddons = 22488;

    /**
     * List of hooks to register
     * @var array
     */
    protected $hooksToRegister = array(
        'actionProductSave',
    );

    /**
     * List of module settings to be saved as Configuration record
     * @var array
     */
    protected $settings = array(
        'api_key' => array(),
        'compress_original_images' => 1,
        'compress_generated_images' => 1,
        'image_formats_to_compress' => array('all'),
        'image_types_to_compress' => array('all'),
        'exclude_paths' => array(),
        'compress_only_active_object_images' => 0,
        'compress_only_visible_product_images' => 0,
        'cron_compress_per_request' => 5,
        'cron_compress_image_groups' => array(),
        'cron_compress_custom_dirs' => array(),
        'cron_analyzed_image_groups' => array(),
        'cron_last_error' => '',
        'date_images_created_from' => '',
        'date_images_created_to' => '',
        'date_images_created_applied_groups' => array(),
        'analyze_per_request' => 30, // Number of images to analyze per ajax request
        'minimum_image_filesize_for_compression' => 10,
        'security_token_key' => '',
        'disable_url_rewrite' => 0,
    );

    /**
     * Current model object being edited on back-office
     */
    private $model = null;

    /**
     * Constructor method called on each newly-created object
     */
    public function __construct()
    {
        $this->name = 'elegantaltinypngimagecompress';
        $this->tab = 'administration';
        $this->version = '5.9.3';
        $this->author = 'ELEGANTAL';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = '0d283326323f534701e0c4987a92716a';

        parent::__construct();

        $this->displayName = $this->l('Image Compressor With TinyPNG');
        $this->description = $this->l('Compress JPG and PNG images in your store with TinyPNG, reduce page size of your store, make your store load much more faster and save a lot of disk space.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    /**
     * This function plays controller role for the back-office page of the module
     * @return html
     */
    public function getContent()
    {
        $this->setTimeLimit();

        if (_PS_VERSION_ < '1.6') {
            $this->context->controller->addCSS($this->_path . 'views/css/elegantaltinypngimagecompress-bootstrap.css', 'all');
            $this->context->controller->addCSS($this->_path . 'views/css/font-awesome.css', 'all');

            if (!in_array(Tools::getValue('event'), array('settings', 'editSettings'))) {
                $this->context->controller->addJS($this->_path . 'views/js/jquery-1.11.0.min.js');
                $this->context->controller->addJS($this->_path . 'views/js/bootstrap.js');
            }
        }

        $this->context->controller->addCSS($this->_path . 'views/css/elegantaltinypngimagecompress-back8.css', 'all');
        $this->context->controller->addJS($this->_path . 'views/js/elegantaltinypngimagecompress-back8.js');

        $this->initModel();

        $html = $this->getRedirectAlerts();

        if ($event = Tools::getValue('event')) {
            switch ($event) {
                case 'settings':
                    $html .= $this->settings();
                    break;
                case 'editSettings':
                    $html .= $this->editSettings();
                    break;
                case 'viewCron':
                    $html .= $this->viewCron();
                    break;
                case 'analyze':
                    $html .= $this->analyze();
                    break;
                case 'compress':
                    $html .= $this->compress();
                    break;
                case 'tinify':
                    $html .= $this->tinify();
                    break;
                case 'compressSingleImage':
                    $html .= $this->compressSingleImage();
                    break;
                case 'selectedProductImages':
                    $html .= $this->selectedProductImages();
                    break;
                case 'customDir':
                    $html .= $this->customDir();
                    break;
                case 'imagesLog':
                    $html .= $this->imagesLog();
                    break;
                case 'delete':
                    $html .= $this->delete();
                    break;
                case 'deleteBulk':
                    $html .= $this->deleteBulk();
                    break;
                case 'loadProductsForSelect':
                    $this->loadProductsForSelect(Tools::getValue('q', false), array(), Tools::getValue('chosenProducts', false));
                    break;
                default:
                    $html .= $this->history();
                    break;
            }
        } else {
            $html .= $this->history();
        }

        return $html;
    }

    /**
     * Initializes current model object and its attributes
     */
    protected function initModel($model_id = null)
    {
        $model = null;
        $model_id = Tools::getValue('id_elegantaltinypngimagecompress', $model_id);
        if ($model_id) {
            $model = new ElegantalTinyPngImageCompressClass($model_id);
            if (Validate::isLoadedObject($model)) {
                $this->model = $model;
            } else {
                $this->setRedirectAlert($this->l('Record not found.'), 'error');
                $this->redirectAdmin();
            }
        }
    }

    /**
     * Renders list of compressions history
     * @return string HTML
     */
    protected function history()
    {
        // Pagination data
        $total = (int) ElegantalTinyPngImageCompressClass::model()->countAll();
        $limit = 20;
        $pages = ceil($total / $limit);
        $currentPage = (int) Tools::getValue('page', 1);
        $currentPage = ($currentPage > $pages) ? $pages : $currentPage;
        $halfVisibleLinks = 5;
        $offset = ($total > $limit) ? ($currentPage - 1) * $limit : 0;

        // Sorting records
        $sortableColumns = array(
            'id_elegantaltinypngimagecompress',
            'image_group',
            'created_at',
            'images_count',
            'compressed',
            'not_compressed',
            'failed',
            'images_size_before',
            'images_size_after',
            'disk_space_saved',
            'status'
        );
        $orderBy = (in_array(Tools::getValue('orderBy'), $sortableColumns)) ? Tools::getValue('orderBy') : 'id_elegantaltinypngimagecompress';
        $orderType = (Tools::getValue('orderType') == 'asc') ? 'asc' : 'desc';

        $models = ElegantalTinyPngImageCompressClass::findAllWithImages($offset, $limit, $orderBy, $orderType);

        $total_images = 0;
        $total_compressed = 0;
        $total_not_compressed = 0;
        $total_failed = 0;
        $total_size_before = 0;
        $total_size_after = 0;
        $total_disk_saved = 0;

        foreach ($models as &$model) {
            $total_images += (int) $model['images_count'];
            $total_compressed += (int) $model['compressed'];
            $total_not_compressed += (int) $model['not_compressed'];
            $total_failed += (int) $model['failed'];
            $total_size_before += (int) $model['images_size_before'];
            $total_size_after += (int) $model['images_size_after'];
            $total_disk_saved += ($model['images_size_before'] > $model['images_size_after']) ? ($model['images_size_before'] - $model['images_size_after']) : 0;

            $model['disk_space_saved'] = ($model['images_size_before'] > $model['images_size_after']) ? ElegantalTinyPngImageCompressTools::displaySize($model['images_size_before'] - $model['images_size_after']) : ElegantalTinyPngImageCompressTools::displaySize(0);
            $model['images_size_before'] = ElegantalTinyPngImageCompressTools::displaySize($model['images_size_before']);
            $model['images_size_after'] = ElegantalTinyPngImageCompressTools::displaySize($model['images_size_after']);

            if ($model['custom_dir']) {
                $model['custom_dir'] = ElegantalTinyPngImageCompressTools::unserialize($model['custom_dir']);
                $model['custom_dir'] = implode(' ' . PHP_EOL, $model['custom_dir']);
            }
        }

        // Statistics
        $api_key = $this->getApiKey();
        $stats_monthly_compression_usage = $api_key ? ElegantalTinyPngImageCompressImagesClass::getTinifyCompressionsCount($api_key) : 0;
        $stats_free_compressions_available = 500 - $stats_monthly_compression_usage;
        $stats_free_compressions_available = $stats_free_compressions_available > 0 ? $stats_free_compressions_available : 0;
        $stats_data = ElegantalTinyPngImageCompressImagesClass::getTotalSizeReduced();

        $this->context->smarty->assign(
            array(
                'adminUrl' => $this->getAdminUrl(),
                'moduleUrl' => $this->getModuleUrl(),
                'documentationUrls' => $this->getDocumentationUrls(),
                'contactDeveloperUrl' => $this->getContactDeveloperUrl(),
                'rateModuleUrl' => $this->getRateModuleUrl(),
                'models' => $models,
                'pages' => $pages,
                'currentPage' => $currentPage,
                'halfVisibleLinks' => $halfVisibleLinks,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'status_analyzing' => ElegantalTinyPngImageCompressClass::$STATUS_ANALYZING,
                'status_compressing' => ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING,
                'status_completed' => ElegantalTinyPngImageCompressClass::$STATUS_COMPLETED,
                'imageGroups' => ElegantalTinyPngImageCompressClass::$imageGroups,
                'stats_monthly_compression_usage' => $stats_monthly_compression_usage,
                'stats_free_compressions_available' => $stats_free_compressions_available,
                'stats_total_images_compressed' => $stats_data['total_count'],
                'stats_total_size_reduced' => ElegantalTinyPngImageCompressTools::displaySize($stats_data['total_size_reduced']),
                'total_images' => $total_images,
                'total_compressed' => $total_compressed,
                'total_not_compressed' => $total_not_compressed,
                'total_failed' => $total_failed,
                'total_size_before' => ElegantalTinyPngImageCompressTools::displaySize($total_size_before),
                'total_size_after' => ElegantalTinyPngImageCompressTools::displaySize($total_size_after),
                'total_disk_saved' => ElegantalTinyPngImageCompressTools::displaySize($total_disk_saved),
                'cron_last_error' => $this->getSetting('cron_last_error'),
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/list.tpl');
    }

    /**
     * Action function to manage settings. Renders and processes settings form
     * @return html
     */
    protected function editSettings()
    {
        $html = "";

        // Process Form
        if ($this->isPostRequest()) {
            $errors = array();

            if (Tools::getValue('api_key')) {
                $api_keys = $this->parseApiKeys(Tools::getValue('api_key'));
                $this->setSetting('api_key', $api_keys);
            } else {
                $errors[] = $this->l('API key is not valid.');
            }

            if (!Tools::getValue('compress_original_images') && !Tools::getValue('compress_generated_images')) {
                $errors[] = $this->l('Please choose to compress either original images or generated images or both.');
            }

            if (Tools::getValue('compress_original_images')) {
                $this->setSetting('compress_original_images', 1);
            } else {
                $this->setSetting('compress_original_images', 0);
            }
            if (Tools::getValue('compress_generated_images')) {
                $this->setSetting('compress_generated_images', 1);
            } else {
                $this->setSetting('compress_generated_images', 0);
            }
            if (Tools::isSubmit('image_formats_to_compress')) {
                $this->setSetting('image_formats_to_compress', Tools::getValue('image_formats_to_compress'));
            }
            if (Tools::isSubmit('image_types_to_compress')) {
                $this->setSetting('image_types_to_compress', Tools::getValue('image_types_to_compress'));
            }
            if (Tools::isSubmit('cron_compress_image_groups')) {
                $this->setSetting('cron_compress_image_groups', Tools::getValue('cron_compress_image_groups'));
            }
            if (Tools::isSubmit('cron_compress_custom_dirs')) {
                $cron_compress_custom_dirs = preg_split("/\\r\\n|\\r|\\n|,/", Tools::getValue('cron_compress_custom_dirs'));
                $this->setSetting('cron_compress_custom_dirs', $cron_compress_custom_dirs);
            }
            if (Tools::isSubmit('exclude_paths')) {
                $exclude_paths = preg_split("/\\r\\n|\\r|\\n|,/", Tools::getValue('exclude_paths'));
                $this->setSetting('exclude_paths', $exclude_paths);
            }
            if (Tools::getValue('compress_only_active_object_images')) {
                $this->setSetting('compress_only_active_object_images', 1);
            } else {
                $this->setSetting('compress_only_active_object_images', 0);
            }
            if (Tools::getValue('compress_only_visible_product_images')) {
                $this->setSetting('compress_only_visible_product_images', 1);
            } else {
                $this->setSetting('compress_only_visible_product_images', 0);
            }
            if (Tools::isSubmit('date_images_created_from')) {
                $this->setSetting('date_images_created_from', Tools::getValue('date_images_created_from'));
            }
            if (Tools::isSubmit('date_images_created_to')) {
                $this->setSetting('date_images_created_to', Tools::getValue('date_images_created_to'));
            }
            if (Tools::isSubmit('date_images_created_applied_groups')) {
                $this->setSetting('date_images_created_applied_groups', Tools::getValue('date_images_created_applied_groups'));
            }
            if (Tools::isSubmit('minimum_image_filesize_for_compression')) {
                $this->setSetting('minimum_image_filesize_for_compression', (int) Tools::getValue('minimum_image_filesize_for_compression'));
            }

            if (empty($errors)) {
                $this->setRedirectAlert($this->l('Settings saved successfully.'), 'success');
                if (Tools::isSubmit('submitAndStay') && !Tools::isSubmit('submitAndNext')) {
                    $this->redirectAdmin(array(
                        'event' => 'editSettings',
                    ));
                } else {
                    $this->redirectAdmin();
                }
            } else {
                $html .= $this->displayError(implode('<br>', $errors));
            }
        }

        // Render Form
        $fields_value = $this->getSettings();
        $fields_value['api_key'] = ($fields_value['api_key'] && is_array($fields_value['api_key'])) ? implode(PHP_EOL, $fields_value['api_key']) : "";
        $fields_value['cron_compress_custom_dirs'] = ($fields_value['cron_compress_custom_dirs'] && is_array($fields_value['cron_compress_custom_dirs'])) ? implode(PHP_EOL, $fields_value['cron_compress_custom_dirs']) : "";
        $fields_value['exclude_paths'] = ($fields_value['exclude_paths'] && is_array($fields_value['exclude_paths'])) ? implode(PHP_EOL, $fields_value['exclude_paths']) : "";
        $fields_value['cron_compress_image_groups[]'] = $fields_value['cron_compress_image_groups'];
        $fields_value['date_images_created_applied_groups[]'] = $fields_value['date_images_created_applied_groups'];
        $fields_value['image_formats_to_compress[]'] = $fields_value['image_formats_to_compress'];
        if (empty($fields_value['image_formats_to_compress[]'])) {
            $fields_value['image_formats_to_compress[]'] = array('all');
        }

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Edit Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('TinyPNG API Key'),
                        'name' => 'api_key',
                        'cols' => 10,
                        'rows' => 1,
                        'required' => true,
                        'desc' => $this->l('Enter your TinyPNG API key.') . ' ' . $this->l('You can get your API key on') . ' ' . '<a href="https://tinypng.com/developers" target="_blank">https://tinypng.com/developers</a>',
                    ),
                    array(
                        'type' => (_PS_VERSION_ < '1.6') ? 'el_switch' : 'switch',
                        'label' => $this->l('Compress Original Images'),
                        'name' => 'compress_original_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'compress_original_images_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'compress_original_images_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Original images will be compressed.') . ' ' . $this->l('This will affect only product, category, manufacturer, supplier and store images.'),
                    ),
                    array(
                        'type' => (_PS_VERSION_ < '1.6') ? 'el_switch' : 'switch',
                        'label' => $this->l('Compress Prestashop Generated Images'),
                        'name' => 'compress_generated_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'compress_generated_images_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'compress_generated_images_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Regenerated thumbnails will be compressed.') . ' ' . $this->l('This will affect only product, category, manufacturer, supplier and store images.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Product Image Formats To Compress'),
                        'name' => 'image_formats_to_compress[]',
                        'multiple' => true,
                        'options' => array(
                            'query' => $this->getProductImageTypesForSelect(),
                            'id' => 'key',
                            'name' => 'value'
                        ),
                        'desc' => $this->l('Select product image formats that you want to compress'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image Types To Compress'),
                        'name' => 'image_types_to_compress',
                        'options' => array(
                            'query' => array(
                                array('key' => 'all', 'value' => $this->l('Both JPG and PNG')),
                                array('key' => 'jpg', 'value' => 'JPG'),
                                array('key' => 'png', 'value' => 'PNG'),
                            ),
                            'id' => 'key',
                            'name' => 'value'
                        ),
                        'desc' => $this->l('Select the image type that you want to compress: JPG or PNG or both.'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Minimum image file size for compression (in KB)'),
                        'name' => 'minimum_image_filesize_for_compression',
                        'desc' => $this->l('The module will skip images that have file size lower than this specified size.') . ' ' . $this->l('You should enter the size in kilobytes.'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Exclude Directories'),
                        'name' => 'exclude_paths',
                        'cols' => 10,
                        'rows' => 1,
                        'desc' => $this->l('Enter directory paths that you want to exclude from compression.') . ' ' . $this->l('Images in these directories will not be compressed.') . ' ' . $this->l('You can enter multiple directories per new line.') . ' ' . sprintf($this->l('You may enter absolute path which must start with "/" e.g. %s or relative path to root folder of your store e.g. %s'), realpath(dirname(__FILE__) . '/../..') . '/example/', 'themes/example/img/'),
                    ),
                    array(
                        'type' => (_PS_VERSION_ < '1.6') ? 'el_switch' : 'switch',
                        'label' => $this->l('Exclude images of disabled entities'),
                        'name' => 'compress_only_active_object_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'compress_only_active_object_images_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'compress_only_active_object_images_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Images of disabled product/category/supplier/manufacturer will not be compressed'),
                    ),
                    array(
                        'type' => (_PS_VERSION_ < '1.6') ? 'el_switch' : 'switch',
                        'label' => $this->l('Exclude images of non-visible products'),
                        'name' => 'compress_only_visible_product_images',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'compress_only_visible_product_images_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'compress_only_visible_product_images_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        ),
                        'desc' => $this->l('Images of products that have visiblity set to nowhere will not be compressed.'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image groups to compress by CRON'),
                        'name' => 'cron_compress_image_groups[]',
                        'multiple' => true,
                        'options' => array(
                            'query' => $this->getImageGroupsForSelect(),
                            'id' => 'key',
                            'name' => 'value'
                        ),
                        'desc' => $this->l('Select image groups that you want to compress by CRON'),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom directory to compress by CRON'),
                        'name' => 'cron_compress_custom_dirs',
                        'cols' => 10,
                        'rows' => 1,
                        'desc' => $this->l('Enter custom directories that you want to compress by CRON.') . ' ' . $this->l('You can enter multiple directories per new line.') . ' ' . sprintf($this->l('You may enter absolute path which must start with "/" e.g. %s or relative path to root folder of your store e.g. %s'), realpath(dirname(__FILE__) . '/../..') . '/example/', 'themes/example/img/'),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Compress images created from'),
                        'name' => 'date_images_created_from',
                        'desc' => $this->l('Specify date if you want to compress images created since this time only'),
                    ),
                    array(
                        'type' => 'date',
                        'label' => $this->l('Compress images created to'),
                        'name' => 'date_images_created_to',
                        'desc' => $this->l('Specify date if you want to compress images created till this time only'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Apply above date to image groups'),
                        'name' => 'date_images_created_applied_groups[]',
                        'multiple' => true,
                        'options' => array(
                            'query' => $this->getImageGroupsForSelect(),
                            'id' => 'key',
                            'name' => 'value'
                        ),
                        'desc' => $this->l('Date range above (Compress images created from/to) will affect the selected image groups only.'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'submitAndNext',
                ),
                'buttons' => array(
                    array(
                        'href' => $this->getAdminUrl(),
                        'title' => $this->l('Back'),
                        'icon' => 'process-icon-back'
                    ),
                    array(
                        'title' => $this->l('Save and stay'),
                        'name' => 'submitAndStay',
                        'type' => 'submit',
                        'class' => 'pull-right',
                        'icon' => 'process-icon-save'
                    ),
                )
            )
        );

        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->submit_action = 'editSettings';
        $helper->name_controller = 'elegantalBootstrapWrapper';
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->getAdminUrl(array('event' => 'editSettings'));
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $lang->id,
                'iso_code' => $lang->iso_code
            ),
            'fields_value' => $fields_value,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $html . $helper->generateForm(array($fields_form));
    }

    /**
     * Returns current API Key (first one in array)
     * @return string
     */
    public function getApiKey()
    {
        $api_keys = $this->getApiKeys();
        return reset($api_keys);
    }

    /**
     * Returns all API Keys saved in settings
     * @return array
     */
    public function getApiKeys()
    {
        return $this->getSetting('api_key');
    }

    /**
     * Returns array of API Keys parsing from string
     * @param string $api_keys_str
     * @return array
     */
    public function parseApiKeys($api_keys_str)
    {
        $valid_api_keys = array();
        $api_keys = preg_split("/\\r\\n|\\r|\\n|,/", $api_keys_str);
        foreach ($api_keys as $api_key) {
            $api_key = trim($api_key);
            if (!empty($api_key)) {
                $valid_api_keys[] = $api_key;
            }
        }
        return $valid_api_keys;
    }

    /**
     * Returns list of product image types
     * @return array
     */
    protected function getProductImageTypesForSelect()
    {
        $result = array(array('key' => 'all', 'value' => $this->l('ALL FORMATS')));
        $imageTypes = ImageType::getImagesTypes();
        foreach ($imageTypes as $imageType) {
            if ($imageType['products'] == 1) {
                $result[] = array('key' => $imageType['name'], 'value' => $imageType['name']);
            }
        }
        return $result;
    }

    /**
     * Returns list of image groups for select
     * @return array
     */
    protected function getImageGroupsForSelect()
    {
        $result = array(array('key' => '', 'value' => ' '));
        $imageGroups = ElegantalTinyPngImageCompressClass::$imageGroups;
        foreach ($imageGroups as $imageGroup) {
            $result[] = array('key' => $imageGroup, 'value' => Tools::ucfirst($imageGroup));
        }
        return $result;
    }

    /**
     * Action to get product IDs from user to compress images of those products
     * @return string HTML
     */
    protected function selectedProductImages()
    {
        $product_ids = Tools::getValue('product_ids');
        if ($product_ids && is_array($product_ids)) {
            $model = new ElegantalTinyPngImageCompressClass();
            $model->image_group = 'product';
            $model->images_count = 0;
            $model->images_size_before = 0;
            $model->images_size_after = 0;
            $model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING;
            if (!$model->add()) {
                $this->setRedirectAlert($this->l('There was a problem analyzing images. Please try again later.'), 'error');
                $this->redirectAdmin(array('event' => 'selectedProductImages'));
            }
            foreach ($product_ids as $product_id) {
                $model->collectProductImages($product_id);
            }
            if ($model->images_count) {
                $this->redirectAdmin(array('event' => 'compress', 'id_elegantaltinypngimagecompress' => $model->id));
            } else {
                $model->delete();
                $this->setRedirectAlert($this->l('No images found to compress OR all images in this group were already processed.'), 'success');
                $this->redirectAdmin();
            }
        }

        $this->context->smarty->assign(array('adminUrl' => $this->getAdminUrl()));
        return $this->display(__FILE__, 'views/templates/admin/selectedProductImages.tpl');
    }

    /**
     * Renders list of products for optional products field. It is ajax based and used as a search.
     * @param string $query
     * @param array $includeProductIds
     * @param array $excludeProductIds
     * @param boolean $return
     * @return string HTML
     */
    protected function loadProductsForSelect($query, $includeProductIds = array(), $excludeProductIds = array(), $return = false)
    {
        $html = "";
        $products = $this->getProductsBySearchQuery($query, $includeProductIds, $excludeProductIds);
        if ($products) {
            $this->context->smarty->assign(
                array(
                    'link' => $this->context->link,
                    'products' => $products,
                    'includeProductIds' => $includeProductIds
                )
            );
            $html = $this->display(__FILE__, 'views/templates/admin/loadProducts.tpl');
        }
        if ($return) {
            return $html;
        } else {
            echo $html;
            exit;
        }
    }

    /**
     * Returns list of products found by search query
     * @param string $query
     * @param array $includeProductIds
     * @param array $excludeProductIds
     * @return array
     */
    protected function getProductsBySearchQuery($query, $includeProductIds = array(), $excludeProductIds = array())
    {
        $products = array();

        if ((!empty($query) && Tools::strlen($query) > 0) || !empty($includeProductIds)) {
            $sql = "SELECT p.`id_product`, p.`reference`, pl.`name`, pl.`link_rewrite`, i.`id_image` 
                FROM `" . _DB_PREFIX_ . "product` p 
                INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = " . (int) $this->context->language->id . Shop::addSqlRestrictionOnLang("pl") . ") 
                INNER JOIN `" . _DB_PREFIX_ . "product_shop` psh ON (psh.`id_product` = p.`id_product` AND psh.`id_shop` = " . (int) $this->context->shop->id . ") 
                LEFT JOIN `" . _DB_PREFIX_ . "image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1) 
                WHERE psh.`active` = 1 ";
            if ($includeProductIds) {
                $sql .= "AND p.`id_product` IN (" . implode(", ", array_map("intval", $includeProductIds)) . ") ";
            }
            if ($excludeProductIds) {
                $sql .= "AND p.`id_product` NOT IN (" . implode(", ", array_map("intval", $excludeProductIds)) . ") ";
            }
            if ($query) {
                $sql .= "AND (p.`id_product` LIKE '%" . pSQL($query) . "%' OR pl.`name` LIKE '%" . pSQL($query) . "%' OR p.`reference` LIKE '%" . pSQL($query) . "%') ";
            }
            $sql .= "GROUP BY p.`id_product` ";
            $sql .= !empty($query) ? "LIMIT 20" : "";

            $products = Db::getInstance()->executeS($sql);
        }

        return $products;
    }

    /**
     * Action to get custom directory from user
     * @return string HTML
     */
    protected function customDir()
    {
        $html = "";
        $path = null;

        if ($this->isPostRequest()) {
            if (Tools::getValue('custom_dir')) {
                $path = Tools::getValue('custom_dir');
                if (Tools::substr($path, 0, 1) == '/') {
                    $path = realpath($path);
                } else {
                    $path = realpath(_PS_ROOT_DIR_ . '/' . $path);
                }
            }

            if ($path && (is_dir($path) || is_file($path))) {
                $this->redirectAdmin(array('event' => 'analyze', 'image_group' => 'custom', 'custom_dir' => $path));
            } else {
                $html .= $this->displayError($this->l('Invalid directory path.'));
            }
        }

        $this->context->smarty->assign(
            array(
                'adminUrl' => $this->getAdminUrl(),
                'absolute_path_eg' => realpath(dirname(__FILE__) . '/../..') . '/example/'
            )
        );

        return $html . $this->display(__FILE__, 'views/templates/admin/customDir.tpl');
    }

    /**
     * Renders log of images compressed
     * @return string HTML
     */
    protected function imagesLog()
    {
        $total = ElegantalTinyPngImageCompressImagesClass::model()->countAll();
        $limit = 20;
        $pages = ceil($total / $limit);
        $currentPage = (int) Tools::getValue('page', 1);
        $currentPage = ($currentPage > $pages) ? $pages : $currentPage;
        $halfVisibleLinks = 5;
        $offset = ($total > $limit) ? ($currentPage - 1) * $limit : 0;

        // Sorting records
        $sortableColumns = array(
            'image_path',
            'image_size_before',
            'image_size_after',
            'status',
        );
        $orderBy = (in_array(Tools::getValue('orderBy'), $sortableColumns)) ? Tools::getValue('orderBy') : 'id_elegantaltinypngimagecompress_images';
        $orderType = (Tools::getValue('orderType') == 'asc') ? 'asc' : 'desc';

        $images = ElegantalTinyPngImageCompressImagesClass::model()->findAll(array(
            'order' => $orderBy . ' ' . $orderType,
            'offset' => $offset,
            'limit' => $limit,
        ));

        foreach ($images as &$image) {
            $image['image_size_before'] = ElegantalTinyPngImageCompressTools::displaySize($image['image_size_before']);
            $image['image_size_after'] = ElegantalTinyPngImageCompressTools::displaySize($image['image_size_after']);
        }

        $this->context->smarty->assign(
            array(
                'adminUrl' => $this->getAdminUrl(),
                'images' => $images,
                'pages' => $pages,
                'currentPage' => $currentPage,
                'halfVisibleLinks' => $halfVisibleLinks,
                'orderBy' => $orderBy,
                'orderType' => $orderType,
                'status_not_compressed' => ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED,
                'status_compressed' => ElegantalTinyPngImageCompressImagesClass::$STATUS_COMPRESSED,
                'status_failed' => ElegantalTinyPngImageCompressImagesClass::$STATUS_FAILED,
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/imagesLog.tpl');
    }

    /**
     * Renders form with options and analyzes images for compression. POST request handled as AJAX
     * @return string HTML
     */
    protected function analyze()
    {
        if (Tools::getValue('ajax')) {
            $result = array();

            if ($this->model) {
                $offset = Tools::getValue('offset');
                $limit = Tools::getValue('limit');

                if ($this->model->collectImages($offset, $limit)) {
                    $result['success'] = true;
                } else {
                    $result['success'] = false;
                    $result['message'] = $this->l('There was a problem analyzing images. Please try again later.');
                }
            } else {
                $result['success'] = false;
                $result['message'] = $this->l('Record not found.');
            }

            die(Tools::jsonEncode($result));
        } else {
            if ($this->model && $this->model->status == ElegantalTinyPngImageCompressClass::$STATUS_ANALYZING) {
                $model = $this->model;

                // Previous analyze did not finish. So delete images of this model and start analyze from beginning
                $model->deleteImages();

                $model->images_count = 0;
                $model->images_size_before = 0;
                $model->images_size_after = 0;
                $model->update();
            } else {
                $image_group = Tools::getValue('image_group');
                if (!$image_group || !in_array($image_group, ElegantalTinyPngImageCompressClass::$imageGroups)) {
                    $this->redirectAdmin();
                }

                $model = new ElegantalTinyPngImageCompressClass();
                $model->image_group = $image_group;
                $model->images_count = 0;
                $model->images_size_before = 0;
                $model->images_size_after = 0;
                $model->status = ElegantalTinyPngImageCompressClass::$STATUS_ANALYZING;

                if (Tools::getValue('custom_dir')) {
                    if ($custom_dir = realpath(Tools::getValue('custom_dir'))) {
                        $model->custom_dir = ElegantalTinyPngImageCompressTools::serialize(array($custom_dir));
                    } else {
                        $this->redirectAdmin();
                    }
                }

                $model->add();
            }

            $totalImageIds = $model->getImageIdsByImageGroup();
            $total = (!empty($totalImageIds) && is_array($totalImageIds)) ? count($totalImageIds) : 0;
            $offset = 0;
            $limit = (int) $this->getSetting('analyze_per_request');
            $numberOfRequests = 1;

            if ($total && $total > $offset && $total > $limit) {
                $numberOfRequests = ceil(($total - $offset) / $limit);
            }

            $this->context->smarty->assign(
                array(
                    'id_elegantaltinypngimagecompress' => $model->id,
                    'image_group' => $model->image_group,
                    'total' => $total,
                    'offset' => $offset,
                    'limit' => $limit,
                    'requests' => $numberOfRequests,
                    'adminUrl' => $this->getAdminUrl(),
                    'compressUrl' => $this->getAdminUrl(array('event' => 'compress', 'id_elegantaltinypngimagecompress' => $model->id)),
                )
            );

            return $this->display(__FILE__, 'views/templates/admin/analyze.tpl');
        }
    }

    /**
     * Renders compression page.
     * @return string HTML
     */
    protected function compress()
    {
        if (!$this->model) {
            $this->setRedirectAlert($this->l('Record not found.'), 'error');
            $this->redirectAdmin();
        }

        if ($this->model->status == ElegantalTinyPngImageCompressClass::$STATUS_COMPLETED) {
            $this->setRedirectAlert($this->l('Compression completed successfully.'), 'success');
            $this->redirectAdmin();
        }

        if (!$this->model->images_count) {
            $this->model->delete();
            $this->setRedirectAlert($this->l('No images found to compress OR all images in this group were already processed.'), 'success');
            $this->redirectAdmin();
        }

        $this->model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING;
        if (!$this->model->update()) {
            $this->setRedirectAlert($this->l('Could not update status.'), 'success');
            $this->redirectAdmin();
        }

        $api_key = $this->getApiKey();
        if (!$api_key) {
            $this->setRedirectAlert($this->l('Enter your TinyPNG API key. You can get your API key on https://tinypng.com/developers'), 'error');
            $this->redirectAdmin(array('event' => 'editSettings'));
        }

        $modelAttrs = ElegantalTinyPngImageCompressClass::findByPkWithImages($this->model->id);
        if (!$modelAttrs) {
            $this->setRedirectAlert($this->l('Record not found.'), 'error');
            $this->redirectAdmin();
        }

        $modelAttrs['images_size_before'] = ElegantalTinyPngImageCompressTools::displaySize($modelAttrs['images_size_before']);
        $modelAttrs['images_size_after'] = ElegantalTinyPngImageCompressTools::displaySize($modelAttrs['images_size_after']);

        // Progress bar
        $total = $modelAttrs['images_count'];
        $processed = $total - $modelAttrs['not_compressed'];
        $progress = ($processed * 100) / $total;
        $progressTxt = round($progress);
        if ($progress < 1) {
            $progressTxt = round($progress, 2);
        }

        $this->context->smarty->assign(
            array(
                'adminUrl' => $this->getAdminUrl(),
                'model' => $modelAttrs,
                'progress' => $progress,
                'progressTxt' => $progressTxt,
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/compress.tpl');
    }

    /**
     * Ajax function to compress image using TinyPNG
     */
    protected function tinify()
    {
        $result = array();

        if (!$this->model) {
            $result['success'] = false;
            $result['next'] = 0;
            $result['message'] = $this->l('Record not found.');
            $result['redirect'] = $this->getAdminUrl();
            die(Tools::jsonEncode($result));
        }

        $api_keys_setting = $this->getApiKeys();
        $api_count = count($api_keys_setting);

        if ($api_count < 1) {
            $result['success'] = false;
            $result['next'] = 0;
            $result['message'] = $this->l('API key is not valid.');
            $result['redirect'] = $this->getAdminUrl(array('event' => 'editSettings'));
            die(Tools::jsonEncode($result));
        }

        $imageModelArr = ElegantalTinyPngImageCompressImagesClass::model()->find(array(
            'condition' => array(
                'id_elegantaltinypngimagecompress' => $this->model->id,
                'status' => ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED
            )
        ));

        if ($imageModelArr) {
            $result['next'] = 1;
            $imageModel = new ElegantalTinyPngImageCompressImagesClass($imageModelArr['id_elegantaltinypngimagecompress_images']);
            if ($imageModel) {
                $api_keys = $api_keys_setting;
                foreach ($api_keys as $key => $api_key) {
                    $compressResult = $imageModel->compress($api_key);
                    if ($compressResult === 'API KEY NOT VALID' && $api_count > 1) {
                        // move this key to the end of $api_keys_setting (not $api_keys)
                        unset($api_keys_setting[$key]);
                        $api_keys_setting[] = $api_key;
                        continue;
                    } else {
                        break;
                    }
                }

                // Save api keys with new order if there are multiple api keys
                if ($api_count > 1) {
                    $this->setSetting('api_key', $api_keys_setting);
                }

                if ($compressResult === 'API KEY NOT VALID') {
                    $compressResult = $this->l('There was a problem with your API key. Your request could not be authorized.');
                }

                if ($compressResult === true) {
                    $this->model->images_size_after -= $imageModel->image_size_before - $imageModel->image_size_after;
                    $result['success'] = true;
                } elseif ($compressResult === false) {
                    $result['success'] = false;
                } else {
                    $result['success'] = false;
                    $result['message'] = $compressResult;
                    $result['next'] = 0;
                    $result['redirect'] = $this->getAdminUrl();
                }
            } else {
                $result['success'] = false;
            }
        } else {
            $this->model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPLETED;
            $result['success'] = false;
            $result['next'] = 0;
        }

        $this->model->update();

        $result['imagesSizeAfter'] = ElegantalTinyPngImageCompressTools::displaySize($this->model->images_size_after);
        $result['sizeSaved'] = ElegantalTinyPngImageCompressTools::displaySize($this->model->images_size_before - $this->model->images_size_after);

        die(Tools::jsonEncode($result));
    }

    /**
     * Action to compress single image from images log
     */
    protected function compressSingleImage()
    {
        $image_id = Tools::getValue('image_id');
        if (!$image_id) {
            $this->setRedirectAlert('Image ID: ' . $this->l('Record not found.'), 'error');
            $this->redirectAdmin(array('event' => 'imagesLog'), true);
        }

        $imageModel = new ElegantalTinyPngImageCompressImagesClass($image_id);
        if (!Validate::isLoadedObject($imageModel)) {
            $this->setRedirectAlert('Image Model: ' . $this->l('Record not found.'), 'error');
            $this->redirectAdmin(array('event' => 'imagesLog'), true);
        }

        if (!is_file($imageModel->image_path)) {
            $imageModel->delete();
            $this->setRedirectAlert($this->l('Image not found.'), 'error');
            $this->redirectAdmin(array('event' => 'imagesLog'), true);
        }

        $model = new ElegantalTinyPngImageCompressClass($imageModel->id_elegantaltinypngimagecompress);
        if (!Validate::isLoadedObject($model)) {
            $this->setRedirectAlert('Model: ' . $this->l('Record not found.'), 'error');
            $this->redirectAdmin(array('event' => 'imagesLog'), true);
        }

        $api_keys_setting = $this->getApiKeys();
        $api_count = count($api_keys_setting);

        if ($api_count < 1) {
            $this->setRedirectAlert($this->l('API key is not valid.'), 'error');
            $this->redirectAdmin(array('event' => 'imagesLog'), true);
        }

        $api_keys = $api_keys_setting;
        foreach ($api_keys as $key => $api_key) {
            $compressResult = $imageModel->compress($api_key);
            if ($compressResult === 'API KEY NOT VALID' && $api_count > 1) {
                // move this key to the end of $api_keys_setting (not $api_keys)
                unset($api_keys_setting[$key]);
                $api_keys_setting[] = $api_key;
                continue;
            } else {
                break;
            }
        }

        // Save api keys with new order if there are multiple api keys
        if ($api_count > 1) {
            $this->setSetting('api_key', $api_keys_setting);
        }

        if ($compressResult === 'API KEY NOT VALID') {
            $compressResult = $this->l('There was a problem with your API key. Your request could not be authorized.');
        }

        if ($compressResult === true) {
            $model->images_size_after -= $imageModel->image_size_before - $imageModel->image_size_after;
            // Check if there is any more image to compress for this model. If no, make it as completed.
            $moreImageExists = ElegantalTinyPngImageCompressImagesClass::model()->find(array(
                'condition' => array(
                    'id_elegantaltinypngimagecompress' => $imageModel->id_elegantaltinypngimagecompress,
                    'status' => ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED
                )
            ));
            if (!$moreImageExists) {
                $model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPLETED;
            }
            $model->update();
            $this->setRedirectAlert($this->l('Compression completed successfully.'), 'success');
        } elseif ($compressResult === false) {
            $this->setRedirectAlert($this->l('The image could not be compressed.') . ' ' . $this->l('Make sure your img folder has proper write permissions, so that the module can delete old images and save new compressed images.'), 'error');
        } else {
            $this->setRedirectAlert($compressResult, 'error');
        }
        $this->redirectAdmin(array('event' => 'imagesLog'), true);
    }

    /**
     * Action to delete model
     */
    protected function delete()
    {
        if (!$this->model) {
            $this->setRedirectAlert($this->l('Record not found.'), 'error');
            $this->redirectAdmin(array(), true);
        }
        if (!$this->deleteModel($this->model->id)) {
            $this->setRedirectAlert($this->l('Could not delete this record.'), 'error');
            $this->redirectAdmin(array(), true);
        }
        $this->setRedirectAlert($this->l('Record deleted successfully.'), 'success');
        $this->redirectAdmin(array(), true);
    }

    /**
     * Delete model by id and uncompressed images of this model
     * @param int $id
     * @return boolean
     * @throws Exception
     */
    protected function deleteModel($id)
    {
        $model = new ElegantalTinyPngImageCompressClass($id);
        if (!$id || !Validate::isLoadedObject($model)) {
            throw new Exception($this->l('Record not found.') . ' ' . $id);
        }

        // Delete model
        if (!$model->delete()) {
            throw new Exception($this->l('Could not delete this record.') . ' ' . $id);
        }

        // Delete uncompressed images of this model and then set model id to 0 for compressed images
        $queries = array();
        $queries[] = "SET foreign_key_checks = 0";
        $queries[] = "DELETE FROM `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
                      WHERE `id_elegantaltinypngimagecompress` = " . (int) $model->id . " AND 
                      `status` != " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_COMPRESSED;
        $queries[] = "UPDATE `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` 
                      SET `id_elegantaltinypngimagecompress` = 0 
                      WHERE `id_elegantaltinypngimagecompress` = " . (int) $model->id;
        $queries[] = "SET foreign_key_checks = 1";
        foreach ($queries as $query) {
            if (Db::getInstance()->execute($query, false) == false) {
                throw new Exception(Db::getInstance()->getMsgError());
            }
        }

        return true;
    }

    /**
     * Action to delete compression records in bulk
     */
    protected function deleteBulk()
    {
        $result = array('redirect' => $this->getAdminUrl());

        if ($this->isPostRequest() && Tools::getValue('ids')) {
            $ids = Tools::getValue('ids');
            foreach ($ids as $id) {
                try {
                    $this->deleteModel($id);
                } catch (Exception $e) {
                    // Nothing
                }
            }
        }

        die(Tools::jsonEncode($result));
    }

    /**
     * Hook action called when product is saved: both add() and update()
     * @param array $params
     */
    public function hookActionProductSave($params)
    {
        $id_product = null;

        if (isset($params['id_product']) && $params['id_product']) {
            $id_product = $params['id_product'];
        } elseif (isset($params['product']) && $params['product']->id) {
            $id_product = $params['product']->id;
        }

        $cron_compress_image_groups = $this->getSetting('cron_compress_image_groups');

        if ($id_product && in_array('product', $cron_compress_image_groups)) {
            $model = new ElegantalTinyPngImageCompressClass();
            $model->image_group = 'product';
            $model->images_count = 0;
            $model->images_size_before = 0;
            $model->images_size_after = 0;
            $model->status = ElegantalTinyPngImageCompressClass::$STATUS_ANALYZING;
            if ($model->add()) {
                $model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING;
                $model->collectProductImages($id_product);
                if (!$model->images_count) {
                    $model->delete();
                }
            }
        }
    }

    /**
     * Renders CRON details
     * @return string HTML
     */
    protected function viewCron()
    {
        $cron_cpanel_doc = null;
        $documentation_urls = $this->getDocumentationUrls();
        foreach ($documentation_urls as $doc => $url) {
            if ($doc == 'Setup Cron Job In Cpanel') {
                $cron_cpanel_doc = $url;
                break;
            }
        }

        $this->context->smarty->assign(
            array(
                'adminUrl' => $this->getAdminUrl(),
                'cronUrl' => $this->getControllerUrl('cron'),
                'cron_cpanel_doc' => $cron_cpanel_doc,
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/cron.tpl');
    }

    /**
     * Action to execute module's CRON job
     */
    public function executeCron()
    {
        $settings = $this->getSettings();
        $secure_key = $settings['security_token_key'];
        $api_keys_setting = Tools::getValue('api_key') ? $this->parseApiKeys(Tools::getValue('api_key')) : $this->getApiKeys();
        $api_count = count($api_keys_setting);

        if ($api_count > 0 && $secure_key && Tools::getValue('secure_key') == $secure_key) {
            $this->setSetting('cron_last_error', '');

            $limit = $settings['cron_compress_per_request'];

            $modelAttr = ElegantalTinyPngImageCompressClass::model()->find(array(
                'condition' => array(
                    'status' => ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING
                ),
                'order' => 'id_elegantaltinypngimagecompress'
            ));

            if ($modelAttr && $modelAttr['id_elegantaltinypngimagecompress']) {
                $model = new ElegantalTinyPngImageCompressClass($modelAttr['id_elegantaltinypngimagecompress']);
                if (Validate::isLoadedObject($model)) {
                    $imageModels = ElegantalTinyPngImageCompressImagesClass::model()->findAll(array(
                        'condition' => array(
                            'id_elegantaltinypngimagecompress' => $model->id,
                            'status' => ElegantalTinyPngImageCompressImagesClass::$STATUS_NOT_COMPRESSED
                        ),
                        'order' => 'id_elegantaltinypngimagecompress_images',
                        'limit' => $limit,
                    ));

                    if ($imageModels) {
                        foreach ($imageModels as $imageModelAttr) {
                            $imageModel = new ElegantalTinyPngImageCompressImagesClass($imageModelAttr['id_elegantaltinypngimagecompress_images']);

                            $api_keys = $api_keys_setting;
                            foreach ($api_keys as $key => $api_key) {
                                $compressResult = $imageModel->compress($api_key);
                                if ($compressResult === 'API KEY NOT VALID' && $api_count > 1) {
                                    // move this key to the end of $api_keys_setting (not $api_keys)
                                    unset($api_keys_setting[$key]);
                                    $api_keys_setting[] = $api_key;
                                    continue;
                                } else {
                                    break;
                                }
                            }

                            // Save api keys with new order if there are multiple api keys and api keys are not from URL
                            if ($api_count > 1 && !Tools::getValue('api_key')) {
                                $this->setSetting('api_key', $api_keys_setting);
                            }

                            if ($compressResult === 'API KEY NOT VALID') {
                                $compressResult = $this->l('There was a problem with your API key. Your request could not be authorized.');
                            } elseif ($compressResult === true) {
                                // Compressed successfully
                                $model->images_size_after -= $imageModel->image_size_before - $imageModel->image_size_after;
                            } elseif ($compressResult === false) {
                                // This particular image failed but it is OK to continue
                                // We don't have to do anything here, just keep compressing
                            } else {
                                // Something went wrong, we should stop here
                                $this->setSetting('cron_last_error', $compressResult);
                                break;
                            }
                        }
                    } else {
                        // If analyze did not find any image, complete this compression
                        $model->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPLETED;
                    }
                    $model->update();
                }
            } else {
                // Analyze new images
                $cron_compress_image_groups = $settings['cron_compress_image_groups'];
                $cron_analyzed_image_groups = $settings['cron_analyzed_image_groups'];

                // Product images are handled after it is saved/updated
                if (!in_array('product', $cron_analyzed_image_groups)) {
                    $cron_analyzed_image_groups[] = 'product';
                }

                // Analyze one image group per cron execution
                $image_group = false;
                foreach ($cron_compress_image_groups as $cron_image_group) {
                    if (!in_array($cron_image_group, $cron_analyzed_image_groups)) {
                        $image_group = $cron_image_group;
                        break;
                    }
                }

                if ($image_group) {
                    // Collect images
                    $newModel = new ElegantalTinyPngImageCompressClass();
                    $newModel->image_group = $image_group;
                    $newModel->custom_dir = ElegantalTinyPngImageCompressTools::serialize($settings['cron_compress_custom_dirs']);
                    $newModel->images_count = 0;
                    $newModel->images_size_before = 0;
                    $newModel->images_size_after = 0;
                    $newModel->status = ElegantalTinyPngImageCompressClass::$STATUS_ANALYZING;
                    if ($newModel->add()) {
                        $newModel->status = ElegantalTinyPngImageCompressClass::$STATUS_COMPRESSING;
                        $newModel->collectImages();
                        if (!$newModel->images_count) {
                            $newModel->delete();
                        }
                    }
                    // Add this image group to analyzed images, so that it will be skipped next time
                    $cron_analyzed_image_groups[] = $image_group;
                } else {
                    // If all image groups were analyzed, empty this setting
                    $cron_analyzed_image_groups = array();
                }
                $this->setSetting('cron_analyzed_image_groups', $cron_analyzed_image_groups);
            }
        }
    }
}
