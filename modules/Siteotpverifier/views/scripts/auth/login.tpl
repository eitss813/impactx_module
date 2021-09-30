<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    login.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<?php if( Engine_Api::_()->hasModuleBootstrap("sitelogin") ): ?>
  <?php include APPLICATION_PATH . '/application/modules/Sitelogin/views/scripts/auth/login.tpl'; ?>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>

