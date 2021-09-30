<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: success.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>

<?php
$type = 'Payment';
?> 

<div class="generic_layout_container layout_middle">
    <div class="sitecrowdfunding_alert_msg b_medium">

        <?php if (empty($this->state) || $this->state == 'active'): ?>
            <div class="sitecrowdfunding_payment_success_message" id="success_message">
                <?php // echo $this->success_message; ?>

                <?php $invoiceUrl = $this->url(Array('module' => 'sitecrowdfunding', 'controller' => 'backer', 'action' => 'print-invoice', 'backer_id' => Engine_Api::_()->sitecrowdfunding()->getDecodeToEncode($this->backer_id)), 'default'); ?>


                <h2 class="thanks_title"><i class="fa fa-check"></i> Thank you for your funding</h2><br>
                <!-- Project Details -->
                <?php $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->project->project_id); ?>
                <a href="<?php echo $item->getHref();?>" class="payment_backing_id" target="_blank"><?php echo $this->project->title;?> </a><br>

                <div class="sitecrowdfunding_grid_thumb" id="sc_grid_thumb">
                    <?php $fsContent = ""; ?>
                    <?php
                      if ($item->photo_id) {
                       echo $this->htmlLink($item->getHref(), $fsContent . $this->itemBackgroundPhoto($item, 'thumb.cover' , null, null, array('tag' => 'i')), array('class' => 'sitecrowdfunding_thumb'));
                      }
                     else {
                       $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                       echo $this->htmlLink($item->getHref(), $fsContent . "<i style='background-image:url(" . $url . ")'></i>", array('class' => 'sitecrowdfunding_thumb'));
                    }
                    ?>
                </div>
                <span class="payment_backing_id">Your backing ID #<?php echo $this->backer_id;?> </span>
                <br>

                <h3 class="spread_message_title">Now help spread the word</h3>
                <span class="spread_message">By sharing this fundraiser, you increase their chance of success by 3x</span>
                <br>
                <div class="share_options">
                    <?php echo $this->sitecrowdfundingShareLinksCustom($this->project, array('facebook', 'twitter', 'linkedin','community'), 'I Just Funded This Project. I recommend you too!'); ?>
                </div>
                <br>
                <p class="thanks_message">
                    A receipt has been sent to your registered mail id. <br>
                    You can also <a style="text-decoration: underline;color: #44AEC1;" href="<?php echo $this->back_details; ?>" target="_blank">click here</a> to view your backing details. <br>
                    To download the Invoice, please <a style="text-decoration: underline;color: #44AEC1;" href="<?php echo $invoiceUrl; ?>" target="_blank">click here</a>.
                </p>
                <br>
            </div>
            <br>
            <br>
            <?php if(!empty($this->page_id)):?>
                <h1 class="project_container_title">You Might also be Interested in:</h1>
                <div class="projects">
                    <?php echo $this->content()->renderWidget("sitepage.page-projects", array(page_id => $this->page_id,'project_id'=>$this->project->project_id)); ?>
                </div>
            <?php endif; ?>
        <?php elseif ($this->state == 'pending'): ?>
            <h3>
                <?php echo $this->translate('%s Pending', $type) ?>
            </h3>
            <p>
                <?php echo $this->translate('Thank you for submitting your %1$s. Your %1$s is currently pending.', $type) ?>
            </p> 
        <?php else: ?>
            <h3>
                <?php echo $this->translate('%s Failed', $type) ?>
            </h3> 
        <?php endif; ?>
    </div>

    <?php /*
    <?php if(empty($this->donationType)): ?>
        <?php if (empty($this->state) || ($this->state == 'active') || ($this->state == 'pending')): ?>
            <?php if (!empty($this->viewer_id)) : ?>
                <div class="clr">
                    <?php $url = $this->url(array('action' => 'manage', 'link' => 'backed'), "sitecrowdfunding_project_general", true); ?>
                    <a class="mtop10 fright common_btn" href="<?php echo $url; ?>">
                        <?php echo $this->translate('My Backed Projects') ?>
                    </a>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <?php if (!empty($this->viewer_id)) : ?>
                <div class="clr">
                    <button class="mtop10 fright" onclick="backToProject()">
                        <?php echo $this->translate('Back to Project') ?>
                    </button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php else:?>
        <div class="clr center">
            <?php echo $this->translate('You will be redirected back to original source in 5 seconds or please '); ?>
            <a href="<?php echo $this->sourceUrl; ?>"><?php echo $this->translate("click here") ?></a>
            <?php echo $this->translate("if your browser does not redirect you.") ?>  
        </div>
    <?php endif; ?>
    */ ?>
</div>


<script type="text/javascript">
    function backToProject() {
        window.location.href = '<?php echo $this->project->getHref(); ?>';
    }
</script>

<style>
    #sc_grid_thumb {
        width: 44%;
        height: 200px;
        margin-bottom: 20px;
    }
    #success_message {
        text-align: center;
        width: 60%;
        display: flex;
        margin: 0 auto;
        flex-direction: column;
        align-items: center;
    }
    .thanks_title{
        border-bottom: 1px solid #f2f0f0;
        padding: 10px;
        font-size: 28px !important;
    }
    .payment_backing_id{
        font-size: 18px;
    }
    .thanks_message{
        font-size: 18px;
        line-height: 30px;
    }
    .spread_message_title{
        font-size: 25px;
        margin-bottom: 20px;
    }
    .spread_message{
        font-size: 18px;
    }
    .share_options{
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .share_options > .social_share_custom_container > a::before{
        font-size: 35px !important;
    }
    .share_options > .social_share_custom_container > a > img{
        width: 46px !important;
        margin-top: -6px;
    }
    .projects{
        border: 1px solid #e4e0e0;
        margin-top: 30px;
    }
    .project_container_title{
        font-size: 24px;
        font-weight: 500;
        border-bottom: 0;
        padding-bottom: 10px;
        text-transform: capitalize;
        background: transparent;
        text-align: center;
        position: relative;
        margin: 20px;
    }
    .project_container_title::before {
        left: 0 !important;
        margin: 0 auto !important;
        right: 0 !important;
        text-align: center !important;
        width: 85px !important;
        background: #44AEC1 !important;
        top: 100% !important;
        content: "" !important;
        display: block !important;
        min-height: 2px !important;
        position: absolute !important;
        border-bottom: unset !important;
    }
</style>
