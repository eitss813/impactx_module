<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 6590 2015-1-22 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfundingintegration_Form_Admin_Module_Add extends Engine_Form {

    public function init() {

        $this->setTitle('Add New Module')
                ->setDescription('Use the form below to enable users to create, edit, view and perform various actions on projects for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

        $notInclude = array('activity', 'advancedactivity', 'sitealbum', 'sitecontentcoverphoto', 'sitepageoffer', 'sitepagebadge', 'featuredcontent', 'sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon', 'seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitetagcheckin', 'sitereviewlistingtype', 'sitegroupoffer', 'sitepageintegration', 'sitebusinessintegration', 'sitegroupintegration', 'sitepagemember', 'sitebusinessmember', 'sitegroupmember', 'sitemailtemplates', 'sitepageurl', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', 'sitestoreinvite', 'sitestorelikebox', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'communityad', 'communityadsponsored', 'sitelike', 'sitestorelikebox', 'sitemobile', 'siteusercoverphoto', 'sitevideo', 'videodocument', 'sitecoupon', 'sitefaq', 'sitegroupadmincontact', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupform', 'sitegroupinvite', 'sitegrouplikebox', 'sitegroupurl', 'sitevideoview', 'sitebusinessurl', 'sitestoreinvite', 'nestedcomment', 'sitemobileapp', 'album', 'blog', 'document', 'video', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepagevideo', 'sitepagereview', 'sitepagedocument', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessvideo', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupvideo', 'sitegroupnote', 'sitegrouppoll'
            , 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview', 'sitevideoinvite', 'sitevideoadmincontact', 'sitevideoemail', 'sitevideodocument', 'classified', 'younet-core', 'sitevideorepeat', 'bigstep', 'siteestore', 'sitevideo' ,'sitecrowdfunding');
        $module_table = Engine_Api::_()->getDbTable('modules', 'core');
        $module_name = $module_table->info('name');
        $select = $module_table->select()
                ->from($module_name, array('name', 'title'))
                ->where($module_name . '.type =?', 'extra')
                ->where($module_name . '.name not in(?)', $notInclude)
                ->where($module_name . '.enabled =?', 1);

        $contentModuloe = $select->query()->fetchAll();
        $contentModuloeArray = array();

        if (!empty($contentModuloe)) {
            $contentModuloeArray[] = '';
            foreach ($contentModuloe as $modules) {
                $contentModuloeArray[$modules['name']] = $modules['title'];
            }
        }

        $type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null);
        if (!empty($contentModuloeArray)) {
            $this->addElement('Select', 'item_module', array(
                'label' => 'Content Module',
                'allowEmpty' => false,
                'onchange' => "setModuleName(this.value, '$type')",
                'multiOptions' => $contentModuloeArray,
            ));
        } else {
            //VALUE FOR LOGO PREVIEW.
            $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>";
            $this->addElement('Dummy', 'item_module', array(
                'description' => $description,
            ));
            $this->item_module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        }

        $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);

        $contentItem = array();
        if (!empty($module)) {
            $this->item_module->setValue($module);
            $contentItem = $this->getContentItem($module);
            if (empty($contentItem))
                $this->addElement('Dummy', 'dummy_title', array(
                    'description' => 'For this module, there is  no item defined in the manifest file.',
                ));
        }
        if (!empty($contentItem)) {
            $this->addElement('Select', 'item_type', array(
                'label' => 'Database Table Item',
                'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
                //  'required' => true,
                'multiOptions' => $contentItem,
            ));

            //ELEMENT PACKAGE TITLE
            $this->addElement('Text', 'item_title', array(
                'label' => 'Title',
                'description' => 'Enter the title for the content module which will be displayed in various widgets to identify the projects associated with it. (Ex: For Events: Events Projects, For Pages: Pages Projects, For Stores: Stores Projects, etc.)',
                'allowEmpty' => FALSE,
                'validators' => array(
                    array('NotEmpty', true),
                )
            ));


            $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
            $item_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_type', null);
            $itemTypeValue = $item_type;
            $integratedTable = Engine_Api::_()->getDbTable('modules', 'sitecrowdfunding');
            if(isset($id) && !empty($id))
            {
                $integratedTableResult = $integratedTable->fetchRow(array('module_id = ?' => $id));
                $integratedRowValues = $integratedTableResult->toArray();
                $itemTypeValue = $integratedRowValues['item_type'];
            }
            elseif (isset($item_type) && !empty($item_type))
            {
                $integratedTableResult = $integratedTable->fetchRow(array('item_type = ?' => $item_type));
                if(is_array($integratedTableResult))
                {
                    $integratedRowValues = $integratedTableResult->toArray();
                    $itemValue = $integratedRowValues['module_id'];                    
                }
            }

            $this->addElement('Radio', "sitecrowdfunding_project_leader_owner_" . $itemTypeValue, array(
                'label' => 'Project Owner',
                'description' => 'Which entity do you want to be associated with this content module’s projects as their owner. The Chosen entity will be displayed with the projects as their owner at various places like widgets, activity feeds, etc.',
                'multiOptions' => array(
                    0 => 'Creator (User)',
                    1 => 'Parent Content'
                ),
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("sitecrowdfunding.project.leader.owner.$itemTypeValue", 1)
            ));

            $this->addElement('Checkbox', 'enabled', array(
                'description' => 'Enable Module for project in the Content',
                'label' => 'Enable Module for project in the Content.',
                'value' => 1
            ));

            $this->addElement('Hidden', 'type', array('order' => 3, 'value' => $type));

            // Element: execute
            $this->addElement('Button', 'execute', array(
                'label' => 'Save Settings',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array('ViewHelper'),
            ));

            // Element: cancel
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' or ',
                'ignore' => true,
                'link' => true,
                'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
                'decorators' => array('ViewHelper'),
            ));
        }
    }

    public function getContentItem($moduleName) {
        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $mixSettingsTable = Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding');
        $mixSettingsTableName = $mixSettingsTable->info('name');
        $moduleArray = $mixSettingsTable->select()
                ->from($mixSettingsTableName, "$mixSettingsTableName.item_type")
                ->where($mixSettingsTableName . '.item_module = ?', $moduleName)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
        $contentItem = array();
        if (@file_exists($file_path)) {
            $ret = include $file_path;
            if (isset($ret['items'])) {
                foreach ($ret['items'] as $item) {
                    if ($id) {
                        $contentItem[$item] = $item . " ";
                    } else {
                        if (!in_array($item, $moduleArray))
                            $contentItem[$item] = $item . " ";
                    }
                }
            }
        }
        return $contentItem;
    }

}
