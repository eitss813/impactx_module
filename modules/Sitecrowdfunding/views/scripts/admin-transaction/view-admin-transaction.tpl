<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-admin-transaction.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
<h2><?php echo 'Transaction Details'; ?></h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo 'Transaction ID'; ?>
  </dd>
  <dt>
  <?php echo $this->locale()->toNumber($this->transaction_id); ?>
  </dt>

  <dd>
    <?php echo 'Project Id' ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->project->getHref(), $this->locale()->toNumber($this->project_id), array('target' => '_blank')) ?>
  </dt>

  <dd>
    <?php echo 'Project Name'; ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->project->getHref(), $this->project->getTitle(), array('target' => '_blank')); ?>
  </dt>

  <dd>
    <?php echo 'Owner Name'; ?>
  </dd>
  <dt>
  <?php echo $this->htmlLink($this->userObj->getHref(), $this->userObj->getTitle(), array('target' => '_blank')); ?>
  </dt>

  <dd>
    <?php echo 'Payment Gateway'; ?>
  </dd>
  <dt>
  <?php if ($this->payment_gateway): ?>
    <?php echo $this->payment_gateway; ?>
  <?php else: ?>
    <i><?php echo 'Unknown Gateway'; ?></i>
  <?php endif; ?>
  </dt>

  <dd>
    <?php echo 'Payment Type'; ?>
  </dd>
  <dt>
  <?php echo ucfirst($this->payment_type) ?>
  </dt>

  <dd>
    <?php echo 'Payment State'; ?>
  </dd>
  <dt>
  <?php echo ucfirst($this->payment_state); ?>
  </dt>

  <dd>
    <?php echo 'Response Amount'; ?>
  </dd>
  <dt>
  <?php echo $this->payment_amount; ?>
  </dt>

  <dd>
    <?php echo 'Gateway Transaction ID'; ?>
  </dd>
  <dt>
  <?php if (!empty($this->gateway_transaction_id)): ?>
    <?php
    echo $this->htmlLink(array(
     'route' => 'admin_default',
     'module' => 'sitecrowdfunding',
     'controller' => 'payment',
     'action' => 'detail-transaction',
     'transaction_id' => $this->transaction_id,
        ), $this->gateway_transaction_id, array(
     'target' => '_blank',
    ))
    ?>
  <?php else: ?>
    -
  <?php endif; ?>
  </dt>

  <dd>
    <?php echo 'Date'; ?>
  </dd>
  <dt>
  <?php echo gmdate('M d,Y, g:i A', strtotime($this->date)) ?>
  </dt>
<!-- 
  <a style="position: fixed;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close fright"></a> -->

</dl>

<div class='buttons'>
  <button type='button' name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo "Close"; ?></button>
</div>

</div>