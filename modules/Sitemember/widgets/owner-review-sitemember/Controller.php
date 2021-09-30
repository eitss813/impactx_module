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
class Sitemember_Widget_OwnerReviewSitememberController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings') == 3)
            return $this->setNoRender();
        
        //CHECK SUBJECT
        if (!Engine_Api::_()->core()->hasSubject('user')) {
            return $this->setNoRender();
        }

        //GET USER SUBJECT
        $this->view->user = $user = Engine_Api::_()->core()->getSubject('user');
        $this->view->user_id = $user_id = $user->getIdentity();
        //GET REVIEW TABLE
        $reviewTable = Engine_Api::_()->getDbTable('reviews', 'sitemember');

        //SET PARAMS
        $this->view->params = $this->_getAllParams();

        //LOADED BY AJAX
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                $params['owner_id'] = $user_id;
                $params['resource_type'] = $user->getType();
                $params['type'] = 'user';
                $paginator = $reviewTable->listReviews($params);
                $this->_childCount = $paginator->getTotalItemCount();
                return;
            }
        }
        $this->view->showContent = true;

        //GET VIEWER ID
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET USER LEVEL ID
        if (!empty($viewer_id)) {
            $this->view->level_id = $level_id = $viewer->level_id;
        } else {
            $this->view->level_id = $level_id = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchRow(array('type = ?' => "public"))->level_id;
        }

        //GET RATING TABLE
        $ratingTable = Engine_Api::_()->getDbTable('ratings', 'sitemember');

        $this->view->isajax = $this->_getParam('isajax', 0);

        //GET FILTER
        $option = $this->_getParam('option', 'fullreviews');
        $this->view->reviewOption = $params['option'] = $option;

        //SET ITEM PER PAGE
        if ($option == 'prosonly' || $option == 'consonly') {
            $this->view->itemProsConsCount = $setItemCountPerPage = $this->_getParam('itemProsConsCount', 20);
        } else {
            $this->view->itemReviewsCount = $setItemCountPerPage = $this->_getParam('itemReviewsCount', 5);
        }

        $sitemember_owner_review = Zend_Registry::isRegistered('sitemember_owner_review') ? Zend_Registry::get('sitemember_owner_review') : null;

        //GET SORTING ORDER
        $this->view->reviewOrder = $params['order'] = $this->_getParam('order', 'creationDate');
        $this->view->rating_value = $this->_getParam('rating_value', 0);
        $params['rating'] = 'rating';
        $params['rating_value'] = $this->view->rating_value;
        $params['owner_id'] = $user_id;
        $params['resource_type'] = $user->getType();
        $params['type'] = 'user';
        $this->view->params = $params;
        $paginator = $reviewTable->listReviews($params);
        $this->view->paginator = $paginator->setItemCountPerPage($setItemCountPerPage);
        $this->view->current_page = $current_page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator->setCurrentPageNumber($current_page);

        if (empty($sitemember_owner_review))
            $this->view->setNoRender();

        //GET TOTAL REVIEWS
        $this->_childCount = $this->view->totalReviews = $paginator->getTotalItemCount();

        $this->view->total_reviewcats = 0;
        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemember.reviews.ratings', 2) == 2) {
            $profiletypeIdsArray = array();
            $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($user);
            if (!empty($fieldsByAlias['profile_type'])) {
                $optionId = $fieldsByAlias['profile_type']->getValue($user);
                if ($optionId) {
                    $optionObj = Engine_Api::_()->fields()
                            ->getFieldsOptions($user)
                            ->getRowMatching('option_id', $optionId->value);
                    if ($optionObj) {
                        $profiletypeIdsArray[] = $optionObj->option_id;
                    }
                }
            }
            $this->view->reviewCategory = Engine_Api::_()->getDbtable('ratingparams', 'sitemember')->memberParams($profiletypeIdsArray, $user->getType());

            $this->view->total_reviewcats = Count($this->view->reviewCategory);

            $this->view->ratingDataTopbox = $ratingTable->ratingbyCategory($user_id, 'user', $user->getType());
        }

        //CUSTOM FIELDS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}