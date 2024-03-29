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

class HybridAuthHelperDiagnostics extends DSCHelperDiagnostics 
{
    /**
     * Performs basic checks on your installation to ensure it is OK
     * @return unknown_type
     */
    function checkInstallation() 
    {
        $functions = array();
        
        foreach ($functions as $function)
        {
            if (!$this->{$function}())
            {
                return $this->redirect( JText::_("COM_HYBRIDAUTH_".$function."_FAILED") .' :: '. $this->getError(), 'error' );
            }            
        }
    }
    
    protected function setCompleted( $fieldname, $value='1' )
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_hybridauth/tables' );
        $config = JTable::getInstance( 'Config', 'HybridAuthTable' );
        $config->load( array( 'config_name'=>$fieldname ) );
        $config->config_name = $fieldname;
        $config->value = '1';
        $config->save();
    }
	
}