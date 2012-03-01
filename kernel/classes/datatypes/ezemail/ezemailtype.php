<?php
/**
 * File containing the eZEmailType class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZEmailType ezemailtype.php
  \ingroup eZDatatype
  \brief Stores an email address

*/

class eZEmailType extends eZDataType
{
    const DATA_TYPE_STRING = "ezemail";

    function eZEmailType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Email", 'Datatype name' ),
                           array( 'serialize_supported' => true,
                                  'object_serialize_map' => array( 'data_text' => 'email' ) ) );
    }

    /*!
     Sets the default value.
    */
    function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
    }

    /**
     * @since 4.7 this function is called validateEmailInput instead of 
     *            validateEmainHTTPInput. it's intended to be used by all kind 
     *            of inputs
     * @private
     * @param string $email
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return int
     */
    private function validateEMailInput( $email, $contentObjectAttribute )
    {
        if ( !eZMail::validate( $email ) )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                 'The email address is not valid.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /*
     * @since 4.7
     * Validates object input. It should be used by all kind of inputs 
     * ( http input, from string ... )
     *
     * @param eZContentObjectAttribute $contentObjectAttribute 
     * @param string $data
     * @return int
     */
    function validateObjectAttributeInput( $contentObjectAttribute, $data )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();
        $trimmedEmail = trim( $data );
        if ( $trimmedEmail == "" )
        {
            // we require user to enter an address only if the attribute is not an informationcollector
            if ( !$classAttribute->attribute( 'is_information_collector' ) and
                 $contentObjectAttribute->validateIsRequired() )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The email address is empty.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            else
                return eZInputValidator::STATE_ACCEPTED;
        }
        else
        {
            // if the entered address is not empty then we should validate it in any case
            return $this->validateEMailInput( $trimmedEmail, $contentObjectAttribute );
        }
    }

    /**
     * Validates the input and returns an eZInputValidator constant
     * depending on the validation
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @return int
     */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {        
        if ( $http->hasPostVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $email = $http->postVariable( $base . '_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            return $this->validateObjectAttributeInput( $contentObjectAttribute, $email );          
        }
        else if ( !$classAttribute->attribute( 'is_information_collector' ) and $contentObjectAttribute->validateIsRequired() )
        {
            $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'Missing email input.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Fetches the http post var string input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }

    function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $email = $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            $trimmedEmail = trim( $email );

            if ( $trimmedEmail == "" )
            {
                // if entered email is empty and required then return state invalid
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes',
                                                                         'The email address is empty.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                    return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                // if entered email is not empty then we should validate it in any case
                return $this->validateEMailHTTPInput( $trimmedEmail, $contentObjectAttribute );
            }
        }
        else
            return eZInputValidator::STATE_INVALID;
    }

    /*!
     Fetches the http post variables for collected information
    */
    function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $collectionAttribute->setAttribute( 'data_text', $dataText );
            return true;
        }
        return false;
    }

    /*!
     Store the content.
    */
    function storeObjectAttribute( $attribute )
    {
    }

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function isIndexable()
    {
        return true;
    }

    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /*!
     \return string representation of an contentobjectattribute data for simplified export

    */
    function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

   /**
     * Sets value from a string
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param string $string
     * @return int
     */
    function fromString( $contentObjectAttribute, $string )
    {
        if ( $this->validateObjectAttributeInput( $contentObjectAttribute, $string ) === eZInputValidator::STATE_ACCEPTED )
        {
            $contentObjectAttribute->setAttribute( 'data_text', $string );
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
    }

    /*!
     Returns the text.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( "data_text" ) ) != '';
    }

    function isInformationCollector()
    {
        return true;
    }

    function sortKey( $contentObjectAttribute )
    {
        return strtolower( $contentObjectAttribute->attribute( 'data_text' ) );
    }

    function sortKeyType()
    {
        return 'string';
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZEmailType::DATA_TYPE_STRING, "eZEmailType" );

?>
