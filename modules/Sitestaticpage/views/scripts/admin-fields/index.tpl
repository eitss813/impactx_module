<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $option_id = $this->topLevelOption->option_id; ?>
<?php echo $this->render('application/modules/Sitestaticpage/views/scripts/_jsAdmin.tpl');?>

<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin'; ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo 'Static Pages / HTML Blocks Forms'; ?> </h3>
<br/> 
<p>
  <?php echo 'This plugin enables you to create and manage multiple forms which can in-turn be embedded into the static pages that you create for your website. Such forms can be used for a variety of purposes to gather information from your users and visitors. For each form, you can specify multiple email addresses to which the form’s responses will be sent. The form labels / names are only indicative and will not be shown to users. Below, you can create the fields / questions for the forms. To re-order the questions, click on their names and drag them up or down.
Please note that when you make changes to any form, they will not be reflected back to the static pages in which they are embedded. You will need to edit the static pages, remove the old form from them, and re-embed the modified form to get those changes in the static pages.' ?>
</p>

<br> <br>
<div class="admin_fields_type">
  <h3><?php echo "Editing Form:" ?></h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br> <br>
<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo 'Add Question'; ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo 'Add Heading'; ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo 'Edit Form Meta-data'; ?></a>
  <?php if (count($this->topLevelOptions) > 1): ?>
    <a href="javascript:void(0);" onclick="Javascript:confirm('Before deleting this form, please ensure that you have removed this form from all static pages where embedded code of this form has been added.');" class="buttonlink admin_fields_options_deletetype"><?php echo 'Delete Form'; ?></a>
  <?php endif; ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo 'Create New Form'; ?></a>
  <a href="<?php echo $this->url(array('module' => 'sitestaticpage', 'controller'=>'fields','action'=>'display-user-data', 'form_id' => $option_id),'admin_default',TRUE);?>" class="buttonlink" target="_blank">
    <?php echo "User Form Data";?>
</a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo 'Save Order'; ?></a>
</div>

<br><br>
<ul class="admin_fields">
  <?php foreach ($this->secondLevelMaps as $map): ?>
    <?php echo $this->adminFieldMeta($map) ?>
  <?php endforeach; ?>
</ul>
<br />