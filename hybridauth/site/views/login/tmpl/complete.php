<?php defined('_JEXEC') or die('Restricted access'); ?>

<div class="hybridauth">

<h2><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN_INCOMPLETE" ); ?></h2>  

<h3><?php echo JText::_( "COM_HYBRIDAUTH_PLEASE_PROVIDE_FOLLOWING" ); ?>:</h3>

<form method="post" action="<?php echo JRoute::_( "index.php?option=com_hybridauth&view=login&task=saveProfile&type=" . $this->hybridauth_login_type ); ?>">

    <fieldset>
        <?php foreach ($this->incomplete_fields as $field) { ?>
        <div class="row">
            <label class="" for="<?php echo $field; ?>" id="<?php echo $field; ?>-lbl"><?php echo JText::_( "COM_HYBRIDAUTH_" . strtoupper( $field ) ); ?></label> 
            <input type="text" size="25" class="validate-<?php echo $field; ?>" value="" id="<?php echo $field; ?>" name="<?php echo $field; ?>">
        </div>
        <?php } ?>
        
        <button class="button" type="submit"><?php echo JText::_( "COM_HYBRIDAUTH_LOGIN" ); ?></button>
        <?php echo JHtml::_('form.token'); ?>
        
    </fieldset>
    
</form>

</div>