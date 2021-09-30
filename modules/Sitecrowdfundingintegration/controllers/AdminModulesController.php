<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfundingintegration
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminModulesController.php 6590 2015-1-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfundingintegration_AdminModulesController extends Core_Controller_Action_Admin {

    protected $_enabledModuleNames;

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_modules');
        $this->view->integrated = $integrated = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules();
    }

    public function addAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_modules');

        $this->view->form = $form = new Sitecrowdfundingintegration_Form_Admin_Module_Add();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $integratedTable = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding');
                $resourceTypeTable = Engine_Api::_()->getItemTable($values['item_type']);
                $primaryId = current($resourceTypeTable->info("primary"));
                if (!empty($primaryId))
                    $values['item_id'] = $primaryId;
                $row = $integratedTable->createRow();
                $row->setFromArray($values);
                $row->save();

                foreach ($values as $key => $value) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sitecrowdfundingintegration/modules');
        }
    }

    public function editAction() {

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitecrowdfunding_admin_main', array(), 'sitecrowdfunding_admin_main_modules');

        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $integratedTable = Engine_Api::_()->getDbTable('modules', 'sitecrowdfunding');
        $integratedTableResult = $integratedTable->fetchRow(array('module_id = ?' => $id));
        $integratedRowValues = $integratedTableResult->toArray();
        $item_module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);
        $this->view->form = $form = new Sitecrowdfundingintegration_Form_Admin_Module_Edit();
        if (isset($integratedRowValues['item_membertype']) && !empty($integratedRowValues['item_membertype']))
            $integratedRowValues['item_membertype'] = unserialize($integratedRowValues['item_membertype']);
        $form->populate($integratedRowValues);

        if ($item_module == 'sitereview') {
            $form->item_type->setValue('sitereview_listing');
        }

        if ($this->getRequest()->isPost() && !$form->isValid($this->getRequest()->getPost())) {
            if ($item_module == 'sitereview') {
                $form->item_module->setValue('sitereview');
                $form->item_type->setValue('sitereview_listing');
            } else {
                $form->item_module->setValue($item_module);
                $form->item_type->setValue($integratedRowValues->item_type);
            }
            $form->item_module->setAttrib('disable', true);
            $form->item_type->setAttrib('disable', true);
            return;
        }

        $form->item_module->setAttrib('disable', true);
        $form->item_type->setAttrib('disable', true);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $item_type = $integratedTableResult->item_type;
            $item_module = $integratedTableResult->item_module;
            $item_id = $integratedTableResult->item_id;
            $type = $item_module . 'project_project';
            unset($values['item_module']);
            unset($values['item_type']);
            if (isset($values['item_membertype']) && !empty($values['item_membertype']))
                $values['item_membertype'] = serialize($values['item_membertype']);


            //BEGIN TRANSACTION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {
                $integratedTableResult->setFromArray($values);
                $integratedTableResult->save();

                foreach ($values as $key => $value) {
                    Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_redirect('admin/sitecrowdfundingintegration/modules');
        }
    }

    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->id = $id = $this->_getParam('id');
        $integratedTable = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding');
        $integratedTableSelect = $integratedTable->fetchRow(array('module_id = ?' => $id));
        $this->view->module = $integratedTableSelect->item_module;
        if ($this->getRequest()->isPost()) {
            $integratedTableSelect->delete();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
            ));
        }
    }

    public function enabledDisabledAction() {
        $id = $this->_getParam('id');
        $coreModuleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        $table = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding');
        $select = $table->select()->where('module_id =?', $id);
        $content = $table->fetchRow($select);
        $enabled = $content->enabled;
        $integrated = $content->integrated;
        $projectParams = '{"title":"Projects","itemCountPerPage":"10","margin_project":"2","projectOption":["title","owner","creationDate","view","like","comment","favourite","facebook","twitter","linkedin","googleplus"],"projectHeight":"265","projectWidth":"283","show_content":"2","nomobile":"0","name":"sitecrowdfunding.contenttype-projects"}';

        if ($content->enabled == 0 && $content->integrated == 0) {
            $item_type = $content->item_type;
            $item_module = $content->item_module;
            $item_id = $content->item_id;
            $name = $item_module . '_admin_main_manageproject';
            $db->query("UPDATE `engine4_core_menuitems` SET `enabled` = '1' WHERE `engine4_core_menuitems`.`name` = '$name';");

            if ($item_module == 'sitepage' || $item_module == 'sitebusiness' || $item_module == 'sitegroup') {

                $type = $item_module . 'project_project';
                $explodedArray = explode('_', $item_id);
                $itemShortType = 'getWidgetized' . ucfirst($explodedArray[0]);
                if (Engine_Api::_()->$item_module()->$itemShortType()) {
                    $page_id = Engine_Api::_()->$item_module()->$itemShortType()->page_id;
                }
                Engine_Api::_()->getDbtable('admincontent', $item_module)->setAdminDefaultInfo('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '117', $projectParams);
                if (Engine_Api::_()->hasModuleBootstrap('sitemobile')) {
                    $itemMobileShortType = 'getMobileWidgetized' . ucfirst($explodedArray[0]);

                    $mobilepage_id = Engine_Api::_()->$item_module()->$itemMobileShortType()->page_id;
                    if ($mobilepage_id) {
                        Engine_Api::_()->getDbtable('mobileadmincontent', $item_module)->setAdminDefaultInfo('sitecrowdfunding.contenttype-projects', $mobilepage_id, 'Projects', 'true', '117', $projectParams);

                        Engine_Api::_()->getApi('mobilelayoutcore', $item_module)->setContentDefaultInfo('sitecrowdfunding.contenttype-projects', $mobilepage_id, 'Projects', 'true', '117', $projectParams);
                    }
                }


                Engine_Api::_()->getApi('layoutcore', $item_module)->setContentDefaultInfo('sitecrowdfunding.contenttype-projects', $page_id, 'Projects', 'true', '117', $projectParams);

                if ($item_module == 'sitepage') {

                    //GET SITEPAGE CONTENT TABLE
                    $sitepagecontentTable = Engine_Api::_()->getDbtable('content', 'sitepage');
                    //GET SITEPAGE CONTENT PAGES TABLE
                    $sitepagepageTable = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
                    $selectsitepagePage = $sitepagepageTable->select()
                            ->from($sitepagepageTable->info('name'), array('contentpage_id'))
                            ->where('name =?', 'sitepage_index_view');
                    $contentpages_id = $selectsitepagePage->query()->fetchAll();
                    foreach ($contentpages_id as $key => $value) {
                        $sitepagecontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['contentpage_id'], 'Projects', 'true', '117', $projectParams);
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedPage()) {

                        //GET SITEPAGE CONTENT TABLE
                        $sitepagemobilecontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
                        //GET SITEPAGE CONTENT PAGES TABLE
                        $sitepagemobilecontentpagesTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
                        $selectsitepagemobilePage = $sitepagemobilecontentpagesTable->select()
                                ->from($sitepagemobilecontentpagesTable->info('name'), array('mobilecontentpage_id'))
                                ->where('name =?', 'sitepage_index_view');
                        $contentmobilepages_id = $selectsitepagemobilePage->query()->fetchAll();
                        foreach ($contentmobilepages_id as $key => $value) {
                            $sitepagemobilecontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['mobilecontentpage_id'], 'Projects', 'true', '117', $projectParams);
                        }
                    }

                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'sitecrowdfunding.project.leader.owner.sitepage.page', '1');");
                    if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
                        Engine_Api::_()->sitepage()->oninstallPackageEnableSubMOdules('sitecrowdfunding');
                    }
                } elseif ($item_module == 'sitebusiness') {
                    //GET SITEBUSINESS CONTENT TABLE
                    $sitebusinesscontentTable = Engine_Api::_()->getDbtable('content', 'sitebusiness');
                    //GET SITEBUSINESS CONTENT PAGES TABLE
                    $sitebusinessbusinessTable = Engine_Api::_()->getDbtable('contentbusinesses', 'sitebusiness');
                    $selectsitebusinessBusiness = $sitebusinessbusinessTable->select()
                            ->from($sitebusinessbusinessTable->info('name'), array('contentbusiness_id'))
                            ->where('name =?', 'sitebusiness_index_view');
                    $contentbusinesses_id = $selectsitebusinessBusiness->query()->fetchAll();
                    foreach ($contentbusinesses_id as $key => $value) {
                        $sitebusinesscontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['contentbusiness_id'], 'Projects', 'true', '117', $projectParams);
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedBusiness()) {
                        //GET SITEBUSINESS CONTENT TABLE
                        $sitebusinesscontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitebusiness');
                        //GET SITEBUSINESS CONTENT PAGES TABLE
                        $sitebusinessbusinessTable = Engine_Api::_()->getDbtable('mobileContentbusinesses', 'sitebusiness');
                        $selectsitebusinessBusiness = $sitebusinessbusinessTable->select()
                                ->from($sitebusinessbusinessTable->info('name'), array('mobilecontentbusiness_id'))
                                ->where('name =?', 'sitebusiness_index_view');
                        $contentbusinesses_id = $selectsitebusinessBusiness->query()->fetchAll();
                        foreach ($contentbusinesses_id as $key => $value) {
                            $sitebusinesscontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['mobilecontentbusiness_id'], 'Projects', 'true', '117', $projectParams);
                        }
                    }


                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'sitecrowdfunding.project.leader.owner.sitebusiness.business', '1');");
                    if (Engine_Api::_()->sitebusiness()->hasPackageEnable()) {
                        Engine_Api::_()->sitebusiness()->oninstallPackageEnableSubMOdules('sitecrowdfunding');
                    }
                } elseif ($item_module == 'sitegroup') {
                    //GET SITEGROUP CONTENT TABLE
                    $sitegroupcontentTable = Engine_Api::_()->getDbtable('content', 'sitegroup');
                    //GET SITEGROUP CONTENT PAGES TABLE
                    $sitegroupgroupTable = Engine_Api::_()->getDbtable('contentgroups', 'sitegroup');
                    $selectsitegroupGroup = $sitegroupgroupTable->select()
                            ->from($sitegroupgroupTable->info('name'), array('contentgroup_id'))
                            ->where('name =?', 'sitegroup_index_view');
                    $contentgroups_id = $selectsitegroupGroup->query()->fetchAll();
                    foreach ($contentgroups_id as $key => $value) {
                        $sitegroupcontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['contentgroup_id'], 'Projects', 'true', '117', $projectParams);
                    }
                    $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'sitecrowdfunding.project.leader.owner.sitegroup.group', '0');");
                    if (Engine_Api::_()->sitegroup()->hasPackageEnable()) {
                        Engine_Api::_()->sitegroup()->oninstallPackageEnableSubMOdules('sitecrowdfunding');
                    }

                    if (Engine_Api::_()->hasModuleBootstrap('sitemobile') && Engine_Api::_()->$item_module()->getMobileWidgetizedGroup()) {
                        //GET SITEGROUP CONTENT TABLE
                        $sitegroupcontentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitegroup');
                        //GET SITEGROUP CONTENT PAGES TABLE
                        $sitegroupgroupTable = Engine_Api::_()->getDbtable('mobileContentgroups', 'sitegroup');
                        $selectsitegroupGroup = $sitegroupgroupTable->select()
                                ->from($sitegroupgroupTable->info('name'), array('mobilecontentgroup_id'))
                                ->where('name =?', 'sitegroup_index_view');
                        $contentgroups_id = $selectsitegroupGroup->query()->fetchAll();
                        foreach ($contentgroups_id as $key => $value) {
                            $sitegroupcontentTable->setDefaultInfo('sitecrowdfunding.contenttype-projects', $value['mobilecontentgroup_id'], 'Projects', 'true', '117', $projectParams);
                        }
                    }
                }

                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"auth_sprcreate" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","owner","member","like_member"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');
                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"sprcreate" as `name`, 
							1 as `value`, 
							NULL as `params`
						FROM `engine4_authorization_levels`;
					');

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");
            } else if ($item_module == 'sitereview') {
                $type = $item_module . 'project_project';
                $explodedArray = explode('_', $item_id);
                $explodedItemTypeArray = explode('_', $item_type);
                $listingTypeId = $explodedItemTypeArray[2];

                $db = Engine_Db_Table::getDefaultAdapter();

                $value = "auth_sprcreate_listtype_$listingTypeId";

                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"sitereview_listing" as `type`, 
							"' . $value . '" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","owner"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');

                $value = "sprcreate_listtype_$listingTypeId";

                $db->query("
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT 
									level_id as `level_id`, 
									'sitereview_listing' as `type`, 
									'$value' as `name`, 
									1 as `value`, 
									NULL as `params` 
						FROM `engine4_authorization_levels` WHERE `type` IN('moderator','admin','user');
					");
                $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingTypeId);
                $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'sitecrowdfunding.project.leader.owner.sitereview.listing. $listingTypeId', '0');");
                //MEMBER PROFILE PAGE WIDGETS
                $page_id = $db->select()
                        ->from('engine4_core_pages', array('page_id'))
                        ->where('name =?', 'sitereview_index_view_listtype_' . $listingTypeId)
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                
                $content_id = $db->select()
                                ->from('engine4_core_content',array('content_id'))
                                ->where('page_id = ?',$page_id)
                                ->where('name = ?','core.container-tabs')
                                ->limit(1)
                                ->query()
                                ->fetchColumn();
                if($content_id){
                    $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `params`, `order`) VALUES ($page_id,'widget','sitecrowdfunding.contenttype-projects',$content_id,'$projectParams',99);");
                }
            } else if ($item_module == 'siteevent') {
                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"auth_sprcreate" as `name`, 
							5 as `value`, 
							\'["registered","owner_network","owner_member_member","owner_member","member","leader"]\' as `params` 
						FROM `engine4_authorization_levels` WHERE `type` NOT IN("public");
					');
                $db->query('
						INSERT IGNORE INTO `engine4_authorization_permissions` 
						SELECT level_id as `level_id`, 
							"' . $item_type . '" as `type`, 
							"sprcreate" as `name`, 
							1 as `value`, 
							NULL as `params`
						FROM `engine4_authorization_levels`;
					');

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'view' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						2 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'comment' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('moderator', 'admin');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions`
					SELECT
						level_id as `level_id`,
						'$type' as `type`,
						'invite' as `name`,
						1 as `value`,
						NULL as `params`
					FROM `engine4_authorization_levels` WHERE `type` IN('user');");

                $db->query("INSERT IGNORE INTO `engine4_authorization_permissions` (`level_id`, `type`, `name`, `value`, `params`) VALUES ('5', '$type', 'view', '1', NULL);");
                $db->query("INSERT IGNORE INTO `engine4_core_settings` ( `name`, `value`) VALUES( 'sitecrowdfunding.project.leader.owner.siteevent.event', '0');");
                $page_id = $db->select()
                        ->from('engine4_core_pages', array('page_id'))
                        ->where('name =?', 'siteevent_index_view')
                        ->limit(1)
                        ->query()
                        ->fetchColumn();
                
                $content_id = $db->select()
                                ->from('engine4_core_content',array('content_id'))
                                ->where('page_id = ?',$page_id)
                                ->where('name = ?','core.container-tabs')
                                ->limit(1)
                                ->query()
                                ->fetchColumn();
                if($content_id){
                    $db->query("INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `params`, `order`) VALUES ($page_id,'widget','sitecrowdfunding.contenttype-projects',$content_id,'$projectParams',99);");
                }
            }
        }

        try {
            $content->enabled = !$content->enabled;
            $content->integrated = 1;
            $content->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/sitecrowdfundingintegration/modules');
    }

}
