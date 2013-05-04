<?php
/**
 * @version	1.5
 * @package	HybridAuth
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

if ( !class_exists('HybridAuth') ) 
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );

HybridAuth::load( 'HybridAuth', 'defines' );

class HybridAuthHelperBase extends DSCHelper
{   
    /**
     * 
     * Enter description here ...
     * @return return_type
     */
    function deleteSessionVariables()
    {
        $session = JFactory::getSession();
        $session->set( 'hybridauth_data', '' );
        
        setcookie("hybridauth_data", '', time()-3600);

        unset($_SESSION["HA::STORE"]);
        unset($_SESSION["hybridauth_data"]);
        
        JRequest::setVar("hybridauth_data", '');
        JRequest::setVar("hybridauth_data", '', 'cookie');
        JRequest::setVar("hybridauth_data", '', 'server');
    }
    
    /**
     * 
     * Enter description here ...
     * @return return_type
     */
    function logout( $type='' )
    {
        jimport( 'joomla.application.component.model' );
		JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_hybridauth/models' );
		$model = JModel::getInstance( 'Config', 'HybridAuthModel' );
		$this->config = $model->getHAConfigArray();
		
        $temp_session = $_SESSION;
        session_write_close();
        
        ini_set( "session.save_handler", "files" );
        session_start();
        // $_SESSION["juser"] = JFactory::getUser();
        $_SESSION["jsession_id"] = JFactory::getSession()->getId();
        session_write_close();
        
		/*
        ini_set( "session.save_handler", "user" );
        $jd = new JSessionStorageDatabase();
        $jd->register();
		*/
        
        session_start();
        // $_SESSION = $temp_session;

        // the HA index.php file needs to know what sessionid to use
        $session_id = session_id();
        setcookie("jsession_id", $session_id, time()+3600, '/');

        require_once( HybridAuth::getPath( 'hybridauth' ) . "/Hybrid/Auth.php" );
        $hybridauth = new Hybrid_Auth( $this->config );
        
        if (!empty($type))
        {
             $auth = $hybridauth->authenticate( $type );
             $auth->logout();
        }
        else 
        {
            $providers = $hybridauth->getConnectedProviders();
            if (!empty($providers))
            {
                foreach ($providers as $provider)
                {
                    $auth = $hybridauth->authenticate( $provider );
                    $auth->logout();
                }
            }            
        }
        setcookie("jsession_id", '', time()-3600, '/');

    }
}