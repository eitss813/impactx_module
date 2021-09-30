<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    reset.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<?php if( empty($this->reset) ): ?>

  <?php echo $this->form->render($this) ?>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate("Your password has been reset. Click %s to sign-in.", $this->htmlLink(array('route' => 'user_login'), $this->translate('here'))) ?>
    </span>
  </div>

<?php endif; ?>
