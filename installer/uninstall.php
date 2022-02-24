<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * This file returns array of SQL queries that are required to be executed during module un-installation.
 */
$sql = array();

// Drop tables that are created during module installation. Note: order of queries is important here.
$sql[] = "SET foreign_key_checks = 0";
$sql[] = "DELETE FROM `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` WHERE `status` != " . (int) ElegantalTinyPngImageCompressImagesClass::$STATUS_COMPRESSED;
$sql[] = "UPDATE `" . _DB_PREFIX_ . "elegantaltinypngimagecompress_images` SET `id_elegantaltinypngimagecompress` = 0";
$sql[] = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "elegantaltinypngimagecompress`";
$sql[] = "SET foreign_key_checks = 1";

return $sql;
