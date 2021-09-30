<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: detail.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
<h2 class="payment_transaction_detail_headline">
  <?php echo $this->translate("Transaction Details") ?>
</h2>

<dl class="payment_transaction_details">
  <dd>
    <?php echo $this->translate('Transaction ID') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toNumber($this->transaction->transaction_id) ?>
  </dt>

  <dd>
    <?php echo $this->translate('User Name') ?>
  </dd>
  <dt>
    <?php if( $this->user && $this->user->getIdentity() ): ?>
      <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?> 
    <?php else: ?>
      <i><?php echo $this->translate('Deleted Project Owner') ?></i>
    <?php endif; ?>
  </dt>
  <dd>
     <?php echo $this->translate('Project Title') ?>
  </dd>
  <dt>
      <a href="<?php echo $this->url(array('project_id' => $this->transaction->source_id, 'slug' => $this->project->getSlug()), "sitecrowdfunding_entry_view"); ?>"  target='_blank' title="<?php echo ucfirst($this->title); ?>"> 
						<?php echo $this->title; ?></a>
  </dt>
  <dd>
    <?php echo $this->translate('Payment Gateway') ?>
  </dd>
  <dt>
    <?php if( $this->gateway ): ?>
      <?php echo $this->translate($this->gateway->title) ?>
    <?php else: ?>
      <i><?php echo $this->translate('Unknown Gateway') ?></i>
    <?php endif; ?>
  </dt> 
  <dd>
    <?php echo $this->translate('Payment State') ?>
  </dd>
  <dt>
    <?php echo $this->translate(ucfirst($this->transaction->state)) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Payment Amount') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toCurrency($this->transaction->amount, $this->transaction->currency) ?> 
  </dt>  
  <dd>
    <?php echo $this->translate('Date') ?>
  </dd>
  <dt>
    <?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
  </dt> 
  <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo $this->translate('Close'); ?></button>
</dl>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>

</div>