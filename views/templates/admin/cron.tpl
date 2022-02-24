{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}
<div class="elegantalBootstrapWrapper">
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-picture-o"></i> {l s='Image Compressor With' mod='elegantaltinypngimagecompress'} 
            <a href="https://tinypng.com" target="_blank">TinyPNG</a>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <i class="icon-time"></i> {l s='Setup CRON job on your server' mod='elegantaltinypngimagecompress'}
                        </div>
                        <div class="panel-body">
                            <p>{l s='You can use CRON to automatically compress images on scheduled time periods.' mod='elegantaltinypngimagecompress'}</p>
                            <p>{l s='You will need to create a crontab for the following URL on your hosting server' mod='elegantaltinypngimagecompress'}: </p>
                            <div class="alert alert-info alert-link-icon">
                                {$cronUrl|escape:'html':'UTF-8'}
                            </div>
                            {l s='Your CRON command for this rule is' mod='elegantaltinypngimagecompress'}: <br>
                            <div class="well">curl "{$cronUrl|escape:'html':'UTF-8'}"</div>
                            {l s='The following is an example crontab which runs every 15 minutes' mod='elegantaltinypngimagecompress'}: <br>
                            <div class="well">*/15 * * * * curl "{$cronUrl|escape:'html':'UTF-8'}"</div>
                            <p>
                                {l s='Learn more about CRON' mod='elegantaltinypngimagecompress'}: <br>
                                <a href="https://en.wikipedia.org/wiki/Cron" target="_blank">https://en.wikipedia.org/wiki/Cron</a>
                            </p>
                            {if $cron_cpanel_doc}
                                <p>
                                    {l s='Learn how to setup CRON Job in cPanel' mod='elegantaltinypngimagecompress'}: <br>
                                    <a href="{$cron_cpanel_doc|escape:'html':'UTF-8'}" target="_blank">
                                        {l s='User guide on how to setup CRON Job in cPanel' mod='elegantaltinypngimagecompress'}
                                    </a>
                                </p>
                            {/if}
                            <p>
                                {l s='If you do not know how to setup CRON Job on your server, there is another easy way to do this.' mod='elegantaltinypngimagecompress'} 
                                <br>
                                {l s='You do not even need to open your cPanel or server. Just use any free or paid online CRON services, for example:' mod='elegantaltinypngimagecompress'} 
                                <a href="https://cron-job.org" target="_blank">https://cron-job.org</a>
                                <br>
                                {l s='You will select time and put command above (curl "http://....") and this online tool will take care of automatic execution of module.' mod='elegantaltinypngimagecompress'} 
                            </p>
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