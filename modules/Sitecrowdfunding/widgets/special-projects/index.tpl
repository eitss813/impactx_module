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
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/favourite.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/like.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/scripts/core.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding.css'); ?>
<?php $className = 'sitecrowdfunding_list_popular_projects' . $this->identity; ?>
<?php $i = 0; ?>
<ul class='projects_manage sitecrowdfunding_popular_projects_grid_view o_hidden' id='projects_manage'>
    <?php foreach ($this->paginator as $item): ?>
        <li>
            <div class="sitecrowdfunding_thumb_wrapper sitecrowdfunding_thumb_viewer"  style=" width:<?php echo $this->projectWidth; ?>px; height:<?php echo $this->projectHeight; ?>px;">
                <?php $fsContent=''; ?>
                <?php if ($item->featured  and in_array('featured', $this->projectInfo)): ?>
                    <?php $fsContent .= '<div class="sitecrowdfunding_featured" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.featuredcolor', '#f72828') . '">' . $this->translate("Featured") . '</div>'; ?>
                <?php endif; ?>
                <?php if ($item->sponsored  and in_array('sponsored', $this->projectInfo)): ?>
                    <?php $fsContent .= '<div class="sitecrowdfunding_sponsored" style="background: ' . Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.sponsoredcolor', '#FC0505') . '">' . $this->translate("Sponsored") . '</div>'; ?>
                <?php endif; ?>
                <?php 
                if ($item->photo_id) {  
                  echo $this->htmlLink($item->getHref(), "<span class='project_overlay'></span>" . $fsContent . $this->itemBackgroundPhoto($item, null, null, array('tag' => 'i')));
                } else {
                  $url = $this->layout()->staticBaseUrl . "application/modules/Sitecrowdfunding/externals/images/nophoto_project_thumb_profile.png";
                  echo $this->htmlLink($item->getHref(), "<span class='project_overlay'></span>" . $fsContent  . $this->itemBackgroundPhoto($url, null, null, array('tag' => 'i')));
                }
                ?>
                <div class='sitecrowdfunding_hover_info'>
                  <div class="sitecrowdfunding_desc sitecrowdfunding_stats">
                    <?php echo $this->sitecrowdfundingShareLinks($item, $this->projectInfo); ?>
                  </div> 
                  <?php $content=''; ?>
                  <?php $content .= "<div class='sitecrowdfunding_stats sitecrowdfunding_grid_stats sitecrowdfunding_likes_comment_wrapper txt_center'>"; ?>
                  <?php if (in_array('like', $this->projectInfo)) : ?>
                      <?php $count = $this->locale()->toNumber($item->likes()->getLikeCount()); ?>
                      <?php $countText = $this->translate(array('%s like', '%s likes', $item->like_count), $count); ?>
                      <?php
                      $content .= '<span class="seaocore_icon_like" title="' . $countText . '">';
                      $content .= '&nbsp;'.$count;
                      $content .= '</span>';
                      ?>
                  <?php endif; ?>
                  <?php if (in_array('comment', $this->projectInfo)) : ?>
                      <?php $count = $this->locale()->toNumber($item->comments()->getCommentCount()); ?>
                      <?php $countText = $this->translate(array('%s comment', '%s comments', $item->comment_count), $count); ?>
                      <?php
                      $content .= '<span class="seaocore_icon_comment" title="' . $countText . '">';
                      $content .= '&nbsp;'.$count;
                      $content .= '</span>';
                      ?>
                  <?php endif; ?>
                  <?php $content .= '</div>'; ?>
                  <?php echo $content; ?>
                    <?php if($item->isFundingApproved()): ?>
                   <?php if (in_array('backer', $this->projectInfo)) : ?>
                    <?php $countText = $this->translate(array('%s backer', '%s backers', $item->backer_count), $this->locale()->toNumber($item->backer_count)) ?>
                      <div class="txt_center">
                      <span class="backers_count" title="<?php echo $countText; ?>">
                          <?php echo $countText; ?>
                      </span>
                      </div>
                   <?php endif; ?>
                    <?php endif; ?>
                  <div class="txt_center">
                  <button onclick="window.location = '<?php echo $item->getHref() ?>'">
                      <?php echo $this->translate('View'); ?>
                  </button>
                  </div>
                    <?php if($item->isFundingApproved()): ?>
                  <div class="sitecrowdfunding_grid_bottom_info"> 
                   <?php 
                      $fundedAmount = $item->getFundedAmount();
                      $totalAmount = $item->goal_amount;
                      $fundedRatio = $item->getFundedRatio();
                      $fundedAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($fundedAmount);                    
                      $totalAmount = Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($totalAmount);
                      ?>
                      <span class="goal_amount">
                         <?php echo $this->translate("Goal Amount<br> %s", $totalAmount); ?> 
                      </span>
                      <span class="funded_amount">
                         <?php echo $this->translate("%s <br>Funded",$fundedRatio.'%'); ?>
                      </span>
                  </div>
                    <?php endif; ?>
                </div>
                <div class="sitecrowdfunding_info">
                    <div class="sitecrowdfunding_bottom_info sitecrowdfunding_grid_bott_info">
                        <?php if (in_array('title', $this->projectInfo)) : ?>
                        <h3>
                          <?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($this->string()->stripTags($this->translate($item->getTitle())), $this->titleTruncation),array('title' => $item->getTitle())) ?>
                        </h3>
                        <?php endif; ?>
                        <?php if (in_array('owner', $this->projectInfo)) : ?>
                         <span class="sitecrowdfunding_author_name"><?php echo $this->translate("by %s", $this->htmlLink($item->getOwner()->getHref(), $this->translate($item->getOwner()->getTitle()))); ?></span>
                        <?php endif; ?>
                        <?php if($item->isFundingApproved()): ?>
                        <span class="total_amount">
                          <?php echo $this->translate("%s <br>Backed",$fundedAmount); ?>
                        </span>
                        <?php if (in_array('endDate', $this->projectInfo)) : ?>
                        <span class="days_left">
                          <?php echo $item->getRemainingDays(); ?>
                        </span>
                      <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>                
            </div>
        </li>
    <?php endforeach; ?>
  </ul>