<?php
/**
 * @package	HybridAuth
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class HybridAuthControllerLogin extends HybridAuthController
{
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'login');
	}
	
	function display($cachable=false, $urlparams = false)
	{
	    $user = JFactory::getUser();
	    if (!empty($user->id)) {
	        if(version_compare(JVERSION,'1.6.0','ge')) {
	            // Joomla! 1.6+ code here
	            $redirect = "index.php?option=com_users&view=login";
	        } else {
	            // Joomla! 1.5 code here
	            $redirect = "index.php?option=com_user&view=login";
	        }
	        
	        $this->setRedirect( $redirect, null, null );
	        return;
	    }
	    
	    // is their a return value in the URL?  if so, set the session return value
	    $return = JRequest::getVar( 'return', '', 'request', 'base64' );
	    if (!empty($return))
	    {
	        setcookie("hybridauth_login_return", $return, time()+3600, '/');
	    }
	    
	    JRequest::setVar( 'layout', 'default' );
	    parent::display($cachable, $urlparams);
	}
	
	function login()
	{	    
		JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_hybridauth/models' );
		$model = JModel::getInstance( 'Config', 'HybridAuthModel' );
		$this->config = $model->getHAConfigArray();
        
	    $type = strtolower( JRequest::getVar('type') );
	    if (!empty($type) && $type != 'default')
	    {
	        switch ($type)
    		{
    		    case "openid":
    		        $openid_identifier = JRequest::getVar('openid_url');
    		        $hybridauth_params = array( "openid_identifier"=>$openid_identifier );
    		        break;
    		    default:
    		        $hybridauth_params = null;
    		        break;
    		}
    		
    	    $session = JFactory::getSession();
    	    $session->set('hybridauth_type', $type);
    	    $session->set('hybridauth_params', $hybridauth_params);
    	    setcookie("hybridauth_type", $type, time()+3600, '/');
    	    setcookie("hybridauth_params", $hybridauth_params, time()+3600, '/');
    	    setcookie("jsession_id", $session->getId(), time()+3600, '/');
    	    $_SESSION["hybridauth_type"] = $type;
    	    $_SESSION["hybridauth_params"] = $hybridauth_params;
    	    
	        $joomla_session = JFactory::getSession();
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
            //$_SESSION = $temp_session;
    
            // the HA index.php file needs to know what sessionid to use
            $session_id = session_id();
            setcookie("jsession_id", $session_id, time()+3600, '/');
	    }

	    switch( $type ) 
	    {
	        case "openid":
	        case "google":
	        case "twitter":
	        case "facebook":
	            $this->login_hybridauth( $type );
	            break;
	        case "joomla":
	        default:
	            $this->login_default();
	            break;
	    }
	}

	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
	public function loginComplete()
	{
        // find the default post-completed-login page, defined in config
        // fire a plugin event that allows plugins to override the post-completed-login page
        // redirect to the post-completed-login page

	    $cookie_return = JRequest::getVar('hybridauth_login_return', '', 'cookie', 'base64');
	    setcookie("hybridauth_login_return", '', time()-3600, '/');
	    
	    $return = base64_decode(JRequest::getVar('return', $cookie_return, 'POST', 'BASE64'));

		if (empty($return)) 
		{
		    $login_redirect = HybridAuth::getInstance()->get('login_redirect_url', '');
            $redirect = HybridAuth::getInstance()->get('login_redirect', '');
            
            if ( ($login_redirect) && ($redirect) )
            {
              	$return = $login_redirect;	
            }
		}
		
		if (empty($return)) 
		{
		    if(version_compare(JVERSION,'1.6.0','ge')) {
		        // Joomla! 1.6+ code here
		        $return = 'index.php?option=com_users&view=profile';
		    } else {
		        // Joomla! 1.5 code here
		        $return = 'index.php?option=com_user&view=user';
		    }
		}
		
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger( 'onHybridAuthLoginComplete', array( &$return ) );
		
		$app = JFactory::getApplication();
		$app->redirect( JRoute::_( $return, false ) );
		return;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
	public function love()
	{
	    JRequest::setVar( 'layout', 'love' );
	    parent::display();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
	public function completeProfile()
	{
        // target function for completing incomplete profiles (twitter)
        // required fields: email, name, username
        $cookie = JRequest::get('cookie');
        $cookie_juser = json_decode( $cookie['juser'] );
        $juser = JFactory::getUser( $cookie_juser->id );
        $incomplete_fields = json_decode( $juser->getParam( 'hybridauth_incomplete_required_fields' ) );
        $hybridauth_login_type = $cookie['hybridauth_login_type'];
        
        $doc = JFactory::getDocument();
        $view   = $this->getView( $this->get('suffix'), $doc->getType() );
        $model  = $this->getModel( $this->get('suffix') );
        $view->setModel( $model, true );
        $view->setTask('completeProfile');
        $view->setLayout('complete');
        $view->assign( 'juser', $juser );
        $view->assign( 'hybridauth_login_type', $hybridauth_login_type );
        $view->assign( 'incomplete_fields', $incomplete_fields );
        $view->display();
        $this->footer();
	}

	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
	public function saveProfile()
	{
        // update the user's joomla profile with the new values
        // login the user using the $hybridauth_login_type
        // redirect to the post-login landing page
	    
		JRequest::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		
        $cookie = JRequest::get('cookie');
        $cookie_juser = json_decode( $cookie['juser'] );
        $juser = JUser::getInstance( $cookie_juser->id );
        
        $post = JRequest::get('post');
        $juser->email = $post['email'];
        $juser->setParam( 'hybridauth_incomplete_required_fields', json_encode(array()) );
        if (!$juser->save())
        {
            // redirect back to the completeProfile task with an error message
	        $redirect = JRoute::_( "index.php?option=com_hybridauth&view=login&task=completeProfile", false );
	        $this->message = $juser->getError();
    	    $this->setRedirect( $redirect, $this->message, $this->messagetype );
    	    return;
        }

        // otherwise, complete the login!
        $hybridauth_login_type = JRequest::getVar( 'type', $cookie['hybridauth_login_type'] );
        $redirect = JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=" . $hybridauth_login_type, false );
	    $this->setRedirect( $redirect, null, null );
        
        return;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
	private function login_default()
	{
		JRequest::checkToken('post') or jexit(JText::_('JInvalid_Token'));

		$app = JFactory::getApplication();

		// Populate the data array:
		$data = array();
		$data['return'] = base64_decode(JRequest::getVar('return', '', 'POST', 'BASE64'));
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		// Set the return URL if empty.
		if (empty($data['return'])) {
			$data['return'] = 'index.php?option=com_hybridauth&view=login&task=loginComplete';
		}

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		$options['return'] = $data['return'];

		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];

		// Perform the log in.
		$error = $app->login($credentials, $options);

		// Check if the log in succeeded.
		if (!JError::isError($error)) {
			$app->setUserState('users.login.form.data', array());
			$app->redirect(JRoute::_($data['return'], false));
		} else {
			$data['remember'] = (int)$options['remember'];
			$app->setUserState('users.login.form.data', $data);
			if(version_compare(JVERSION,'1.6.0','ge')) {
			    // Joomla! 1.6+ code here
			    $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
			} else {
			    // Joomla! 1.5 code here
			    $app->redirect(JRoute::_('index.php?option=com_user&view=login', false));
			}			
		}
	}

	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $type
	 * @return return_type
	 */
	private function login_hybridauth( $type )
	{
		$config = $this->config;
		
        require_once( HybridAuth::getPath( 'hybridauth' ) . "/Hybrid/Auth.php" );
        
        try {
    		$hybridauth = new Hybrid_Auth( $config );
    		$hybridauth_params = null;
    		
    		switch ($type)
    		{
    		    case "openid":
    		        $openid_identifier = JRequest::getVar('openid_url');
    		        $hybridauth_params = array( "openid_identifier"=>$openid_identifier );
    		        $auth = $hybridauth->authenticate( $type, $hybridauth_params );
    		        break;
    		    default:
    		        $auth = $hybridauth->authenticate( $type );
    		        break;
    		}

    	    // Set the JAuthentication object so Joomla knows that we're logged in
    	    $app = JFactory::getApplication();
    	    $app->login( array( 'username'=>'', 'password'=>'' ), array( 'remember'=>1, 'hybridauth_type'=>$type, 'hybridauth_params'=>$hybridauth_params ) );

    	    // does this account require completion?
    	    $user = JFactory::getUser();
    	    $incomplete_fields = json_decode( $user->getParam( 'hybridauth_incomplete_required_fields' ) );
    	    if (!empty($incomplete_fields))
    	    {
    	        setcookie("hybridauth_login_type", $type, time()+3600, '/');
                setcookie("juser", json_encode( $user ), time()+3600, '/');
                
                HybridAuth::load( 'HybridAuthHelperBase', 'helpers._base' );
                $helper = new HybridAuthHelperBase();
                $helper->logout();
                $helper->deleteSessionVariables();
                
                $app->logout();
                
    	        $redirect = JRoute::_( "index.php?option=com_hybridauth&view=login&task=completeProfile", false );
    	        $this->message = JText::_( "COM_HYBRIDAUTH_LOGIN_INCOMPLETE" );
    	    }
    	    else
    	    {
        	    /*
        	    $redirect = JRoute::_( "index.php?option=com_hybridauth&view=login&task=loginComplete", false );
        	    $this->message = JText::_( "COM_HYBRIDAUTH_LOGIN_SUCCESS" );
        	    $this->messagetype = 'message';
        	    */
    	        $this->loginComplete();
    	        return;
    	    }
        }
		catch( Exception $e )
		{
		    $redirect = JRoute::_( "index.php?option=com_hybridauth&view=login" );
		    $this->message = JText::_( "COM_HYBRIDAUTH_LOGIN_FAILED" ) . ": [" . $e->getCode() . "] " . $type;
		    $this->messagetype = 'notice';
		     
		    $message = $e->getMessage();
		    
    		$code = $e->getCode();
    		switch ($code)
    		{
    		    case "8":
    		        // 8	Provider does not support this feature
    		    	break;
    		    case "7":
    		        // 7	User not connected to the provider
    		    	break;
    		    case "6":
    		        // 6	User profile request failed
    		    	break;
    		    case "5":
    		        // 5	Authentification failed
    		    	break;
    		    case "4":
    		        // 4	Missing provider application credentials (your application id, key or secret)
    		    	break;
    		    case "3":
    		        // 3	Unknown or disabled provider
    		    	break;
    		    case "2":
    		        // 2	Provider not properly configured 
    		    	break;
    		    case "1":
    		        // 1	Hybriauth configuration error
    		    	break;
    		    case "0":
    		        // 0	Unspecified error
    		    	break;
    		}
	    }

	    $this->setRedirect( $redirect, $this->message, $this->messagetype );
	    return;
	}

	/**
	 * 
	 * Enter description here ...
	 * @return return_type
	 */
    private function dump()
    {
        $request = JRequest::get('request');
        $session = JRequest::get('session');
        $cookie = JRequest::get('cookie');
        echo "REQUEST: <pre>" . print_r( $request, true ) . "</pre><br />";
        echo "SESSION: <pre>" . print_r( $session, true ) . "</pre><br />";
        echo "COOKIE: <pre>" . print_r( $cookie, true ) . "</pre><br />";
        echo "_SESSION VARIABLE: <pre>" . print_r( $_SESSION, true ) . "</pre><br />";
    }
}