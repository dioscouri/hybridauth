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

HybridAuth::load( 'HybridAuthHelperBase', 'helpers._base' );

class HybridAuthHelperUser extends DSCHelperUser
{
    /**
     *
     * @param $string
     * @return unknown_type
     */
    public static function emailExists( $string, $table='users'  ) 
    {
        switch($table)
        {
            case  'users':
            default     :
                $table = '#__users';
        }

        $success = false;
        $database = JFactory::getDBO();
        $string = $database->getEscaped($string);
        $query = "
        SELECT
        *
        FROM
        $table
        WHERE
        `email` = '{$string}'
        LIMIT 1
        ";
        $database->setQuery($query);
        $result = $database->loadObject();
        if ($result) {
            $success = true;
        }
        return $result;
    }
    
    /**
     *
     * Method to validate a user password via PHP
     * @param $pass								Password for validation
     * @param $force_validation		Can forces this method to validate the password, even thought PHP validation is turned off
     *
     * @return	Array with result of password validation (position 0) and list of requirements which the password does not fullfil (position 1)
     */
    function validatePassword( $password, $force_validation=false )
    {
        $errors = array();
        $result = true;
        $defines = HybridAuth::getInstance();
    
        $validate_php = $force_validation || $defines->get( 'password_php_validate', 0 );
        if( !$validate_php ) {
            return array( $result, $errors );
        }
    
        $min_length = $defines->get( 'password_min_length', 5 );
        $req_num = $defines->get( 'password_req_num', 1 );
        $req_alpha = $defines->get( 'password_req_alpha', 1 );
        $req_spec = $defines->get( 'password_req_spec', 1 );
    
        if( strlen( $password ) < $min_length )
        {
            $result = false;
            $errors[] = JText::sprintf("COM_HYBRIDAUTH_PASSWORD_MIN_LENGTH", $min_length);
        }
    
        if( $req_num && !preg_match( '/[0-9]/', $password ) )
        {
            $result = false;
            $errors[] = JText::_("COM_HYBRIDAUTH_PASSWORD_REQ_NUMBER");
        }
    
        if( $req_alpha && !preg_match( '/[a-zA-Z]/', $password ) )
        {
            $result = false;
            $errors[] = JText::_("COM_HYBRIDAUTH_PASSWORD_REQ_ALPHA");
        }
    
        if( $req_spec && !preg_match( '/[\\/\|_\-\+=\."\':;\[\]~<>!@?#$%\^&\*()]/', $password ) )
        {
            $result = false;
            $errors[] = JText::_("COM_HYBRIDAUTH_PASSWORD_REQ_SPEC");
        }
    
        return array( $result, $errors );
    }
    
    /**
     * Returns yes/no
     * @param object
     * @param mixed Boolean
     * @return array
     */
    protected function sendMail(&$user, $details, $useractivation, $guest = false) 
    {
        $com = DSC::getApp();
        $com_name = strtoupper('com_' . $com -> getName());
    
        $lang = JFactory::getLanguage();
        $lang -> load('lib_dioscouri', JPATH_ADMINISTRATOR);
    
        $mainframe = JFactory::getApplication();
    
        $db = JFactory::getDBO();
    
        $name = $user -> get('name');
        $email = $user -> get('email');
        $username = $user -> get('username');
        $activation = $user -> get('activation');
        $password = $details['password2'];
        // using the original generated pword for the email
    
        $usersConfig = JComponentHelper::getParams('com_users');
        // $useractivation = $usersConfig->get( 'useractivation' );
        $sitename = $mainframe -> getCfg('sitename');
        $mailfrom = $mainframe -> getCfg('mailfrom');
        $fromname = $mainframe -> getCfg('fromname');
        $siteURL = JURI::base();
        
        $uri = JURI::getInstance();
        $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));        
        $activation_url = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $activation, false);
        
        $subject = sprintf(JText::_($com_name . '_ACCOUNT_DETAILS_FOR'), $name, $sitename);
        $subject = html_entity_decode($subject, ENT_QUOTES);
    
        if ($useractivation == 1) {
            $message = sprintf(JText::_($com_name . '_EMAIL_MESSAGE_ACTIVATION'), $sitename, $siteURL, $username, $activation_url);
        } else {
            $message = sprintf(JText::_($com_name . '_EMAIL_MESSAGE'), $sitename, $siteURL, $username);
        }
    
        if ($guest) {
            $message = sprintf(JText::_($com_name . '_EMAIL_MESSAGE_GUEST'), $sitename, $siteURL, $username);
        }
    
        $message = html_entity_decode($message, ENT_QUOTES);
    
        //get all super administrator
        $query = 'SELECT name, email, sendEmail' . ' FROM #__users' . ' WHERE LOWER( usertype ) = "super administrator"';
        $db -> setQuery($query);
        $rows = $db -> loadObjectList();
    
        // Send email to user
        if (!$mailfrom || !$fromname) {
            $fromname = $rows[0] -> name;
            $mailfrom = $rows[0] -> email;
        }
    
        $success = $this->doMail($mailfrom, $fromname, $email, $subject, $message);
    
        return $success;
    }
    
    /**
     * Gets an order, formatted for email
     *
     * return html
     */
    protected function getHtmlForEmail( $values, $options=array() )
    {
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin( 'hybridauth' );
    
        JLoader::register( "HybridAuthViewEmails", JPATH_SITE."/components/com_hybridauth/views/emails/view.html.php" );
    
        // tells JView to load the front-end view, and enable template overrides
        $config = array();
        $config['base_path'] = JPATH_SITE."/components/com_hybridauth";
        if ($app->isAdmin())
        {
            // finds the default Site template
            $db = JFactory::getDBO();
            if (version_compare(JVERSION, '1.6.0', 'ge')) {
                // Joomla! 1.6+ code here
                $db -> setQuery("SELECT `template` FROM #__template_styles WHERE `home` = '1' AND `client_id` = '0';");
            } else {
                // Joomla! 1.5 code here
                $db -> setQuery("SELECT `template` FROM #__templates_menu WHERE `menuid` = '0' AND `client_id` = '0';");
            }
            	
            $template = $db->loadResult();
    
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            if (JFolder::exists(JPATH_SITE.'/templates/'.$template.'/html/com_hybridauth/emails'))
            {
                // (have to do this because we load the same view from the admin-side)
                $config['template_path'] = JPATH_SITE.'/templates/'.$template.'/html/com_hybridauth/emails';
            }
        }
        $view = new HybridAuthViewEmails( $config );
    
        $view->set( '_controller', 'emails' );
        $view->set( '_view', 'emails' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        //$view->assign( 'order', $order );
        $view->setLayout( 'email' );
    
        // Perform the requested task
        ob_start();
        $output = $view->loadTemplate();
        ob_end_clean();
    
        return $output;
    }    
}
