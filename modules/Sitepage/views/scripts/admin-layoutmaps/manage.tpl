<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
	<div class='seaocore_admin_tabs clr'>
		<?php
    // Render the menu
    //->setUlClass()
		echo $this->navigation()->menu()->setContainer($this->navigation)->render()
		?>
	</div>
<?php endif; ?>
<!-- check extension installed or not -->
<?php
$featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
if ($featureExtension) :?>
<div class='tabs'>
	<ul class="navigation">
		<li>
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'predefinedlayout','action'=>'index'), $this->translate('Layout Editor'), array())
			?>
		</li>
		<li  class="active">
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'layoutmaps','action'=>'manage'), $this->translate('Mapping of layouts'), array())
			?>
		</li>
	</ul>
</div>
<div class='clear seaocore_settings_form'>
	<div class='settings'>
		<form class="global_form">
			<div>
				<h3><?php echo $this->heading; ?> </h3>
				<?php if(count($this->data)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th><?php echo $this->columnName ?></th>
								<th><?php echo $this->translate("Associated Layout") ?></th>
								<th><?php echo $this->translate("Mapping") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->data as $option): ?>
								<tr>
									<td><?php echo $option['title'] ?></td>
									<?php if(!empty($option['layout_id'])):?>
										<td><?php echo $option['layout_name'] ?></td>
									<?php else: ?>
										<td>---</td>
									<?php endif; ?>
									<td width="150">
										<?php if(empty($option['layout_id'])):?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'layoutmaps', 'action' => 'map', 'id' => $option['id'],'name' => $option['name']), $this->translate('Add'), array(
												'class' => 'smoothbox',
												)) ?>
											<?php else: ?>
												<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'layoutmaps', 'action' => 'edit', 'id' =>$option['id'],'name' => $option['name']), $this->translate('Edit'), array(
													'class' => 'smoothbox buttonlink seaocore_icon_edit',
													)) ?>
												<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'layoutmaps', 'action' => 'delete', 'id' =>$option['id'],'name' => $option['name']), $this->translate('Remove'), array(
													'class' => 'smoothbox buttonlink seaocore_icon_delete',
													)) ?>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else:?>
							<br/>
							<div class="tip">
								<span><?php echo $this->translate("No Mapping options are available") ?></span>
							</div>
						<?php endif;?>
					</div>
				</form>
			</div>
		</div>
	<?php else:?>
		<div class="tip">
			<span><?php echo $this->translate("Please install the new Feature extension to use this feature.") ?></span>
		</div>
	<?php endif;?>
