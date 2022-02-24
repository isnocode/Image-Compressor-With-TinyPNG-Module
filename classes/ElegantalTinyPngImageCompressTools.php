<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * This is a helper class which provides some functions used all over the module
 */
class ElegantalTinyPngImageCompressTools
{

    /**
     * Serializes array to store in database
     * @param array $array
     * @return string
     */
    public static function serialize($array)
    {
        // return Tools::jsonEncode($array);
        // return serialize($array);
        // return base64_encode(serialize($array));
        return call_user_func('base64_encode', serialize($array));
    }

    /**
     * Un-serializes serialized string
     * @param string $string
     * @return array
     */
    public static function unserialize($string)
    {
        // $array = Tools::jsonDecode($string, true);
        // $array = @unserialize($string);
        // $array = @unserialize(base64_decode($string));
        $array = @unserialize(call_user_func('base64_decode', $string));
        return empty($array) ? array() : $array;
    }

    /**
     * Returns formatted file size in GB, MB, KB or bytes
     * @param int $size
     * @return string
     */
    public static function displaySize($size)
    {
        $size = (int) $size;

        $display_bytes = false;

        if ($display_bytes && $size < 1024) {
            $size .= " bytes";
        } elseif ($size < 1048576) {
            $size = round($size / 1024) . " KB";
        } elseif ($size < 1073741824) {
            $size = round($size / 1048576, 1) . " MB";
        } else {
            $size = round($size / 1073741824, 1) . " GB";
        }

        return $size;
    }

    /**
     * Returns mime type of given file
     * @param string $file
     * @return string
     * @throws Exception
     */
    public static function getMimeType($file)
    {
        if (!is_file($file)) {
            throw new Exception('File does not exist: ' . $file);
        }

        $mime = null;
        if (function_exists('finfo_file') && function_exists('finfo_open') && defined('FILEINFO_MIME_TYPE')) {
            // Use the Fileinfo PECL extension (PHP 5.3+)
            $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
        } elseif (function_exists('mime_content_type')) {
            // Deprecated in PHP 5.3
            $mime = mime_content_type($file);
        } elseif (function_exists('exif_imagetype')) {
            if (exif_imagetype($file) == IMAGETYPE_PNG) {
                $mime = 'image/png';
            } elseif (exif_imagetype($file) == IMAGETYPE_JPEG) {
                $mime = 'image/jpeg';
            }
        } else {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $extension = Tools::strtoupper($extension);
            switch ($extension) {
                case "JPG":
                case "JPEG":
                    $mime = "image/jpeg";
                    break;
                case "PNG":
                    $mime = "image/png";
                    break;
                default:
                    break;
            }
        }

        return $mime ? Tools::strtolower($mime) : false;
    }
}
