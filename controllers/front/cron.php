<?php
/**
 * @author    ELEGANTAL
 * @copyright (c) 2022,
 * @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
 */

/**
 * This is controller for CRON
 */
class ElegantalTinyPngImageCompressCronModuleFrontController extends ModuleFrontController
{

    public function display()
    {
        $this->module->executeCron();
    }
}
