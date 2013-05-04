<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_hybridauth/js/'); ?>
<?php
if ( !class_exists('HybridAuth') ) {
    JLoader::register( "HybridAuth", JPATH_ADMINISTRATOR.DS."components".DS."com_hybridauth".DS."defines.php" );
}
HybridAuth::load( 'HybridAuthModelConfig', 'models.config' );
$model = new HybridAuthModelConfig();
$config = $model->getHAConfigArray();
$lang = JFactory::getLanguage();
$lang->load('com_hybridauth');
?>

<div class="hybridauth">

<h2><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN" ); ?></h2>  

<h3><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN_WITH_FOLLOWING" ); ?>:</h3>

<ul id="sso_providers" class="flat">
    <?php if (!empty($config["providers"]["Facebook"]["enabled"])) { ?>
    <li>
        <a href="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=facebook" ); ?>">
            <img src="<?php echo HybridAuth::getURL('images') . 'button_facebook_connect.png'; ?>" />
        </a>
    </li>
    <?php } ?>
    
    <?php if (!empty($config["providers"]["Twitter"]["enabled"])) { ?>
    <li>
        <a href="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=twitter" ); ?>">
            <img src="<?php echo HybridAuth::getURL('images') . 'button_twitter.png'; ?>" />
        </a>
    </li>
    <?php } ?>
    
    <?php if (!empty($config["providers"]["Google"]["enabled"])) { ?>
    <li>
        <a href="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=google" ); ?>">
            <img src="<?php echo HybridAuth::getURL('images') . 'button_google_login.png'; ?>" />
        </a>
    </li>
    <?php } ?>
    
    <?php if (!empty($config["providers"]["OpenID"]["enabled"])) { ?>
    <li>
        <a href="javascript:void(0);" onclick="hybridauthShowHideDiv('openid_form');">
            <img src="<?php echo HybridAuth::getURL('images') . 'button_openid.png'; ?>" />
        </a>
        
        <div id="openid_form" style="display: none;">
            <form method="post" action="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=openid" ); ?>">
                <?php echo JText::_( "COM_HYBRIDAUTH_OPENID_URL" ); ?>
                <div>
                    <input type="text" size="50" value="" id="openid_url" name="openid_url" />
                    <input class="button" type="submit" value="<?php echo JText::_( "COM_HYBRIDAUTH_LOGIN" ); ?>" />
                    <?php echo JHtml::_('form.token'); ?>
                </div>
            </form> 
        </div>
    </li>
    <?php } ?>
    
</ul>

<h3><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN_WITH_EMAIL" ); ?>:</h3>

<ul>
    <li>
        <form method="post" action="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=login&type=default" ); ?>">
        
            <fieldset>
                <div class="login-fields">
                    <label class="" for="username" id="username-lbl"><?php echo JText::_( "COM_HYBRIDAUTH_USERNAME" ); ?></label> 
                    <input type="text" size="25" class="validate-username" value="" id="username" name="username">
                </div>
                <div class="login-fields">
                    <label class="" for="password" id="password-lbl"><?php echo JText::_( "COM_HYBRIDAUTH_PASSWORD" ); ?></label> 
                    <input type="password" size="25" class="validate-password" value="" id="password" name="password">
                </div>
                <button class="button" type="submit"><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN" ); ?></button>
                <?php echo JHtml::_('form.token'); ?>
            </fieldset>
            
        </form>
    </li>

<?php 
if(version_compare(JVERSION,'1.6.0','ge')) {
    // Joomla! 1.6+ code here
    ?>
    
    <li>
        <a href="<?php echo JRoute::_( "index.php?option=com_users&view=reset" ); ?>">
            <?php echo JText::_( "COM_HYBRIDAUTH_FORGOT_PASSSWORD" ); ?>
        </a>
    </li>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
		<?php echo JText::_('COM_HYBRIDAUTH_LOGIN_REMIND'); ?></a>
	</li>
	<?php
	$usersConfig = JComponentHelper::getParams('com_users');
	if ($usersConfig->get('allowUserRegistration')) : ?>
	<li>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
			<?php echo JText::_('COM_HYBRIDAUTH_LOGIN_REGISTER'); ?></a>
	</li>
	<?php endif; ?>
    
    <?php
} else {
    // Joomla! 1.5 code here
    ?>
    <li>
        <a href="<?php echo JRoute::_( "index.php?option=com_user&view=reset" ); ?>">
        <?php echo JText::_( "COM_HYBRIDAUTH_FORGOT_PASSSWORD" ); ?>
        </a>
    </li>
    <li>
    	<a href="<?php echo JRoute::_('index.php?option=com_user&view=remind'); ?>">
        <?php echo JText::_('COM_HYBRIDAUTH_LOGIN_REMIND'); ?></a>
    </li>
        <?php
        $usersConfig = JComponentHelper::getParams('com_users');
        if ($usersConfig->get('allowUserRegistration')) : ?>
        <li>
        	<a href="<?php echo JRoute::_('index.php?option=com_user&view=register'); ?>">
            <?php echo JText::_('COM_HYBRIDAUTH_LOGIN_REGISTER'); ?></a>
        </li>
        <?php endif; ?>        
    <?php    
}
?>
</ul>

</div>