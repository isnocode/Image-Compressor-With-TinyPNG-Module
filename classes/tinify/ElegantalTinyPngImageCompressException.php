<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * Exception class
 */
class ElegantalTinyPngImageCompressException extends Exception
{

    public $status;

    public static function create($message, $type, $status)
    {
        if ($status == 401 || $status == 429) {
            $klass = "ElegantalTinyPngImageCompressAccountException";
        } elseif ($status >= 400 && $status <= 499) {
            $klass = "ElegantalTinyPngImageCompressClientException";
        } elseif ($status >= 500 && $status <= 599) {
            $klass = "ElegantalTinyPngImageCompressServerException";
        } else {
            $klass = "ElegantalTinyPngImageCompressException";
        }

        if (empty($message)) {
            $message = "No message was provided";
        }

        return new $klass($message, $type, $status);
    }

    public function __construct($message, $type = null, $status = null)
    {
        $this->status = $status;
        if ($status) {
            parent::__construct($message . " (HTTP " . $status . "/" . $type . ")");
        } else {
            parent::__construct($message);
        }
    }
}

class ElegantalTinyPngImageCompressAccountException extends ElegantalTinyPngImageCompressException
{
    
}

class ElegantalTinyPngImageCompressClientException extends ElegantalTinyPngImageCompressException
{
    
}

class ElegantalTinyPngImageCompressServerException extends ElegantalTinyPngImageCompressException
{
    
}

class ElegantalTinyPngImageCompressConnectionException extends ElegantalTinyPngImageCompressException
{
    
}
