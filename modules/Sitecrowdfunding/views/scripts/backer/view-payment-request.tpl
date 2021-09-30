<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecorwdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-payment-request.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecorwdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php
if ($this->payment_req_obj->request_status == 0):
  $request_status = 'Requested';
elseif ($this->payment_req_obj->request_status == 1):
  $request_status = '<i><font color="red">Deleted</font></i>';
elseif ($this->payment_req_obj->request_status == 2):
  $request_status = '<i><font color="green">Completed</font></i>';
endif;

if ($this->payment_req_obj->payment_status != 'active'):
  $payment_status = 'No';
else:
  $payment_status = 'Yes';
endif;
?>
<div class="global_form_popup" style="width:600px;">
  <div id="manage_order_tab">
    <div class="invoice_order_details_wrap mtop10" style="border-width:1px;width:600px;">
      <ul>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Request Id'); ?> : </strong></div>
          <div><?php echo $this->request_id; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Project Name'); ?> : </strong></div>
          <div><?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('onclick' => 'redirectLink(\'' . $this->project->getHref() . '\')')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Owner Name'); ?> : </strong></div>
          <div><?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('onclick' => 'redirectLink(\'' . $this->userObj->getHref() . '\')')); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Requested Amount'); ?> : </strong></div>
          <div><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->payment_req_obj->request_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Requested Message'); ?> : </strong></div>
          <div><?php echo empty($this->payment_req_obj->request_message) ? '-' : $this->payment_req_obj->request_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Requested Date'); ?> : </strong></div>
          <div><?php echo $this->payment_req_obj->request_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Response Amount'); ?> : </strong></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->payment_req_obj->response_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Response Message'); ?> : </strong></div>
          <div><?php echo empty($this->payment_req_obj->response_message) ? '-' : $this->payment_req_obj->response_message; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Response Date'); ?> : </strong></div>
          <div><?php echo empty($this->payment_req_obj->response_amount) ? '-' : $this->payment_req_obj->response_date; ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Remaining Amount'); ?> : </strong></div>
          <div><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->payment_req_obj->remaining_amount); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Status'); ?> : </strong></div>
          <div><?php echo $this->translate($request_status); ?></div>
        </li>
        <li>
          <div class="invoice_order_info fleft"><strong><?php echo $this->translate('Payment Status'); ?> : </strong></div>
          <div><?php echo $this->translate($payment_status); ?></div>
        </li>
      </ul> 
    </div>
  </div>
<!--   <div class='buttons mtop10'>
    <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
  </div> -->
</div>
<a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a>

<script type="text/javascript">
  function redirectLink(url)
  {
    parent.window.location.href = url;
    parent.Smoothbox.close();
  }
</script>