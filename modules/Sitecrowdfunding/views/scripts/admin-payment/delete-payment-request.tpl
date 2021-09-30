<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete-payment-request.tpl 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
  <div>
    <h3><?php echo "Cancel Payment Request" ?></h3>
    <p>
      <?php echo "Are you sure that you want to cancel this payment request? This payment request will not be recoverable after being canceled." ?>
    </p>
    <br />
    <p>
      <button type='submit'><?php echo "Cancel Payment Request" ?></button>
      <?php echo " or " ?> 
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo "cancel" ?></a>
    </p>
  </div>
</form>