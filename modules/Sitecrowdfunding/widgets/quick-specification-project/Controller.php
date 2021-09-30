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
class Sitecrowdfunding_Widget_QuickSpecificationProjectController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            return $this->setNoRender();
        }

        $this->view->review = $review = '';
        if (Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project')) {
            $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        }

        //LISITNG SHOULD BE MAPPED WITH PROFILE
        if (empty($this->view->project->profile_type)) {
            return $this->setNoRender();
        }

        $itemCount = $this->_getParam('itemCount', 5);

        $sitecrowdfundingQuickSpecification = Zend_Registry::isRegistered('sitecrowdfundingQuickSpecification') ? Zend_Registry::get('sitecrowdfundingQuickSpecification') : null;
        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitecrowdfunding/View/Helper', 'Sitecrowdfunding_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($project);

        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSitecrowdfunding($project, $this->view->fieldStructure, $itemCount);
        } else {
            $this->view->show_fields = $this->view->FieldValueLoopQuickInfoSMSitecrowdfunding($project, $this->view->fieldStructure, $itemCount);
        }
        if (empty($sitecrowdfundingQuickSpecification))
            return $this->setNoRender();
        if (empty($this->view->show_fields)) {
            return $this->setNoRender();
        }
    }

}
