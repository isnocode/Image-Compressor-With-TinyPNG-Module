<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * ResultMeta class
 */
class ElegantalTinyPngImageCompressResultMeta
{

    protected $meta;

    public function __construct($meta)
    {
        $this->meta = $meta;
    }

    public function width()
    {
        return (int) $this->meta["image-width"];
    }

    public function height()
    {
        return (int) $this->meta["image-height"];
    }

    public function location()
    {
        return isset($this->meta["location"]) ? $this->meta["location"] : null;
    }
}
