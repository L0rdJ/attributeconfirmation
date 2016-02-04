<?php

/**
 * @package attributeConfirmation
 * @class   attributeConfirmationType
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    03 Feb 2016
 * */
class attributeConfirmationType extends eZDataType {

    const DATA_TYPE_STRING = 'attributeconfirmation';
    const FIELD_ATTRIBUTE  = 'data_text1';

    public function __construct() {
        $this->eZDataType(
            self::DATA_TYPE_STRING, ezpI18n::tr( 'extension/attributeconfirmation', 'Attribute Confirmation' ), array( 'serialize_supported' => false )
        );
    }

    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute ) {
        $attributeName = $base . '_' . self::FIELD_ATTRIBUTE . '_' . $classAttribute->attribute( 'id' );
        if( $http->hasPostVariable( $attributeName ) ) {
            $classAttribute->setAttribute( self::FIELD_ATTRIBUTE, $http->postVariable( $attributeName ) );
        }
        return true;
    }

    public function fetchObjectAttributeHTTPInput( $http, $base, $attribute ) {
        $field = $base . '_attributeconfirmation_' . $attribute->attribute( 'id' );
        $attribute->setAttribute( 'data_text', $http->postVariable( $field, null ) );
        return $http->hasPostVariable( $field );
    }

    public function validateObjectAttributeHTTPInput( $http, $base, $attribute ) {
        $classAttribute = $attribute->contentClassAttribute();
        if( $classAttribute->attribute( 'is_information_collector' ) ) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        return self::validateConfirmation( $http, $base, $attribute );
    }

    public function isInformationCollector() {
        return true;
    }

    public function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute ) {
        $field = $base . '_attributeconfirmation_' . $contentObjectAttribute->attribute( 'id' );
        $collectionAttribute->setAttribute( 'data_text', $http->postVariable( $field, null ) );
        return $http->hasPostVariable( $field );
    }

    public function validateCollectionAttributeHTTPInput( $http, $base, $attribute ) {
        return self::validateConfirmation( $http, $base, $attribute, true );
    }

    public function diff( $old, $new, $options = false ) {
        return null;
    }

    protected static function validateConfirmation( eZHTTPTool $http, $base, eZContentObjectAttribute $attribute, $isCollection = false ) {
        $field         = $attributeName = $base . '_attributeconfirmation_' . $attribute->attribute( 'id' );
        $value         = $http->postVariable( $field, null );
        if( empty( $value ) ) {
            if( (bool) $attribute->attribute( 'is_required' ) ) {
                $attribute->setValidationError(
                    ezpI18n::tr( 'extension/attributeconfirmation', 'Input required.' )
                );
                return eZInputValidator::STATE_INVALID;
            } else {
                return eZInputValidator::STATE_ACCEPTED;
            }
        }

        $attributeToConfirm      = $attribute->attribute( 'contentclass_attribute' )->attribute( self::FIELD_ATTRIBUTE );
        $attributeToConfirmValue = null;
        $version                 = $attribute->attribute( 'object_version' );
        $dataMap                 = $version->attribute( 'data_map' );
        if( $isCollection ) {
            if( isset( $dataMap[ $attributeToConfirm ] ) ) {
                $attributeID = $dataMap[ $attributeToConfirm ]->attribute( 'id' );
                $fields      = array_keys( $_POST );
                foreach( $fields as $field ) {
                    if( preg_match( '/^' .  $base . '.*' . $attributeID . '$/i', $field ) === 1 ) {
                        $attributeToConfirmValue = $http->postVariable( $field, null );
                        break;
                    }
                }
            }
        } else {
            if( isset( $dataMap[ $attributeToConfirm ] ) ) {
                $attributeToConfirmValue = $dataMap[ $attributeToConfirm ]->attribute( 'content' );
            }
        }

        if( empty( $attributeToConfirmValue ) ) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        if( $attributeToConfirmValue != $value ) {
            $attribute->setValidationError(
                ezpI18n::tr( 'extension/attributeconfirmation', 'Input does not match.' )
            );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

}

eZDataType::register( attributeConfirmationType::DATA_TYPE_STRING, 'attributeConfirmationType' );

