<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript" >
    var submitformajax = 1;

</script>
<script type="text/javascript">
    var viewer_id = '<?php echo $this->viewer_id; ?>';
    var url = '<?php echo $this->url(array(), 'sitecrowdfunding_general', true) ?>';

    var manageinfo = function (url) {
        window.location.href = url;
    };
</script>
<?php $rewardCount = $this->rewardCount; ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="sitecrowdfunding_dashboard_content">
    <?php if (empty($this->is_ajax)) : ?>

        <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project)); ?>

        <div class="layout_middle">

            <div class="sitecrowdfunding_edit_content">

                <div id="show_tab_content">

                <?php endif; ?>
                <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
                <div class="global_form">
                    <div>
                        <div>
                            <div class="sitecrowdfunding_manage_rewards">
                                <h3> <?php echo $this->translate('Manage Rewards'); ?> </h3>
                                <p class="form-description"><?php echo $this->translate("Below, you can create and manage the rewards for your project.") ?></p>
                                <?php if ($project->isOpen()) : ?>
                                    <div>
                                        <a href='<?php echo $this->url(array('controller' => 'reward', 'action' => 'create', 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true) ?>' class="icon seaocore_icon_add"><?php echo $this->translate("Create New Reward"); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if (count($this->rewards) > 0) : ?>
                                    <?php foreach ($this->rewards as $item): ?>
                                        <div id='<?php echo $item->reward_id ?>_project_main'  class='sitecrowdfunding_manage_rewards_list'>
                                            <div id='<?php echo $item->reward_id ?>_project' class="sitecrowdfunding_manage_rewards_list_details">
                                                <div class="sitecrowdfunding_manage_rewards_option">
                                                    <?php $url = $this->url(array('controller' => 'reward', 'action' => 'delete'), 'sitecrowdfunding_extended', true); ?>
                                                    <a href='<?php echo $this->url(array('controller' => 'reward', 'action' => 'edit', 'reward_id' => $item->reward_id, 'project_id' => $this->project_id), 'sitecrowdfunding_extended', true) ?>' class="buttonlink icon seaocore_icon_edit_sqaure"><?php echo $this->translate("Edit"); ?></a>

                                                    <?php
                                                    if ($item->spendRewardQuantity() <= 0) :
                                                        echo $this->htmlLink(array('route' => 'sitecrowdfunding_extended', 'module' => 'sitecrowdfunding', 'controller' => 'reward', 'action' => 'delete', 'reward_id' => $item->reward_id, 'project_id' => $this->project_id), $this->translate('Delete'), array('class' => 'smoothbox seaocore_txt_red seaocore_icon_remove_square'));
                                                    else :
                                                        ?>
                                                        <a href="javascript:void(0);" class="seaocore_txt_red seaocore_icon_remove_square" onclick='rewardPrompt()'><?php echo $this->translate('Delete'); ?></a>
                                                    <?php endif; ?> 
                                                </div>
                                                <?php $pledgeAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->pledge_amount); ?>
                                                <span class="reward_amount"><?php echo $this->translate("%s or more", $pledgeAmount); ?></span>
                                                <div class="sitecrowdfunding_manage_rewards_title">
                                                    <?php echo $this->translate($item->title); ?>
                                                </div>
                                            
                                                <?php if ($item->photo_id): ?>
                                                  <div class="sitecrowdfunding_reward_img">
                                                   <?php $src = Engine_Api::_()->storage()->get($item->photo_id, '')->getPhotoUrl(); ?>
                                                   <img src="<?php echo $src; ?>" title = '<?php echo $item->title; ?>'>
                                                  </div>
                                                <?php endif; ?>
                                            
                                                <div class="sitecrowdfunding_manage_rewards_pledged">
                                                    <?php $amount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($item->pledge_amount) ?>
                                                    <?php echo $this->translate("Backed Amount"); ?> : 
                                                    <?php echo $this->translate("%s", $amount); ?>
                                                </div> 
                                                <div class="sitecrowdfunding_manage_rewards_body show_content_body"> 
                                                    <?php echo $this->translate($item->description); ?>
                                                </div>
                                                <div class="sitecrowdfunding_manage_rewards_quantity">
                                                    <?php if ($item->quantity): ?>
                                                        <span class="mtop10">
                                                            <?php $quantity = $item->quantity; ?>
                                                            <?php $remainingRewards = $quantity - $item->spendRewardQuantity(); ?> 
                                                            <strong><?php echo $this->translate("Limited Rewards"); ?> : </strong><?php echo $this->translate("$remainingRewards left out of $quantity"); ?>

                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="mtop10">
                                                        <strong><?php echo $this->translate("Estimated Delivery"); ?> : </strong>
                                                        <?php echo date('F Y', strtotime($item->delivery_date));
                                                        ?>
                                                    </span>
                                                    <div class="mtop10">
                                                        <?php if ($item->shipping_method == 1): ?>
                                                            <?php echo $this->translate("No shipping Required"); ?>
                                                        <?php else: ?>
                                                            <?php echo $this->htmlLink(array('controller' => 'reward', 'action' => 'view-shipping-locations', 'reward_id' => $item->getIdentity()), $this->translate('View Shipping Details'), array('class' => 'smoothbox')); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <br />
                                    <div class="tip">
                                        <span><?php echo $this->translate('No rewards have been created for this project yet.'); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php $item = count($this->paginator) ?>
                            <input type="hidden" id='count_div' value='<?php echo $item ?>' />
                        </div>
                    </div>
                </div>
                <br />	
                <div id="show_tab_content_child">
                </div>
                <?php if (empty($this->is_ajax)) : ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
</div></div>
<script type="text/javascript">
    function rewardPrompt() {
        Smoothbox.open('<div><span><?php echo $this->string()->escapeJavascript($this->translate("You cannot delete this Reward as it is already selected by backers of this project. Still, you want to delete this Reward then please contact site admin to do so. [Note: It would be better if you can inform the backers about the deletion of the reward.]")); ?></span></div>');
    }
</script>