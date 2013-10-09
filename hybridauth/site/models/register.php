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

HybridAuth::load( 'HybridAuthModelAccounts', 'models.accounts' );

class HybridAuthModelRegister extends HybridAuthModelAccounts
{
    function getTable($name='Accounts', $prefix='HybridAuthTable', $options = array())
    {
        return parent::getTable($name, $prefix, $options);
    }
    
    public function validate( $values, $options=array() )
    {
        // fail if no user->id and email address fails validation
        jimport('joomla.mail.helper');
        if( !JMailHelper::isEmailAddress($values['email_address'])) {
            $this->setError( JText::_('COM_HYBRIDAUTH_PLEASE_ENTER_CORRECT_EMAIL') );
        }
    
        // fail if registering new user but one of passwords is empty
        if( (empty($values["register-new-password"]) ) ) {
            $this->setError( JText::_('COM_HYBRIDAUTH_PASSWORD_INVALID') );
        }
    
        // fail if registering new user but passwords don't match
        if( $values["register-new-password"] != $values["register-new-password2"] ) {
            $this->setError( JText::_('COM_HYBRIDAUTH_PASSWORDS_DO_NOT_MATCH') );
        }
    
        // fail if registering new user but account exists for email address provided
        $userHelper = new HybridAuthHelperUser();
        if ( $userHelper->emailExists($values['email_address']) ) {
            $this->setError( JText::_('COM_HYBRIDAUTH_EMAIL_ALREADY_EXIST') );
        }
        
        // fail if password doesn't validate and validation is enabled
        if( $this->defines->get('password_php_validate', '1')) {
            $validate_pass = $userHelper->validatePassword( $values['register-new-password'] );
            if (!$validate_pass[0])
            {
                foreach ($validate_pass[1] as $error)
                {
                    $this->setError( $error );
                }
            }
        }
        
        return $this->check();        
    }    

    public function save( $values, $options=array() )
    {
        $result = new stdClass();
        $result->error = false;
    
        HybridAuth::load( 'HybridAuthHelperUser', 'helpers.user' );
        $userHelper = new HybridAuthHelperUser();
        
        if (!empty($options['useractivation'])) {
            $userHelper->useractivation = 1;
        }        
        
        $details = array(
                'email' => $values['email_address'],
                'name' => $values['email_address'],
                'username' => $values['email_address']
        );
        
        if ( strlen(trim($details['name'])) == 0 ) {
            $details['name'] = JText::_('COM_HYBRIDAUTH_USER');
        }
        
        $details['password']    = $values["register-new-password"];
        $details['password2']   = $values["register-new-password2"];
        $user_id = null;
        
        if (!$user = $userHelper->createNewUser( $details, false ))
        {
            $result->error = true;
        }
        else
        {
            if (!empty($options['autologin'])) 
            {
                $userHelper->login(
                        array('username' => $user->username, 'password' => $details['password'])
                );
            }
        
            $user_id = $user->id;
        }

        $result->user_id = $user_id;
        $this->result = $result;

        if ($result->error)
        {
            return false;
        }
        
        return $result;        
    }    
}