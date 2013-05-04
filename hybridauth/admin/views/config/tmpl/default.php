<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'common.js', 'media/com_hybridauth/js/'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<?php JFilterOutput::objectHTMLSafe($row); ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

		<div id='onBeforeDisplay_wrapper'>
			<?php 
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onBeforeDisplayConfigForm', array() );
			?>
		</div>                

		<table style="width: 100%;">
			<tbody>
                <tr>
					<td style="vertical-align: top; min-width: 70%;">

					<?php
					echo $this->sliders->startPane( "pane_1" );
					
					echo $this->sliders->startPanel( JText::_( "Connection Settings" ), 'connections' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_ENABLE_FACEBOOK' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'fb_enable', 'class="inputbox"', $this->row->get('fb_enable', '0') ); ?>
			                </td>
			                <td>
			                </td>
						</tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Facebook App ID' ); ?>
                            </th>
                            <td>
                                <input name="fb_app_id" value="<?php echo $this->row->get('fb_app_id', ''); ?>" type="text" size="50"/>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Facebook App Secret Key' ); ?>
                            </th>
                            <td>
                                <input name="fb_app_secret" value="<?php echo $this->row->get('fb_app_secret', ''); ?>" type="text" size="100" />
                            </td>
                            <td>
                                
                            </td>
                        </tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_ENABLE_TWITTER' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'twitter_enable', 'class="inputbox"', $this->row->get('twitter_enable', '0') ); ?>
			                </td>
			                <td>
			                </td>
						</tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Twitter App ID' ); ?>
                            </th>
                            <td>
                                <input name="twitter_app_id" value="<?php echo $this->row->get('twitter_app_id', ''); ?>" type="text" size="50"/>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Twitter App Secret Key' ); ?>
                            </th>
                            <td>
                                <input name="twitter_app_secret" value="<?php echo $this->row->get('twitter_app_secret', ''); ?>" type="text" size="100" />
                            </td>
                            <td>
                            </td>
                        </tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_ENABLE_GOOGLE' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'google_enable', 'class="inputbox"', $this->row->get('google_enable', '0') ); ?>
			                </td>
			                <td>
			                </td>
						</tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Google App ID' ); ?>
                            </th>
                            <td>
                                <input name="google_app_id" value="<?php echo $this->row->get('google_app_id', ''); ?>" type="text" size="50"/>
                            </td>
                            <td>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Google App Secret Key' ); ?>
                            </th>
                            <td>
                                <input name="google_app_secret" value="<?php echo $this->row->get('google_app_secret', ''); ?>" type="text" size="100" />
                            </td>
                            <td>
                            </td>
                        </tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_ENABLE_OPENID' ); ?>
							</th>
			                <td>
								<?php echo JHTML::_('select.booleanlist', 'openid_enable', 'class="inputbox"', $this->row->get('openid_enable', '0') ); ?>
			                </td>
			                <td>
			                </td>
						</tr>
					</tbody>
					</table>
					<?php
					echo $this->sliders->endPanel();
					
					echo $this->sliders->startPanel( JText::_( "COM_HYBRIDAUTH_REDIRECTION" ), 'redirection' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_LOGIN' ); ?>
							</th>
			                <td>
                                <p>
    								<?php echo JHTML::_('select.booleanlist', 'login_redirect', 'class="inputbox"', $this->row->get('login_redirect', '0') ); ?>
                                </p>
                                <p class="tip clear">
                                <?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_LOGIN_TIP' ); ?>
                                </p>
			                </td>
			                <td>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_LOGIN_URL' ); ?>
							</th>
			                <td>
                                <p>
    			                	<input type="text" name="login_redirect_url" value="<?php echo $this->row->get('login_redirect_url', ''); ?>" size="100" />
                                </p>
                                <p class="tip clear">
                                    <?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_LOGIN_URL_TIP' ); ?>
                                </p>
			                </td>
			                <td>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_REGISTRATION' ); ?>
							</th>
			                <td>
                                <p>
    								<?php  echo JHTML::_('select.booleanlist', 'registration_redirect', 'class="inputbox"', $this->row->get('registration_redirect', '0') ); ?>
                                </p>
                                <p class="tip clear">
                                    <?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_REGISTRATION_TIP' ); ?>
                                </p>
			                </td>
			                <td>
			                </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_REGISTRATION_URL' ); ?>
							</th>
			                <td>
                                <p>
    			                	<input type="text" name="registration_redirect_url" value="<?php echo $this->row->get('registration_redirect_url', ''); ?>" size="100" />
                                </p>
                                <p class="tip clear">
                                    <?php echo JText::_( 'COM_HYBRIDAUTH_REDIRECT_ON_REGISTRATION_URL_TIP' ); ?>
                                </p>
			                </td>
			                <td>
			                </td>
						</tr>
						
					</tbody>
					</table>
					<?php
					echo $this->sliders->endPanel();
					
					echo $this->sliders->startPanel( JText::_( "GENERAL_SETTINGS" ), 'general' );
					?>
					
					<table class="adminlist">
					<tbody>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'SET_DATE_FORMAT' ); ?>
                            </th>
                            <td>
                                <input name="date_format" value="<?php echo $this->row->get('date_format', '%a, %d %b %Y, %I:%M%p'); ?>" type="text" size="40"/>
                            </td>
                            <td>
                                <?php echo JText::_( "CONFIG_SET_DATE_FORMAT" ); ?>
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'SHOW_LINKBACK' ); ?>
                            </th>
                            <td>
                                <?php echo JHTML::_('select.booleanlist', 'show_linkback', 'class="inputbox"', $this->row->get('show_linkback', '1') ); ?>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
                        <tr>
                            <th style="width: 25%;">
                                <?php echo JText::_( 'Include Site CSS' ); ?>
                            </th>
                            <td>
                                <?php echo JHTML::_('select.booleanlist', 'include_site_css', 'class="inputbox"', $this->row->get('include_site_css', '1') ); ?>
                            </td>
                            <td>
                                
                            </td>
                        </tr>
					</tbody>
					</table>
					<?php
					echo $this->sliders->endPanel();

					echo $this->sliders->startPanel( JText::_( "Administrator ToolTips" ), 'admin_tooltips' );
					?>
					
					<table class="adminlist">
					<tbody>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Dashboard Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_dashboard_disabled', 'class="inputbox"', $this->row->get('page_tooltip_dashboard_disabled', '0') ); ?>
							</td>
                            <td>
                                
                            </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Configuration Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_config_disabled', 'class="inputbox"', $this->row->get('page_tooltip_config_disabled', '0') ); ?>
							</td>
                            <td>
                                
                            </td>
						</tr>
						<tr>
			            	<th style="width: 25%;">
								<?php echo JText::_( 'Hide Tools Note' ); ?>
							</th>
							<td>
		                        <?php echo JHTML::_('select.booleanlist', 'page_tooltip_tools_disabled', 'class="inputbox"', $this->row->get('page_tooltip_tools_disabled', '0') ); ?>
							</td>
                            <td>
                                
                            </td>
						</tr>
					</tbody>
					</table>
					<?php

                    echo $this->sliders->endPanel();
					
		
					?>
					</td>
					<td style="vertical-align: top; max-width: 30%;">
						
						<?php echo DSCGrid::pagetooltip( JRequest::getVar('view') ); ?>
						
						<div id='onDisplayRightColumn_wrapper'>
							<?php
								$dispatcher = JDispatcher::getInstance();
								$dispatcher->trigger( 'onDisplayConfigFormRightColumn', array() );
							?>
						</div>

					</td>
                </tr>
            </tbody>
		</table>

		<div id='onAfterDisplay_wrapper'>
			<?php 
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger( 'onAfterDisplayConfigForm', array() );
			?>
		</div>
        
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>
