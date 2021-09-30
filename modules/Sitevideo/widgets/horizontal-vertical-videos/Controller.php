<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Widget_HorizontalVerticalVideosController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        if ($this->_getParam('is_ajax_load', false)) {
            $this->view->is_ajax_load = true;
            if ($this->_getParam('contentpage', 1) > 1)
                $this->getElement()->removeDecorator('Title');
            $this->getElement()->removeDecorator('Container');
        } else {
            $this->view->is_ajax_load = true;
        }
        $this->view->vertical = $values['viewType'] = $this->_getParam('viewType', 0);
        $values = array();
        $this->view->category_id = $values['category_id'] = $this->_getParam('hidden_video_category_id');
        $this->view->subcategory_id = $values['subcategory_id'] = $this->_getParam('hidden_video_subcategory_id');
        $this->view->subsubcategory_id = $values['subsubcategory_id'] = $this->_getParam('hidden_video_subsubcategory_id');
        $this->view->showPagination = $values['showPagination'] = $this->_getParam('showPagination', 1);
        $this->view->interval = $values['interval'] = $this->_getParam('interval', 300);
        $this->view->blockHeight = $values['blockHeight'] = $this->_getParam('blockHeight', 240);
        $this->view->blockWidth = $values['blockWidth'] = $this->_getParam('blockWidth', 150);
        $this->view->viewType = $values['viewType'] = $this->_getParam('viewType', 0);
        $this->view->limit = $values['selectLimit'] = $this->_getParam('itemCount', 3);
        $this->view->videoOption = $values['videoOption'] = $this->_getParam('videoOption', array('title'));
        $this->view->orderby = $values['orderby'] = $this->_getParam('orderby', 'creationDate');
        if ($values['orderby'] == 'creationDate')
            $this->view->orderby = $values['orderby'] = 'creation_date';

        $this->view->params = $params = $values;
        if (empty($this->view->videoOption))
            $this->view->videoOption = array();
        //FETCH VIDEOS
        $this->view->videos = $videos = Engine_Api::_()->getDbTable('videos', 'sitevideo')->getVideoPaginator($params);
        $videos->setItemCountPerPage($params['selectLimit']);
        //GET LIST COUNT
        $this->view->totalCount = $videos->getTotalItemCount();
        if (($this->view->totalCount <= 0)) {
            return $this->setNoRender();
        }
    }

}

?>