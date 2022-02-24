<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * Result class
 */
class ElegantalTinyPngImageCompressResult extends ElegantalTinyPngImageCompressResultMeta
{

    protected $data;

    public function __construct($meta, $data)
    {
        $this->meta = $meta;
        $this->data = $data;
    }

    public function data()
    {
        return $this->data;
    }

    public function toBuffer()
    {
        return $this->data;
    }

    public function toFile($path)
    {
        return file_put_contents($path, $this->toBuffer());
    }

    public function size()
    {
        return (int) $this->meta["content-length"];
    }

    public function mediaType()
    {
        return $this->meta["content-type"];
    }

    public function contentType()
    {
        return $this->mediaType();
    }
}
