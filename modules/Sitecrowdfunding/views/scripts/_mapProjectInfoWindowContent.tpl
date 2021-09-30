<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _mapInfoWindowContent.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style>
    .sitecrowdfunding_map_info_tip{
        width: auto !important;
        min-height: auto !important;
    }
    .sitecrowdfunding_map_info_tip_photo{
        /*float: none !important;*/
        /*text-align: center !important;*/
    }
    .sitecrowdfunding_map_info_tip_photo > a > img{
        max-width: 100px !important;
        max-height: 150px !important;
    }
    .sitecrowdfunding_map_info_tip_title a{
        font-size: 16px !important;
        font-weight: bold !important;
    }
    .days > b{
        font-weight: unset !important;
    }
</style>
<?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $this->project_id); ?>
<?php $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null; ?>

<div id="content">
    <div id="siteNotice">
    </div>
    <div class="sitecrowdfunding_map_info_tip o_hidden">
        <div class="sitecrowdfunding_map_info_tip_top o_hidden">
            <div class="sitecrowdfunding_map_info_tip_title">
                <?php echo $this->htmlLink($project->getHref(), $this->translate("Project - ".$project->getTitle())) ?>
            </div>
        </div>
        <div class="sitecrowdfunding_map_info_tip_photo prelative" >
            <?php echo $this->htmlLink($project->getHref(), $this->itemPhoto($project, 'thumb.cover')) ?>
        </div>
        <div class="sitecrowdfunding_map_info_tip_info">
            <div class="seao_listings_stats o_hidden mbot5">
                <?php $owner = $project->getOwner(); ?>
                <?php echo $this->translate('By %s', $this->htmlLink($owner->getHref(), $this->string()->truncate($this->string()->stripTags($owner->getTitle()), 17), array('title' => $owner->getTitle()))); ?>
            </div>

            <?php
                $memberCount = Engine_Api::_()->getDbtable('memberships', 'sitecrowdfunding')->getMembersCount($project->getIdentity());
                $followerCount = Engine_Api::_()->getApi('favourite', 'seaocore')->favouriteCount($project->getType(), $project->getIdentity());
                $adminCount = Engine_Api::_()->getDbTable('listItems', 'sitecrowdfunding')->getProjectLeadersCount($project->getIdentity());
                $backerCount = $project->backer_count;
                $days = $project->getRemainingDays(0,1);
                $totalAmount = $project->goal_amount;
                $totalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($totalAmount);
                $fundedAmount = $project->getFundedAmount();
                $fundedRatio = $project->getFundedRatio();
                $pledged = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);
            ?>

            <a href="javascript:void(0);" class="follower_count">
                <?php echo $this->translate(array('%s followers', '%s followers', $followerCount),$this->locale()->toNumber($followerCount)); ?>
            </a>
            &middot;
            <a href="javascript:void(0);" class="members_count">
                <?php echo $this->translate(array('%s members', '%s members', $memberCount),$this->locale()->toNumber($memberCount)); ?>
            </a>
            &middot;
            <a href="javascript:void(0);" class="admins_count">
                <?php echo $this->translate(array('%s admins', '%s admins', $adminCount),$this->locale()->toNumber($adminCount)); ?>
            </a>

            <br/>

            <?php if($project->isFundingApproved()): ?>

                <a href="javascript:void(0);" class="backers_count">
                    <?php echo $this->translate(array('%s backers', '%s backers', $backerCount),$this->locale()->toNumber($backerCount)); ?>
                </a>
                <br/>

                <!-- days-->
                <a href="javascript:void(0);" class="days">
                    <?php echo $days; ?>
                </a>
                <br/>

                <a href="javascript:void(0);" class="goal_amt">
                    <?php echo $this->translate("Goal Amount: %s", $totalAmount); ?>
                </a>
                <br/>

                <a href="javascript:void(0);" class="funded_ratio">
                    <?php echo $this->translate("%s",$fundedRatio.'% Funded'); ?>
                </a>
                <br/>

                <a href="javascript:void(0);" class="backers_pledged">
                    <?php echo $this->translate($pledged . ' Backed'); ?>
                </a>
                <br/>

            <?php endif; ?>
        </div>
    </div>
</div>