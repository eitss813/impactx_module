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
class Sitecrowdfunding_Widget_ProjectInitiativeAnswersController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $this->view->project_id = $project_id = $project->getIdentity();

        $initiativeAnswerTable = Engine_Api::_()->getDbtable('initiativeanswers', 'sitecrowdfunding');
        $this->view->projectInitiativeAnswers = $projectInitiativeAnswers = $initiativeAnswerTable->getProjectInitiativeAnswers($project_id);

        // Hide if the content is empty
         if (count($projectInitiativeAnswers) == 0){
           return $this->setNoRender();
         }

    }

}
