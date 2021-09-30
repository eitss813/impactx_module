<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Widget_ProjectFollowersController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        // get params
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $params = $request->getParams();
        $params['resource_id'] = $project->project_id;
        $params['resource_type'] = $project->getType();

        // fetch all params
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');

                $this->view->showContent = true;
            }
        } else {
            $this->view->showContent = true;
        }

        // get followers for project
        $resource_id = $project->getIdentity();
        $resource_type = $project->getType();
        $paginator = Engine_Api::_()->getApi('favourite', 'seaocore')->peopleFavourite($resource_type, $resource_id);
        $this->view->paginator = $paginator;

    }

}
