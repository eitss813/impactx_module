<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
	<?php foreach ($this->paginator as $sitepagevideo): ?>
    <?php  $this->partial()->setObjectKey('sitepagevideo');
        echo $this->partial('application/modules/Sitepagevideo/views/scripts/partialWidget.tpl', $sitepagevideo);
		?>	
          <?php
	          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
	          $tmpBody = strip_tags($sitepagevideo->page_title);
	          $page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagevideo->page_id, $sitepagevideo->owner_id, $sitepagevideo->getSlug()), $page_title, array('title' => $sitepagevideo->page_title)) ?> 

          <?php if (($sitepagevideo->rating > 0)): ?>
           <div class="sitepage_sidebar_list_details">
							<?php
							$currentRatingValue = $sitepagevideo->rating;
							$difference = $currentRatingValue - (int) $currentRatingValue;
							if ($difference < .5) {
								$finalRatingValue = (int) $currentRatingValue;
							} else {
								$finalRatingValue = (int) $currentRatingValue + .5;
							}
							?>
            </div>
						
            <?php for ($x = 1; $x <= $sitepagevideo->rating; $x++): ?>
            	<span class="rating_star_generic rating_star sitepage_video_rate" title="<?php echo $finalRatingValue .' '. $this->translate('rating'); ?>"></span>
            <?php endfor; ?>
            <?php if ((round($sitepagevideo->rating) - $sitepagevideo->rating) > 0): ?>
            	<span class="rating_star_generic rating_star_generic_half sitepage_video_rate" title="<?php echo $finalRatingValue .' '. $this->translate('rating'); ?>"></span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>  
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('ratedvideo'=> 1), 'sitepagevideo_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>