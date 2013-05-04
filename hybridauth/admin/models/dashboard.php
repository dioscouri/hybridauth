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

HybridAuth::load( 'HybridAuthModelBase', 'models._base' );

class HybridAuthModelDashboard extends HybridAuthModelBase 
{
	function getTable($name='Config', $prefix=null, $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}
}