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
class Sitemember_Widget_MostRecommendTableViewController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $values['viewMembers'] = 'recommend_count';
        $values['limit'] = $itemCount = $this->_getParam('itemCount', 10);
        $values = array_merge($values, $this->_getAllParams());
        $values = array_merge($values, $_GET);
        $this->view->circularImage = $values['circularImage'] = $this->_getParam('circularImage', 0);
        $this->view->values = $values = $this->_getParam('values', $values);
        $searchWidgetName = Engine_Api::_()->sitemember()->getContentWidgetName('sitemember_review_most-recommended-members');
        $customFieldValues = array();
        if ($searchWidgetName == 'sitemember.search-sitemember') {
            $form = new Sitemember_Form_Search();
            $customFieldValues = array_intersect_key($values, $form->getFieldElements());
        } else if ($searchWidgetName == 'sitemember.searchbox-sitemember') {
            $form = new Sitemember_Form_Searchbox();
            $customFieldValues = $values;
        }
        $this->view->page = $this->_getParam('page', 1);
        $this->view->isAjax = $this->_getParam('isAjax', null);

        if ($this->view->isAjax) {
            $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        }
        $this->view->paginator = $paginator = Engine_Api::_()->sitemember()->getUsersSelect($values, $customFieldValues);
        $paginator->setItemCountPerPage($itemCount);
        $this->view->paginator = $paginator->setCurrentPageNumber($this->view->page);
        $this->view->totalResults = $paginator->getTotalItemCount();
    }

}