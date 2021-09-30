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
class Sitecrowdfunding_Widget_ProjectOutcomeOutputController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        $this->view->project_id = $project_id = $project->getIdentity();

        //GET PROJECT ITEM
        $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        // Get outcome and output
        $this->view->outcomes =  $outcomes = Engine_Api::_()->getDbtable('outcomes','sitecrowdfunding')->getAllOutcomesByProjectId($project_id);
        $this->view->outputs =  $output = Engine_Api::_()->getDbtable('outputs','sitecrowdfunding')->getAllOutputsByProjectId($project_id);

        // Hide if the content is empty
        if (count($outcomes) == 0 &&  count($output) == 0 ){
            return $this->setNoRender();
        }

    }
}
