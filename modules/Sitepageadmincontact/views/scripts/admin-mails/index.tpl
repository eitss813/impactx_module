<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageadmincontact
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-11-15 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/pluginLink.tpl'; ?>
<h2><?php echo $this->translate('Directory / Pages - Contact Page Owners Extension') ?></h2>

<script type="text/javascript">
  window.addEvent('domready', function() {
    var pagecontactEmailDemo = "<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('page.contactemail.demo',1);?>";   

showOption(pagecontactEmailDemo);
  });
function showOption(option) {
  if(option == true) {
    $('page_contactemail_admin-wrapper').style.display = 'block';
  } else {
    $('page_contactemail_admin-wrapper').style.display = 'none';
  }
}
</script>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (!empty($this->messageSent)): ?>
  <ul class="form-notices" >
    <li>
      <?php echo $this->successMessge; ?>
    </li>
  </ul>
<?php endif; ?>
<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>
</div>


<style type="text/css">
.defaultSkin iframe {
 height: 250px !important;
 width: 625px !important;
}
</style>