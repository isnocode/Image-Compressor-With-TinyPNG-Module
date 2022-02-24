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
            <div class="row elegantal_compress_panel">
                <div class="col-xs-12 col-md-offset-2 col-md-8">
                    <div class="panel">
                        <div class="panel-heading" style="margin-bottom:0">
                            <i class="icon-clock-o"></i> {l s='Compressing %1$s images...' sprintf=$model.image_group mod='elegantaltinypngimagecompress'}
                        </div>
                        <div class="panel-body elegantal_compress">
                            <div class="row">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Total number of images' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_count">{$model.images_count|intval}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Number of images compressed' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_compressed">{$model.compressed|intval}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Number of images left to compress' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_not_compressed">{$model.not_compressed|intval}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Number of images failed' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_failed">{$model.failed|intval}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Total size of images before compression' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_size_before">
                                        {$model.images_size_before|escape:'html':'UTF-8'}
                                    </span>
                                </div>
                            </div>
                            <div class="row {if $model.not_compressed == $model.images_count}elegantal_hidden elegantal_show_on_compress{/if}">
                                <div class="col-xs-10 col-sm-9">
                                    {l s='Total size of images after compression' mod='elegantaltinypngimagecompress'}
                                </div>
                                <div class="col-xs-2 col-sm-3 text-right">
                                    <span class="elegantal_compress_num elegantal_images_size_after">
                                        {$model.images_size_after|escape:'html':'UTF-8'}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-center" style="height: auto;">
                            <div class="row elegantal_hide_on_compress">
                                <button type="button" class="btn btn-success btn-lg col-xs-12 col-md-offset-3 col-md-6 elegantal_compress_btn" data-id="{$model.id_elegantaltinypngimagecompress|intval}">
                                    <i class="icon-paw"></i> &nbsp;{l s='Start Compression' mod='elegantaltinypngimagecompress'}
                                </button><br><br><br>
                                <a href="{$adminUrl|escape:'html':'UTF-8'}" class="col-xs-12" style="text-decoration: none">
                                    <i class="icon-angle-left"></i> &nbsp;{l s='Continue Later' mod='elegantaltinypngimagecompress'}
                                </a>
                            </div>
                            <div class="row elegantal_hidden elegantal_show_on_compress elegantal_hide_on_complete">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="progress">
                                            <div class="elegantal_compress_progress_bar progress-bar progress-bar-success" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="min-width: 3em; width: {$progress|escape:'html':'UTF-8'}%;">
                                                {$progressTxt|escape:'html':'UTF-8'}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        {l s='Please wait. This might take several minutes.' mod='elegantaltinypngimagecompress'}
                                    </div>
                                </div>
                                <div class="row">
                                    <br>
                                    <button type="button" class="btn btn-default col-xs-12 col-md-offset-4 col-md-4 elegantal_hide_on_pause elegantal_show_on_resume elegantal_pause_btn">
                                        <i class="icon-pause"></i> &nbsp;{l s='Pause Compression' mod='elegantaltinypngimagecompress'}
                                    </button>
                                    <div class="elegantal_hidden elegantal_show_on_pause elegantal_hide_on_resume">
                                        <a href="{$adminUrl|escape:'html':'UTF-8'}" class="btn btn-default col-xs-12 col-md-offset-3 col-md-3">
                                            <i class="icon-home"></i> &nbsp;{l s='Continue Later' mod='elegantaltinypngimagecompress'}
                                        </a> 
                                        <button type="button" class="btn btn-success col-xs-12 col-md-3 elegantal_resume_btn">
                                            <i class="icon-play"></i> &nbsp;{l s='Resume Compression' mod='elegantaltinypngimagecompress'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row elegantal_hidden elegantal_show_on_complete">
                                {l s='Congratulations! Your images have been compressed. You have saved ' mod='elegantaltinypngimagecompress'} 
                                <span class="elegantal_compress_num elegantal_images_size_saved"></span><br><br>
                                <a href="{$adminUrl|escape:'html':'UTF-8'}" class="btn btn-default col-xs-12 col-md-offset-4 col-md-4">
                                    <i class="icon-home"></i> &nbsp;{l s='Home' mod='elegantaltinypngimagecompress'}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="elegantaltinypngimagecompressJsDef elegantal_hidden" data-adminurl="{$adminUrl|escape:'html':'UTF-8'}" data-modelid="{$model.id_elegantaltinypngimagecompress|intval}" data-total="{$model.images_count|intval}" data-notprocessed="{$model.not_compressed|intval}" data-compressed="{$model.compressed|intval}" data-failed="{$model.failed|intval}"></div>
    {include file='./modal.tpl'}
</div>