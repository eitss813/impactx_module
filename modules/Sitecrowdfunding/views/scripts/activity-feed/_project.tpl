<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _project.tpl 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="sitecrowdfunding_review_rich_content">

  <div class="sitecrowdfunding_activity_feed_desc">
  <div class="feed_item_link_title">
 
	<?php echo $this->htmlLink($this->project->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->translate($this->project->getTitle()), 50), array('class' => 'sea_add_tooltip_link', 'rel' => $this->project->getType() . ' ' . $this->project->getIdentity())) ?> 
    </div>
    <div class="Sitecrowdfunding_project_goal_amount mtop5 mbot5">
    	<p><?php echo $this->translate('Goal Amount : ');?>
    	<?php echo Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($this->project->goal_amount);?></p>
    </div>
      <?php
          $routeName =  Zend_Controller_Front::getInstance()->getRouter()->getCurrentRouteName();
          $actionName = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
          $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
          $urlFlag = "true" ;
          $word = "project/";

          // Test if string contains the word
          if(strpos($actual_link, $word) !== false){
          $urlFlag = "false";
          }

      ?>
      <?php if ( $routeName != 'sitecrowdfunding_entry_view'  && $urlFlag=="true"): ?>
              <div class="feed_item_link_desc">
                  <?php echo $this->translate(Engine_Api::_()->seaocore()->seaocoreTruncateText($this->project->description , 100)) ?>
              </div>
            </div>
            <div class="sitecrowdfunding_activity_feed_img">
                <?php echo $this->htmlLink($this->project->getHref(), "<img src='$this->photoURL' />", array('class' => 'aaf-feed-photo')); ?>
            </div>
      <?php endif; ?>

</div>