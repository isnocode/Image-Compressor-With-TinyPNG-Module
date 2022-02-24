{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}
<div class="elegantalBootstrapWrapper">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-picture-o"></i> {l s='Image Compressor With' mod='elegantaltinypngimagecompress'} <a href="https://tinypng.com" target="_blank"> TinyPNG</a>
        </div>
        <div class="panel-body">
            <div class="row elegantal_analyze_panel" data-id="{$id_elegantaltinypngimagecompress|intval}" data-total="{$total|intval}" data-offset="{$offset|intval}" data-limit="{$limit|intval}" data-requests="{$requests|intval}" data-reloadmsg="{l s='Analyzing images has not finished yet.' mod='elegantaltinypngimagecompress'}">
                <div class="col-xs-12 col-md-offset-2 col-md-8">
                    <div class="bootstrap elegantal_hidden elegantal_analyze_error">
                        <div class="module_error alert alert-danger">
                            <span class="elegantal_analyze_error_txt"></span>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-filter"></i> {l s='Analyzing %1$s images...' sprintf=$image_group|replace:'_':' ' mod='elegantaltinypngimagecompress'}
                        </div>
                        <div class="panel-body">
                            <br><br>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="progress">
                                        <div class="elegantal_analyze_progress_bar progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="min-width: 3em; width: 0%;">
                                            0%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 text-center">
                                    {l s='Please wait. It may take a few minutes.' mod='elegantaltinypngimagecompress'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="elegantaltinypngimagecompressJsDef elegantal_hidden" data-adminurl="{$adminUrl|escape:'html':'UTF-8'}" data-compressurl="{$compressUrl|escape:'html':'UTF-8'}"></div>
    {include file='./modal.tpl'}
</div>