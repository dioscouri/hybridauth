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

class HybridAuthModelLogin extends HybridAuthModelAccounts
{
    function getTable($name='Accounts', $prefix='HybridAuthTable', $options = array())
    {
        return parent::getTable($name, $prefix, $options);
    }
}