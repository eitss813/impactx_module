<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php echo $this->render('_jsAdmin.tpl') ?>

<h2>
    Crowdfunding / Fundraising / Donations Plugin
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
    </div>
<?php endif; ?>

<h3>Profile Fields for Projects</h3>

<p>
   Profile information will enable Project Owner to add additional information about their Project. This non-generic additional information will help others know more specific details about the Project. Below, you can create Profile Types for the Projects on your site, and then create Profile Information Fields for those profile types. Multiple profile types enable you to have different profile information fields for different type of Projects. You can also map the Categories for Projects with Profile Types from the "Category-Project Profile Mapping" section such that if a Project belongs to a category, it will automatically have the corresponding profile fields.
</p>

<br />

<div class="admin_fields_type">
    <h3>Editing Profile Information Fields for Project Profile Type:</h3>
    <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion">    Add Question</a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading">Add Heading</a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype">Rename Profile Type</a>
    <?php if (count($this->topLevelOptions) > 1): ?>
        <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype">Delete Profile Type</a>
    <?php endif; ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype">Create New Profile Type</a>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;">Save Order</a>
</div>

<br />

<ul class="admin_fields">
    <?php foreach ($this->secondLevelMaps as $map): ?>
        <?php echo $this->adminFieldMeta($map) ?>
    <?php endforeach; ?>
</ul>

<br />
