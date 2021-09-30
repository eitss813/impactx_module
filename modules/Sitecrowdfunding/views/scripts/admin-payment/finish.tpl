<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: finish.tpl 2017-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
  <?php echo 'Crowdfunding / Fundraising / Donations Plugin'; ?>
</h2>


<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>


<div>
  <div>
    <?php if ($this->state == 'pending'): ?>

      <h3>
        <?php echo 'Payment Pending' ?>
      </h3>
      <p class="form-description">
        <?php echo 'Thank you for submitting your payment. Your payment is currently pending - your ad will be activated when we are notified that the payment has completed successfully. Please return to our login page when you receive an email notifying you that the payment has completed.' ?>
      </p>

    <?php elseif ($this->state == 'active'): ?>

      <h3>
        <?php echo 'Payment Successful' ?>
      </h3>
      <p class="form-description">
        <?php echo 'Thank you! Your payment has been completed successfully.' ?>
      </p>

    <?php else: ?>

      <h3>
        <?php echo 'Payment Failed' ?>
      </h3>
      <p class="form-description">
        <?php if (empty($this->error)): ?>
          <?php echo 'Our payment processor has notified us that your payment could not be completed successfully. We suggest that you try again with another credit card or funding source.' ?>
        <?php else: ?>
          <?php echo $this->error ?>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div>
</div>


<?php
$url = $this->url(array('module' => 'sitecrowdfunding', 'controller' => 'payment', 'action' => 'index'), "admin_default", true);
echo 'Now click <a href="' . $url . '">here</a> to go to manage payment request.';
?>