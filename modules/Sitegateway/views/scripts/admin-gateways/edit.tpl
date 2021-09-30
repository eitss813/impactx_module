<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    edit.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='clear'>
    <a href="<?php echo $this->url(array('module' => 'sitegateway', 'controller' => 'gateways', "action" => "index"), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/back.png);padding-left:23px;"><?php echo $this->translate("Back to Manage Gateways"); ?></a>
</div>  
<br/>
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
