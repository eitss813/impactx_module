<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegateway
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    detail.tpl 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="payment_transaction_detail_headline">
  <?php echo "Transaction Details" ?>
</h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo 'Transaction ID' ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toNumber($this->transaction->transaction_id) ?>
  </dt>

  <dd>
    <?php echo 'Member' ?>
  </dd>
  <dt>
    <?php if( $this->user && $this->user->getIdentity() ): ?>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>
      <?php //echo $this->user->__toString() ?>
      <?php if( !_ENGINE_ADMIN_NEUTER ): ?>
        <?php echo $this->translate('(%1$s)', '<a href="mailto:' .
            $this->escape($this->user->email) . '">' . $this->user->email . '</a>') ?>
      <?php endif; ?>
    <?php else: ?>
      <i><?php echo 'Deleted Member' ?></i>
    <?php endif; ?>
  </dt>

  <dd>
    <?php echo 'Payment Gateway' ?>
  </dd>
  <dt>
      <?php echo $this->gatewayName ?>
  </dt>

  <dd>
    <?php echo 'Payment Type' ?>
  </dd>
  <dt>
    <?php echo ucfirst($this->transaction->type) ?>
  </dt>

  <dd>
    <?php echo 'Payment State' ?>
  </dd>
  <dt>
    <?php echo ucfirst($this->transaction->state) ?>
  </dt>

  <dd>
    <?php echo 'Payment Amount' ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toCurrency($this->transaction->amount, $this->transaction->currency) ?>
    <?php echo $this->translate('(%s)', $this->transaction->currency) ?>
  </dt>

  <dd>
    <?php echo 'Gateway Transaction ID' ?>
  </dd>
  <dt>
    <?php if( !empty($this->transaction->gateway_transaction_id) ): ?>
      <?php echo $this->transaction->gateway_transaction_id;
//      echo $this->htmlLink(array(
//          'route' => 'admin_default',
//          'module' => 'sitegateway',
//          'controller' => 'index',
//          'action' => 'detail-transaction',
//          'transaction_id' => $this->transaction->transaction_id,
//        ), $this->transaction->gateway_transaction_id, array(
//          //'class' => 'smoothbox',
//          'target' => '_blank',
//      )) 
              ?>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>

  <?php if( !empty($this->transaction->gateway_parent_transaction_id) ): ?>
  <dd>
    <?php echo 'Gateway Parent Transaction ID' ?>
  </dd>
  <dt>
    <?php $this->transaction->gateway_parent_transaction_id;
//    echo $this->htmlLink(array(
//        'route' => 'admin_default',
//        'module' => 'sitegateway',
//        'controller' => 'index',
//        'action' => 'detail-transaction',
//        'transaction_id' => $this->transaction->transaction_id,
//        'show-parent' => 1,
//      ), $this->transaction->gateway_parent_transaction_id, array(
//        //'class' => 'smoothbox',
//        'target' => '_blank',
//    ))
            ?>
  </dt>
  <?php endif; ?>

  <?php if( !empty($this->transaction->gateway_order_id) ): ?>
    <dd>
      <?php echo 'Gateway Order ID' ?>
    </dd>
    <dt>
      <?php if( !empty($this->transaction->gateway_order_id) ): ?>
        <?php echo $this->transaction->gateway_order_id;
//        echo $this->htmlLink(array(
//            'route' => 'admin_default',
//            'module' => 'sitegateway',
//            'controller' => 'index',
//            'action' => 'detail-order',
//            'transaction_id' => $this->transaction->transaction_id,
//          ), $this->transaction->gateway_order_id, array(
//            //'class' => 'smoothbox',
//            'target' => '_blank',
//        )) 
                ?>
      <?php else: ?>
        -
      <?php endif; ?>
    </dt>
  <?php endif; ?>
  <dd>
    <?php echo 'Date' ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
  </dt>
</dl>