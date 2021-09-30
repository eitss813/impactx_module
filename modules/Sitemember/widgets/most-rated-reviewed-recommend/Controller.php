<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_MostRatedReviewedRecommendController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        $params = array();
        $params['viewMembers'] = $this->_getParam('orderby', 'rating_avg');
        $params['limit'] = $this->_getParam('itemCount', 5);
        $this->view->viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $this->view->circularImage = $params['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->statistics = $params['memberInfo'] = $this->_getParam('memberInfo', array(""));
        $this->view->truncation = $params['truncation'] = $this->_getParam('truncation', 16);
        $this->view->columnWidth = $params['columnWidth'] = $this->_getParam('columnWidth', '180');
        $this->view->columnHeight = $params['columnHeight'] = $this->_getParam('columnHeight', '328');
        $this->view->identity = $params['identity'] = $this->_getParam('identity', $this->view->identity);
        $this->view->viewType = $params['viewType'] = $this->_getParam('viewType', 'gridview');
        $this->view->circularImageHeight = $params['circularImageHeight'] = $this->_getParam('circularImageHeight', 190);
        $this->view->params = $params;
        //GET MEMBERS
        $this->view->members = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($params);

        if (@count($paginator) <= 0) {
            return $this->setNoRender();
        }
    }

}
