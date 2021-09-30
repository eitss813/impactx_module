<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: SitecrowdfundingShareLinks.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_View_Helper_sitecrowdfundingShareLinksCustom extends Zend_View_Helper_Abstract {

    public function sitecrowdfundingShareLinksCustom($subject, $params = array(), $message=null) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $urlencode = urlencode(((!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $subject->getHref());
        $object_link = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $subject->getHref();
        $urlShare = $this->view->url(array('module' => 'seaocore', 'controller' => 'activity', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'not_parent_refresh' => 1, 'format' => 'smoothbox'), 'default', true);
        $baseUrl = $view->baseUrl();
        $imgpath = $baseUrl . "/application/modules/Sitecrowdfunding/externals/images/webshare.png";
        $imgpath2 = $baseUrl . "/application/modules/Sitecrowdfunding/externals/images/custom-share-icon.png";
        $resource_id = $subject->getIdentity();
        $resource_type = $subject->getType();
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        if(!empty($message)){
            $setMsg = $message;
        }else{
            $setMsg = $subject->getTitle();
        }
        ?>


        <div class="social_share_custom_container">
            <style>
                .social_share_custom_container{
                    display: flex;
                    align-items: center;
                    margin-top: 10px;
                    margin-bottom: 10px;
                }
                .social_share_custom_container > a::before {
                    padding: 5px 10px !important;
                    border-radius: 3px;
                    font-size: 25px !important;
                }
                .social_share_custom_container > a.seao_icon_facebook::before {
                    background: #3b5998;
                    color: #fff;
                }
                .social_share_custom_container > a.seao_icon_twitter::before {
                    background: #00aced;
                    color: #fff
                }
                .social_share_custom_container > a.seao_icon_linkedin::before {
                    background: #0077b5;
                    color: #fff
                }
                .seao_icon_shareicon::before {content: "\\f1e0";}
            </style>

            <?php
                $short_url = Engine_Api::_()->getApi('Shorturl', 'core')->generateShorturl($subject,null);

                if($short_url) {
                    $urlencode = $short_url;
                }
            ?>

            <?php if (in_array('facebook', $params)) : ?>
                <a target="_blank" href="https://www.facebook.com/sharer/sharer.php?quote=<?php echo $setMsg; ?>&u=<?php echo $urlencode; ?>" class="seao_icon_facebook fb_icon" title="<?php echo $view->translate("Facebook"); ?>"></a>
            <?php endif; ?>
            <?php if (in_array('twitter', $params)) : ?>
                <a href="https://twitter.com/share?text=<?php echo $setMsg; ?>&url=<?php echo $urlencode; ?>" target="_blank" class="seao_icon_twitter tt_icon" title="Twitter"></a>
            <?php endif; ?>
            <?php if (in_array('linkedin', $params)) : ?>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $urlencode; ?>" target="_blank" class="seao_icon_linkedin li_icon" title="Linkedin"></a>
            <?php endif; ?>
            <?php if (in_array('community', $params)) : ?>
              <!--  <a href="<?php echo $urlShare; ?>" class = 'smoothbox' title="<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $view->translate('_SITE_TITLE')) ?>">
                    <img class="icon" src="<?php echo $imgpath2; ?>" style="width:35px;"/>
                </a>-->
                <a href="projects/get-link/subject/sitecrowdfunding_project_<?php echo $resource_id; ?>" class="smoothbox" title="ImpactX" style="user-select: auto;">
                    <img class="icon" src="public/sitecrowdfunding_project/copy-link.png" style="width: 35px; user-select: auto;">
                </a>
            <?php endif; ?>
        </div>
        <?php
    }

}
