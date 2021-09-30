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

<?php if( count($this->navigation) ): ?>
  <div class='seaocore_admin_tabs clr'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear seaocore_settings_form'>
	<div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate("Category to Page Profile Mapping") ?> </h3>
        <p class="form-description">
          <?php echo $this->translate('This mapping will associate a Page Profile Type with a Category. After such a mapping for a category, page admins of pages belonging to that category will be able to fill profile information fields for that profile type. With this mapping, you will also be able to associate a profile type with multiple categories.<br />For information on page profile types, profile fields and to create new profile types or profile fields, please visit the "Profile Fields" section.<br />An example use case of this feature would be associating category books with profile type having profile fields related to books and so on.<br />(Note: Availability of Profile fields to pages also depends on their package; if packages are disabled, then it depends on the member level settings for the page owner.)') ?>
        </p>
        <?php if(count($this->paginator)>0):?>
					<table class='admin_table' width="100%">
						<thead>
							<tr>
								<th align="left"><?php echo $this->translate("Category Name") ?></th>
								<th align="left"><?php echo $this->translate("Associated Profile") ?></th>
								<th align="left"><?php echo $this->translate("Mapping") ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($this->paginator as $category): ?>
								<tr>
									<td><?php echo $this->translate($category->category_name) ?></td>
									<?php if(!empty($category->label)):?>
										<td><?php echo $this->translate($category->label) ?></td>
									<?php else: ?>
										<td>---</td>
									<?php endif; ?>
									<td width="150">
										<?php if(empty($category->profilemap_id)):?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'profilemaps', 'action' => 'map', 'category_id' => $category->category_id), $this->translate('Add'), array(
												'class' => 'smoothbox',
											)) ?>
										<?php else: ?>
											<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'profilemaps', 'action' => 'delete', 'profilemap_id' =>$category->profilemap_id), $this->translate('Remove'), array(
												'class' => 'smoothbox',
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
						<span><?php echo $this->translate("There are currently no categories to be mapped.") ?></span>
					</div>
				<?php endif;?>
			</div>
		</form>
	</div>
</div>
