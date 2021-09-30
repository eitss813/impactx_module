<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="global_form_popup sitecrowdfunding_dashbord_popup_form">

  <!-- IF THERE IS NO PAYMENT GATEWAY ENABLE -->
  <?php if (!empty($this->gateway_disable)) : ?>
    <!-- IF PAGE ADMIN REQUEST FOR PAYMENT -->
    <?php if (empty($this->req_page_owner)) : ?>
      <div class="tip">
        <span>
          <?php echo $this->translate("Project owner has not enabled any payment gateway. So you can't proceed for payment request. Please contact to project owner.") ?>
          <div class="buttons mtop10">
            <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
          </div>
        </span>
      </div>
      <?php
      return;
    endif;
    ?>

    <div class="tip">
      <span>
  <?php echo $this->translate('You have not configured or enabled the payment gateways for your project. Please first configure and enable payment gateways to request for payment. Please %1$sclick here%2$s to go to Manage Gateways.', '<a href="javascript:void(0)" onclick="gatewayEnable();" class="bold">', '</a>') ?>
        <div class="buttons mtop10">
          <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
<?php elseif (!empty($this->not_allowed_for_payment_request)) : ?>
    <div class="tip">
      <span>
  <?php echo $this->translate('Sorry, you are not allowed to send request for this amount as the balance amount is less than the threshold amount. So, please request when your balance amount is greater than or equal to the threshold amount.') ?>
        <div class="buttons mtop10">
          <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php else: ?>
    <?php echo $this->form->render($this); ?>
<?php endif; ?>

  <script type="text/javascript">
    function gatewayEnable()
    {
      parent.window.location.href = '<?php echo $this->url(array('action' => 'payment-info', 'project_id' => $this->project_id), 'sitecrowdfunding_specific', true) ?>';
      parent.Smoothbox.close();
    }
  </script>
</div>
</div>	
</div>	

</div>