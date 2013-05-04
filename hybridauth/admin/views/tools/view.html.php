<?php
/**
 * @version 1.5
 * @package HybridAuth
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

HybridAuth::load( 'HybridAuthViewBase', 'views._base' );

class HybridAuthViewTools extends HybridAuthViewBase 
{
    function getLayoutVars($tpl=null) 
    {
        $layout = $this->getLayout();
        switch(strtolower($layout))
        {
            case "form":
                JRequest::setVar('hidemainmenu', '1');
                $this->_form($tpl);
              break;
            case "view":
                $this->_form($tpl);
                break;
            case "default":
            default:
                $this->_default($tpl);
              break;
        }
    }
    
    function _form($tpl=null)
    {
        parent::_form($tpl);
        
        // load the plugin
        $row = $this->getModel()->getItem();
        $import = JPluginHelper::importPlugin( 'hybridauth', $row->element );
    }
    
    function _defaultToolbar()
    {
    }
    
    function _viewToolbar($isNew = null)
    {
        JToolBarHelper::custom( 'view', 'forward', 'forward', JText::_('Submit'), false );
        JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
    }
}
