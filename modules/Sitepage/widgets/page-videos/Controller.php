<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageVideosController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

      $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
      $this->view->page_id = $page_id = $sitepage->getIdentity();
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

      $this->view->videos = $videos = array();
      $this->view->integratedWithVideo = false;

      $sitevideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');

      $isIntegrated = Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => "sitepage_page", 'item_module' => 'sitepage'));

      if ($sitevideoEnabled && $isIntegrated) {
          $params = array();
          $params['parent_type'] = $sitepage->getType();
          $params['parent_id'] = $sitepage->getIdentity();
          $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
          $this->view->integratedWithVideo = true;
      } else {
          if (Engine_Api::_()->sitecrowdfunding()->enableVideoPlugin()) {
              $this->view->videos = $videos = array();
          }
      }

      $this->view->count = count($videos);
  }

}
?>