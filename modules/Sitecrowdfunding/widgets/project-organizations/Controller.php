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
class Sitecrowdfunding_Widget_ProjectOrganizationsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');
        $project_id = $project->getIdentity();
        $this->view->externalorganizations =  $externalorganizations = Engine_Api::_()->getDbtable('organizations','sitecrowdfunding')->fetchOrganizationByProjectId($project_id);
        $this->view->internalorganizations =  $internalorganizations = Engine_Api::_()->getDbtable('pages','sitecrowdfunding')->getPagesbyProjectId($project_id);


        // Hide if the content is empty
        if (count($externalorganizations) == 0 &&  count($internalorganizations) == 0 ){
            return $this->setNoRender();
        }

    }

}
