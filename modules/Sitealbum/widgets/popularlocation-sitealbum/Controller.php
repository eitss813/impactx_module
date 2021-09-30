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
class Sitealbum_Widget_PopularlocationSitealbumController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $params['limit'] = $this->_getParam('itemCount', 10);
  
        //DONT RENDER IF LOCATION IS DIS-ABLED BY ADMIN
        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitealbum.location', 1)) {
            return $this->setNoRender();
        }

        $this->view->sitealbumLocation = Engine_Api::_()->getDbTable('albums', 'sitealbum')->getPopularLocation($params);

        //DONT RENDER IF SITEALBUM COUNT IS ZERO
        if (!(count($this->view->sitealbumLocation) > 0)) {
            return $this->setNoRender();
        }

        $this->view->searchLocation = null;
        if (isset($_GET['location']) && !empty($_GET['location'])) {
            $this->view->searchLocation = $_GET['location'];
        }
    }

}