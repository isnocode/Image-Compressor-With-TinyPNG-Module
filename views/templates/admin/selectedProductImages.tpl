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
            <div class="row">
                <div class="col-xs-12 col-md-offset-2 col-md-8">
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-folder-open-o"></i> {l s='Compress images of selected products' mod='elegantaltinypngimagecompress'}
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <form action="{$adminUrl|escape:'html':'UTF-8'}&event=selectedProductImages" method="post">
                                        <div class="input-group">
                                            <input type="text" name="elegantal_products_search" id="elegantal_products_search" class="form-control" placeholder="{l s='Find product by ID, reference or name' mod='elegantaltinypngimagecompress'}" aria-describedby="elegantal_products_search_icon">
                                            <span class="input-group-addon" id="elegantal_products_search_icon" style="cursor: pointer;">
                                                <i class="icon-search icon-rotate-90"></i>
                                            </span>
                                        </div>
                                        <div class="elegantal_products_list" style="max-height:300px; overflow: auto;">
                                            <div id="elegantal_chosen_products_list">
                                                <table class="table table-hover">
                                                    <thead></thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                            <div id="elegantal_loaded_products_list">
                                                <table class="table table-hover">
                                                    <thead></thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <br>
                                            <button class="btn btn-success" type="submit" style="text-transform: uppercase">
                                                <i class="icon-paw"></i> {l s='Compress images' mod='elegantaltinypngimagecompress'}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <a href="{$adminUrl|escape:'html':'UTF-8'}" class="btn btn-default">
                <i class="process-icon-back"></i> {l s='Back' mod='elegantaltinypngimagecompress'}
            </a>
        </div>
    </div>
</div>
<div class="elegantaltinypngimagecompressJsDef elegantal_hidden" data-adminurl="{$adminUrl|escape:'html':'UTF-8'}"></div>