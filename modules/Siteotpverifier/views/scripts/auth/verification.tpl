<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    verification.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Siteotpverifier/externals/styles/siteotpverifier_style.css');
?>
<div class="otp_phone_settings">
  <h3><?php echo $this->translate("Phone Number Details") ?></h3>
  <?php if( empty($this->otpUser->phoneno) ) : ?>
    <!-- Add mobile no -->
    <?php echo $this->form->render($this) ?>
  <?php else: ?>
    <div class="otp_phone_block_content">
            <!-- Show mobile no edit and delete button -->
      <div class="otp_action_links">
        <span><i class="fa fa-phone" aria-hidden="true"></i><?php echo $this->translate($this->otpUser->country_code . $this->otpUser->phoneno) ?></span>
        <span class="otp_action_links_item">
          <?php
          echo $this->htmlLink(array('route' => 'default', 'module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'edit-number'), '<i class="fa fa-pencil" aria-hidden="true"></i> Edit', array('class' => 'smoothbox', 'title' => 'Edit Phone Number'));
          ?><?php
          echo $this->htmlLink(array('route' => 'default', 'module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'delete-mobile'), '<i class="fa fa-trash" aria-hidden="true"> </i>Remove', array('class' => 'smoothbox', 'title' => 'Remove Phone Number'));
          ?>
        </span>
      </div>
      <?php if( $this->allowOption && !$this->dontallowtwostep && !empty($this->loginAllowed) ): ?>
          <i class="fa fa-shield fa-2x" aria-hidden="true"></i>
            <div class="otp_lock_content">
               <h2><?php echo $this->translate("Use two-factor authentication") ?></h2>
          <?php if( $this->otpUser->enable_verification ): ?>
                <span class="otp_enabled">
                  <a href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'enable-verification', 'enable_verification' => '0'), 'default', true) ?>" title="<?php echo $this->translate('Turn off 2-step verification') ?>"><i class="fa fa-toggle-on fa-2x" aria-hidden="true"></i></a>
                </span>
          <?php else : ?>
                <span class="otp_disabled">
                  <a href="<?php echo $this->url(array('module' => 'siteotpverifier', 'controller' => 'auth', 'action' => 'enable-verification', 'enable_verification' => '1'), 'default', true) ?>" title="<?php echo $this->translate('Turn on 2-step verification') ?>"><i class="fa fa-toggle-off fa-2x" aria-hidden="true"></i></a>
                </span>
          <?php endif; ?>
          <p><?php echo $this->translate("Log in with a code from your phone as well as a password") ?></p>
          </div>
          <!-- toggle button -->
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php if( $this->addnumber ): ?>
  <script type="text/javascript">

  <?php $this->addnumber = false; ?>
    Smoothbox.open(en4.core.baseUrl + 'siteotpverifier/auth/code-verification/type/add');

  </script>
<?php endif; ?>