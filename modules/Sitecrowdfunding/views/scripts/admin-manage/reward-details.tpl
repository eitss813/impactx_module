<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: reward-details.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
 <?php if(!empty($this->reward_id)) : ?>

<h2 class="payment_transaction_detail_headline ">
  <?php echo $this->translate("Reward Details"); ?>
</h2>

<dl class=" payment_transaction_details"> 

  <dd>
    <?php echo $this->translate('Project:'); ?>
  </dd> 
  <dt>
      <a href="<?php echo $this->url(array('project_id' => $this->project->project_id, 'slug' => $this->project->getSlug()), "sitecrowdfunding_entry_view"); ?>"  target='_blank' title="<?php echo ucfirst($this->project->title); ?>">
                <?php echo $this->string()->truncate($this->project->title, 60); ?></a> 
  </dt>
  <dd>
    <?php echo $this->translate('Project Owner Name:'); ?>
  </dd>
  <dt>
  <?php if ($this->projectOwner && $this->projectOwner->getIdentity()): ?>
    <?php echo $this->htmlLink($this->projectOwner->getHref(), $this->projectOwner->getTitle(), array('target' => '_parent')) ?> 
  <?php else: ?>
    <i><?php echo $this->translate('Deleted Project Owner'); ?></i>
<?php endif; ?>
  </dt> 
  <dd>
    <?php echo $this->translate('Back Amount:'); ?>
  </dd>
  <dt>
  <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrencyAdmin($this->reward->pledge_amount); ?>
  </dt>

  <dd>
    <?php echo $this->translate('Creation Date:'); ?>
  </dd>

  <dt>
  <?php echo $this->locale()->toDateTime($this->reward->creation_date) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Delivery Date:'); ?>
  </dd>

  <dt>
  <?php echo $this->locale()->toDateTime($this->reward->delivery_date) ?>
  </dt>

  <dd>
    <?php echo $this->translate('Quantity:'); ?>
  </dd>
  <dt>
    <?php echo ($this->reward->quantity) ? $this->translate($this->reward->quantity) : 'UNLIMITED' ?>
  </dt>

   <dd>
    <?php echo $this->translate('Count of Selected Rewards:'); ?>
  </dd>

  <dt>
  <?php echo $this->translate($this->locale()->toNumber($this->selectedRewardCount)); ?>
  </dt>

  <dd>
    <?php echo $this->translate('Count of Dispatched Rewards:'); ?>
  </dd>

  <dt>
  <?php echo $this->translate($this->locale()->toNumber($this->dispatchedRewardQuantity)); ?>
  </dt>

 

<button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>

</dl>
<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?> 

<?php else :?>
  <?php echo $this->message; ?>
<?php endif; ?>

   