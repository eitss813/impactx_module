<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_PageServicesController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // check extension installed or not
        $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
        if (!$featureExtension) {
          return $this->setNoRender();
        }
        //DON'T RENDER IF NOT AUTHORIZED.
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
        //GET THE SUBJECT OF PAGE.
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
        $page_id = $sitepage->page_id;

        //check service is enable or not
        $servicePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'service');
        if (empty($servicePrivacy)) {
          return $this->setNoRender();
        }
        $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
        $count= $this->_getParam('itemCount',2);
        $this->view->services=Engine_Api::_()->getDbTable('services', 'sitepage')->getListing(array('page_id'=>$page_id, 'page' => $page, 'count' => $count));
        $totalServices = Engine_Api::_()->getDbTable('services', 'sitepage')->countPageServices($page_id);
        if($totalServices > $count) {
            $this->view->more = true;
        }
    }
}
?>