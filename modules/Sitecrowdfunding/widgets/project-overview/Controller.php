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
class Sitecrowdfunding_Widget_ProjectOverviewController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET PROJECT SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->showComments = $this->_getParam('showComments', 0);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitecrowdfunding.overview', 1)) {
            return $this->setNoRender();
        }

        if (Engine_Api::_()->sitecrowdfunding()->hasPackageEnable()) {
            if (!Engine_Api::_()->sitecrowdfunding()->allowPackageContent($project->package_id, "overview")) {
                return $this->setNoRender();
            }
        } else if (!Engine_Api::_()->authorization()->isAllowed('sitecrowdfunding_project', $viewer, "overview")) {
            // todo: Naaziya: 10th Jan 2020
            // return $this->setNoRender();
        }
        
        $params = array();
        $params = $this->_getAllParams();
        $params['resource_id'] = $project->project_id;
        $params['resource_type'] = $project->getType();
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
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $this->view->overview = $overview = $tableOtherinfo->getColumnValue($project->getIdentity(), 'overview');
     }

}
