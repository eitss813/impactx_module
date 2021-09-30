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

<?php
$sitepage = $this->sitepage;
$location = $this->location;
$page_type = $this->page_type;
?>
<div id="content">
    <div id="siteNotice">
    </div>
    <div class="map_popup_header">
        <h3><?php echo $this->translate($page_type);?></h3>
    </div>
    <ul class="sitepages_locationdetails">
        <li>
            <div class="sitepages_locationdetails_info_title">
                <?php echo $this->htmlLink($sitepage->getHref(), $sitepage->getTitle()) ?>
                <div class="fright">
                    <span>
                        <?php if ($sitepage->featured == 1): ?>
                        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sitepage_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Featured')))) ?>
                        <?php endif; ?>
                        </span>
                    <span>
                        <?php if ($sitepage->sponsored == 1): ?>
                        <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->string()->escapeJavascript($this->translate('Sponsored')))) ?>
                        <?php endif; ?>
                        </span>
                </div>
                <div class="clr"></div>
            </div>

            <div class="sitepages_locationdetails_indo_details">

                <div class="sitepages_locationdetails_photo">
                    <?php echo $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepage->page_id, $sitepage->owner_id, $sitepage->getSlug()), $this->itemPhoto($sitepage, 'thumb.normal')); ?>
                </div>

                <div class="sitepages_locationdetails_info">
                    <?php if ($this->ratngShow): ?>

                        <?php if (($sitepage->rating > 0)): ?>

                            <?php
                            $currentRatingValue = $sitepage->rating;
                            $difference = $currentRatingValue- (int)$currentRatingValue;
                            if($difference < .5) {
                            $finalRatingValue = (int)$currentRatingValue;
                            }
                            else {
                            $finalRatingValue = (int)$currentRatingValue + .5;
                            }
                            ?>

                            <span class="clr" title="<?php echo $finalRatingValue.$this->translate(' rating'); ?>">
                                <?php for ($x = 1; $x <= $sitepage->rating; $x++): ?>
                                    <span class="rating_star_generic rating_star"></span>
                                <?php endfor; ?>

                                <?php if ((round($sitepage->rating) - $sitepage->rating) > 0): ?>
                                    <span class="rating_star_generic rating_star_half"></span>
                                <?php endif; ?>
                            </span>

                        <?php endif; ?>

                    <?php endif; ?>

                    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.postedby', 1)):?>
                        <div class="sitepages_locationdetails_info_date">
                            <?php echo $this->timestamp(strtotime($sitepage->creation_date)) ?> - <?php echo $this->string()->escapeJavascript($this->translate('posted by')); ?>
                            <?php echo $this->htmlLink($sitepage->getOwner()->getHref(),$this->string()->escapeJavascript($sitepage->getOwner()->getTitle())) ?>
                        </div>
                    <?php endif; ?>

                    <div class="sitepages_locationdetails_info_date">
                        <?php $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding'); ?>
                        <?php $projects = $pagesTable->getPageProjects($sitepage->page_id); ?>
                        <?php $projectsCount = count($projects); ?>

                        <?php $admin = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminUserLocation($sitepage->page_id); ?>
                        <?php $adminCount = count($projects); ?>

                        <?php $partnerCount = Engine_Api::_()->getDbtable('partners', 'sitepage')->getPartnerPagesCount($sitepage->page_id); ?>

                        <?php echo $this->string()->escapeJavascript($this->translate(array('%s Followers', '%s Followers',$sitepage->follow_count), $this->locale()->toNumber($sitepage->follow_count))) ?> |
                        <?php echo $this->string()->escapeJavascript($this->translate(array('%s Members', '%s Members',$sitepage->member_count), $this->locale()->toNumber($sitepage->member_count))) ?> |
                        <?php echo $this->string()->escapeJavascript($this->translate(array('%s Projects', '%s Projects',$projectsCount), $this->locale()->toNumber($projectsCount))) ?> |
                        <?php echo $this->string()->escapeJavascript($this->translate(array('%s Admins', '%s Admins',$adminCount), $this->locale()->toNumber($adminCount))) ?> |
                        <?php echo $this->string()->escapeJavascript($this->translate(array('%s Partners', '%s Partners',$partnerCount), $this->locale()->toNumber($partnerCount))) ?>
                    </div>

                    <div class="sitepages_locationdetails_info_date">
                        <?php if (!empty($sitepage->phone)): ?>
                            <?php  echo  $this->string()->escapeJavascript($this->translate("Phone: ")) . $sitepage->phone ?><br/>
                        <?php endif; ?>

                        <?php if (!empty($sitepage->email)): ?>
                            <?php echo $this->string()->escapeJavascript($this->translate("Email: ")) . $sitepage->email?><br/>
                        <?php endif; ?>

                        <?php if (!empty($sitepage->website)): ?>
                            <?php echo $this->string()->escapeJavascript($this->translate("Website: ")) .$sitepage->website?>
                        <?php endif; ?>
                    </div>

                    <?php if($sitepage->price && $this->enablePrice): ?>
                        <div class="sitepages_locationdetails_info_date">
                            <i>
                                <b>
                                    <?php echo Engine_Api::_()->sitepage()->getPriceWithCurrency($sitepage->price); ?>
                                </b>
                            </i>
                        </div>
                    <?php endif; ?>

                    <div class="sitepages_locationdetails_info_date">
                        <i>
                            <b>
                                <?php echo $this->string()->escapeJavascript( $location->location); ?>
                            </b>
                        </i>
                    </div>

                </div>

            </div>

            <div class="clr"></div>
        </li>
    </ul>
</div>

<style>
    ul.sitepages_locationdetails .sitepages_locationdetails_info_date{
        font-size: 12px !important;
    }
</style>