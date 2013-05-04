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

class HybridAuthViewBase extends DSCViewAdmin
{
	function display($tpl=null)
	{
		JHTML::_('stylesheet', 'admin.css', 'media/com_hybridauth/css/');
		
		$parentPath = JPATH_ADMINISTRATOR . '/components/com_hybridauth/helpers';
		DSCLoader::discover('HybridAuthHelper', $parentPath, true);
		
		$parentPath = JPATH_ADMINISTRATOR . '/components/com_hybridauth/library';
		DSCLoader::discover('HybridAuth', $parentPath, true);
		
		parent::display($tpl);
	}
}