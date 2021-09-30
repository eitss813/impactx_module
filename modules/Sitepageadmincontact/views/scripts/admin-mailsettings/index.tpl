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


<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<div class='clear sitepage_settings_form'>
  <div class='settings'>
    <div class="tip">
	  	<span>
        <?php $sitemailtemplates = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemailtemplates');?>
        <?php if($sitemailtemplates):?>
					<?php echo $this->translate('To configure the email template, please click %s.',$this->htmlLink(array('route'=>'admin_default','module'=>'sitemailtemplates','controller'=>'settings'), $this->translate('here'), array('target' => '_blank'))); ?>
        <?php else:?>
          <?php echo $this->translate('To configure the email template, please click %s.',$this->htmlLink(array('route'=>'admin_default','module'=>'sitepage','controller'=>'settings','action'=>'email'), $this->translate('here'), array('target' => '_blank'))); ?>
        <?php endif;?>
      </span> 
	  </div>
  </div>
</div>
