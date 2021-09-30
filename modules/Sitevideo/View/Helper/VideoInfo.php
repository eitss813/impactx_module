<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: VideoInfo.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_View_Helper_VideoInfo extends Zend_View_Helper_Abstract {

    public function videoInfo($subject, $subjectInfo, $params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $statistics = '';
        if (!empty($subjectInfo) && in_array('creationDate', $subjectInfo)) {
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_time" title="' . $view->translate("Creation Date") . '"></i><div class="o_hidden">' . $view->timestamp($subject->creation_date) . '</div></div>';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.location', 0) && isset($subject->location) && !empty($subject->location) && !empty($subjectInfo) && in_array('location', $subjectInfo)) {
            $truncationLocation = 35;
            if (isset($params['truncationLocation']) && !empty($params['truncationLocation'])) {
                $truncationLocation = $params['truncationLocation'];
            }
            $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($subject->location, $truncationLocation);
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_location" title="' . $view->translate("Location") . '"></i><div class="o_hidden">';
            echo '<span title="' . $subject->location . '">' . $location . '</span>';
            echo '</div></div>';
        }
        if (!empty($subjectInfo) && in_array('view', $subjectInfo)) {
            $statistics .= $view->translate(array('%s view', '%s views', $subject->view_count), $view->locale()->toNumber($subject->view_count)) . ', ';
        }

        if (!empty($subject) && !empty($subjectInfo) && in_array('like', $subjectInfo)) {
            $statistics .= $view->translate(array('%s like', '%s likes', $subject->like_count), $view->locale()->toNumber($subject->like_count)) . ', ';
        }

        if (!empty($subjectInfo) && in_array('comment', $subjectInfo)) {
            $statistics .= $view->translate(array('%s comment', '%s comments', $subject->comment_count), $view->locale()->toNumber($subject->comment_count)) . ', ';
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        if (!empty($statistics)) {
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_stats" title="' . $view->translate("Statistics") . '"></i><div class="o_hidden">' . $statistics . '</div></div>';
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.rating', 1) && $subject->rating > 0 && !empty($subjectInfo) && in_array('rating', $subjectInfo)) {
            ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_rating" title="<?php echo $view->translate("Rating") ?>"></i>

                <div class="o_hidden" >
                    <span title="<?php echo $view->translate('Overall Rating: %s', $subject->rating); ?>">

                        <?php for ($x = 1; $x <= $subject->rating; $x++) { ?>
                            <span class="seao_rating_star_generic rating_star_y" title="<?php echo $view->translate('Overall Rating: %s', $subject->rating); ?>"></span>
                            <?php
                        }
                        $roundrating = round($subject->rating);
                        if (($roundrating - $subject->rating) > 0) {
                            ?>
                            <span class="seao_rating_star_generic rating_star_half_y" title="<?php echo $view->translate('Overall Rating: %s', $subject->rating); ?>"></span>
                            <?php
                        }
                        $roundrating++;
                        for ($x = $roundrating; $x <= 5; $x++) {
                            ?>
                            <span class="seao_rating_star_generic seao_rating_star_disabled" title="<?php echo $view->translate('Overall Rating: %s', $subject->rating); ?>"></span>
                        <?php } ?>
                    </span>
                </div>
            </div>
            <?php
        }
        
    }

}
