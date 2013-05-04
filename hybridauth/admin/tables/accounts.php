<?php
/**
 * @package HybridAuth
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

HybridAuth::load( 'HybridAuthTable', 'tables._base' );

class HybridAuthTableAccounts extends HybridAuthTable 
{
    function HybridAuthTableAccounts ( &$db=null ) 
    {
        if (empty($db)) {
            $db = JFactory::getDBO();
        }
        
        $tbl_key    = 'account_id';
        $tbl_suffix = 'accounts';
        $this->set( '_suffix', $tbl_suffix );
        $name       = "hybridauth";
        
        parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );
    }
    
    function check()
    {
        return true;
    }
    
    /**
     * Gets a Joomla User for this account,
     * creating one if no existing user is found
     * 
     * @return return_type
     */
    function getJoomlaUser()
    {
        if ($user = $this->findJoomlaUser())
        {
            return $user;
        }
        
        return $this->createJoomlaUser();
    }
    
    /**
     * 
     * Enter description here ...
     * @return return_type
     */
    function findJoomlaUser()
    {
        // do a simple email search
        $email = trim($this->email);
        if (empty($email))
        {
            return false;
        }
        
        $db = JFactory::getDBO();
        $string = $db->getEscaped( $email );
        $query = "SELECT * FROM #__users WHERE `email` = '$string' LIMIT 1;";
        $db->setQuery( $query );
        $result = $db->loadObject();
        
        if (empty($result->id))
        {
            $result = false;
            
            // search the accounts DB table
            $table = JTable::getInstance( 'Accounts', 'HybridAuthTable' );
            $table->load( array( 'email'=>$email ) );
            if (!empty($table->user_id))
            {
                $result = JFactory::getUser( $table->user_id ); 
            }
        }
        
        return $result;
    }
    
    /**
     * Creates a Joomla User from an Account (which is basically an SSO/HA profile)
     * @return return_type
     */
    function createJoomlaUser()
    {
        HybridAuth::load( 'HybridAuthHelperUser', 'helpers.user' );
		$userHelper = new HybridAuthHelperUser();
		$lastUserId = $userHelper->getLastUserId();
		$guestId = $lastUserId + 1;

        $username = $this->displayName;
        $username = $this->validateUsername( $username );
		
        $name = trim( $this->firstName . " " . $this->lastName );
        if (empty($name))
        {
            $name = $username;
        }
        
        $guest_email = false;
        $email = $this->email;
        if (empty($email))
        {
    		// create a guest email address to be stored in the __users table
    		// get the domain from the uri
    		$uri = JURI::getInstance();
    		$domain = $uri->gethost();
            $email = "guest_". $guestId . "@" . $domain;
            $guest_email = true; 
        } 

		$details = array(
			'email' => $email,
			'name' => $name,
			'username' => $username			
		);
			
		// use a random password, and send password2 for the email
		jimport('joomla.user.helper');
		$details['password']    = JUserHelper::genRandomPassword();
		$details['password2']   = $details['password'];

		// create the new user
		$user = $userHelper->createNewUser($details);
        if ($guest_email)
        {
            $user->setParam( 'hybridauth_incomplete_required_fields', array( 'email' ) );
            $user->save();
        }
        return $user;
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $username
     * @return return_type
     */
    function validateUsername( $username )
    {
        HybridAuth::load( 'HybridAuthHelperUser', 'helpers.user' );
		$userHelper = new HybridAuthHelperUser();
		
		$file = new DSCFile();

		$username = $file->cleanTitle( $username );
		
		$string = $username;
		for ($i=1; ($userHelper->usernameExists( $string ) === true); $i++)
		{
		    $string = $username . $i;
		}
		$username = $string;
		
        return $username;
    }
    
    /**
     * 
     * Enter description here ...
     * @param object $user  A valid JUser object
     * @return return_type
     */
    function checkUser( $user )
    {
        $guest_email = false;
		$uri = JURI::getInstance();
		$domain = $uri->gethost();

		if ((strpos($user->email, "guest_") !== false) && (strpos($user->email, $domain) !== false))
		{
		    $guest_email = true; 
		}
		
        if ($guest_email)
        {
            $user->setParam( 'hybridauth_incomplete_required_fields', json_encode(array( 'email' )) );
            $user->save();
        }
    }
}
