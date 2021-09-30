<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: BadgeController.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_BadgeController extends Core_Controller_Action_Standard {

    public function init() {
        $this->view->badgeEnable = $badge_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitevideo.video.badge', 1);

        if (empty($badge_enable)) {
            $action = $this->_getParam('action', null);
            if ($action != 'index') {
                return $this->_forward('notfound', 'error', 'core');
            }
        }
    }

    public function indexAction() {
        // $this->_helper->layout->setLayout('default-simple');
        $this->_helper->layout->disableLayout();
        extract($_GET);
        $params = array('channel_id' => $id);
        if (empty($no_of_image))
            $no_of_image = 10;

        $paginator = $this->view->paginator = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $paginator->setItemCountPerPage($no_of_image);
        $paginator->setCurrentPageNumber(1);
        $this->view->border_color = $border_color;
        $this->view->background_color = $background_color;
        $this->view->text_color = $text_color;
        $this->view->link_color = $link_color;
        $this->view->height = $height;
        $width = $width - 8;
        $this->view->inOneRowWidth = @floor($width / 116) * 116;
        $this->view->owner = $owner = Engine_Api::_()->user()->getUser($owner);
    }

    public function createAction() {
        //USER VALIDATION
        if (!$this->_helper->requireUser()->isValid())
            return;

        // Render
        $this->_helper->content->setEnabled();

        // Get navigation
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('sitevideo_main');
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        $form = $this->view->form = new Sitevideo_Form_Badge_Create();
        $form->owner->setValue($viewer_id);
        $channel_id = $this->view->channel_id = (int) $this->_getParam('channel_id', 0);
        if (!empty($channel_id)) {
            $this->view->type = 'sitevideo_channel';
        }
    }

    public function getSourceAction() {

        $params = $this->_getAllParams();
        extract($params);
        $url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->view->url(array('action' => 'index'), 'sitevideo_badge', true);
        $url.='?type=' . urlencode($type) . '&amp;amp;id=' . urlencode($id) . '&amp;amp;width=' . urlencode($width) . '&amp;amp;height=' . urlencode($height) . '&amp;amp;owner=' . urlencode($owner) . '&amp;amp;no_of_image=' . urlencode($no_of_image) . '&amp;amp;background_color=' . urlencode($background_color) . '&amp;amp;border_color=' . urlencode($border_color) . '&amp;amp;text_color=' . urlencode($text_color) . '&amp;amp;link_color=' . urlencode($link_color);
        $code = '&lt;iframe scrolling="no" frameborder="0" id="badge_video_iframe" src="' . $url . '" style="overflow: auto; width: ' . $width . 'px; height: ' . $height . 'px;" allowTransparency="true" &gt;';
        $code.="&lt;center&gt;&lt;img src='" . "http://" . $_SERVER['HTTP_HOST'] . $this->view->baseUrl() . "/application/modules/Sitevideo/externals/images/loader.gif' /&gt; &lt;/center&gt;";
        $code .='&lt;/iframe&gt;';
        $this->view->code = $code;
    }

}
