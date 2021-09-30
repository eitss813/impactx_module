<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<h2>
  <?php echo SITECORETHEME_PLUGIN_NAME; ?>
</h2>

<div  class='seaocore_admin_tabs tabs clr'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>