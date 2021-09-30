<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view-shipping-locations.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
    <div class="reward_view_shipping_popup">
        <h2>
            <?php echo $this->translate($this->reward->getTitle()); ?>
        </h2>
        <div class="sitecrowdfunding_manage_rewards_pledged mbot10">
            <?php $amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->reward->pledge_amount) ?>
            <strong><?php echo $this->translate("Backed Amount"); ?> : </strong>
            <?php echo $this->translate("$amount"); ?>
        </div> 

        <div class="sitecrowdfunding_manage_rewards_quantity mbot10">
            <?php if ($this->reward->quantity): ?>
                <span class="">
                    <?php $quantity = $this->reward->quantity; ?>
                    <?php $remainingRewards = $quantity - $this->reward->spendRewardQuantity(); ?>
                    <?php echo $this->translate("<strong>Limited Rewards</strong> : ($remainingRewards left out of $quantity) "); ?>
                </span>
            <?php endif; ?>
        </div>
        <div class="mbot10">
            <strong><?php echo $this->translate("Estimated Delivery : "); ?></strong>
            <?php echo date('F Y', strtotime($this->reward->delivery_date));
            ?>
        </div>
        <div class="sitecrowdfunding_manage_rewards_body show_content_body mbot10"> 
            <strong><?php echo $this->translate("Description : "); ?></strong>
            <?php echo $this->translate($this->reward->description); ?>
        </div>
    </div>
    <ul>
        <h3><?php echo $this->translate("Shipping Details"); ?></h3>
        <?php foreach ($this->locations as $location): ?>
            <li>
                <?php if (empty($location['region_id'])): ?>
                    <?php echo $this->translate("Rest of World"); ?> : 
                <?php else: ?>
                    <?php $region = Engine_Api::_()->getItem('sitecrowdfunding_region', $location['region_id']); ?>
                    <?php echo $this->translate($region->country_name); ?> : 
                <?php endif; ?>
                <?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($location['amount']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <button onclick='javascript:parent.Smoothbox.close()' style="float:right;"><?php echo 'Close'; ?></button>
    <?php if (@$this->closeSmoothbox): ?>
        <script type="text/javascript">
            TB_close();
        </script>
    <?php endif; ?> 
</div>