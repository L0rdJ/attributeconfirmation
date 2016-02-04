{def $data_text = cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].data_text, $attribute.content )}
<input type="text" name="ContentObjectAttribute_ezstring_data_text_{$attribute.id}" value="{$data_text|wash( xhtml )}" />
{undef $data_text}