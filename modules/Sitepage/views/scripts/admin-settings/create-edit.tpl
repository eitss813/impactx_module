<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 $coreSettings = Engine_Api::_()->getApi('settings', 'core');
?>

<h2 class="fleft"><?php echo $this->translate('Directory / Pages Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/manageExtensions.tpl'; ?>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
    <div class='seaocore_admin_tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php
        echo $this->form->render($this);
        ?>
    </div>
</div>

<script type="text/javascript">
  var display_msg=0;
  window.addEvent('domready', function() {
    showDescription('<?php echo $coreSettings->getSetting('sitepage.description.allow', 1) ?>');
    display_msg=1;
  });

  function showDescription(option) {
    if($('sitepage_requried_description-wrapper')) {
      if(option == 1) {
        $('sitepage_requried_description-wrapper').style.display='block';
      } else{
        $('sitepage_requried_description-wrapper').style.display='none';
      }
    }
  }

</script>