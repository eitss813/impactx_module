<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ShareLinks.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_View_Helper_ShareLinks extends Zend_View_Helper_Abstract {

    public function shareLinks($subject, $params = array(), $showText = false) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
        $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
        $resource_id = $subject->getIdentity();
        $resource_type = $subject->getType();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();
        ?>
        <script type="text/javascript">
            // var seaocore_content_type = '<?php echo $resource_type; ?>';
            var seaocore_favourite_url = en4.core.baseUrl + 'seaocore/favourite/favourite';
            var seaocore_like_url = en4.core.baseUrl + 'seaocore/like/like';
        </script>
        <?php if(!in_array('hideShareDiv',$params)) :?>
        <div class="seao_share_links">
            <div class="social_share_wrap">
        <?php endif; ?>
                <?php if (in_array('facebook', $params)) : ?>
                    <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $urlencode; ?>" class="seao_icon_facebook" title="<?php echo $view->translate("Facebook"); ?>"></a>
                <?php endif; ?>
                <?php if (in_array('twitter', $params)) : ?>
                    <a href="https://twitter.com/share?text=<?php echo $subject->getTitle(); ?>" target="_blank" class="seao_icon_twitter" title="Twitter"></a>
                <?php endif; ?>
                <?php if (in_array('linkedin', $params)) : ?>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $object_link; ?>" target="_blank" class="seao_icon_linkedin" title="Linkedin"></a>
                <?php endif; ?>
                <?php if (in_array('googleplus', $params)) : ?>
                    <a href="https://plus.google.com/share?url=<?php echo $urlencode; ?>&t=<?php echo $subject->getTitle(); ?>" target="_blank" class="seao_icon_google_plus" title="Google Plus"></a>
                <?php endif; ?>
                <?php if (in_array('favourite', $params) && $viewer_id) : ?>
                    <?php $hasFavourite = Engine_Api::_()->getApi('favourite', 'seaocore')->hasFavourite($resource_type, $resource_id); ?>

                    <?php $unfavourites = $resource_type . '_unfavourites_' . $resource_id ?>
                    <?php $favourites = $resource_type . '_most_favourites_' . $resource_id ?>
                    <?php $fav = $resource_type . '_favourite_' . $resource_id; ?>
                    <?php $lbfv = 0; ?>
                    <?php if (in_array('lightbox', $params)) : ?>
                        <?php $lbfv = 1; ?>
                    <?php endif; ?>
                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $unfavourites; ?>" style ='display:<?php echo $hasFavourite ? "inline-block" : "none" ?>' <?php if ($lbfv == 1): ?> class="lightbox_btm_bl_btn <?php echo $unfavourites; ?>" <?php else: ?> class="sitevideo_unfavourite_icon <?php echo $unfavourites; ?>" <?php endif; ?> title="<?php echo $view->translate("Unfavourite"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Unfavourite"); ?>
                        <?php endif; ?>
                    </a>

                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_favourites('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $favourites; ?>" style ='display:<?php echo empty($hasFavourite) ? "inline-block" : "none" ?>' <?php if ($lbfv == 1): ?> class="lightbox_btm_bl_btn <?php echo $favourites; ?>" <?php else: ?> class="sitevideo_favourite_icon <?php echo $favourites; ?>" <?php endif; ?> title="<?php echo $view->translate("Favourite"); ?>"><?php if ($showText) : ?>
                            <?php echo $view->translate("Favourite"); ?>
                        <?php endif; ?>

                    </a>

                    <input type ="hidden" id = "<?php echo $fav ?>" value = '<?php echo $hasFavourite ? $hasFavourite[0]['favourite_id'] : 0; ?>' />
                <?php endif; ?>
                <?php
                if (in_array('like', $params) && $viewer_id) :
                    ?>
                    <?php $hasLike = Engine_Api::_()->getApi('like', 'seaocore')->hasLike($resource_type, $resource_id); ?>


                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');"  id="<?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" style ='display:<?php echo $hasLike ? "inline-block" : "none" ?>' class="sitevideo_unlike_icon <?php echo $resource_type; ?>_unlikes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Unlike"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Unlike"); ?>
                        <?php endif; ?>
                    </a>


                    <a href = "javascript:void(0);" onclick = "seaocore_content_type_likes('<?php echo $resource_id; ?>', '<?php echo $resource_type; ?>');" id="<?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" style ='display:<?php echo empty($hasLike) ? "inline-block" : "none" ?>' class="sitevideo_like_icon <?php echo $resource_type; ?>_most_likes_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Like"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Like"); ?>
                        <?php endif; ?>
                    </a>


                    <input type ="hidden" id = "<?php echo $resource_type; ?>_like_<?php echo $resource_id; ?>" value = '<?php echo $hasLike ? $hasLike[0]['like_id'] : 0; ?>' />
                <?php endif; ?>
                <?php if (in_array('watchlater', $params) && $viewer_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.watchlater.allow', 1)) : ?>
                    <?php $hasWatched = $subject->isWatched(); ?>
                    <?php $removeWatchLater = "removewatchlater_$resource_id"; ?>
                    <?php $addWatchLater = "addwatchlater_$resource_id"; ?>
                    <?php $lb = 0; ?>
                    <?php if (in_array('lightbox', $params)) : ?>
                        <?php $lb = 1; ?>
                    <?php endif; ?>
                    <a href = "javascript:void(0);" onclick = "en4.sitevideo.watchlaters.remove('<?php echo $resource_id; ?>');"  id="<?php echo $removeWatchLater; ?>" style ='display:<?php echo $hasWatched ? "inline-block" : "none" ?>' <?php if ($lb == 1): ?> class="lightbox_btm_bl_btn <?php echo $removeWatchLater; ?>" <?php else: ?> class="sitevideo_remove_watch_later <?php echo $removeWatchLater; ?>"<?php endif; ?>  title="<?php echo $view->translate("Remove from Watch Later"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Added to Watch Later"); ?>
                        <?php endif; ?>
                    </a>
                    <a href = "javascript:void(0);" onclick = "en4.sitevideo.watchlaters.add('<?php echo $resource_id; ?>');"  id="<?php echo $addWatchLater; ?>" style ='display:<?php echo empty($hasWatched) ? "inline-block" : "none" ?>' <?php if ($lb == 1): ?> class="lightbox_btm_bl_btn <?php echo $addWatchLater; ?>" <?php else: ?> class="sitevideo_add_watch_later <?php echo $addWatchLater; ?>"<?php endif; ?>  title="<?php echo $view->translate("Add To Watch Later"); ?>">
                        <?php if ($showText) : ?>
                            <?php echo $view->translate("Watch Later"); ?>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if (in_array('subscribe', $params) && $viewer_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1)) : ?>
                    <?php
                    $viewer_id = Engine_Api::_()->user()->getViewer()->user_id;
                    if ($viewer_id != $subject->getOwner()->user_id) :
                        $hasSubscribed = $subject->isSubscribed();
                        ?>

                        <a href = "javascript:void(0);" onclick = "en4.sitevideo.subscriptions.unsubscribe('<?php echo $resource_id; ?>');"  id="unsubscription_<?php echo $resource_id; ?>" style ='display:<?php echo $hasSubscribed ? "inline-block" : "none" ?>' class="sitevideo_unsubscribe_icon unsubscription_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Unsubscribe"); ?>"> 
                            <?php if ($showText) : ?>
                                <?php echo $view->translate("Unsubscribe"); ?> 
                            <?php endif; ?>
                        </a>
                        <a href = "javascript:void(0);" onclick = "en4.sitevideo.subscriptions.subscribe('<?php echo $resource_id; ?>');"  id="subscription_<?php echo $resource_id; ?>" style ='display:<?php echo empty($hasSubscribed) ? "inline-block" : "none" ?>' class="sitevideo_subscribe_icon subscription_<?php echo $resource_id; ?>" title="<?php echo $view->translate("Subscribe"); ?>">
                            <?php if ($showText) : ?>
                                <?php echo $view->translate("Subscribe"); ?> 
                            <?php endif; ?>
                        </a>

                        <?php
                    endif;
                endif;
                ?>
                <?php if (in_array('subscriptionSettings', $params) && $viewer_id && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.subscriptions.enabled', 1)) : ?>
                    <?php
                    $viewer_id = Engine_Api::_()->user()->getViewer()->user_id;
                    if ($viewer_id != $subject->getOwner()->user_id) :
                        $hasSubscribed = $subject->isSubscribed();
                        ?>
                        <?php if ($hasSubscribed) : ?>

                            <?php
                            echo $this->view->htmlLink(array(
                                'module' => 'sitevideo',
                                'controller' => 'subscription',
                                'action' => 'notification-settings',
                                'route' => 'default',
                                'channel_id' => $subject->channel_id,
                                'format' => 'smoothbox'
                                    ), "", array(
                                'class' => 'smoothbox sitevideo_setting_icon',
                                'title' => $view->translate('Notification Settings'),
                            ));
                            ?>
                            <?php
                        endif;
                    endif;
                endif;
                ?>
        <?php if(!in_array('hideShareDiv',$params)) :?>
            </div>
        </div>
        <?php endif; ?>
        <?php
    }

}
