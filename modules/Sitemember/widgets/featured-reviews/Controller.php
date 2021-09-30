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
class Sitemember_Widget_FeaturedReviewsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 0 || Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();

        //GET PARAMETERS FOR FETCH DATA
        $this->view->itemCount = $itemCount = $this->_getParam('itemCount', 3);

        //FETCH REVIEW DATA
        $params = array();
        $params['resource_type'] = 'user';
        $params['limit'] = $itemCount;
        $params['featured'] = 1;
        $this->view->circularImage = $values['circularImage'] = $this->_getParam('circularImage', 0);
        $sitemember_featured_review = Zend_Registry::isRegistered('sitemember_featured_review') ? Zend_Registry::get('sitemember_featured_review') : null;
        $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('reviews', 'sitemember')->getReviewsPaginator($params);
        $this->view->paginator = $paginator->setItemCountPerPage($itemCount);
        $this->view->paginator = $paginator->setCurrentPageNumber(1);

        //CALCULATE TOTAL PAGES
        $total_items = $paginator->getTotalItemCount();

        //DON'T RENDER IF NO DATA FOUND
        if (empty($sitemember_featured_review) || ($total_items <= 0)) {
            return $this->setNoRender();
        }
    }

}
