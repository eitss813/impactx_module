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
class Sitecrowdfunding_Widget_ProjectMilestoneController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {

            if($this->_getParam('project_id') != null){
                $this->view->project_id = $project_id = $this->_getParam('project_id');
                if ($project_id){
                    $this->view->project = $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
                }
            }else{
                //GET SUBJECT
                return $this->setNoRender();
            }
        }else{
            $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        }



        $this->view->statusLabels = array("yettostart" => "Yet to start", "inprogress" => "In Progress", 'completed'=> 'Completed');

        $this->view->milestones =  $milestones = Engine_Api::_()->getDbtable('milestones','sitecrowdfunding')->getAllMilestonesByProjectId($project->getIdentity());

        // Hide if the content is empty
        if (count($milestones) == 0){
            return $this->setNoRender();
        }

    }

}
