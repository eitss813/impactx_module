<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/_commonFunctions.js');
?>

<?php if($this->layoutType != 'fundingDetails'): ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/_DashboardNavigation.tpl'; ?>
<?php endif; ?>

<div class="sitecrowdfunding_dashboard_content">

    <?php if($this->layoutType != 'fundingDetails'): ?>
    <?php echo $this->partial('application/modules/Sitecrowdfunding/views/scripts/dashboard/header.tpl', array('project' => $this->project, 'isFundingSection'=> true, 'sectionTitle' => 'Edit funding details', 'sectionDescription'=> 'Edit funding related details in this project using the form below.' )); ?>
    <?php endif; ?>

    <div class="sitecrowdfunding_project_form" style="overflow: auto;">

        <?php if($this->layoutType == 'fundingDetails'): ?>
        <h3 class="form_title">Edit fundings </h3>
        <?php endif; ?>

        <?php echo $this->form->render(); ?>
    </div>

    <script type="text/javascript">
        let isFundRaisableValue = "<?php echo $this->form->getValues()['is_fund_raisable']; ?>"
        if (!isFundRaisableValue || isFundRaisableValue == '0') {
            $("goal_amount-wrapper").style.display = "none";
            $("invested_amount-wrapper").style.display = "none";
            $("starttime-wrapper").style.display = "none";
            $("endtime-wrapper").style.display = "none";
        }else{
            $("goal_amount-wrapper").style.display = "block";
            $("invested_amount-wrapper").style.display = "block";
            $("starttime-wrapper").style.display = "block";
            $("endtime-wrapper").style.display = "block";
        }

        let paymentIsTaxDeductibleValue = "<?php echo $this->form->getValues()['payment_is_tax_deductible']; ?>"
        if (!paymentIsTaxDeductibleValue || paymentIsTaxDeductibleValue == '0') {
            $("payment_tax_deductible_label-wrapper").style.display = "none";
        }else{
            $("payment_tax_deductible_label-wrapper").style.display = "block";
        }

        function checkIsFundable(value){
            if (!value || value == '0') {
                $("goal_amount-wrapper").style.display = "none";
                $("invested_amount-wrapper").style.display = "none";
                $("starttime-wrapper").style.display = "none";
                $("endtime-wrapper").style.display = "none";
            }else{
                $("goal_amount-wrapper").style.display = "block";
                $("invested_amount-wrapper").style.display = "block";
                $("starttime-wrapper").style.display = "block";
                $("endtime-wrapper").style.display = "block";
            }
        }

        function onChangeIsTaxDeductible(value){
            if (!value || value == '0') {
                $("payment_tax_deductible_label-wrapper").style.display = "none";
            }else{
                $("payment_tax_deductible_label-wrapper").style.display = "block";
            }
        }
    </script>

    <?php if($this->layoutType != 'fundingDetails'): ?>
    <script type="text/javascript">
        var viewer_id = '<?php echo $this->viewer_id; ?>';
        var url = '<?php echo $this->url(array(), 'sitecrowdfunding_general', true) ?>';

        var manageinfo = function (url) {
            window.location.href = url;
        };
    </script>
    <?php $rewardCount = $this->rewardCount; ?>
    <?php if (empty($this->is_ajax)) : ?>
    <div class="layout_middle" style="border: 1px solid #f2f0f0;margin-top: 20px;">
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
    <?php endif; ?>
    <script type="text/javascript">
        function rewardPrompt() {
            Smoothbox.open('<div><span><?php echo $this->string()->escapeJavascript($this->translate("You cannot delete this Reward as it is already selected by backers of this project. Still, you want to delete this Reward then please contact site admin to do so. [Note: It would be better if you can inform the backers about the deletion of the reward.]")); ?></span></div>');
        }
    </script>

</div>
</div>
</div>
<script type="text/javascript">

    window.addEvent('domready', function () {
        var disableCalBtn = '<?php echo $this->disableCalBtn; ?>'
        if(disableCalBtn){
            $('starttime-date').getParent().getChildren("button")[0].set('disabled', true);
            $('endtime-date').getParent().getChildren("button")[0].set('disabled', true);
        }

        if ($('starttime-minute')) {
            $('starttime-minute').value = 0
            $('starttime-minute').style.display = 'none';
        }
        if ($('starttime-ampm')) {
            $('starttime-ampm').value = 'AM'
            $('starttime-ampm').style.display = 'none';
        }
        if ($('starttime-hour')) {
            $('starttime-hour').value = 12
            $('starttime-hour').style.display = 'none';
        }
        if ($('endtime-minute')) {
            $('endtime-minute').value = 50
            $('endtime-minute').style.display = 'none';
        }
        if ($('endtime-ampm')) {
            $('endtime-ampm').value = 'PM'
            $('endtime-ampm').style.display = 'none';
        }
        if ($('endtime-hour')) {
            $('endtime-hour').value = 11
            $('endtime-hour').style.display = 'none';
        }

    });

    // en4.core.runonce.add(function () {

    //  if ($('lifetime-0')) {
    //   if ($('lifetime-0').checked) {
    //  initializeCalendar(0,'<?php echo date('Y-m-d'); ?>');
    //  } else {
    //   initializeCalendar(1,'<?php echo date('Y-m-d'); ?>');
    //  }
    // } else {
    //initializeCalendar(0,'<?php echo date('Y-m-d'); ?>');
    //  }
    // })
</script>
<style type="text/css">
    div#buttons-element {
        margin-top: -28px;
    }
    form#sitecrowdfundings_editfunding_form {
        overflow: auto;
        height: 520px;
    }
    /*edit funding form*/
    .sitecrowdfunding_project_form .global_form > div
    {
        padding: 0px !important;
    }
    .form_title{
        padding: 15px;
    }
</style>