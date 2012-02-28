<?php
/**
 * File containing the eZAuthorType class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package kernel
 */

/*!
  \class eZAuthorType ezauthortype.php
  \ingroup eZDatatype
  \brief eZAuthorType handles multiple authors

*/

class eZAuthorType extends eZDataType
{
    const DATA_TYPE_STRING = "ezauthor";

    function eZAuthorType()
    {
        $this->eZDataType( self::DATA_TYPE_STRING, ezpI18n::tr( 'kernel/classes/datatypes', "Authors", 'Datatype name' ),
                           array( 'serialize_supported' => true ) );
    }
    
    /**
     * Validates input for the object
     *
     * @param eZContentObjectAttribute $contentObjectAttribute
     * @param array $data should be an associative array with 2 keys, 
     *                    name_list and email_list
     */
    function validateObjectAttributeInput( $contentObjectAttribute, $data )
    {
        $nameList = $data['name_list'];
        $emailList = $data['email_list'];

        if ( $contentObjectAttribute->validateIsRequired() )
        {
            if ( trim( $nameList[0] ) == "" )
            {
                $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'At least one author is required.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            if ( trim( $nameList[0] ) != "" )
            {
                for ( $i=0;$i<count( $nameList );$i++ )
                {
                    $name =  $nameList[$i];
                    $email =  $emailList[$i];
                    if ( trim( $name )== "" )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'The author name must be provided.' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                    $isValidate =  eZMail::validate( $email );
                    if ( !$isValidate )
                    {
                        $contentObjectAttribute->setValidationError( ezpI18n::tr( 'kernel/classes/datatypes', 'The email address is not valid.' ) );
                        return eZInputValidator::STATE_INVALID;
                    }
                }
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /*!
     Validates the input and returns true if the input was
     valid for this datatype.
    */
    function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $classAttribute = $contentObjectAttribute->contentClassAttribute();

        $nameList = $http->postVariable( $base . "_data_author_name_" . $contentObjectAttribute->attribute( "id" ) );
        $emailList = $http->postVariable( $base . "_data_author_email_" . $contentObjectAttribute->attribute( "id" ) );

        $data = array( 
            'name_list' => $nameList,
            'email_list' => $emailList
        );
            
        return $this->validateObjectAttributeInput( $contentObjectAttribute, $data );       
    }

    /*!
     Store content
    */
    function storeObjectAttribute( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        $contentObjectAttribute->setAttribute( "data_text", $author->xmlString() );
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

    /*!
     Returns the content.
    */
    function objectAttributeContent( $contentObjectAttribute )
    {
        $author = new eZAuthor( );

        if ( trim( $contentObjectAttribute->attribute( "data_text" ) ) != "" )
        {
            $author->decodeXML( $contentObjectAttribute->attribute( "data_text" ) );
            $temp = $contentObjectAttribute->attribute( "data_text");
        }
        else
        {
            $user = eZUser::currentUser();
            $userobject = $user->attribute( 'contentobject' );
            if ( $userobject )
            {
                $author->addAuthor( $userobject->attribute( 'id' ), $userobject->attribute( 'name' ), $user->attribute( 'email' ) );
            }
         }

        if ( count( $author->attribute( 'author_list' ) ) == 0 )
        {
//             $author->addAuthor( "Default", "" );
        }

        return $author;
    }


    /*!
     Returns the meta data used for storing search indeces.
    */
    function metaData( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content();
        if ( !$author )
            return false;

        return $author->metaData();
    }

    function toString( $contentObjectAttribute )
    {
        $authorList = array();
        $content = $contentObjectAttribute->attribute( 'content' );
        foreach ( $content->attribute( 'author_list') as $author )
        {
            $authorList[] = eZStringUtils::implodeStr( array( $author['name'], $author['email'],$author['id'] ), '|' );
        }
        return eZStringUtils::implodeStr( $authorList, "&" );
    }
    
    /**
     * @todo Can we avoid double loop here?
     */ 
    function fromString( $contentObjectAttribute, $string )
    {
        $authorList = eZStringUtils::explodeStr( $string, '&' );
        $data = array(
            'name_list' => array(),
            'email_list' => array(),
        );
        // first loop for building validate data array
        foreach ( $authorList as $authorStr )
        {
            $authorData = eZStringUtils::explodeStr( $authorStr, '|' );
            $data['name_list'][] = $authorData[0];
            $data['email_list'][] = $authorData[1];
        }       
        
        $validation = $this->validateObjectAttributeInput( $contentObjectAttribute, $data );
        if( $validation === eZInputValidator::STATE_ACCEPTED )
        {
            $author = new eZAuthor();
            foreach ( $authorList as $authorStr )
            {
                $authorData = eZStringUtils::explodeStr( $authorStr, '|' );
                $author->addAuthor( $authorData[2], $authorData[0], $authorData[1] );
            }
            $contentObjectAttribute->setContent( $author );
        }
        return $validation;
    }

    /*!
     Fetches the http post var integer input and stores it in the data instance.
    */
    function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $authorIDArray = $http->postVariable( $base . "_data_author_id_" . $contentObjectAttribute->attribute( "id" ) );
            $authorNameArray = $http->postVariable( $base . "_data_author_name_" . $contentObjectAttribute->attribute( "id" ) );
            $authorEmailArray = $http->postVariable( $base . "_data_author_email_" . $contentObjectAttribute->attribute( "id" ) );

            $author = new eZAuthor( );

            $i = 0;
            foreach ( $authorIDArray as $id )
            {
                $author->addAuthor( $authorIDArray[$i], $authorNameArray[$i], $authorEmailArray[$i] );
                $i++;
            }
            $contentObjectAttribute->setContent( $author );
        }
        return true;
    }

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch ( $action )
        {
            case "new_author" :
            {
                $author = $contentObjectAttribute->content( );

                $author->addAuthor( -1, "", "" );
                $contentObjectAttribute->setContent( $author );
            }break;
            case "remove_selected" :
            {
                $author = $contentObjectAttribute->content( );
                $postvarname = $parameters['base_name'] . "_data_author_remove_" . $contentObjectAttribute->attribute( "id" );
                if ( !$http->hasPostVariable( $postvarname ) )
                    break;
                $array_remove = $http->postVariable( $postvarname );

                $author->removeAuthors( $array_remove );
                $contentObjectAttribute->setContent( $author );
            }break;
            default :
            {
                eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZAuthorType" );
            }break;
        }
    }

    function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $author = $contentObjectAttribute->content( );
        $authorList = $author->attribute( 'author_list' );
        return count( $authorList ) > 0;
    }

    /*!
     Returns the string value.
    */
    function title( $contentObjectAttribute, $name = null )
    {
        $author = $contentObjectAttribute->content( );
        $name = $author->attribute( 'name' );
        if ( trim( $name ) == '' )
        {
            $authorList = $author->attribute( 'author_list' );
            if ( is_array( $authorList ) and isset( $authorList[0]['name'] ) )
            {
                $name = $authorList[0]['name']; // Get the first name of Auhtors
                $author->setName( $name );
            }
        }
        return $name;
    }

    function isIndexable()
    {
        return true;
    }

    function serializeContentObjectAttribute( $package, $objectAttribute )
    {
        $node = $this->createContentObjectAttributeDOMNode( $objectAttribute );

        $dom = new DOMDocument( '1.0', 'utf-8' );
        $success = $dom->loadXML( $objectAttribute->attribute( 'data_text' ) );

        $nodeDOM = $node->ownerDocument;
        $importedElement = $nodeDOM->importNode( $dom->documentElement, true );
        $node->appendChild( $importedElement );

        return $node;
    }

    function unserializeContentObjectAttribute( $package, $objectAttribute, $attributeNode )
    {
        $rootNode = $attributeNode->getElementsByTagName( 'ezauthor' )->item( 0 );
        $xmlString = $rootNode->ownerDocument->saveXML( $rootNode );
        $objectAttribute->setAttribute( 'data_text', $xmlString );
    }

    function supportsBatchInitializeObjectAttribute()
    {
        return true;
    }
}

eZDataType::register( eZAuthorType::DATA_TYPE_STRING, "eZAuthorType" );

?>
