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

jimport('joomla.plugin.plugin');

class plgAuthenticationHybridAuth extends JPlugin
{
    /**
	 * This method should handle any authentication and report back to the subject
	 *
	 * @access	public
	 * @param   array	$credentials Array holding the user credentials
	 * @param	array   $options	Array of extra options
	 * @param	object	$response	Authentication response object
	 * @return	boolean
	 * @since 1.7
	 */
	function onUserAuthenticate( $credentials, $options, &$response ) 
	{
	    $this->authenticate( $credentials, $options, $response );
	}
	
    /**
     * This method should handle any authentication and report back to the subject
     *
     * @access  public
     * @param   array   $credentials    Array holding the user credentials
     * @param   array   $options        Array of extra options
     * @param   object  $response       Authentication response object
     * @return  boolean
     * @since   1.5
     */
    function onAuthenticate( $credentials, $options, &$response )
    {
        $this->authenticate( $credentials, $options, $response );
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $credentials
     * @param unknown_type $options
     * @param unknown_type $response
     * @return return_type
     */
    private function authenticate( $credentials, $options, &$response )
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            $response->status		= JAuthentication::STATUS_FAILURE;
            $response->error_message	= JText::sprintf('JGLOBAL_AUTH_FAILED', $message);
            return false;
        }
        
        $cookie_type = JRequest::getVar( 'hybridauth_type', '', 'cookie' );
        $cookie_params = json_decode( JRequest::getVar( 'hybridauth_params', '', 'cookie' ) );
        $session = JFactory::getSession();
        $session_type = $session->get( 'hybridauth_type' );
        $session_params = $session->get( 'hybridauth_params' );
        $provider_type = empty($options['hybridauth_type']) ? strtolower( $session_type ) : strtolower( $options['hybridauth_type'] );
        $provider_params = empty($options['hybridauth_params']) ? $session_params : $options['hybridauth_params'];
        /*
        echo "session_id: " . $session->getId() . "<br/>";
        echo "cookie_type: " . $cookie_type . "<br/>";
        echo "cookie_params: " . $cookie_params . "<br/>";
        echo "session_type: " . $session_type . "<br/>";
        echo "session_params: " . $session_params . "<br/>";
        echo "provider_type: " . $provider_type . "<br/>";
        echo "provider_params: " . $provider_params . "<br/>";
        echo "option_type: " . @$options['hybridauth_type'] . "<br/>";
        echo "option_params: " . @$options['hybridauth_params'] . "<br/>";
        echo "PHP_session_type: " . @$_SESSION['hybridauth_type'] . "<br/>";
        echo "PHP_session_params: " . @$_SESSION["hybridauth_params"] . "<br/>";
		*/
        if (empty($provider_type))
        {
            $provider_type = $cookie_type;
        }
        if (empty($provider_params))
        {
            $provider_params = $cookie_params;
        }

        if (!empty($provider_type)) {
            $session->set( 'hybridauth_type', $provider_type );       
            setcookie("hybridauth_type", $provider_type, time()+3600, '/');
        }

        if (!empty($provider_params)) {
            $session->set( 'hybridauth_params', $provider_params );       
            setcookie("hybridauth_params", $provider_params, time()+3600, '/');
        }

        if (empty($provider_type))
        {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'Invalid Authorization Type';
            return false;
        }

        // If there is no association between joomla.user_id and this hybridauth_type+ID pair, 
        // then create one in #__hybridauth_accounts
        if ( !class_exists('HybridAuth') ) { 
            JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
        }
    
        jimport( 'joomla.application.component.model' );        
		JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_hybridauth/models' );
		$model = JModel::getInstance( 'Config', 'HybridAuthModel' );
		$config = $model->getHAConfigArray();
		
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

		$is_user_logged_in = $this->hybridauth->isConnectedWith( $provider_type );
		
		if (!$is_user_logged_in)
		{
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = 'User Not Logged In';
            return false;
		}

		$this->connection = $this->hybridauth->authenticate( $provider_type, $provider_params );
		
		$is_user_connected = $this->connection->isUserConnected();		
		if ($is_user_connected)
		{
    		$user_profile = $this->connection->getUserProfile();
    		$provider_id = $user_profile->identifier;
    		
            JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_hybridauth/tables' );
            $account = JTable::getInstance( 'Accounts', 'HybridAuthTable' );
            $account->load( array( 'provider_type'=>$provider_type, 'provider_id'=>$provider_id ) ); 
            
            if (empty($account->account_id) || empty($account->user_id))
            {
                $account->bind( $user_profile );
                $account->provider_type = $provider_type;
                $account->provider_id = $provider_id;
                if (!$account->store())
                {
                    // TODO What to do if saving the account fails?
                }
                
                if (empty($account->user_id))
                {
                    // TODO try to associate this user with any other providers -- meaning:
                        // foreach enabled provider_type, see if the user is logged in/connected with any of them
                        // if so, see if they have a HybridAuthTableAccounts.user_id for that provider_id
                        // if so, use that user_id
                        
                    // otherwise, create a new joomla user account
                    $juser = $account->getJoomlaUser();
                    if (empty($juser->id))
                    {
                        $response->status = JAUTHENTICATE_STATUS_FAILURE;
                        $response->error_message = 'Could Not Create User';
                        return false;
                    }
                    
                    $account->user_id = $juser->id;
                    $account->store();                 
                }
                
            }
    
            $user = JUser::getInstance($account->user_id);
            $account->checkUser( $user );
            $response->username = $user->username;
            $response->email = $user->email;
            $response->fullname = $user->name;
            $response->status = JAUTHENTICATE_STATUS_SUCCESS;
            $response->error_message = '';
            $response->set('type', 'hybridauth');		    
		}
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
