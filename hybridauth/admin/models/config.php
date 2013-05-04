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

if ( !class_exists('HybridAuth') ) {
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
}

HybridAuth::load( 'HybridAuthModelBase', 'models._base' );

class HybridAuthModelConfig extends HybridAuthModelBase 
{
    public function getHAConfigArray()
    {
        $config = HybridAuth::getInstance();
        
        $return = array();
        $return["base_url"] = HybridAuth::getUrl( 'hybridauth' );
        $return["debug_mode"] = false; // TODO get from config
		$return["debug_file"] = ""; // TODO get from config
        $return["providers"] = array();
        
        if ($config->get('fb_app_id') && $config->get('fb_app_secret') && $config->get('fb_enable'))
        {
            $return["providers"]["Facebook"] = array( 
    			"enabled" 	=> true,
    			"keys"	 	=> array ( "id" => $config->get('fb_app_id'), "secret" => $config->get('fb_app_secret') )
    		);            
        }
        
        if ($config->get('twitter_app_id') && $config->get('twitter_app_secret') && $config->get('twitter_enable'))
        {
            $return["providers"]["Twitter"] = array( 
    			"enabled" 	=> true,
    			"keys"	 	=> array ( "key" => $config->get('twitter_app_id'), "secret" => $config->get('twitter_app_secret') )
    		);            
        }

        if ($config->get('google_enable'))
        {
            $return["providers"]["Google"] = array( 
    			"enabled" 	=> true,
            	"keys"	 	=> array ( "id" => $config->get('google_app_id'), "secret" => $config->get('google_app_secret') )
    		);
        }

        if ($config->get('openid_enable'))
        {
            $return["providers"]["OpenID"] = array( 
    			"enabled" 	=> true
    		);
        }
        
		return $return;
    }
    
    public function getHybridAuth( $options=array() )
    {
        if (empty($this->hybridauth))
        {
            $cookie_type = JRequest::getVar( 'hybridauth_type', '', 'cookie' );
            $cookie_params = json_decode( JRequest::getVar( 'hybridauth_params', '', 'cookie' ) );
            $session = JFactory::getSession();
            $session_type = $session->get( 'hybridauth_type' );
            $session_params = $session->get( 'hybridauth_params' );
            $this->provider_type = empty($options['hybridauth_type']) ? strtolower( $session_type ) : strtolower( $options['hybridauth_type'] );
            $this->provider_params = empty($options['hybridauth_params']) ? $session_params : $options['hybridauth_params'];
            if (empty($this->provider_type))
            {
                $this->provider_type = $cookie_type;
            }
            if (empty($this->provider_params))
            {
                $this->provider_params = $cookie_params;
            }
    
            if (!empty($this->provider_type)) {
                $session->set( 'hybridauth_type', $this->provider_type );       
                setcookie("hybridauth_type", $this->provider_type, time()+3600, '/');
            }
    
            if (!empty($this->provider_params)) {
                $session->set( 'hybridauth_params', $this->provider_params );       
                setcookie("hybridauth_params", $this->provider_params, time()+3600, '/');
            }
            
            $config = $this->getHAConfigArray();
            
        	require_once( HybridAuth::getPath( 'hybridauth' ) . "/Hybrid/Auth.php" );
    		$this->hybridauth = new Hybrid_Auth( $config );
    		
    		if (!empty($_SESSION["HA::STORE"]))
    		{
    			$sdata = $this->hybridauth->getSessionData();
                if (!empty($sdata)) {
                    $session->set( 'hybridauth_data', $sdata );       
                    setcookie("hybridauth_data", $sdata, time()+3600, '/');
                }
                $this->migrateSession();		    
    		} else {
    		    $cookie_sdata = JRequest::getVar( 'hybridauth_data', '', 'cookie' );
    		    $sdata = $session->get( 'hybridauth_data', $cookie_sdata );
    		    $this->hybridauth->restoreSessionData( $sdata );
    		}
        }
        
        return $this->hybridauth;
    }
    
    public function getConnection( $options=array() )
    {
		$this->getHybridAuth( $options );
		
		$this->is_user_logged_in = $this->hybridauth->isConnectedWith( $this->provider_type );
		
		if (!$this->is_user_logged_in)
		{
            return false; // TODO Change this?
		}
		
		$this->connection = $this->hybridauth->authenticate( $this->provider_type, $this->provider_params );
		$this->user_profile = $this->connection->getUserProfile();
		$this->provider_id = $this->user_profile->identifier;
		
		return $this->connection;
    }

    public function logout( $options=array() )
    {
        $this->getHybridAuth( $options );
        $idps = $this->hybridauth->getConnectedProviders();

        if (!empty($this->hybridauth) && !empty($idps))
        {
            $this->hybridauth->logoutAllProviders();
            $this->destroySessionData();
            $this->hybridauth->storage()->clear();
        }
    }
    
    public function destroySessionData()
    {
        $session = JFactory::getSession();
        
        $session->set( 'hybridauth_type', '' );       
        setcookie("hybridauth_type", '', time()-3600, '/');
        JRequest::setVar( 'hybridauth_type', '', 'cookie' );
        
        $session->set( 'hybridauth_params', '' );       
        setcookie("hybridauth_params", '', time()-3600, '/');
        JRequest::setVar( 'hybridauth_params', '', 'cookie' );
        
        $session->set( 'hybridauth_data', '' );       
        setcookie("hybridauth_data", '', time()-3600, '/');
        JRequest::setVar( 'hybridauth_data', '', 'cookie' );
    }
    
    /**
     * 
     * Enter description here ...
     * @return return_type
     */
    private function migrateSession()
    {
        $session = JFactory::getSession();
        if (!empty($_SESSION))
        {
            foreach ($_SESSION as $key=>$value)
            {
            	if ((substr($key, 0, 1) != '_'))
	            {
	                $session->set( $key, $value );
	            }
            }
        }
    }
}
