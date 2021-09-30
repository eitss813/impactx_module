<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: level.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
  window.addEvent('domready', function () {
    showcommissionType();
  });
  
  function showcommissionType(){
    if(document.getElementById('commission_handling')){
          if(document.getElementById('commission_handling').value == 1) {
            document.getElementById('commission_fee-wrapper').style.display = 'none';
            document.getElementById('commission_rate-wrapper').style.display = 'block';		
          } else{
            document.getElementById('commission_fee-wrapper').style.display = 'block';
            document.getElementById('commission_rate-wrapper').style.display = 'none';
          }
        }
  }
</script>

<h2>
    <?php echo $this->translate('Crowdfunding / Fundraising / Donations Plugin'); ?>
</h2>

<script type="text/javascript">
    var fetchLevelSettings = function(level_id) {
        window.location.href = en4.core.baseUrl + 'admin/sitecrowdfunding/settings/level/id/' + level_id;
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>