<?php

 /**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblogpackage
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: detail.tpl 2020-03-26 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */
 
?>
<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate("Transaction Details") ?>
</h2>
<dl class="payment_transaction_details">
  <dd><?php echo $this->translate('Transaction ID') ?></dd>
  <dt><?php echo $this->locale()->toNumber($this->transaction->getIdentity()) ?></dt>
  <dd><?php echo $this->translate('Blog Owner') ?></dd>
  <dt><?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?></dt>
  <dd><?php echo $this->translate('Blog Title') ?></dd>
  <dt><a href="<?php echo $this->item->getHref(); ?>"  target='_blank' title="<?php echo  ucfirst($this->item->getTitle()) ?>">
	<?php echo $this->item->getTitle(); ?></a>
  </dt>
  <dd><?php echo $this->translate('Payment Gateway') ?></dd>
  <dt><?php echo $this->translate($this->gateway->title) ?></dt>
  <dd><?php echo $this->translate('Payment Status') ?></dd>
  <dt><?php echo $this->translate(ucfirst($this->transaction->state)) ?></dt>
  <dd><?php echo $this->translate('Amount') ?></dd>
  <dt>
    <?php echo $this->locale()->toCurrency($this->transaction->total_amount, $this->transaction->currency_symbol) ?>
    <?php echo $this->translate('(%s)', $this->transaction->currency_symbol) ?>
  </dt>
  <dd><?php echo $this->translate('Gateway Transaction ID/Profile ID') ?></dd>
  <dt>
    <?php if( !empty($this->transaction->gateway_transaction_id) ): ?>
      <b> <?php echo $this->transaction->gateway_transaction_id; ?></b>
    <?php elseif( !empty($this->transaction->gateway_profile_id) ): ?>
      <b> <?php echo $this->transaction->gateway_profile_id; ?></b>
    <?php else: ?>
      -
    <?php endif; ?>
  </dt>
  <dd>
    <?php echo $this->translate('Date') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toDateTime($this->transaction->creation_date) ?>
  </dt>
  <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo $this->translate('Close'); ?></button>
</dl>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
