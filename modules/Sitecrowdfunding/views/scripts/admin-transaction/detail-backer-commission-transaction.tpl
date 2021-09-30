<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-backer-commission-transaction.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?> 
<div class="global_form_popup">  
<h3><?php echo 'Transaction Details'; ?></h3>

<dl class="payment_transaction_details">
  <dd>
    <?php echo 'Transaction ID'; ?>
  </dd>
  <dt>
  <?php echo $this->locale()->toNumber($this->transaction_obj->transaction_id) ?>
  </dt>

   <dd>
    <?php echo 'Project Name'; ?>
  </dd>
  <dt>
  <?php if (empty($this->project)) : ?>
    <i><?php echo 'Project Deleted'; ?></i>
  <?php else: ?>
    <?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('title' => $this->project->getTitle(), 'target' => '_blank')) ?>
  <?php endif; ?>
  </dt>

   <dd>
    <?php echo 'Owner Name'; ?>
  </dd>
  <dt>
  <?php if (empty($this->project)) : ?>
    <?php echo '-'; ?>
  <?php else: ?>
    <?php echo $this->htmlLink($this->project->getOwner()->getHref(), $this->project->getOwner()->getTitle(), array('title' => $this->project->getOwner()->getTitle(), 'target' => '_blank')) ?>
  <?php endif; ?>
  </dt> 

  <dd>
    <?php echo 'Payment Gateway'; ?>
  </dd>
  <dt>
  <?php echo ($this->transaction_obj->gateway_id) ? Engine_Api::_()->sitecrowdfunding()->getGatwayName($this->transaction_obj->gateway_id) : 'Unknown Gateway'?>
  </dt> 

   <dd>
    <?php echo 'Payment State'; ?>
  </dd>
  <dt>
  <?php echo ucfirst($this->transaction_obj->state) ?>
  </dt> 

  <dd>
   <?php echo 'Payment Amount'; ?>
  </dd>
  <dt>
  <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->transaction_obj->amount) ?>
  </dt>

  <dd>
   <?php echo 'Message'; ?>
  </dd>
  <dt>
  <?php echo empty($this->message) ? '-' : $this->message; ?>
  </dt>

  <dd>
    <?php echo 'Gateway Transaction ID'; ?>
  </dd>
  <dt>
 <?php
      if (!empty($this->transaction_obj->gateway_transaction_id) && $this->transaction_obj->gateway_id != 3):
        echo $this->htmlLink(array(
         'route' => 'admin_default',
         'module' => 'sitecrowdfunding',
         'controller' => 'payment',
         'action' => 'detail-transaction',
         'transaction_id' => $this->transaction_obj->transaction_id,
            ), $this->transaction_obj->gateway_transaction_id, array(
         'target' => '_blank',
        )); 
      else:
        echo '-';
      endif;
      ?>
  </dt>

  <dd>
    <?php echo 'Date'; ?>
  </dd>
  <dt>
  <?php echo gmdate('M d,Y, g:i A', strtotime($this->transaction_obj->timestamp)) ?>
  </dt> 
  <!-- <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a> -->

</dl>
<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close"; ?></button>
</div>




