<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: bill-details.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>


<?php
if ($this->projectBillObj->status != 'active'):
  $payment_status = 'No';
else:
  $payment_status = 'Yes';
endif;
?>

<script type="text/javascript"> 
  Asset.css('<?php echo $this->layout()->staticBaseUrl
	    . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'?>');
</script>

<div class="sitecrowdfunding_details_view global_form_popup">
	<h3><?php echo $this->translate('Bill Detail'); ?></h3>

  <table class="clr sitecrowdfunding_transaction_details">
    <tbody>
      <tr>
        <td width="200"><strong><?php echo $this->translate('Transaction Id'); ?></strong></td>
        <td> <?php echo $this->transaction->transaction_id; ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Bill Id'); ?></strong></td>
        <td> <?php echo $this->projectBillObj->projectbill_id; ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Project Name'); ?></strong></td>
        <td><?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('onclick' => 'redirectLink(\'' . $this->project->getHref() . '\')')); ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Owner Name'); ?></strong></td>
        <td><?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('onclick' => 'redirectLink(\'' . $this->project->getHref() . '\')')); ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Amount'); ?></strong></td>
        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->projectBillObj->amount); ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Message'); ?></strong></td>
        <td><?php echo empty($this->projectBillObj->message) ? '-' : $this->projectBillObj->message; ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Date'); ?></strong></td>
        <td><?php echo gmdate('M d,Y, g:i A', strtotime($this->projectBillObj->creation_date)); ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Payment'); ?></strong></td>
        <td><?php echo $this->translate($payment_status); ?></td>
      </tr>	
      <tr>
        <td><strong><?php echo $this->translate('Gateway'); ?></strong></td>
        <td><?php echo Engine_Api::_()->sitecrowdfunding()->getGatwayName($this->projectBillObj->gateway_id); ?></td>
      </tr>
      <tr>
        <td><strong><?php echo $this->translate('Gateway Transaction Id'); ?></strong></td>
        <td>
        	<?php if (!empty($this->transaction->gateway_transaction_id)): ?>
          	<a href="sitecrowdfunding/payment/detail-transaction/transaction_id/<?php echo $this->transaction->transaction_id ?>" target="_blank"><?php echo $this->transaction->gateway_transaction_id ?></a>
          <?php else: ?>
              -
          <?php endif; ?>
        </td>
      </tr>
    </tbody>
  </table>
  <div class='buttons'>
  	<button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Cancel") ?></button>
	</div>
</div>

<script type="text/javascript">
  function redirectLink(url)
  {
    parent.window.location.href = url;
    parent.Smoothbox.close();
  }
</script>