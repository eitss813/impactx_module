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
class Sitecrowdfunding_Widget_AdditionalController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {

        //GET SUBJECT
        $this->view->project = $project = Engine_Api::_()->core()->getSubject('sitecrowdfunding_project');

        $project_id = $project->getIdentity();

        $db = Engine_Db_Table::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $this->view->additional_section = $additional_section = $select
            ->from('engine4_sitecrowdfunding_projects_additionalsection', '*')
            ->where('project_id = ?', $project_id)
            ->query()->fetchAll();

        if(!$this->view->additional_section){
            return $this->setNoRender();
        }
    }

}
