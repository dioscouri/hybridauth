<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_hybridauth/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<table style="width: 100%;">
	<tr>
		<td style="width: 70%; max-width: 70%; vertical-align: top; padding: 0px 5px 0px 5px;">

			<?php
			$modules = JModuleHelper::getModules("hybridauth_dashboard_main");
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$attribs 	= array();
			$attribs['style'] = 'xhtml';
			foreach ( @$modules as $mod )
			{
				echo $renderer->render($mod, $attribs);
			}
			?>
		</td>
		<td style="vertical-align: top; width: 30%; min-width: 30%; padding: 0px 5px 0px 5px;">

			<?php
			$modules = JModuleHelper::getModules("hybridauth_dashboard_right");
			$document	= JFactory::getDocument();
			$renderer	= $document->loadRenderer('module');
			$attribs 	= array();
			$attribs['style'] = 'xhtml';
			foreach ( @$modules as $mod )
			{
				$mod_params = new DSCParameter( $mod->params );
				if ($mod_params->get('hide_title', '1')) { $mod->showtitle = '0'; }
				echo $renderer->render($mod, $attribs);
			}
			?>
		</td>
	</tr>
	</table>

	<?php echo $this->form['validate']; ?>
</form>