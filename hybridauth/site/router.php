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

if ( !class_exists('HybridAuth') ) { 
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
}

HybridAuth::load( "HybridAuthHelperRoute", 'helpers.route' );

/**
 * Build the route
 * Is just a wrapper for HybridAuthHelperRoute::build()
 * 
 * @param unknown_type $query
 * @return unknown_type
 */
function HybridAuthBuildRoute(&$query)
{
    return HybridAuthHelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for HybridAuthHelperRoute::parse()
 * 
 * @param unknown_type $segments
 * @return unknown_type
 */
function HybridAuthParseRoute($segments)
{
    return HybridAuthHelperRoute::parse($segments);
}