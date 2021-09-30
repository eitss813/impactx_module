<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AlbumInfo.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_View_Helper_AlbumInfo extends Zend_View_Helper_Abstract {

    public function albumInfo($subject, $subjectInfo, $params = array()) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $statistics = '';
        if (!isset($params['doNotShowStatistics'])) {
            if (!empty($subjectInfo) && in_array('viewCount', $subjectInfo)) {
                $statistics .= $view->translate(array('%s view', '%s views', $subject->view_count), $view->locale()->toNumber($subject->view_count)) . ', ';
            }

            if (!empty($subject) && !empty($subjectInfo) && in_array('likeCount', $subjectInfo)) {
                $statistics .= $view->translate(array('%s like', '%s likes', $subject->like_count), $view->locale()->toNumber($subject->like_count)) . ', ';
            }

            if (!empty($subjectInfo) && in_array('commentCount', $subjectInfo)) {
                $statistics .= $view->translate(array('%s comment', '%s comments', $subject->comment_count), $view->locale()->toNumber($subject->comment_count)) . ', ';
            }
        }

        if (!empty($subjectInfo) && in_array('creationDate', $subjectInfo)) {
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_time" title="' . $view->translate("Creation Date") . '"></i><div class="o_hidden">' . $view->timestamp($subject->creation_date) . '</div></div>';
        }

        $statistics = trim($statistics);
        $statistics = rtrim($statistics, ',');
        if (!empty($statistics)) {
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_stats" title="' . $view->translate("Statistics") . '"></i><div class="o_hidden">' . $statistics . '</div></div>';
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1) && isset($subject->location) && !empty($subject->location) && !empty($subjectInfo) && in_array('location', $subjectInfo)) {
            $truncationLocation = 35;
            if (isset($params['truncationLocation']) && !empty($params['truncationLocation'])) {
                $truncationLocation = $params['truncationLocation'];
            }
            $location = Engine_Api::_()->seaocore()->seaocoreTruncateText($subject->location, $truncationLocation);
            echo '<div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_location" title="' . $view->translate("Location") . '"></i><div class="o_hidden">';
            if (!in_array('directionLink', $subjectInfo)) {
                echo '<span title="' . $subject->location . '">' .
                $location . '</span>';
            } else if (in_array('directionLink', $subjectInfo)) {
                echo $view->htmlLink(array('route' => 'seaocore_viewmap', "id" => $subject->seao_locationid, 'resouce_type' => 'seaocore'), $location, array('onclick' => 'openSmoothbox(this);return false', 'title' => $subject->location));
            }

            echo '</div></div>';
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.category.enabled', 1) && !empty($subjectInfo) && in_array('categoryLink', $subjectInfo) && $subject->category_id && Engine_Api::_()->getItem('album_category', $subject->category_id)) {
            $categoryName = Engine_Api::_()->getDbtable('categories', 'sitealbum')->getCategoryName($subject->category_id);
            ?>
            <div class="seao_listings_stats"><i class="seao_icon_strip seao_icon seao_icon_category" title="<?php echo $view->translate('Category') ?>"></i>
                <div class="o_hidden">
                    <a href="<?php echo $view->url(array('category_id' => $subject->category_id, 'categoryname' => Engine_Api::_()->getItem('album_category', $subject->category_id)->getCategorySlug()), 'sitealbum_general_category', true) ?>">
                        <span><?php echo  $view->translate($categoryName); ?></span>
                    </a> 
                </div>
            </div>
            <?php
        }
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.rating', 1) && $subject->rating > 0 && !empty($subjectInfo) && in_array('ratingStar', $subjectInfo)) {
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
        ?>
        <?php if (isset($params['infoOnHover']) && empty($params['infoOnHover'])): ?>
            <?php
            $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
            $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
            ?>
            <div class="seao_share_links">
                <div class="social_share_wrap">
                    <?php if (!empty($subjectInfo) && in_array('facebook', $subjectInfo)): ?>
                        <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook"></a>
                    <?php endif; ?>
                    <?php if (!empty($subjectInfo) && in_array('twitter', $subjectInfo)) : ?>
                        <a href="https://twitter.com/share?text='<?php echo $subject->getTitle(); ?>'" target="_blank" class="seao_icon_twitter"></a>
                    <?php endif; ?>
                    <?php if (!empty($subjectInfo) && in_array('linkedin', $subjectInfo)): ?>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url='<?php echo $object_link; ?>'" target="_blank" class="seao_icon_linkedin"></a>
                    <?php endif; ?>
                    <?php if (!empty($subjectInfo) && in_array('google', $subjectInfo)): ?>
                        <a href="https://plus.google.com/share?url='<?php echo $urlencode; ?>'&t=<?php echo $subject->getTitle(); ?>" target="_blank" class="seao_icon_google_plus"></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
    }

}
