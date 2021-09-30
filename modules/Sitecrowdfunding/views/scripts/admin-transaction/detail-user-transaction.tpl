 <?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail-user-transaction.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
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
    <?php echo 'Backer Id' ?>
  </dd>
  <dt>
   <?php echo $this->backer_id; ?>
  </dt>

   <dd>
    <?php echo 'Buyer Name'; ?>
  </dd>
  <dt>
  <?php echo empty($this->transaction_obj->user_id) ? '-' : $this->htmlLink($this->user_obj->getHref(), $this->user_obj->getTitle(), array('target' => '_blank')) ?>
  </dt> 

  <dd>
    <?php echo 'Payment Gateway'; ?>
  </dd>
  <dt>
  <?php if (empty($this->gateway_name)): ?>
    <div><i><?php echo 'Unknown Gateway' ?></i></div>
  <?php else: ?>
    <div><?php echo $this->gateway_name ?></div>
  <?php endif; ?>
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

<!--   <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a> -->

<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close"; ?></button>
</div>

</dl>


 








