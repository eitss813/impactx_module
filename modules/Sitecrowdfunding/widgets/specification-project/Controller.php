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
class Sitecrowdfunding_Widget_SpecificationProjectController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF NOT AUTHORIZED
        if (!Engine_Api::_()->core()->hasSubject('sitecrowdfunding_project'))
            return $this->setNoRender();
        $this->view->project = $project = Engine_Api::_()->core()->getSubject();
        //GET QUICK INFO DETAILS
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($project);
        if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
            $this->view->show_fields = $this->view->fieldValueLoop($project, $this->view->fieldStructure);
        }
        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }

        $tableOtherinfo = Engine_Api::_()->getDbTable('otherinfo', 'sitecrowdfunding');
        $this->view->address = $address = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_address');
        $this->view->phone = $phone = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_phone');
        $this->view->email = $email = $tableOtherinfo->getColumnValue($project->getIdentity(), 'contact_email');


        $fundingDatas = Engine_Api::_()->getDbTable('externalfundings','sitecrowdfunding')->getExternalFundingAmount($project->getIdentity());
        //$this->view->totalFundingAmount = $fundingDatas['totalFundingAmount'];
        $this->view->total_backer_count = $fundingDatas['memberCount'] + $fundingDatas['orgCount'];

        $sitecrowdfundingSpecificationProject = Zend_Registry::isRegistered('sitecrowdfundingSpecificationProject') ? Zend_Registry::get('sitecrowdfundingSpecificationProject') : null;

        if (empty($sitecrowdfundingSpecificationProject))
            return $this->setNoRender();
    }

}
