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

class HybridAuthControllerRegister extends HybridAuthController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->set('suffix', 'register');
	}
	
	public function save()
	{
        $itemid_string = '';
        $itemid = $this->router->findItemid(array('view'=>'login'));
        if ($itemid) {
            $itemid_string = '&Itemid=' . $itemid;
        }
        $redirect = "index.php?option=com_hybridauth&view=login" . $itemid_string;
	    
	    $app = JFactory::getApplication();
	    $values = JRequest::get('post');
	    
	    $model = $this->getModel( $this->get('suffix') );
	    $errorMessage = '';
	    if (!$validate = $model->validate( $values ))
	    {
	        foreach ($model->getErrors() as $error)
	        {
	        	$error = trim($error);
	            if (!empty($error)) {
	                $app->enqueueMessage($error, 'warning');
	            }
	        }
	        
	        $itemid_string = '';
	        $itemid = $this->router->findItemid(array('view'=>'register'));
	        if ($itemid) {
	            $itemid_string = '&Itemid=' . $itemid;
	        }
	        $redirect = "index.php?option=com_hybridauth&view=register" . $itemid_string;
	        $redirect = JRoute::_( $redirect, false );
	        $this->setRedirect( $redirect, $this->message, $this->messagetype );
	        return;	        
	    }
	    
	    $options = array();
	    $options["autologin"] = true;
	    $options["useractivation"] = false;
	    
	    if (!$order = $model->save( $values, $options ))
	    {
	        foreach ($model->getErrors() as $error)
	        {
	        	$error = trim($error);
	            if (!empty($error)) {
	                $app->enqueueMessage($error, 'warning');
	            }
	        }
	        
	        $itemid_string = '';
	        $itemid = $this->router->findItemid(array('view'=>'register'));
	        if ($itemid) {
	            $itemid_string = '&Itemid=' . $itemid;
	        }
	        $redirect = "index.php?option=com_hybridauth&view=register" . $itemid_string;
	    }
	    
	    if (!empty($options["useractivation"])) {
	        $app->enqueueMessage( JText::_( "COM_HYBRIDAUTH_REGISTRATION_COMPLETE_ACTIVATE" ) );
	    }	    

	    if (!empty($options["autologin"])) {
	        $this->loginComplete();
	        return;
	    }
	    
	    $redirect = JRoute::_( $redirect, false );
	    $this->setRedirect( $redirect, $this->message, $this->messagetype );
	}
	
	/**
	 *
	 * Enter description here ...
	 * @return return_type
	 */
	private function loginComplete()
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

}