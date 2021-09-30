<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitealbum_Widget_InformationSitealbumController extends Seaocore_Content_Widget_Abstract {

  public function indexAction() {

    //DONT RENDER IF SUBJECT IS NOT SET
    if (!Engine_Api::_()->core()->hasSubject('album')) {
      return $this->setNoRender();
    }

    //GET SETTING
    $this->view->showContent = $this->_getParam('showContent', array("viewCount", "likeCount", "commentCount", "tags", "category", "creationDate", "updationDate", "location"));

    if (Count($this->view->showContent) <= 0) {
      $this->view->setNoRender();
    }

    //GET ALBUM SUBJECT
    $this->view->sitealbum = $sitealbum = Engine_Api::_()->core()->getSubject('album');

    $tableCategory = Engine_Api::_()->getDbTable('categories', 'sitealbum');
    $this->view->category_name = '';
    if (!empty($sitealbum->category_id)) {
      $this->view->category_name = $tableCategory->getCategory($sitealbum->category_id)->category_name;
    }

    $this->view->viewer = Engine_Api::_()->user()->getViewer();

    //GET ALBUM TAGS
    $this->view->sitealbumTags = $sitealbum->tags()->getTagMaps();
    
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
            $this->view->code = $settings->getSetting('sitealbum.code.share', $social_share_default_code);

        }
  }

}