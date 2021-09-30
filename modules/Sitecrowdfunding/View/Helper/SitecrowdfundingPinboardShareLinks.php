<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SitecrowdfundingPinboardShareLinks.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_View_Helper_SitecrowdfundingPinboardShareLinks extends Zend_View_Helper_Abstract {

    public function sitecrowdfundingPinboardShareLinks($subject, $params = array(), $showText = false) {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
        $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
        $resource_id = $subject->getIdentity();
        $resource_type = $subject->getType();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        
        ?>
        <script type="text/javascript">
            var seaocore_content_type = '<?php echo $resource_type; ?>';
            var seaocore_favourite_url = en4.core.baseUrl + 'seaocore/favourite/favourite';
            var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';
        </script>
        <?php if (in_array('facebook', $params)) : ?>
            <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="pb_ch_wd seaocore_board_icon fb_icon" title="<?php echo $view->translate("Facebook"); ?>"></a>
        <?php endif; ?>
        <?php if (in_array('twitter', $params)) : ?>
            <a href="https://twitter.com/share?text=<?php echo $subject->getTitle(); ?>" target="_blank" class="pb_ch_wd seaocore_board_icon tt_icon" title="<?php echo $view->translate("Twitter"); ?>"></a>
        <?php endif; ?>
        <?php if (in_array('linkedin', $params)) : ?>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $object_link; ?>" target="_blank" class="pb_ch_wd seaocore_board_icon li_icon" title="<?php echo $view->translate("Linkedin"); ?>"></a>
        <?php endif; ?>
        <?php if (in_array('googleplus', $params)) : ?>
            <a href="https://plus.google.com/share?url=<?php echo $urlencode; ?>&t=<?php echo $subject->getTitle(); ?>" target="_blank" class="pb_ch_wd seaocore_board_icon gp_icon" title="<?php echo $view->translate("Google+"); ?>"></a>
        <?php endif; ?>
        <?php if (in_array('like', $params) && $viewer_id) : ?>
            <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type, $resource_id); ?>
            <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');"  id="<?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" style ='display:<?php echo $hasLike ? "inline-block" : "none" ?>' class="seaocore_icon_dislike <?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Unlike"); ?>">
                <?php if ($showText) : ?>
                    <?php echo $view->translate("Unlike"); ?>
                <?php endif; ?>
            </a>
            <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "inline-block" : "none" ?>' class="seaocore_icon_like <?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Like"); ?>">
                <?php if ($showText) : ?>
                    <?php echo $view->translate("Like"); ?>
                <?php endif; ?>
            </a>
            <input type ="hidden" id = "<?php echo $resource_type; ?>_like_<?php echo $resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
        <?php endif; ?>
        <?php if (in_array('favourite', $params) && $viewer_id) : ?>
            <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id); ?>
            <?php $unfavourites = $resource_type . '_unfavourites_' . $resource_id ?>
            <?php $favourites = $resource_type . '_most_favourites_' . $resource_id ?>
            <?php $fav = $resource_type . '_favourite_' . $resource_id; ?>
            <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $unfavourites; ?>" style ='display:<?php echo $hasFavourite ? "inline-block" : "none" ?>'  class="seaocore_icon_unfavourite <?php echo $unfavourites; ?>" title="<?php echo $view->translate("Unfavourite"); ?>">
                <?php if ($showText) : ?>
                    <?php echo $view->translate("Unfavourite"); ?>
                <?php endif; ?>
            </a>
            <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $favourites; ?>" style ='display:<?php echo empty($hasFavourite) ? "inline-block" : "none" ?>' class="seaocore_icon_favourite <?php echo $favourites; ?>" title="<?php echo $view->translate("Favourite"); ?>"><?php if ($showText) : ?>
                    <?php echo $view->translate("Favourite"); ?>
                <?php endif; ?>

            </a>
            <input type ="hidden" id = "<?php echo $fav ?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] : 0; ?>' />
        <?php endif; ?>
        <?php
    }

}
