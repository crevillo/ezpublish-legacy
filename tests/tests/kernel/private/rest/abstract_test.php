<?php
/**
 * File containing ezpRestTestCase class
 *
 * @copyright Copyright (C) 1999-2013 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package tests
 */

/**
 * Abstract class for REST test cases
 * Makes sure every resources are correctly loaded
 */
abstract class ezpRestTestCase extends ezpTestCase
{
    /**
     * @var eZINI
     */
    protected $restINI;

    /**
     * @var ezpMvcConfiguration
     */
    protected $mvcConfig;

    public function __construct( $name = NULL, array $data = array(), $dataName = '' )
    {
        $this->mvcConfig = new ezpMvcConfiguration();

        // Load rest.ini and forces it to load its data
        $this->restINI = eZINI::instance( 'rest.ini' );
        $this->restINI->load( true );

        $this->loadDummySettings();

        parent::__construct( $name, $data, $dataName );
    }

    /**
     * Method to initialize dummy settings, mostly in rest.ini
     */
    protected function loadDummySettings()
    {
        $this->restINI->setVariable( 'ApiProvider', 'ProviderClass', array( 'test' => 'ezpRestTestApiProvider' ));
    }

    /**
     * Returns a valid test controller from URI
     * @param $uri
     * @return ezpRestMvcController
     */
    protected function getTestControllerFromUri( $uri )
    {
        $r = new ezpRestRequest();
        $r->uri = $uri;
        $r->variables = array( 'ResponseGroups' => array() );
        $r->contentVariables = array();
        $r->protocol = 'http-get';
        return $this->getTestControllerFromRequest( $r );
    }

    /**
     * Returns a valid test controller from a request object
     * @param ezpRestRequest $r
     * @return ezpRestMvcController
     */
    protected function getTestControllerFromRequest( ezpRestRequest $r )
    {
        $this->mvcConfig->runPreRoutingFilters( $r );
        $router = $this->mvcConfig->createRouter( $r );
        $routingInfos = $router->getRoutingInformation();
        $controllerClass = $routingInfos->controllerClass;
        $controller = new $controllerClass( $routingInfos->action, $r );
        $controller->setRouter( $router );

        return $controller;
    }
}
?>
