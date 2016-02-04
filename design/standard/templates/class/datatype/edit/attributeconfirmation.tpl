{def
    $class_attributes    = fetch( 'content', 'class_attribute_list', hash( 'class_id', $class_attribute.contentclass_id ) )
    $supported_datatypes = array( 'ezstring' )
}

<div class="block">
    <label>{'Attribute to confirm'|i18n( 'extension/attributeconfirmation' )}:</label>
    <select name="ContentClass_data_text1_{$class_attribute.id}">
        <option value="">{'- None -'|i18n( 'extension/attributeconfirmation' )}</option>
        {foreach $class_attributes as $attr}
            {if $supported_datatypes|contains( $attr.data_type_string )}
            <option value="{$attr.identifier}"{if $class_attribute.data_text1|eq( $attr.identifier )} selected="selected"{/if}>{$attr.identifier|wash}</option>
            {/if}
        {/foreach}
    </select>
</div>