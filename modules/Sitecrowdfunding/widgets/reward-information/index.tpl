<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/style_comment.css'); ?>
<div class="sitecrowdfunding_reward_final_information">
    <div class="sitecrowdfunding_reward_information_pledged_amount">
        <div class="sitecrowdfunding_reward_information_pledged_amount_title mbot10">
            <?php echo $this->translate("Total Donation Amount"); ?>
        </div>
        <div class="sitecrowdfunding_reward_information_pledged_amount_value">
            <strong><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->pledge_amount); ?></strong>
            <?php $url = $this->url(array("action" => "reward-selection", 'project_id' => $this->project->project_id), "sitecrowdfunding_backer", true) ?>
            <span style="text-align: right;">
                <a href="<?php echo "$url"; ?>"><?php echo $this->translate('Edit'); ?> </a>
            </span>
        </div> 
    </div>
    <div class="sitecrowdfunding_reward_information_shipping_amount_value"> 
        <?php if ($this->shipping_amount): ?>
            <span><?php echo $this->translate('Shipping Cost :'); ?> </span>
            <strong ><?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->shipping_amount); ?>
            </strong> 
        <?php endif; ?>
    </div> 

    <?php if (!empty($this->reward_id)): ?>
        <div class="sitecrowdfunding_reward_information_selected">
            <div class="mtop10">
                <?php echo $this->translate('SELECTED REWARD'); ?>
            </div>
            <div class="mtop10" title="<?php echo $this->reward->getTitle(); ?>">
                <?php echo $this->translate($this->string()->truncate($this->string()->stripTags($this->reward->getTitle()), $this->titleTruncation)) ?>    
            </div>
            <div title="<?php echo $this->reward->getDescription(); ?>">
                <?php echo $this->translate($this->string()->truncate($this->string()->stripTags($this->reward->getDescription()), $this->descriptionTruncation)); ?>        
            </div>
        </div>
        <div class="sitecrowdfunding_reward_information_delivery mtop10">
            <span><?php echo $this->translate("Estimated Delivery : "); ?> </span>
            <?php echo date('F Y', strtotime($this->reward->delivery_date)); ?>
        </div>
    <?php endif; ?>
</div>
