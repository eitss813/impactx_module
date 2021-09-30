<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<!-- IF ADMIN HAS DELETED THE PAYMENT REQUEST -->
<?php if (!empty($this->sitecrowdfunding_payment_request_deleted)) : ?> 
  <div class="tip">
    <span>
      <?php echo $this->translate("Site administrator has already cancelled this payment request."); ?>
      <div class='buttons'>
        <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
      </div>
    </span>
  </div>
  <?php
  return;
endif;
?>

<!-- IF ADMIN HAS COMPLETED THE PAYMENT REQUEST -->
<?php if (!empty($this->sitecrowdfunding_payment_request_completed)) : ?> 
  <div class="tip">
    <span>
      <?php echo $this->translate("Site administrator has completed this payment request. So you can't cancelled this payment request."); ?>
      <div class='buttons'>
        <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
      </div>
    </span>
  </div>
  <?php
  return;
endif;
?>

<!-- IF ADMIN IS RESPONDING THE AMOUNT -->
<?php if (!empty($this->sitecrowdfunding_admin_responding_request)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("Site administrator is responding your request. So you can't cancelled this payment request."); ?>
      <div class='buttons'>
        <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
      </div>
    </span>
  </div>
  <?php
  return;
endif;
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo $this->translate("Cancel Payment Request") ?></h3>
    <p>
      <?php echo $this->translate("Are you sure that you want to cancel this payment request? This payment request will not be recoverable after being cancelled.") ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo $this->translate("Cancel Payment Request") ?></button>
      <?php echo $this->translate(" or ") ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
    </p>
  </div>
</form>