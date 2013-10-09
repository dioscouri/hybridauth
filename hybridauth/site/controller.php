<?php
/**
 * @version	0.1
 * @package	HybridAuth
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class HybridAuthController extends DSCControllerSite 
{	
    /**
    * default view
    */
    public $default_view = 'login';
    
	var $_models = array();
	var $message = "";
	var $messagetype = "";
	
	function __construct( $config=array() )
	{
	    parent::__construct( $config );
	
	    $this->defines = HybridAuth::getInstance();
	
	    HybridAuth::load( "HybridAuthHelperRoute", 'helpers.route' );
	    $this->router = new HybridAuthHelperRoute();
	
	    $this->user = JFactory::getUser();
	}
}

?>