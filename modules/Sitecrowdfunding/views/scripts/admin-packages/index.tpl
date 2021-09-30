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
<script type="text/javascript">
  window.addEvent('domready', function() {
    showUiOption();
  });
  
  function showUiOption() 
  {
    if($('sitecrowdfunding_package_view-wrapper')) {
      if($('sitecrowdfunding_package_setting-1').checked) { 
        $('sitecrowdfunding_package_view-wrapper').style.display='block';	
      }
      else{
        $('sitecrowdfunding_package_view-wrapper').style.display='none';
      }		
    }
    if($('sitecrowdfunding_package_information-wrapper')) {
      if($('sitecrowdfunding_package_setting-1').checked) { 
        $('sitecrowdfunding_package_information-wrapper').style.display='block';	
      }
      else{
        $('sitecrowdfunding_package_information-wrapper').style.display='none';
      }		
    }  
  }
  </script>
<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>
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

