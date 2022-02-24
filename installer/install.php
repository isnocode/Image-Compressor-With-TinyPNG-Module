<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * This file returns array of SQL queries that are required to be executed during module installation.
 */
$sql = array();

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "elegantaltinypngimagecompress` (
    `id_elegantaltinypngimagecompress` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `image_group` varchar(255) NOT NULL,
    `custom_dir` varchar(255),
    `images_count` int(11) unsigned NOT NULL,
    `images_size_before` bigint(20) unsigned NOT NULL,
    `images_size_after` bigint(20) unsigned NOT NULL,
    `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY  (`id_elegantaltinypngimagecompress`) 
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=UTF8;";

$sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` (
    `id_elegantaltinypngimagecompress_images` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `id_elegantaltinypngimagecompress` int(11) unsigned NOT NULL, 
    `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `image_path` varchar(255) NOT NULL,
    `image_size_before` int(11) unsigned NOT NULL,
    `image_size_after` int(11) unsigned NOT NULL,
    `modified_at` DATETIME,
    PRIMARY KEY (`id_elegantaltinypngimagecompress_images`) 
) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=UTF8;";

return $sql;
