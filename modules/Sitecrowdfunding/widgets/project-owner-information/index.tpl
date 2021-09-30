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

<ul class="sitecrowdfunding_side_widget sitecrowdfunding_profile_side_project">
    <li class="sitecrowdfunding_profile_info_host">
        <div class="sitecrowdfunding_profile_photo">
            <?php if($this->projectOwner->getOwner()->photo_id): ?>
            <?php echo $this->htmlLink($this->projectOwner->getOwner()->getHref(), $this->itemPhoto($this->projectOwner->getOwner(), 'thumb.profile')); ?>
            <?php else :?>
            <?php echo $this->htmlLink($this->projectOwner->getOwner()->getHref(), "<img src='" . $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/owner-defaul-image.jpg' >"); ?>
            <?php endif; ?>
        </div>
        <div class="sitecrowdfunding_profile_side_project_info">
            <div class="sitecrowdfunding_profile_side_project_title">  
                    <?php echo $this->htmlLink($this->projectOwner->getHref(), $this->translate($this->projectOwner->getTitle())); ?> 
            </div>
            <br>
            <div class="sitecrowdfunding_listings_stats">
                <?php if($this->showContactButton): ?>
                    <div>
                        <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'contact-owner', 'project_id' => $this->project_id, 'format' => 'smoothbox'), $this->translate($this->contactMeTitle), array('class' => 'smoothbox common_btn', 'title' => $this->contactMeTitle)) ?>
                    </div> 
                <?php endif; ?>
                 <div>
                    <?php echo $this->htmlLink(array('route' => 'default','module' => 'sitecrowdfunding', 'controller' => 'project', 'action' => 'user-full-bio', 'project_id' => $this->project_id, 'format' => 'smoothbox'), $this->translate($this->seefullBioTitle), array('class' => 'smoothbox common_btn', 'title' => $this->seefullBioTitle)) ?>
                </div>  

            </div>
        </div>
    </li>

</ul>