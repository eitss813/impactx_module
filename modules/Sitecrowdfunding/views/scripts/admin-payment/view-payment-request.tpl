<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/admin/style_sitecrowdfunding.css'); ?>

<?php
if ($this->payment_req_obj->request_status == 0):
  $request_status = 'Requested';
elseif ($this->payment_req_obj->request_status == 1):
  $request_status = '<i><font color="red">Deleted</font></i>';
elseif ($this->payment_req_obj->request_status == 2):
  $request_status = '<i><font color="green">Complete</font></i>';
endif;
?>

<div class="global_form_popup">
  <div id="manage_order_tab">
    <div class="invoice_order_details_wrap" style="border-width:1px;">
      <ul>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Request Id'; ?></b></div>
          <div><?php echo $this->request_id; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Project Name'; ?></b></div>
          <div><?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('target' => '_blank')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Owner Name'; ?></b></div>
          <div><?php echo $this->htmlLink($this->user_obj->getHref(), $this->user_obj->getTitle(), array('target' => '_blank')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Request Amount'; ?></b></div>
          <div><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->payment_req_obj->request_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Request Message'; ?></b></div>
          <div><?php echo empty($this->payment_req_obj->request_message) ? '-' : $this->payment_req_obj->request_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Request Date'; ?></b></div>
          <div><?php echo $this->payment_req_obj->request_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><?php echo 'Response Amount'; ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->payment_req_obj->response_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Response Message'; ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_message) ? '-' : $this->payment_req_obj->response_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Response Date'; ?></b></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : $this->payment_req_obj->response_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Remaining Amount'; ?></b></div>
          <div><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->payment_req_obj->remaining_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Request Status'; ?></b></div>
          <div><?php echo $request_status; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><b><?php echo 'Payment Status'; ?></b></div>
          <div><?php echo ucfirst($this->payment_req_obj->payment_status); ?></div>
        </li>
      </ul>

    </div>
    <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>
  </div>
<!--   <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Cancel" ?></button>
  </div> -->

</div>



