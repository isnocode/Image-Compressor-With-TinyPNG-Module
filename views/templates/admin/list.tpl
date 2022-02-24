{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}
<div class="elegantalBootstrapWrapper">
    <div class="elegantal_ajax_loader"><img src="{$moduleUrl|escape:'html':'UTF-8'}views/img/loading.gif" alt="Wait..."></div>
    <div class="panel">
        <div class="panel-heading">
            <div class="pull-left">
                <i class="icon-picture-o"></i> {l s='Image Compressor With' mod='elegantaltinypngimagecompress'} <a href="https://tinypng.com" target="_blank"> TinyPNG</a>
            </div>
            <div class="pull-right">
                <a href="#" class="elegantal_readme_btn">{l s='Readme' mod='elegantaltinypngimagecompress'}</a>
            </div>
        </div>
        <div class="panel-body">
            {if $cron_last_error}
                <div class="module_error alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    {l s='Last CRON execution ended with an error: ' mod='elegantaltinypngimagecompress'} {$cron_last_error|escape:'html':'UTF-8'}
                </div>
                <br>
            {/if}
            <div class="row elegantal_image_compress_stats_row">
                <div class="col-xs-12 col-md-3 elegantal_image_compress_stats_border">
                    <div class="col-xs-6 elegantal_image_compress_stats_text">
                        {l s='Total images compressed' mod='elegantaltinypngimagecompress'}
                    </div>
                    <div class="col-xs-6 elegantal_image_compress_stats_number text-danger">
                        {$stats_total_images_compressed|intval}
                    </div>
                    <div class="col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3 elegantal_image_compress_stats_border">
                    <div class="col-xs-7 elegantal_image_compress_stats_text">
                        {l s='Total image size reduced' mod='elegantaltinypngimagecompress'}
                    </div>
                    <div class="col-xs-5 elegantal_image_compress_stats_number text-info">
                        {$stats_total_size_reduced|regex_replace:"/[^0-9.]/":""|escape:'html':'UTF-8'}<small>{$stats_total_size_reduced|regex_replace:"/[^a-zA-Z]/":""|escape:'html':'UTF-8'}</small>
                    </div>
                    <div class="col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3 elegantal_image_compress_stats_border">
                    <div class="col-xs-7 elegantal_image_compress_stats_text">
                        {l s='Monthly compression usage' mod='elegantaltinypngimagecompress'}
                    </div>
                    <div class="col-xs-5 elegantal_image_compress_stats_number text-primary">
                        {$stats_monthly_compression_usage|intval}
                    </div>
                    <div class="col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-3">
                    <div class="col-xs-7 elegantal_image_compress_stats_text">
                        {l s='Free compressions available' mod='elegantaltinypngimagecompress'}
                    </div>
                    <div class="col-xs-5 elegantal_image_compress_stats_number text-success">
                        {$stats_free_compressions_available|intval}
                    </div>
                    <div class="col-xs-12">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row elegantal_buttons_row">
                <div class="col-xs-12">
                    <a href="{$adminUrl|escape:'html':'UTF-8'}&event=editSettings" class="btn btn-default btn-lg">
                        <i class="icon-cogs"></i> {l s='Edit settings' mod='elegantaltinypngimagecompress'}
                    </a>
                    <a href="{$adminUrl|escape:'html':'UTF-8'}&event=viewCron" class="btn btn-default btn-lg">
                        <i class="icon-time"></i> {l s='Setup CRON Job' mod='elegantaltinypngimagecompress'}
                    </a>
                    <a href="{$adminUrl|escape:'html':'UTF-8'}&event=imagesLog" class="btn btn-default btn-lg">
                        <i class="icon-list"></i> {l s='Images log' mod='elegantaltinypngimagecompress'}
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-paw"></i> {l s='Compress images' mod='elegantaltinypngimagecompress'} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu elegantal_images_group">
                            {foreach from=$imageGroups item=imageGroup}
                                {if $imageGroup == 'custom'}
                                    <li>
                                        <a href="{$adminUrl|escape:'html':'UTF-8'}&event=selectedProductImages">
                                            <span>{l s='Selected Product Images' mod='elegantaltinypngimagecompress'}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{$adminUrl|escape:'html':'UTF-8'}&event=customDir">
                                            <span>{l s='Selected Directory Images' mod='elegantaltinypngimagecompress'}</span>
                                        </a>
                                    </li>
                                {else}
                                    <li>
                                        <a href="{$adminUrl|escape:'html':'UTF-8'}&event=analyze&image_group={$imageGroup|escape:'html':'UTF-8'}">
                                            <span>{$imageGroup|replace:'_':' '|escape:'html':'UTF-8'} {l s='images' mod='elegantaltinypngimagecompress'}</span>
                                            {if $imageGroup == 'other'}
                                                <br>
                                                <small>{l s='Images not mentioned above. It includes cms images, logo, icons, etc.' mod='elegantaltinypngimagecompress'}</small>
                                            {/if}
                                        </a>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                    {if $documentationUrls}
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-lg dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-file-text-o"></i> {l s='Documentation' mod='elegantaltinypngimagecompress'} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu elegantal_images_group" style="left:0; right: auto;">
                                {foreach from=$documentationUrls key=docLang item=documentationUrl}
                                    <li>
                                        <a href="{$documentationUrl|escape:'html':'UTF-8'}" target="_blank">
                                            {if $docLang == 'en'}
                                                {l s='English' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'fr'}
                                                {l s='French' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'de'}
                                                {l s='German' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'it'}
                                                {l s='Italian' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'pt'}
                                                {l s='Portuguese' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'es'}
                                                {l s='Spanish' mod='elegantaltinypngimagecompress'}
                                            {elseif $docLang == 'ru'}
                                                {l s='Russian' mod='elegantaltinypngimagecompress'}
                                            {else}
                                                {$docLang|escape:'html':'UTF-8'}
                                            {/if}
                                        </a>
                                    </li>
                                {/foreach}
                            </ul>
                        </div>
                    {/if}
                    <a href="{$rateModuleUrl|escape:'html':'UTF-8'}" target="_blank" class="btn btn-default btn-lg">
                        <i class="icon-star"></i> {l s='Rate module' mod='elegantaltinypngimagecompress'}
                    </a>
                    <a href="{$contactDeveloperUrl|escape:'html':'UTF-8'}" target="_blank" class="btn btn-default btn-lg">
                        <i class="icon-envelope-o"></i> {l s='Contact developer' mod='elegantaltinypngimagecompress'}
                    </a>
                </div>
            </div>
            {if $models}
                <div>
                    <table class="table table-hover elegantal_history_table">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>
                                    {l s='Date' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=created_at&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=created_at&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Image' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=image_group&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=image_group&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Total Images' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_count&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_count&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Compressed' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=compressed&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=compressed&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Not Started' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=not_compressed&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=not_compressed&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Failed' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=failed&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=failed&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Size Before' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_size_before&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_size_before&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Size After' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_size_after&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=images_size_after&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Size Reduced' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=disk_space_saved&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=disk_space_saved&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>
                                    {l s='Status' mod='elegantaltinypngimagecompress'} 
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=status&orderType=desc"><i class="icon-caret-down"></i></a>
                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy=status&orderType=asc"><i class="icon-caret-up"></i></a>
                                </th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        {if $models|@count > 1}
                            <tfoot>
                                <tr>
                                    <td colspan="3">&nbsp;</td>
                                    <td>{$total_images|escape:'html':'UTF-8'}</td>
                                    <td>{$total_compressed|escape:'html':'UTF-8'}</td>
                                    <td>{$total_not_compressed|escape:'html':'UTF-8'}</td>
                                    <td>{$total_failed|escape:'html':'UTF-8'}</td>
                                    <td>{$total_size_before|escape:'html':'UTF-8'}</td>
                                    <td>{$total_size_after|escape:'html':'UTF-8'}</td>
                                    <td>{$total_disk_saved|escape:'html':'UTF-8'}</td>
                                    <td>&nbsp;</td>
                                </tr>
                            </tfoot>
                        {/if}
                        <tbody>
                            {foreach from=$models item=model}
                                <tr>
                                    <td class="elegantal_select_checkbox">
                                        <input type="checkbox" data-id="{$model.id_elegantaltinypngimagecompress|intval}" title="ID: {$model.id_elegantaltinypngimagecompress|intval}">
                                    </td>
                                    <td title="Started:   {$model.created_at|escape:'html':'UTF-8'|date_format:'%e %b, %Y %H:%M:%S'} &#xA;Finished: {$model.updated_at|escape:'html':'UTF-8'|date_format:'%e %b, %Y %H:%M:%S'}">
                                        {$model.created_at|escape:'html':'UTF-8'|date_format:'%e %b %Y'}
                                    </td>
                                    <td {if $model.image_group == 'custom'}title="{$model.custom_dir|escape:'html':'UTF-8'}"{/if}>
                                        {$model.image_group|escape:'html':'UTF-8'}
                                    </td>
                                    <td>
                                        {$model.images_count|intval}
                                    </td>
                                    <td>
                                        {$model.compressed|intval}
                                    </td>
                                    <td>
                                        {$model.not_compressed|intval}
                                    </td>
                                    <td>
                                        {$model.failed|intval}
                                    </td>
                                    <td>
                                        {$model.images_size_before|escape:'html':'UTF-8'}
                                    </td>
                                    <td>
                                        {$model.images_size_after|escape:'html':'UTF-8'}
                                    </td>
                                    <td>
                                        {$model.disk_space_saved|escape:'html':'UTF-8'}
                                    </td>
                                    <td>                                       
                                        {if $model.status == $status_completed}
                                            <span class="label label-success">{l s='Completed' mod='elegantaltinypngimagecompress'}</span>
                                        {else}
                                            <div class="btn-group btn-group-xs elegantal_resume_btn_group" role="group">
                                                {if $model.status == $status_analyzing}
                                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&event=analyze&id_elegantaltinypngimagecompress={$model.id_elegantaltinypngimagecompress|intval}" class="btn btn-warning btn-sm">
                                                        <i class="icon-play"></i> {l s='Resume' mod='elegantaltinypngimagecompress'}
                                                    </a>
                                                {elseif $model.status == $status_compressing}
                                                    <a href="{$adminUrl|escape:'html':'UTF-8'}&event=compress&id_elegantaltinypngimagecompress={$model.id_elegantaltinypngimagecompress|intval}" class="btn btn-warning btn-sm">
                                                        <i class="icon-play"></i> {l s='Resume' mod='elegantaltinypngimagecompress'}
                                                    </a>
                                                {/if}
                                            </div>
                                        {/if}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-xs" role="group">
                                            <a href="{$adminUrl|escape:'html':'UTF-8'}&event=delete&id_elegantaltinypngimagecompress={$model.id_elegantaltinypngimagecompress|intval}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page={$currentPage|intval}" class="btn btn-danger btn-sm" onclick="return confirm('{l s='Are you sure you want to delete this?' mod='elegantaltinypngimagecompress'}')">
                                                <i class="icon-trash" style="font-size: 13px;"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>                    
                            {/foreach}
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="btn-group elegantal_bulk_actions dropup">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    {l s='Bulk actions' mod='elegantaltinypngimagecompress'} <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" class="elegantal_select_all"><i class="icon-check-sign"></i> {l s='Select all' mod='elegantaltinypngimagecompress'}</a></li>
                                    <li><a href="#" class="elegantal_unselect_all"><i class="icon-check-empty"></i> {l s='Unselect all' mod='elegantaltinypngimagecompress'}</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" class="elegantal_delete_selected" data-url="{$adminUrl|escape:'html':'UTF-8'}&event=deleteBulk"><i class="icon-trash"></i> {l s='Delete selected' mod='elegantaltinypngimagecompress'}</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    {*START PAGINATION*}
                    {if $pages > 1}
                        {assign var="pMax" value=2 * $halfVisibleLinks + 1} {*Number of visible pager links*}
                        {assign var="pStart" value=$currentPage - $halfVisibleLinks} {*Starter link*}
                        {assign var="moveStart" value=$currentPage - $pages + $halfVisibleLinks} {*Numbers that pStart can be moved left to fill right side space*}
                        {if $moveStart > 0}
                            {assign var="pStart" value=$pStart - $moveStart}
                        {/if}                                    
                        {if $pStart < 1}
                            {assign var="pStart" value=1}
                        {/if}
                        {assign var="pNext" value=$currentPage + 1} {*Next page*}
                        {if $pNext > $pages}
                            {assign var="pNext" value=$pages}
                        {/if}
                        {assign var="pPrev" value=$currentPage - 1} {*Previous page*}
                        {if $pPrev < 1}
                            {assign var="pPrev" value=1}
                        {/if}
                        <div class="text-center">
                            <nav>
                                <ul class="pagination pagination-sm" style="margin-top: 12px">
                                    {if $pPrev < $currentPage}
                                        <li>
                                            <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page=1" aria-label="Previous">
                                                <span aria-hidden="true">&lt;&lt; {l s='First' mod='elegantaltinypngimagecompress'}</span>
                                            </a>
                                        </li>
                                        {if $pPrev > 1}
                                            <li>
                                                <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page={$pPrev|intval}" aria-label="Previous">
                                                    <span aria-hidden="true">&lt; {l s='Prev' mod='elegantaltinypngimagecompress'}</span>
                                                </a>
                                            </li>
                                        {/if}
                                    {/if}
                                    {for $i=$pStart to $pages max=$pMax}
                                        <li{if $i == $currentPage} class="active" onclick="return false;"{/if}>
                                            <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page={$i|intval}">{$i|intval}</a>
                                        </li>
                                    {/for}
                                    {if $pNext > $currentPage && $pNext <= $pages}
                                        {if $pNext < $pages}
                                            <li>
                                                <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page={$pNext|intval}" aria-label="Next">
                                                    <span aria-hidden="true">{l s='Next' mod='elegantaltinypngimagecompress'} &gt;</span>
                                                </a>
                                            </li>
                                        {/if}
                                        <li>
                                            <a href="{$adminUrl|escape:'html':'UTF-8'}&orderBy={$orderBy|escape:'html':'UTF-8'}&orderType={$orderType|escape:'html':'UTF-8'}&page={$pages|intval}" aria-label="Next">
                                                <span aria-hidden="true">{l s='Last' mod='elegantaltinypngimagecompress'} &gt;&gt;</span>
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </nav>
                        </div>
                    {/if}
                    {*END PAGINATION*}
                </div>
            {else}
                <div style="height: 60px"></div>
            {/if}
        </div>
    </div>
    {include file='./modal.tpl'}
</div>