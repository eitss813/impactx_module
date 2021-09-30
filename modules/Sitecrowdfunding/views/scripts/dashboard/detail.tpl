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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>
<div class="global_form_popup" style="width: 535px;">
<div class="sitecrowdfunding_popup">
<?php if (!empty($this->transaction_id)) : ?>

    <h2 class="payment_transaction_detail_headline">
        <?php echo $this->translate("Transaction Details"); ?>
    </h2>
    <div class="commission_view">
        <dl class="payment_transaction_details">
            <dd><?php echo $this->translate('Transaction ID :'); ?> </dd>
            <dt><?php echo $this->locale()->toNumber($this->transaction->transaction_id) ?></dt>
            <div class="clr"></div>
            <dd><?php echo $this->translate('User Name :'); ?> </dd>
            <dt><?php if ($this->user && $this->user->getIdentity()): ?>
                <?php echo $this->htmlLink($this->user->getHref(), $this->user->getTitle(), array('target' => '_parent')) ?>  
            <?php else: ?>
                <i><?php echo $this->translate('Deleted Backer'); ?></i>
            <?php endif; ?>
            </dt>
            <div class="clr"></div>
            <dd><?php echo $this->translate('Project Title :'); ?> </dd>
            <dt><a href="<?php echo $this->url(array('project_id' => $this->project->project_id, 'slug' => $this->project->getSlug()), "sitecrowdfunding_entry_view"); ?>"  target='_blank' title="<?php echo ucfirst($this->project->title); ?>">
                <?php echo $this->project->title; ?></a></dt>
            <div class="clr"></div>
            <dd><?php echo $this->translate('Payment Gateway :'); ?> </dd>
            <dt><?php if ($this->gateway): ?><?php echo $this->translate($this->gateway->title); ?><?php else: ?><i><?php echo $this->translate('Unknown Gateway'); ?></i><?php endif; ?></dt>     <div class="clr"></div>
            <dd><?php echo $this->translate('Payment State :'); ?> </dd>
            <dt><?php echo ucfirst($this->transaction->state); ?></dt>
            <div class="clr"></div>
            <dd><?php echo $this->translate('Payment Amount :'); ?> </dd>
            <dt><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->transaction->amount)?></dt>
            <div class="clr"></div>
            <!-- Naaziya: 7th July 2020: set commission=0% -->
            <!-- instead of getting value from $package->commission_settings, get commision rate from saved from db -->
            <?php /* <dd><?php echo $this->translate('Package commission : (%s)', $this->commissionRate ? $this->commissionRate . '%' : ''); ?></dd> */ ?>
            <dd><?php echo $this->translate('Package commission : (%s)',  $this->backer->commission_rate. '%'); ?></dd>
            <dt><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->backer->commission_value) ?></dt> 
            <div class="clr"></div>
            <dd><?php echo $this->translate('Date'); ?> : </dd>
            <dt>
            <?php echo $this->locale()->toDateTime($this->transaction->timestamp) ?>
            </dt> 
           <!-- <dt class="trans-details-btn">
            <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>
            </dt>-->

        </dl>
    </div>
    <?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
            TB_close();
        </script>
    <?php endif; ?> 

<?php else : ?>
    <?php echo $this->translate($this->message); ?>
<?php endif; ?>
</div>
</div>
<a style="position: fixed; right: 20px;" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();" class="popup_close"></a>