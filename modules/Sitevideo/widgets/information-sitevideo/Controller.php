<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_InformationSitevideoController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitevideo_channel')) {
            return $this->setNoRender();
        }

        //GET SETTING
        $this->view->showContent = $this->_getParam('showContent', array("viewCount", "likeCount", "commentCount", "tags", "category", "creationDate", "updationDate"));

        if (Count($this->view->showContent) <= 0) {
            $this->view->setNoRender();
        }

        //GET CHANNEL SUBJECT
        $this->view->sitevideo = $sitevideo = Engine_Api::_()->core()->getSubject('sitevideo_channel');
        $tableCategory = Engine_Api::_()->getDbtable('channelCategories', 'sitevideo');
        $this->view->category_name = '';

        if (!empty($sitevideo->category_id)) {
            $this->view->category_name = $tableCategory->getCategory($sitevideo->category_id)->category_name;
        }

        $this->view->viewer = Engine_Api::_()->user()->getViewer();

        //GET CHANNEL TAGS
        $this->view->sitevideoTags = $sitevideo->tags()->getTagMaps();

        if (!empty($this->view->showContent) && in_array('socialShare', $this->view->showContent)) {

            $social_share_default_code = '<div class="addthis_toolbox addthis_default_style ">
                <a class="addthis_button_preferred_1"></a>
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
                <a class="addthis_button_preferred_5"></a>
                <a class="addthis_button_compact"></a>
                <a class="addthis_counter addthis_bubble_style"></a>
                </div>
                <script type="text/javascript">
                var addthis_config = {
                                    services_compact: "facebook, twitter, linkedin, google, digg, more",
                                    services_exclude: "print, email"
                }
                </script>
                <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js"></script>';

            $settings = Engine_Api::_()->getApi('settings', 'core');

            //GET CODE FROM LAYOUT SETTING
            $this->view->code = $settings->getSetting('sitevideo.code.share', $social_share_default_code);
        }
    }

}
