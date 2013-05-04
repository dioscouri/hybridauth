<?php
/**
* @version		0.1.0
* @package		HybridAuth
* @copyright	Copyright (C) 2011 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class HybridAuth extends DSC
{
    protected $_name 			= 'hybridauth';
    protected $_version 		= '0.10.0';
    protected $_build          = '';
    protected $_versiontype    = '';
    protected $_copyrightyear 	= '2012';
    protected $_min_php		= '5.3';

    // View Options
    var $show_linkback						= '1';
    var $include_site_css                   = '1';
    var $amigosid                           = '';
    var $page_tooltip_dashboard_disabled	= '0';
    var $page_tooltip_config_disabled		= '0';
    var $page_tooltip_tools_disabled		= '0';
    var $page_tooltip_accounts_disabled		= '0';
    var $page_tooltip_payouts_disabled		= '0';
    var $page_tooltip_logs_disabled			= '0';
    var $page_tooltip_payments_disabled		= '0';
    var $page_tooltip_commissions_disabled	= '0';
    var $page_tooltip_users_view_disabled   = '0';
    
    var $fb_enable = '0';
    var $fb_app_id = '';
    var $fb_app_secret = '';
    var $twitter_enable = '0';
    var $twitter_app_id = '';
    var $twitter_app_secret = '';
    var $google_enable = '0';
    var $google_app_id = '';
    var $google_app_secret = '';
    var $openid_enable = '0';
    
    var $login_redirect_url                 = 'index.php?option=com_hybridauth&view=login&task=love';
    var $login_redirect                     = '0';
    var $registration_redirect_url			= 'index.php?option=com_hybridauth&view=login&task=love';
    var	$registration_redirect				= '0';
    
    
	/**
     * Get the URL to the folder containing all media assets
     *
     * @param string	$type	The type of URL to return, default 'media'
     * @return 	string	URL
     */
    public static function getURL($type = 'media', $com='com_hybridauth')
    {
    	$url = parent::getUrl($type, $com);

    	switch($type)
    	{
    		case 'media' :
    			$url = JURI::root(true).'/media/com_hybridauth/';
    			break;
    		case 'css' :
    			$url = JURI::root(true).'/media/com_hybridauth/css/';
    			break;
    		case 'images' :
    			$url = JURI::root(true).'/media/com_hybridauth/images/';
    			break;
    		case 'js' :
    			$url = JURI::root(true).'/media/com_hybridauth/js/';
    			break;
    		case 'hybridauth' :
    			$url = JURI::root(false).'media/com_hybridauth/hybridauth/';
    			break;
    	}

    	return $url;
    }

	/**
     * Get the path to the folder containing all media assets
     *
     * @param 	string	$type	The type of path to return, default 'media'
     * @return 	string	Path
     */
    public static function getPath($type = 'media', $com='com_hybridauth')
    {
    	$path = parent::getPath($type, $com);

    	switch($type)
    	{
    		case 'media' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_hybridauth';
    			break;
    		case 'css' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_hybridauth'.DS.'css';
    			break;
    		case 'images' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_hybridauth'.DS.'images';
    			break;
    		case 'js' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_hybridauth'.DS.'js';
    			break;
    		case 'hybridauth' :
    			$path = JPATH_SITE.DS.'media'.DS.'com_hybridauth'.DS.'hybridauth';
    			break;
    	}

    	return $path;
    }

    /**
    * Returns the query
    * @return string The query to be used to retrieve the rows from the database
    */
    public function _buildQuery()
    {
        $query = "SELECT * FROM #__hybridauth_config";
        return $query;
    }
    
    /**
     * Get component config
     *
     * @acces	public
     * @return	object
     */
    public static function getInstance()
    {
        static $instance;
    
        if (!is_object($instance)) {
            $instance = new Hybridauth();
        }
    
        return $instance;
    }
    
    /**
     * Intelligently loads instances of classes in framework
     *
     * Usage: $object = Hybridauth::getClass( 'HybridauthHelperCarts', 'helpers.carts' );
     * Usage: $suffix = Hybridauth::getClass( 'HybridauthHelperCarts', 'helpers.carts' )->getSuffix();
     * Usage: $categories = Hybridauth::getClass( 'HybridauthSelect', 'select' )->category( $selected );
     *
     * @param string $classname   The class name
     * @param string $filepath    The filepath ( dot notation )
     * @param array  $options
     * @return object of requested class (if possible), else a new JObject
     */
    public static function getClass( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_hybridauth' )  )
    {
        return parent::getClass( $classname, $filepath, $options  );
    }
    
    /**
     * Method to intelligently load class files in the framework
     *
     * @param string $classname   The class name
     * @param string $filepath    The filepath ( dot notation )
     * @param array  $options
     * @return boolean
     */
    public static function load( $classname, $filepath='controller', $options=array( 'site'=>'admin', 'type'=>'components', 'ext'=>'com_hybridauth' ) )
    {
        return parent::load( $classname, $filepath, $options  );
    }

}
?>
