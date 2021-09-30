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

<ul class="sitepage_sidebar_list">
	<?php foreach ($this->paginator as $sitepagevideo): ?>
    <?php  $this->partial()->setObjectKey('sitepagevideo');
        echo $this->partial('application/modules/Sitepagevideo/views/scripts/partialWidget.tpl', $sitepagevideo);
		?>	
          <?php if (($sitepagevideo->rating > 0)): ?>

            <?php
            $currentRatingValue = $sitepagevideo->rating;
            $difference = $currentRatingValue - (int) $currentRatingValue;
            if ($difference < .5) {
              $finalRatingValue = (int) $currentRatingValue;
            } else {
              $finalRatingValue = (int) $currentRatingValue + .5;
            }
            ?>

            <?php for ($x = 1; $x <= $sitepagevideo->rating; $x++): ?><span class="rating_star_big_generic rating_star sitepage_video_rate" title="<?php echo $finalRatingValue ?> <?php echo $this->translate('rating'); ?>"></span><?php endfor; ?><?php if ((round($sitepagevideo->rating) - $sitepagevideo->rating) > 0): ?><span class="rating_star_big_generic rating_star_half sitepage_video_rate" title="<?php echo $finalRatingValue . $this->translate('rating'); ?>"></span><?php endif; ?>
          <?php endif; ?>
        </div>
      </div>  
    </li>
  <?php endforeach; ?>
</ul>