{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}
<div class="elegantalBootstrapWrapper">
    <div class="elegantal_readme_modal modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{l s='Checklist before starting compression' mod='elegantaltinypngimagecompress'}</h4>
                </div>
                <div class="modal-body">
                    <ul>
                        <li class="elegantal_hidden elegantal_show_on_compress">
                            <span class="alert-danger">
                                {l s='One or more images failed. Please check with the requirements below and try again:' mod='elegantaltinypngimagecompress'}
                            </span>
                        </li>
                        <li>
                            {l s='Get your free TinyPNG API key from' mod='elegantaltinypngimagecompress'} <a href="https://tinypng.com/developers" target="_blank">https://tinypng.com/developers</a>
                        </li>
                        <li>
                            {l s='Make sure you have entered your valid TinyPNG API key' mod='elegantaltinypngimagecompress'} 
                            <a href="{$adminUrl|escape:'html':'UTF-8'}&event=editSettings">{l s='in settings' mod='elegantaltinypngimagecompress'}</a>
                        </li>
                        <li>
                            {l s='TinyPNG allows 500 free compressions monthly. Make sure you have sufficient compression allowance.' mod='elegantaltinypngimagecompress'}
                        </li>
                        <li>
                            {l s='Make sure your img folder has proper write permissions, so that the module can delete old images and save new compressed images.' mod='elegantaltinypngimagecompress'}
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{l s='Okay' mod='elegantaltinypngimagecompress'}</button>
                </div>
            </div>
        </div>
    </div>
</div>
