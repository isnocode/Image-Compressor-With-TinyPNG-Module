{*
* @author    ELEGANTAL <info@elegantal.com>
* @copyright (c) 2022, ELEGANTAL <www.elegantal.com>
* @license   Proprietary License - It is forbidden to resell or redistribute copies of the module or modified copies of the module.
*}
{foreach from=$products item=product}
    <tr id="elegantal_optional_product_{$product.id_product|intval}" data-id="{$product.id_product|intval}">
        <td style="cursor: pointer;">
            <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" class="img-thumbnail" style="width: 50px; margin-right: 5px;"> 
            {$product.name|escape:'html':'UTF-8'}
        </td>
        <td class="text-right">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="product_ids[]" value="{$product.id_product|intval}" {if in_array($product.id_product|intval, $includeProductIds)}checked="checked"{/if} style="cursor: pointer;">
                </label>
            </div>
        </td>
    </tr>
{/foreach}
