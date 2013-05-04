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

class plgUserHybridAuth extends JPlugin
{
	/**
	 * 
	 * @param	array		$user	Holds the user data
	 * @param	boolean		$success	True if user was succesfully stored in the database
	 * @param	string		$msg	Message
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
	    return $this->delete( $user, $success, $msg );
	}
	
	/**
	 * J1.5 function
	 *
	 * @param 	array	  	holds the user data
	 * @param	boolean		true if user was succesfully stored in the database
	 * @param	string		message
	 * 
	 * @since	1.5
	 */
	function onAfterDeleteUser($user, $success, $msg)
	{
	    return $this->delete( $user, $success, $msg );
	}
	
    /**
     * 
     * Enter description here ...
     * @param unknown_type $credentials
     * @param unknown_type $options
     * @param unknown_type $response
     * @return return_type
     */
    private function delete( $user, $success, $msg )
    {
		if(!$success) {
			return false;
		}

		$db = JFactory::getDbo();
		$db->setQuery(
			'DELETE FROM `#__hybridauth_accounts`' .
			' WHERE `user_id` = '.(int) $user['id']
		);
		$db->Query();

		return true;
    }
    
	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param	array	$user		Holds the user data.
	 * @param	array	$options	Array holding options (client, ...).
	 *
	 * @return	object	True on success
	 * @since	1.6
	 */
	public function onUserLogout($user, $options = array())
	{
	    return $this->logout( $user, $options );
	}
	
	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @access public
	 * @param  array	holds the user data
	 * @param 	array   array holding options (client, ...)
	 * @return object   True on success
	 * 
	 * @since 1.5
	 */
	function onLogoutUser($user, $options = array())
	{
	    return $this->logout( $user, $options );
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $user
	 * @param unknown_type $options
	 * @return return_type
	 */
	private function logout( $user, $options=array() )
	{
	    // kill the HA session and logout the user from all HA connectors
        if ( !class_exists('HybridAuth') ) {
            JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
        }
        
        HybridAuth::load( 'HybridAuthHelperBase', 'helpers._base' );
        $helper = new HybridAuthHelperBase();
        $helper->logout();
        $helper->deleteSessionVariables();
        
	    return true;
	}
}
