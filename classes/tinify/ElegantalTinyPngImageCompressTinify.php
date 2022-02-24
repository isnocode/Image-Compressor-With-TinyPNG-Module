<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * Main class of API
 */
class ElegantalTinyPngImageCompressTinify
{

    const VERSION = "1.5.2";

    private static $key = null;
    private static $appIdentifier = null;
    private static $proxy = null;
    private static $compressionCount = null;
    private static $client = null;

    public static function setKey($key)
    {
        self::$key = $key;
        self::$client = null;
    }

    public static function setAppIdentifier($appIdentifier)
    {
        self::$appIdentifier = $appIdentifier;
        self::$client = null;
    }

    public static function setProxy($proxy)
    {
        self::$proxy = $proxy;
        self::$client = null;
    }

    public static function getCompressionCount()
    {
        return self::$compressionCount;
    }

    public static function setCompressionCount($compressionCount)
    {
        self::$compressionCount = $compressionCount;
    }

    public static function getClient()
    {
        if (!self::$key) {
            throw new ElegantalTinyPngImageCompressAccountException("Provide valid API key.");
        }

        if (!self::$client) {
            self::$client = new ElegantalTinyPngImageCompressClient(self::$key, self::$appIdentifier, self::$proxy);
        }

        return self::$client;
    }

    public static function setClient($client)
    {
        self::$client = $client;
    }

    public static function validate()
    {
        try {
            self::getClient()->request("post", "/shrink");
        } catch (ElegantalTinyPngImageCompressAccountException $err) {
            if ($err->status == 429) {
                return true;
            }
            throw $err;
        } catch (ElegantalTinyPngImageCompressClientException $err) {
            return true;
        }
    }
}
