<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Widget_InfoSitepageController extends Engine_Content_Widget_Abstract {

    public function indexAction() {

        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        $this->view->layoutSetting = Engine_Api::_()->getApi("settings", "core")->getSetting('sitepage.layout.setting', 1);
        //GET SUBJECT
        $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

        //GET Total Followers Count
        $this->view->totalNoOfFollowers = Engine_Api::_()->sitepage()->getTotalNoOfFollowersCount($sitepage->page_id);

        $this->view->isManageAdmin = Engine_Api::_()->sitepage()->isPageOwner($sitepage);

        //SEND DATA TO TPL
        $layout = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
        $this->view->widgets = $widgets = Engine_Api::_()->sitepage()->getwidget($layout, $sitepage->page_id);
        $this->view->content_id = Engine_Api::_()->sitepage()->GetTabIdinfo('sitepage.info-sitepage', $sitepage->page_id, $layout);
        $this->view->module_tabid = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
        $this->view->identity_temp = $this->view->identity;
        $this->view->showtoptitle = Engine_Api::_()->sitepage()->showtoptitle($layout, $sitepage->page_id);
        $tableCategories = Engine_Api::_()->getDbTable('categories', 'sitepage');
        $this->view->category_name = $this->view->subcategory_name == $this->view->subsubcategory_name = '';
        if ($sitepage->category_id) {
            $categoriesNmae = $tableCategories->getCategory($sitepage->category_id);
            if (!empty($categoriesNmae->category_name)) {
                $this->view->category_name = $categoriesNmae->category_name;
            }

            if ($sitepage->subcategory_id) {
                $subcategory_name = $tableCategories->getCategory($sitepage->subcategory_id);
                if (!empty($subcategory_name->category_name)) {
                    $this->view->subcategory_name = $subcategory_name->category_name;
                }

                //GET SUB-SUB-CATEGORY
                if ($sitepage->subsubcategory_id) {
                    $subsubcategory_name = $tableCategories->getCategory($sitepage->subsubcategory_id);
                    if (!empty($subsubcategory_name->category_name)) {
                        $this->view->subsubcategory_name = $subsubcategory_name->category_name;
                    }
                }
            }
        }

        //GET TAGS
        $this->view->sitepageTags = $sitepage->tags()->getTagMaps();

        $pagesTable = Engine_Api::_()->getDbtable('pages', 'sitecrowdfunding');
        $projects = $pagesTable->getPageProjects($sitepage->page_id);

        $totalFundingAmount = 0;

//        foreach ($fundingResult as $key => $value ){
//            $item = Engine_Api::_()->getItem('sitecrowdfunding_project', $value);
//            $totalFundingAmount += $item->getFundedAmount(true);
//        }

        // funding amount
        $fundingTable = Engine_Api::_()->getDbtable('externalfundings', 'sitecrowdfunding');
        $fundings = $fundingTable->getFundingAmountByOrgId($sitepage->page_id);
        if(!empty($fundings) && isset($fundings['funding_amount'])){
            $totalFundingAmount = $fundings['funding_amount'];
        }
        // funding amount

        $this->view->totalNoOfProjects = count($projects);
        $this->view->totalFundingAmount =  Engine_Api::_()->sitecrowdfunding()->getPriceWithCurrency($totalFundingAmount);

        //CUSTOM FIELD WORK
        $this->view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitepage/View/Helper', 'Sitepage_View_Helper');
        $this->view->fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($sitepage);

    }

}

?>