{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}

{extends file="helpers/form/form.tpl"}

{block name="field"}
    {if $input.type == 'el_switch'}
        <div class="radio clearfix" style="margin-bottom: 20px;">
            {foreach $input.values as $value}
                <label>
                    <input type="radio" name="{$input.name|escape:'html':'UTF-8'}"{if $value.value == 1} id="{$input.name|escape:'html':'UTF-8'}_on"{else} id="{$input.name|escape:'html':'UTF-8'}_off"{/if} value="{$value.value|escape:'html':'UTF-8'}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if} style="margin-top: 1px"/>
                    {if $value.value == 1}
                        {l s='Yes' mod='elegantaltinypngimagecompress'}
                    {else}
                        {l s='No' mod='elegantaltinypngimagecompress'}
                    {/if}
                </label>
            {/foreach}
        </div>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}