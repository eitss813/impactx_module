<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: RatingInfo.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_View_Helper_RatingInfo extends Zend_View_Helper_Abstract {

    public function ratingInfo($subject, $params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        if (!$subject)
            return;
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1))
            return;
        $resource_id = $subject->getIdentity();
        if (isset($params['widget_id']) && !empty($params['widget_id']))
            $resource_id .="_" . $params['widget_id'];
        ?>

        <div id="channel_rating_<?php echo $resource_id ?>" class="rating" title="<?php echo $subject->rating; ?> ratings">
            <span id="rate_<?php echo $resource_id ?>_1" class="seao_rating_star_generic" ></span>
            <span id="rate_<?php echo $resource_id ?>_2" class="seao_rating_star_generic"></span>
            <span id="rate_<?php echo $resource_id ?>_3" class="seao_rating_star_generic"></span>
            <span id="rate_<?php echo $resource_id ?>_4" class="seao_rating_star_generic"  ></span>
            <span id="rate_<?php echo $resource_id ?>_5" class="seao_rating_star_generic"></span>
        </div>
        <script type="text/javascript">
            en4.core.runonce.add(function () {
                var subject_pre_rate = <?php echo $subject->rating; ?>;
                en4.sitevideo.ratings.setRating(subject_pre_rate, '<?php echo $resource_id ?>');
            });
        </script>
        <?php
    }

}
