<?php
/**
 * @package HybridAuth
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// Check the registry to see if our HybridAuth class has been overridden
if ( !class_exists('HybridAuth') ) { 
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
}

// before executing any tasks, check the integrity of the installation
HybridAuth::getClass( 'HybridAuthHelperDiagnostics', 'helpers.diagnostics' )->checkInstallation();

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_hybridauth' );

// Require the base controller
HybridAuth::load( 'HybridAuthController', 'controller', $options );

// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar( 'view' ) );
if (!HybridAuth::load( 'HybridAuthController'.$controller, "controllers.$controller", $options ))
    $controller = '';

if (empty($controller))
{
    // redirect to default
    $default_controller = new HybridAuthController();
    $redirect = "index.php?option=com_hybridauth&view=" . $default_controller->default_view;
    $redirect = JRoute::_( $redirect, false );
    JFactory::getApplication()->redirect( $redirect );
}

$doc = JFactory::getDocument();
$uri = JURI::getInstance();
$js = "var com_hybridauth = {};\n";
$js.= "com_hybridauth.jbase = '".$uri->root()."';\n";
$doc->addScriptDeclaration($js);

// load the plugins
JPluginHelper::importPlugin( 'hybridauth' );

// Create the controller
$classname = 'HybridAuthController'.$controller;
$controller = HybridAuth::getClass( $classname );

// ensure a valid task exists
$task = JRequest::getVar('task');
if (empty($task))
{
    $task = 'display';  
    JRequest::setVar( 'layout', 'default' );
}
JRequest::setVar( 'task', $task );

// Perform the requested task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();

?>