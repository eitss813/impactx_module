<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: index.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php 
// from fields module
echo $this->render('_jsAdmin.tpl'); ?>

<?php include APPLICATION_PATH .  '/application/modules/Sesblog/views/scripts/dismiss_message.tpl';?>

<div class='clear sesbasic-form'>
  <div>
    <?php if( count($this->subnavigation) ): ?>
      <div class='sesbasic-admin-sub-tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->subnavigation)->render(); ?>
      </div>
    <?php endif; ?>
    <div class="sesbasic-form-cont">
      <div class='settings sesbasic_admin_form'>
				<h3><?php echo $this->translate('Review Parameters Custom Fields'); ?></h3>
				<p>
				  <?php echo $this->translate('You might want your users to provide some more information about their blogs review. Here, you can create some custom fields of your choice and requirement.<br /><br />To reorder the custom fields, click on their names and drag them up or down. If you want to show different sets of fields to different types of categories, you can create multiple "Profile Types". While adding / editing a category, from the "Map Profile Type” field, you can map and associate Profile Types with Categories.'); ?>
				</p>
				<br />
				<div class="admin_fields_type">
				  <h3><?php echo $this->translate("Editing Profile Type:") ?></h3>
				  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
				</div>
				<br />
				<div class="admin_fields_options">
				  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate('Add Question'); ?></a>
				  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate('Rename Profile Type'); ?></a>
				  <?php if (count($this->topLevelOptions) > 1): ?>
				  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate('Delete Profile Type'); ?></a>
				  <?php endif; ?>
				  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate('Create New Profile Type'); ?></a>
				  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order'); ?></a>
				</div>
				<br />
				<ul class="admin_fields clear">
				  <?php foreach ($this->secondLevelMaps as $map): ?>
				  <?php echo $this->adminFieldMeta($map) ?>
				  <?php endforeach; ?>
				</ul>
      </div>
    </div>
  </div>
</div>
