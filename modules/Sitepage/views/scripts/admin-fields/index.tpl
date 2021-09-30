<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
// Render the admin js
echo $this->render('_jsAdmin.tpl')
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

<h3><?php echo $this->translate('Profile Fields for Directory Items / Pages'); ?></h3>
<p>
  <?php echo $this->translate('Profile information will enable page admins to add additional information about their page. This non-generic additional information will help others know more specific details about the page. Below, you can create Profile Types for the Directory Items / Pages on your site, and then create Profile Information Fields for those profile types. Multiple profile types enable you to have different profile information fields for different type of directory items / pages. You can also map the Categories for pages with Profile Types from the "Category-Page Profile Mapping" section such that if a page belongs to a category, it will automatically have the corresponding profile fields. If you have not done this mapping, then the page admin will be able to select the profile type for his page to enter its profile information.<br />You can set the sequence of the profile fields by drag-and-drop.<br />An example use case of this feature would be creating different profile information fields for business and education oriented pages.<br />(Note: Availability of Profile fields to pages also depends on their package; if packages are disabled, then it depends on the member level settings for the page owner.)'); ?>
</p>

<br />

<div class="admin_fields_type">
  <h3><?php echo $this->translate("Editing Profile Information Fields for Page Profile Type:") ?></h3>
  <?php echo $this->formSelect('profileType', $this->topLevelOption->option_id, array(), $this->topLevelOptions) ?>
</div>

<br />

<div class="admin_fields_options">
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addquestion"><?php echo $this->translate('Add Question'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addheading"><?php echo $this->translate('Add Heading'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_renametype"><?php echo $this->translate('Rename Profile Type'); ?></a>
  <?php if (count($this->topLevelOptions) > 1): ?>
    <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_deletetype"><?php echo $this->translate('Delete Profile Type'); ?></a>
  <?php endif; ?>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_addtype"><?php echo $this->translate('Create New Profile Type'); ?></a>
  <a href="javascript:void(0);" onclick="void(0);" class="buttonlink admin_fields_options_saveorder" style="display:none;"><?php echo $this->translate('Save Order'); ?></a>
</div>

<br />


<ul class="admin_fields">
  <?php foreach ($this->secondLevelMaps as $map): ?>
    <?php echo $this->adminFieldMeta($map) ?>
  <?php endforeach; ?>
</ul>

<br />
