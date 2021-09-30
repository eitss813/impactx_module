<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_IndexController extends Seaocore_Controller_Action_Standard
{

  protected $_navigation;

  //SET THE VALUE FOR ALL ACTION DEFAULT
  public function init()
  {

    //CHECK VIEW PRIVACY
    if( !$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'view')->isValid() )
      return;

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext
    ->addActionContext('rate', 'json')
    ->addActionContext('validation', 'html')
    ->initContext();

    //GET PAGE ID AND PAGE URL
    $page_url = $this->_getParam('page_url', null);
    $page_id = $this->_getParam('page_id', null);

    if( $page_url ) {
      $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
    }

    if( $page_id ) {
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if( $sitepage && !Engine_Api::_()->core()->hasSubject() ) {
        Engine_Api::_()->core()->setSubject($sitepage);
      }
    }

    //FOR UPDATE EXPIRATION
    if( (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.task.updateexpiredpages') + 900) <= time() ) {
      Engine_Api::_()->sitepage()->updateExpiredPages();
    }
  }

  //ACTION FOR SHOWING THE PAGE LIST
  public function indexAction()
  {

    $searchForm = new Sitepage_Form_Search(array('type' => 'sitepage_page'));
    Zend_Registry::set('Sitepage_Form_Search', $searchForm);

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
      ->setNoRender()
      ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE PAGE LIST
  public function pinboardBrowseAction()
  {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
      ->setNoRender()
      ->setContentName("sitepage_index_pinboard_browse")
      ->setEnabled();
    }
  }

  //ACTION FOR SHOWING THE HOME PAGE
  public function homeAction()
  {

    $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
    $coreversion = $coremodule->version;
    if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
      $this->_helper->content->render();
    } else {
      $this->_helper->content
      ->setNoRender()
      ->setEnabled();
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
  public function mapAction()
  {

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);

    if( empty($enableLocation) ) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    } else {
      $this->_helper->content->setEnabled();
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
  public function mobilemapAction()
  {

    $enableLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.locationfield', 1);

    if( empty($enableLocation) ) {
      return $this->_forwardCustom('notfound', 'error', 'core');
    } else {
      $this->_helper->content->setEnabled();
    }
  }

  //ACTION FOR SHOWING SPONSERED PAGE AT HOME PAGE
  public function homeSponsoredAction()
  {

    //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
    $limit_sitepage = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.sponserdsitepage.widgets', 4);

    $totalSitepage = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Total Sponsored Sitepage', array(), null, null, array('page_id'));

    // Total Count Sponsored Page
    $totalCount = $totalSitepage->count();

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];

    if( $startindex > $totalCount ) {
      $startindex = $totalCount - $limit_sitepage;
    }

    if( $startindex < 0 ) {
      $startindex = 0;
    }

    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $this->view->direction = $_GET['direction'];
    $values['start_index'] = $startindex;
    $values['totalpages'] = $_GET['limit'];
    $values['category_id'] = $_GET['category_id'];
    $this->view->titletruncation = $_GET['titletruncation'];
    $this->view->totalpages = $_GET['limit'];

    // Sitepage Sitepage Sponsored
    $this->view->sitepages = $result = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings('Sponsored Sitepage AJAX', $values, null, null, array('page_id', 'photo_id', 'owner_id', 'title', 'page_url'));

    //Pass the total number of result in tpl file
    $this->view->count = count($result);
  }

  //ACTION FOR SHOWING SPONSORED LISTINGS IN WIDGET
  public function ajaxCarouselAction()
  {

    //SEAOCORE API
    $this->view->seacore_api = Engine_Api::_()->seaocore();

    //RETURN THE OBJECT OF LIMIT PER PAGE FROM CORE SETTING TABLE
    $this->view->sponserdSitepagesCount = $limit_sitepage = $_GET['curnt_limit'];
    $limit_sitepage_horizontal = $limit_sitepage * 2;

    $values = array();
    $values = $this->_getAllParams();

    //GET COUNT
    $totalCount = $_GET['total'];

    //RETRIVE THE VALUE OF START INDEX
    $startindex = $_GET['startindex'];

    if( $startindex > $totalCount ) {
      $startindex = $totalCount - $limit_sitepage;
    }

    if( $startindex < 0 ) {
      $startindex = 0;
    }

    $this->view->sponsoredIcon = $this->_getParam('sponsoredIcon', 1);
    //$this->view->showOptions = $this->_getParam('showOptions', array("category", "rating", "review"));
    $this->view->featuredIcon = $this->_getParam('featuredIcon', 1);
    //RETRIVE THE VALUE OF BUTTON DIRECTION
    $this->view->direction = $_GET['direction'];
    $values['start_index'] = $startindex;
    $sitepageTable = Engine_Api::_()->getDbTable('pages', 'sitepage');
    $this->view->totalItemsInSlide = $values['limit'] = $limit_sitepage_horizontal;
    $this->view->popularity = $values['popularity'] = $this->_getParam('popularity', 'page_id');
    $this->view->fea_spo = $fea_spo = $this->_getParam('fea_spo', null);
    if( $fea_spo == 'featured' ) {
      $values['featured'] = 1;
    } elseif( $fea_spo == 'sponsored' ) {
      $values['sponsored'] = 1;
    } elseif( $fea_spo == 'fea_spo' ) {
      $values['sponsored'] = 1;
      $values['featured'] = 1;
    }

    //GET LISTINGS
    $this->view->sitepages = $sitepageTable->getListing('', $values);
    $this->view->count = count($this->view->sitepages);
    $this->view->vertical = $_GET['vertical'];
    $this->view->ratingType = $this->_getParam('ratingType', 'rating');
    $this->view->title_truncation = $this->_getParam('title_truncation', 50);
    $this->view->blockHeight = $this->_getParam('blockHeight', 245);
    $this->view->blockWidth = $this->_getParam('blockWidth', 150);
    $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
  }

  //ACTION FOR PAGE PROFILE PAGE
  public function viewAction()
  {

    if( !Engine_Api::_()->core()->hasSubject('sitepage_page') )
      return $this->_forwardCustom('notfound', 'error', 'core');

    //VIEWER INFORMATION
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->viewer_id = $viewer_id = $viewer->getIdentity();

    //GET SUBJECT AND PAGE ID
    $this->view->sitepage = $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');

    $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);
    $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);

    //Start page member work for privacy.
    $this->view->sitepageMemberEnabled = $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');

    // Display layout even if not logged in
    /*
    if( empty($sitepageMemberEnabled) ) {
      $this->view->member_approval = $sitepage->member_approval;
      $this->view->hasMembers = Engine_Api::_()->getDbTable('membership', 'sitepage')->hasMembers($viewer_id, $sitepage->page_id);

      //START MANAGE-ADMIN CHECK
      $this->view->viewPrivacy = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
      if( empty($isManageAdmin) ) {
        if( !$sitepage->isViewableByNetwork() ) {
          return $this->_forwardCustom('requireauth', 'error', 'core');
        } else {
          return;
        }
      }

      //PAGE VIEW AUTHORIZATION
      if( !Engine_Api::_()->sitepage()->canViewPage($sitepage) ) {
        return;
      }
    } else {
      //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'view');
      if( empty($isManageAdmin) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

      //PAGE VIEW AUTHORIZATION
      if( !Engine_Api::_()->sitepage()->canViewPage($sitepage) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    }*/
    //End page member work for privacy.

    $LevelHost = $this->checkLevelHost($levelHost, 'sitepage');

    //INCREMENT IN NUMBER OF VIEWS
    $PackagesHost = $this->checkPackageHost($package);

    if( ($PackagesHost != $LevelHost) && ($sitepage->view_count % 20 == $maxView) ) {
      Engine_Api::_()->sitepage()->setDisabledType();
      Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
    }

    $edit_layout_setting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0);
    //OPEN TAB IN NEW PAGE
    if( !$edit_layout_setting && $this->renderWidgetCustom() )
      return;

    //OPEN TAB IN NEW PAGE
    if( $edit_layout_setting && $this->renderUserWidgetCustom() )
      return;

    $this->view->can_edit_overview = $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');

    $this->view->headLink()
    ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage_profile.css');

    $commonCss = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.common.css');
    $businessEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness');
    $groupEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup');
    if( $commonCss && $businessEnabled && $groupEnabled ) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business_group.css');
    } elseif( $commonCss && $businessEnabled ) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_business.css');
    } elseif( $commonCss && $groupEnabled ) {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/common_style_page_group.css');
    } else {
      $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/styles/style_sitepage.css');
    }

    if( !$sitepage->all_post && !Engine_Api::_()->sitepage()->isPageOwner($sitepage) ) {
      $this->view->headStyle()->appendStyle(".activity-post-container{
        display:none;
      }");
      $this->view->headStyle()->appendStyle(".adv_post_container_box{
        display:none;
      }");
    }
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') ) {
      $this->view->headLink()
      ->appendStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Sitepagealbum/externals/styles/style_sitepagealbum.css'
    );
    }

    //CALL FUNCTION FOR INCRESING THE MEMORY LIMIT
    $this->setPhpIniMemorySize();
    $maxView = 19;

    $pageStatistics = <<<EOF
    en4.core.runonce.add(function(){
      en4.sitepage.pageStatistics("$sitepage->page_id");
      });
EOF;
      $this->view->headScript()->appendScript($pageStatistics);

      $style = $sitepage->getPageStyle();
      if( !empty($style) ) {
        $this->view->headStyle()->appendStyle($style);
      }

      if( null !== ($tab = $this->_getParam('tab')) ) {
      //PROVIDE WIDGETISE PAGES
        $pageprofile_tab_function = <<<EOF
        var content_id = "$tab";
        this.onload = function()
        {
          if(window.tabContainerSwitch)
          {
            tabContainerSwitch($('main_tabs').getElement('.tab_' + content_id));
          }
        }
EOF;
        $this->view->headScript()->appendScript($pageprofile_tab_function);
      }

      $layoutSetting = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage_layout_enable', 0);
      if ($layoutSetting == 0 || (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension',0))) {
        if( !empty($edit_layout_setting) ) {
          $showHideHeaderFooter = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.show.hide.header.footer', 'default');
          if( $showHideHeaderFooter == 'default-simple' ) {
            $this->_helper->layout->setLayout('default-simple');
          }
          $cont = Engine_Content::getInstance();
          if( Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
            $storage = Engine_Api::_()->getDbtable('contentpages', 'sitepage');
          } else {
            $storage = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
          }
          $cont->setStorage($storage);
          $this->view->sitemain = $this->view->content('sitepage_index_view');
          $cont = Engine_Content::getInstance();
          $storage = Engine_Api::_()->getDbtable('pages', 'core');
          $cont->setStorage($storage);
        } else {
          $this->_helper->content
                ->setContentName('sitepage_index_view')
                //->setNoRender()
                ->setEnabled();
        }
      } else {
        $pageName = 'sitepage_index_view';
        if($layoutSetting == 1) {
         $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($sitepage->category_id);
         $details = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayout($row->layout_id);
         if($details['status'] == 1) {
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageName = $pageTable->select()
                              ->from($pageTable->info('name'), array('name'))
                              ->where('page_id = ?',$details['page_id'])
                              ->query()
                              ->fetchColumn();
            if( !empty($details['style']))
              $this->view->headStyle()->appendStyle($details['style']);
            }
        } else if ($layoutSetting == 2) {
          $table = Engine_Api::_()->getDbtable('packages', 'sitepage');
          $select = $table->select()
              ->from($table->info('name'), array('layout_id'))
              ->where('package_id =?',$sitepage->package_id);
         $id = $table->fetchRow($select);
         $details = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayout($id->layout_id);
         if($details['status'] == 1) {
            $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
            $pageName = $pageTable->select()
                              ->from($pageTable->info('name'), array('name'))
                              ->where('page_id = ?',$details['page_id'])
                              ->query()
                              ->fetchColumn();
            if( !empty($details['style']))
              $this->view->headStyle()->appendStyle($details['style']);
            }
        } else if ($layoutSetting == 3) {
          $details = Engine_Api::_()->getDbTable('definedlayouts', 'sitepage')->getLayout($sitepage->layout_id);
         if($details['status'] == 1) {
          $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
          $pageName = $pageTable->select()
                            ->from($pageTable->info('name'), array('name'))
                            ->where('page_id = ?',$details['page_id'])
                            ->query()
                            ->fetchColumn();
            if( !empty($details['style']))
              $this->view->headStyle()->appendStyle($details['style']);
            }
        }
          $this->_helper->content
                ->setContentName($pageName)
                //->setNoRender()
                ->setEnabled();
    }

    // Start: Suggestion work.
      $is_moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
    // Here we are delete this poll suggestion if viewer have.
      if( !empty($is_moduleEnabled) ) {
        Engine_Api::_()->getApi('suggestion', 'sitepage')->deleteSuggestion($viewer->getIdentity(), 'sitepage', $sitepage->page_id, 'page_suggestion');
      }
    // End: Suggestion work.
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
      if( !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
        if( !Zend_Registry::isRegistered('sitemobileNavigationName') ) {
          Zend_Registry::set('sitemobileNavigationName', 'setNoRender');
        }
      }
      if( Engine_Api::_()->seaocore()->isSitemobileApp() ) {
        Zend_Registry::set('setFixedCreationFormBack', 'Back');
      }
    }

  //ACTINO FOR MANAGING MY PAGES
    public function manageAction()
    {
     //USER VALDIATION
      if( !$this->_helper->requireUser()->isValid() )
        return;

      $this->view->autoContentLoad = $isappajax = $this->_getParam('isappajax', 0);

      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();

    //PAGE CREATION PRIVACY
      $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->checkRequire();

      if( empty($this->view->can_create) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

      if( !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
        $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'edit')->checkRequire();
        $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);
        $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
        $this->view->can_delete = $this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'delete')->checkRequire();

        Engine_Api::_()->getDbtable('pagestatistics', 'sitepage')->setViews();

      //GET VIEWER CLAIMS
        $claim_id = Engine_Api::_()->getDbtable('claims', 'sitepage')->getViewerClaims($viewer_id);

      //CLAIM IS ENABLED OR NOT
        $canClaim = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'claim');

        $this->view->showClaimLink = 0;
        if( !empty($claim_id) && !empty($canClaim) ) {
          $this->view->showClaimLink = 1;
        }

      //NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitepage_main');

      //QUICK NAVIGATION
        $this->view->quickNavigation = $quickNavigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('sitepage_quick');

      //FORM GENERATION
        $this->view->form = $form = new Sitepage_Form_Managesearch(array(
          'type' => 'sitepage_page'
        ));

        $form->removeElement('show');

      //SITEPAGE-REVIEW IS ENABLED OR NOT
        $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');

      //CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
        $adminpages = Engine_Api::_()->getDbtable('manageadmins', 'sitepage')->getManageAdminPages($viewer_id);

      //GET STUFF
        $manageadmin_ids = array();
        foreach( $adminpages as $adminpage ) {
          $manageadmin_ids[] = $adminpage->page_id;
        }
        $manageadmin_values = array();
        $manageadmin_values['adminpages'] = $manageadmin_ids;
        $manageadmin_values['orderby'] = 'creation_date';
        $manageadmin_data = Engine_Api::_()->sitepage()->getSitepagesPaginator($manageadmin_values, null);
        $this->view->manageadmin_count = $manageadmin_data->getTotalItemCount();
      //END CHECK THAT WE HAVE TO SHOW MANAGE ADMIN LINK OR NOTE
//    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') &&) {
//        $onlymember = Engine_Api::_()->getDbtable('membership', 'sitepage')->getJoinPages($viewer->getIdentity(), 'onlymember');
//
//        $onlymemberids = array();
//        foreach ($onlymember as $onlymembers) {
//          $onlymemberids[] = $onlymembers->page_id;
//        }
//        if (!empty($onlymemberids)) {
//            $values['adminjoinedpages'] = array_merge($onlymemberids, $manageadmin_values['adminpages']);
//        }
//    } else {
        $values['adminjoinedpages'] = $manageadmin_values['adminpages'];
      //}
      //PROCESS FORM
        $request = $this->getRequest()->getPost();

      //PROCESS FORM
        if( $form->isValid($this->_getAllParams()) ) {
          $values = $form->getValues();
          if( $values['subcategory_id'] == 0 ) {
            $values['subsubcategory_id'] = 0;
            $values['subsubcategory'] = 0;
          }
        } else {
          $values = array();
        }

      //CHECK TO SEE IF REQUEST IS FOR SPECIFIC USER'S PAGES
      // $values['user_id'] = $viewer->getIdentity();
        $values['type'] = 'manage';
        $values['type_location'] = 'manage';
        $this->view->formValues = $values;
      //GET PAGINATOR
        $this->view->paginator = $paginator = Engine_Api::_()->sitepage()->getSitepagesPaginator($values, null);
        $items_count = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.page', 10);

        $paginator->setItemCountPerPage($items_count);
        $this->view->paginator = $paginator->setCurrentPageNumber($values['page']);

        $this->view->page = $values['page'] ? $values['page'] : 1;
        $this->view->totalPages = ceil(($paginator->getTotalItemCount()) / $items_count);
      //MAXIMUM ALLOWED PAGES
      //WE HAVE IMPORT PAGES FUNCTIONALITY, WE DONT WANT TO SHOW PAGE LIMIT ALERT MESSAGE TO SUPERADMIN SO WE ARE SETTING $this->view->quota = 0;
        $this->view->quota = 0;
        if( $viewer->level_id != 1 ) {
          $this->view->quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'max');
        }
        $this->view->current_count = $paginator->getTotalItemCount();
        $this->view->category_id = $values['category_id'];
        $this->view->subcategory_id = $values['subcategory_id'];
        $this->view->subsubcategory_id = $values['subsubcategory_id'];

      //if (!Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if( !$isappajax ) {
          if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
            $this->_helper->content->render();
          } else {
            $this->_helper->content
            //->setNoRender()
            ->setEnabled();
          }
        }
      } else {
        $coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
        $coreversion = $coremodule->version;
        if( !$isappajax ) {
          if( empty(Engine_Api::_()->seaocore()->checkVersion($coreversion, '4.1.0')) ) {
            $this->_helper->content->render();
          } else {
            $this->_helper->content
            ->setNoRender()
            ->setEnabled();
          }
        }
      }
    //}
    }

  // create  sitepage sitepage
    public function createAction()
    {

    //USER VALIDATION
      if( !$this->_helper->requireUser()->isValid() )
        return;

      $this->view->quick = $quick = $this->_getParam('seaoSmoothbox', false);
      if( !$quick ) {
      //RENDER PAGE
        $this->_helper->content
        //->setNoRender()
        ->setEnabled()
        ;
      }

      if( Engine_Api::_()->getApi('settings', 'core')->hasSetting('sitepage.createFormFields') ) {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $createFormFields = $settings->getSetting('sitepage.createFormFields');
        if( !$this->_getParam('seaoSmoothbox', false) && !empty($createFormFields) && in_array('showHideAdvancedOptions', $createFormFields) ) {
          $this->view->quick = $quick = 1;
        }
      }

    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES
      if( !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
        Engine_API::_()->sitemobile()->setupRequestError();
      } else {
        $this->_helper->content->setEnabled();
      }

    //PAGE CREATE PRIVACY
      if( !$this->_helper->requireAuth()->setAuthParams('sitepage_page', null, 'create')->isValid() )
        return;
      $package_id = 0;
      $viewer = Engine_Api::_()->user()->getViewer();
      $sitepageHostName = str_replace('www.', '', @strtolower($_SERVER['HTTP_HOST']));
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sitepage_main', array(), 'sitepage_main_create');
      $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');

      $levelHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.level.createhost', 0);
      $package = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lsettings', 0);
      $LevelHost = $this->checkLevelHost($levelHost, 'sitepage');
      $PackagesHost = $this->checkPackageHost($package);
      $sub_status_table = Engine_Api::_()->getDbTable('pagestatistics', 'sitepage');

      if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
      //REDIRECT
        $package_id = $this->_getParam('id');
        if( empty($package_id) ) {
          return $this->_forwardCustom('notfound', 'error', 'core');
        }
        $this->view->package = $package = Engine_Api::_()->getItemTable('sitepage_package')->fetchRow(array('package_id = ?' => $package_id, 'enabled = ?' => '1'));
        if( empty($this->view->package) ) {
          return $this->_forwardCustom('notfound', 'error', 'core');
        }

        if( !empty($package->level_id) && !in_array($viewer->level_id, explode(",", $package->level_id)) ) {
          return $this->_forwardCustom('notfound', 'error', 'core');
        }
      } else {
        $package_id = Engine_Api::_()->getItemtable('sitepage_package')->fetchRow(array('defaultpackage = ?' => 1))->package_id;
      }

      $maxCount = 10;
      $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
      $tablename = $table->info('name');
      $select = $table->select()->from($tablename, array('count(*) as count'));
      $results = $table->fetchRow($select);
      if( ($PackagesHost != $LevelHost) && ($results->count > $maxCount) ) {
        Engine_Api::_()->sitepage()->setDisabledType();
        Engine_Api::_()->getItemtable('sitepage_package')->setEnabledPackages();
      }
      $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
      $row = $manageadminsTable->createRow();

    //FORM VALIDATION
      $this->view->form = $form = new Sitepage_Form_Create(array("packageId" => $package_id, "owner" => $viewer, 'quick' => $quick,'create' => '1'));
      $this->view->sitepageUrlEnabled = $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
      $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1);
      if( !empty($sitepageUrlEnabled) && empty($show_url) ) {
        $form->removeElement('page_url');
        $form->removeElement('page_url_msg');
      }

      $isHost = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.isHost', 0);
      if( empty($isHost) ) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting('sitepage.viewpage.sett', convert_uuencode($sitepageHostName));
      }

    //SET UP DATA NEEDED TO CHECK QUOTA

      $values['user_id'] = $viewer->getIdentity();
    // $paginator = Engine_Api::_()->getApi('core', 'sitepage')->getSitepagesPaginator($values);
      $count = Engine_Api::_()->getDbtable('pages', 'sitepage')->countUserPages($values);
      $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'sitepage_page', 'max');

      $this->view->current_count = $count;
      $this->view->sitepage_render = 'sitepage_form';
      if( $this->getRequest()->isPost() && !$form->isValid($this->getRequest()->getPost()) ) {
        $this->view->res=1;
        $this->view->cat_id=$form->getValue('category_id');
      }

    //IF NOT POST OR FORM NOT VALID, RETURN
      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $table = Engine_Api::_()->getItemTable('sitepage_page');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
        // Create sitepage
          $values = array_merge($form->getValues(), array(
            'owner_id' => $viewer->getIdentity(),
            'package_id' => $package_id
          ));

            // set header name as title, it header name is not set
            if(empty($values['page_header_name']) && !empty($values['title'])){
              $values['page_header_name'] = $values['title'];
            }

          $is_error = 0;
          if( isset($values['category_id']) && empty($values['category_id']) ) {
            $is_error = 1;
          }
          if( empty($values['subcategory_id']) ) {
            $values['subcategory_id'] = 0;
          }
          if( empty($values['subsubcategory_id']) ) {
            $values['subsubcategory_id'] = 0;
          }

        //SET ERROR MESSAGE
          if( $is_error == 1 ) {
            $this->view->status = false;
            $error = Zend_Registry::get('Zend_Translate')->_('Page Category * Please complete this field - it is required.');
            $form->getDecorator('errors')->setOption('escape', false);
            $form->addError($error);
            return;
          }
          $sitepage = $table->createRow();

          if( Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable() ) {
            if( isset($values['networks_privacy']) && !empty($values['networks_privacy']) ) {
              if( in_array(0, $values['networks_privacy']) ) {
                $values['networks_privacy'] = new Zend_Db_Expr('NULL');
              } else {
                $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
              }
            }
          }
          if( !empty($sitepageUrlEnabled) ) {
            if( empty($show_url) ) {
              $resultPageTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
              ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
              $count_index = count($resultPageTable);
              $resultPageUrl = $table->select()->where('page_url =?', $values['title'])->from($table, 'page_url')
              ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
              $count_index_url = count($resultPageUrl);
            }
            $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
            if( !empty($show_url) ) {
              if( in_array(strtolower($values['page_url']), $urlArray) ) {
                $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this URL has been restricted by our automated system. Please choose a different URL.'));
                return;
              }
            } elseif( !empty($sitepageUrlEnabled) ) {
              $lastpage_id = $table->select()
              ->from($table->info('name'), array('page_id'))->order('page_id DESC')
              ->query()
              ->fetchColumn();
              $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
              if( !empty($count_index) || !empty($count_index_url) ) {
                $lastpage_id = $lastpage_id + 1;
                $values['page_url'] = $values['page_url'] . '-' . $lastpage_id;
              } else {
                $values['page_url'] = $values['page_url'];
              }
              if( in_array(strtolower($values['page_url']), $urlArray) ) {

                $form->addError(Zend_Registry::get('Zend_Translate')->_('Sorry, this Page Title has been restricted by our automated system. Please choose a different Title.', array('escape' => false)));
                return;
              }
            }
          }
          $sitepage->setFromArray($values);


          $user_level = $viewer->level_id;
          if( !Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            $sitepage->featured = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'featured');
            $sitepage->sponsored = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'sponsored');
            //$sitepage->approved = Engine_Api::_()->authorization()->getPermission($user_level, 'sitepage_page', 'approved');
              $sitepage->approved = 0;
          } else {
            $sitepage->featured = $package->featured;
            $sitepage->sponsored = $package->sponsored;
            if( $package->isFree() && !empty($getPackageAuth) ) {
              //$sitepage->approved = $package->approved;
                $sitepage->approved = 0;
            } else {
              $sitepage->approved = 0;
            }
          }

          if( !empty($sitepage->approved) ) {
            $sitepage->pending = 0;
            $sitepage->aprrove_date = date('Y-m-d H:i:s');

            if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
              $expirationDate = $package->getExpirationDate();
              if( !empty($expirationDate) )
                $sitepage->expiration_date = date('Y-m-d H:i:s', $expirationDate);
              else
                $sitepage->expiration_date = '2250-01-01 00:00:00';
            }
            else {
              $sitepage->expiration_date = '2250-01-01 00:00:00';
            }
          }
            $sitepage->notify_project_donate = '0,1,2,3,4,5,6,7';
            $sitepage->notify_project_comment = '0,1,2,3,4,5,6,7';
            $sitepage->is_user_followed_after_comment_yn =  1;
            $sitepage->is_user_followed_after_donate_yn =  1;
          $sitepage->save();
          $page_id = $sitepage->page_id;

          if( !empty($sitepage->approved) ) {
            //Engine_Api::_()->sitepage()->sendMail("ACTIVE", $sitepage->page_id);
          } else {
            //Engine_Api::_()->sitepage()->sendMail("APPROVAL_PENDING", $sitepage->page_id);
          }

          $manageadminsTable = Engine_Api::_()->getDbtable('manageadmins', 'sitepage');
          $row = $manageadminsTable->createRow();
          $row->user_id = $sitepage->owner_id;
          $row->page_id = $sitepage->page_id;
          $row->save();

        //START PROFILE MAPS WORK
          Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->profileMapping($sitepage);


          $page_id = $sitepage->page_id;
          if( !empty($sitepageUrlEnabled) && empty($show_url) ) {
            $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
            if( !empty($count_index) || !empty($count_index_url) ) {
              $values['page_url'] = $values['page_url'] . '-' . $page_id;
              $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
            } else {
              $values['page_url'] = $values['page_url'];
              $table->update(array('page_url' => $values['page_url']), array('page_id = ?' => $page_id));
            }
          }

          $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
          if( $sitepageFormEnabled ) {
            $tablecontent = Engine_Api::_()->getDbtable('content', 'core');
            $params = $tablecontent->select()
            ->from($tablecontent->info('name'), 'params')
            ->where('name = ?', 'sitepageform.sitepage-viewform')
            ->query()->fetchColumn();
            $decodedParam = !empty($params) ? Zend_Json::decode($params) : array();
            $tabName = $decodedParam['title'];
            if( empty($tabName) )
              $tabName = 'Form';
            $sitepageformtable = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
            $optionid = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
            $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');
            $sitepageform = $table_option->createRow();
            $sitepageform->setFromArray($values);
            $sitepageform->label = $values['title'];
            $sitepageform->field_id = 1;
            $option_id = $sitepageform->save();
            $optionids = $optionid->createRow();
            $optionids->option_id = $option_id;
            $optionids->page_id = $page_id;
            $optionids->save();
            $sitepageforms = $sitepageformtable->createRow();
            if( isset($sitepageforms->offer_tab_name) )
              $sitepageforms->offer_tab_name = $tabName;
            $sitepageforms->description = 'Please leave your feedback below and enter your contact details.';
            $sitepageforms->page_id = $page_id;
            $sitepageforms->save();
          }
        //SET PHOTO
          if( !empty($values['photo']) ) {
            $sitepage->setPhoto($form->photo);
            $sitepageinfo = $sitepage->toarray();
            $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
            $album_id = $albumTable->update(array('photo_id' => $sitepageinfo['photo_id'], 'owner_id' => $sitepageinfo['owner_id']), array('page_id = ?' => $sitepageinfo['page_id']));
          } else {
            $sitepageinfo = $sitepage->toarray();
            $albumTable = Engine_Api::_()->getDbtable('albums', 'sitepage');
            $album_id = $albumTable->insert(array(
              'photo_id' => 0,
              'owner_id' => $sitepageinfo['owner_id'],
              'page_id' => $sitepageinfo['page_id'],
              'title' => $sitepageinfo['title'],
              'creation_date' => $sitepageinfo['creation_date'],
              'modified_date' => $sitepageinfo['modified_date']));
          }

        //ADD TAGS
          $tags = preg_split('/[,]+/', $values['tags']);
          $tags = array_filter(array_map("trim", $tags));
          $sitepage->tags()->addTagMaps($viewer, $tags);

          if( !empty($page_id) ) {
            $sitepage->setLocation();
          }

        // Set privacy
          $auth = Engine_Api::_()->authorization()->context;

        //get the page admin list.
          $ownerList = $sitepage->getPageOwnerList();

          $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
          if( !empty($sitepagememberEnabled) ) {
            $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          } else {
            $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          }


          if( !isset($values['auth_view']) || empty($values['auth_view']) ) {
            $values['auth_view'] = "everyone";
          }

          if( !isset($values['auth_comment']) || empty($values['auth_comment']) ) {
            $values['auth_comment'] = "everyone";
          }

          $viewMax = array_search($values['auth_view'], $roles);
          $commentMax = array_search($values['auth_comment'], $roles);

          foreach( $roles as $i => $role ) {
            $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
            $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
            $auth->setAllowed($sitepage, $role, 'print', 1);
            $auth->setAllowed($sitepage, $role, 'tfriend', 1);
            $auth->setAllowed($sitepage, $role, 'overview', 1);
            $auth->setAllowed($sitepage, $role, 'map', 1);
            $auth->setAllowed($sitepage, $role, 'insight', 1);
            $auth->setAllowed($sitepage, $role, 'layout', 1);
            $auth->setAllowed($sitepage, $role, 'contact', 1);
            $auth->setAllowed($sitepage, $role, 'form', 1);
            $auth->setAllowed($sitepage, $role, 'offer', 1);
            $auth->setAllowed($sitepage, $role, 'invite', 1);
          }

          $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
          if( !empty($sitepagememberEnabled) ) {
            $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          } else {
            $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
          }

        //START WORK FOR SUB PAGE.
          if( empty($values['auth_sspcreate']) ) {
            $values['auth_sspcreate'] = "owner";
          }

          $createMax = array_search($values['auth_sspcreate'], $roles);
          foreach( $roles as $i => $role ) {
            if( $role === 'like_member' ) {
              $role = $ownerList;
            }
            $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $createMax));
          }
        //END WORK FOR SUBPAGE
        //START SITEPAGEDISCUSSION PLUGIN WORK
          $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
          if( $sitepagediscussionEnabled ) {
          //START DISCUSSION PRIVACY WORK
            if( empty($values['sdicreate']) ) {
              $values['sdicreate'] = "registered";
            }

            $createMax = array_search($values['sdicreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $createMax));
            }
          //END DISCUSSION PRIVACY WORK
          }
        //END SITEPAGEDISCUSSION PLUGIN WORK
        //START SITEPAGEALBUM PLUGIN WORK
          $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
          if( $sitepagealbumEnabled ) {
          //START PHOTO PRIVACY WORK
            if( empty($values['spcreate']) ) {
              $values['spcreate'] = "registered";
            }

            $createMax = array_search($values['spcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $createMax));
            }
          //END PHOTO PRIVACY WORK
          }
        //END SITEPAGEALBUM PLUGIN WORK
        //START SITEPAGEDOCUMENT PLUGIN WORK
          $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
          if( $sitepageDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
            $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
            if( !empty($sitepagememberEnabled) ) {
              $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            } else {
              $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
            }

            if( empty($values['sdcreate']) ) {
              $values['sdcreate'] = "registered";
            }

            $createMax = array_search($values['sdcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $createMax));
            }
          }
        //END SITEPAGEDOCUMENT PLUGIN WORK
        //START SITEPAGEVIDEO PLUGIN WORK
          $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
          if( $sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
            if( empty($values['svcreate']) ) {
              $values['svcreate'] = "registered";
            }

            $createMax = array_search($values['svcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $createMax));
            }
          }
        //END SITEPAGEVIDEO PLUGIN WORK
        //START SITECROWDFUNDING PLUGIN WORK
          $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
          if( $sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
            if( empty($values['sprcreate']) ) {
              $values['sprcreate'] = "registered";
            }

            $createMax = array_search($values['sprcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'sprcreate', ($i <= $createMax));
            }
          }
        //END SITECROWDFUNDING PLUGIN WORK
        //START SITEMUSIC PLUGIN WORK
          $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
          if( $sitemusicEnabled && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
            if( empty($values['samcreate']) ) {
              $values['samcreate'] = "registered";
            }

            $createMax = array_search($values['samcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'samcreate', ($i <= $createMax));
            }
          }
        //END SITEMUSIC PLUGIN WORK
        //START SITEPAGEPOLL PLUGIN WORK
          $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
          if( $sitepagePollEnabled ) {
            if( empty($values['splcreate']) ) {
              $values['splcreate'] = "registered";
            }

            $createMax = array_search($values['splcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $createMax));
            }
          }
        //END SITEPAGEPOLL PLUGIN WORK
        //START SITEPAGENOTE PLUGIN WORK
          $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
          if( $sitepageNoteEnabled ) {
            if( empty($values['sncreate']) ) {
              $values['sncreate'] = "registered";
            }

            $createMax = array_search($values['sncreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $createMax));
            }
          }
        //END SITEPAGENOTE PLUGIN WORK
        //START SITEPAGEMUSIC PLUGIN WORK
          $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
          if( $sitepageMusicEnabled ) {
            if( empty($values['samcreate']) ) {
              $values['samcreate'] = "registered";
            }

            $createMax = array_search($values['samcreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'samcreate', ($i <= $createMax));
            }
          }
        //END SITEPAGEMUSIC PLUGIN WORK
        //START SITEPAGEEVENT PLUGIN WORK
          $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
          if( $sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
            if( empty($values['secreate']) ) {
              $values['secreate'] = "registered";
            }

            $createMax = array_search($values['secreate'], $roles);
            foreach( $roles as $i => $role ) {
              if( $role === 'like_member' ) {
                $role = $ownerList;
              }
              $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $createMax));
            }
          }
        //END SITEPAGEEVENT PLUGIN WORK
        //START SITEPAGEMEMBER PLUGIN WORK
          $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
          if( $sitepageMemberEnabled ) {
            $membersTable = Engine_Api::_()->getDbtable('membership', 'sitepage');
            $row = $membersTable->createRow();
            $row->resource_id = $sitepage->page_id;
            $row->page_id = $sitepage->page_id;
            $row->user_id = $sitepage->owner_id;
            $row->notification = '0';
          //$row->action_notification = '["posted","created"]';
            $row->save();
            Engine_Api::_()->sitepage()->updateMemberCount($sitepage);
            $sitepage->save();
          }
          $memberInvite = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.option', 1);
          $member_approval = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.option', 1);
          if( empty($memberInvite) ) {
            $memberInviteOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.invite.automatically', 1);
            $sitepage->member_invite = $memberInviteOption;
            $sitepage->save();
          }
          if( empty($member_approval) ) {
            $member_approvalOption = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.member.approval.automatically', 1);
            $sitepage->member_approval = $member_approvalOption;
            $sitepage->save();
          }
        //END SITEPAGEMEMBER PLUGIN WORK
        //START INTERGRATION EXTENSION WORK
        //START BUSINESS INTEGRATION WORK
          $business_id = $this->_getParam('business_id');
          if( !empty($business_id) ) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusinessintegration');
            if( !empty($moduleEnabled) ) {
              $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitebusinessintegration');
              $row = $contentsTable->createRow();
              $row->owner_id = $viewer_id;
              $row->resource_owner_id = $sitepage->owner_id;
              $row->business_id = $business_id;
              $row->resource_type = 'sitepage_page';
              $row->resource_id = $sitepage->page_id;
              $row->save();
            }
          }
          $group_id = $this->_getParam('group_id');
          if( !empty($group_id) ) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupintegration');
            if( !empty($moduleEnabled) ) {
              $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitegroupintegration');
              $row = $contentsTable->createRow();
              $row->owner_id = $viewer_id;
              $row->resource_owner_id = $sitepage->owner_id;
              $row->group_id = $group_id;
              $row->resource_type = 'sitepage_page';
              $row->resource_id = $sitepage->page_id;
              $row->save();
            }
          }
        //END BUSINESS INTEGRATION WORK
        //START STORE INTEGRATION WORK
          $store_id = $this->_getParam('store_id');
          if( !empty($store_id) ) {
            $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
            $moduleEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreintegration');
            $sitestoreEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore');
            if( !empty($moduleEnabled) && !empty($sitestoreEnabled) ) {
              $contentsTable = Engine_Api::_()->getDbtable('contents', 'sitestoreintegration');
              $row = $contentsTable->createRow();
              $row->owner_id = $viewer_id;
              $row->resource_owner_id = $sitepage->owner_id;
              $row->store_id = $store_id;
              $row->resource_type = 'sitepage_page';
              $row->resource_id = $sitepage->page_id;
              ;
              $row->save();
            }
          }
        //END STORE INTEGRATION WORK
        //END INTERGRATION EXTENSION WORK
        //START SUB PAGE WORK
          $parent_id = $this->_getParam('parent_id');
          if( !empty($parent_id) ) {
            $sitepage->subpage = 1;
            $sitepage->parent_id = $parent_id;
            $sitepage->save();
          }
        //END  SUB PAGE WORK
        //CUSTOM FIELD WORK
          if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.fields', 1) ) {
            $customfieldform = $form->getSubForm('fields');
            if ($customfieldform) {
              $customfieldform->setItem($sitepage);
              $customfieldform->saveValues();
            }
          }

        //START DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.
          $emails = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.defaultpagecreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress());
          if( !empty($emails) ) {
            $emails = explode(",", $emails);
            $host = $_SERVER['HTTP_HOST'];
            $newVar = _ENGINE_SSL ? 'https://' : 'http://';
            $object_link = $newVar . $host . $sitepage->getHref();
            $viewerGetTitle = $viewer->getTitle();
            $sender_link = '<a href=' . $newVar . $host . $viewer->getHref() . ">$viewerGetTitle</a>";
            foreach( $emails as $email ) {
              $email = trim($email);
              Engine_Api::_()->getApi('mail', 'core')->sendSystem($email, 'SITEPAGE_PAGE_CREATION', array(
                'sender' => $sender_link,
                'object_link' => $object_link,
                'object_title' => $sitepage->getTitle(),
                'object_description' => $sitepage->getDescription(),
                'queue' => true
              ));
            }
          }
        //END DEFAULT EMAIL TO SUPERADMIN WHEN ANYONE CREATE PAGES.
        // Commit
          $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }
        if( !empty($sitepage) && !empty($sitepage->draft) && empty($sitepage->pending) ) {
          Engine_Api::_()->sitepage()->attachPageActivity($sitepage);


        //START AUTOMATICALLY LIKE THE PAGE WHEN MEMBER CREATE A PAGE.
          $autoLike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.automatically.like', 1);
          if( !empty($autoLike) ) {
            Engine_Api::_()->sitepage()->autoLike($sitepage->page_id, 'sitepage_page');
          }
        //END AUTOMATICALLY LIKE THE PAGE WHEN MEMBER CREATE A PAGE.
        //SENDING ACTIVITY FEED TO FACEBOOK.
          $enable_Facebooksefeed = $enable_fboldversion = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebooksefeed');
          if( !empty($enable_Facebooksefeed) ) {

            $sitepage_array = array();
            $sitepage_array['type'] = 'sitepage_new';
            $sitepage_array['object'] = $sitepage;

            Engine_Api::_()->facebooksefeed()->sendFacebookFeed($sitepage_array);
          }
        }

      //REDIRECT
        return $this->_helper->redirector->gotoRoute(array('action' => 'get-started', 'page_id' => $sitepage->page_id, 'saved' => '1'), 'sitepage_dashboard', true);
      } else {
        $results = $this->getRequest()->getPost();
        if( !empty($results) && isset($results['subcategory_id']) ) {
          $this->view->category_id = $results['category_id'];
          if( !empty($results['subcategory_id']) ) {
            $this->view->subcategory_name = Engine_Api::_()->getDbtable('categories', 'sitepage')->getCategory($results['subcategory_id'])->category_name;
            $this->view->subcategory_id = $results['subcategory_id'];
          }
          return;
        }
      }
    }

  //ACTION FOR PAGE EDI
    public function editAction()
    {

    //USER VALDIATION
      if( !$this->_helper->requireUser()->isValid() )
        return;

    //SITEMOBILE_MODULE_NOT_SUPPORT_DESC_FOR_SOMEPAGES
      if( !Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode') ) {
        Engine_API::_()->sitemobile()->setupRequestError();
      } else {
        $this->_helper->content->setEnabled();
      }

    //GET NAVIGATION
      $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('sitepage_main');

      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      $this->view->page_id = $page_id = $this->_getParam('page_id');
      $viewer = Engine_Api::_()->user()->getViewer();
      $viewer_id = $viewer->getIdentity();
      $this->view->sitepages_view_menu = 1;
      $getPackageAuth = Engine_Api::_()->sitepage()->getPackageAuthInfo('sitepage');
      $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      $previous_category_id = $sitepage->category_id;
    //$previous_location = $sitepage->location;

      $ownerList = $sitepage->getPageOwnerList();

      if( empty($sitepage) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }

    //START MANAGE-ADMIN CHECK
      $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if( empty($isManageAdmin) ) {
        return $this->_forwardCustom('requireauth', 'error', 'core');
      }
    //END MANAGE-ADMIN CHECK
      $this->view->owner_id = $owner_id = $sitepage->owner_id;
      $user_subject = Engine_Api::_()->user()->getUser($owner_id);

    //FORM GENERATION
      $this->view->form = $form = new Sitepage_Form_Edit(array('item' => $sitepage, "packageId" => $sitepage->package_id, "layoutId" => $sitepage->layout_id, "owner" => $user_subject, 'create' => 0));
      $this->view->show_url = $show_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1);
      $this->view->edit_url = $edit_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.edit.url', 0);
      $this->view->sitepageurlenabled = $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
      if( !empty($sitepageUrlEnabled) && empty($show_url) ) {
        $form->removeElement('page_url');
        $form->removeElement('page_url_msg');
      }
      $this->view->is_ajax = $this->_getParam('is_ajax', '');
      if( !empty($sitepage->draft) ) {
        $form->removeElement('draft');
      }
      $form->removeElement('photo');

      $this->view->category_id = $sitepage->category_id;
      $subcategory_id = $this->view->subcategory_id = $sitepage->subcategory_id;
      $this->view->subsubcategory_id = $sitepage->subsubcategory_id;
      $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id);
      if( !empty($row->category_name) ) {
        $this->view->subcategory_name = $row->category_name;
      }

      $sitepageFormEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
      if( $sitepageFormEnabled ) {
        $quetion = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
        $select_quetion = $quetion->select()->where('page_id = ?', $page_id);
        $result_quetion = $quetion->fetchRow($select_quetion);
        $this->view->option_id = $result_quetion->option_id;
      }

      $values['user_id'] = $viewer_id;

    //SAVE SITEPAGE ENTRY
      if( !$this->getRequest()->isPost() ) {

      // prepare tags
        $sitepageTags = $sitepage->tags()->getTagMaps();
        $tagString = '';

        foreach( $sitepageTags as $tagmap ) {

          if( $tagString !== '' )
            $tagString .= ', ';
          $tagString .= $tagmap->getTag()->getTitle();
        }

        $this->view->tagNamePrepared = $tagString;
        $form->tags->setValue($tagString);

      // etc
        $form->populate($sitepage->toArray());
        $auth = Engine_Api::_()->authorization()->context;
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if( !empty($sitepagememberEnabled) ) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        foreach( $roles as $roleString ) {
          $role = $roleString;

          if( $form->auth_view && 1 == $auth->isAllowed($sitepage, $role, 'view') ) {
            $form->auth_view->setValue($roleString);
          }

          if( $form->auth_comment && 1 == $auth->isAllowed($sitepage, $role, 'comment') ) {
            $form->auth_comment->setValue($roleString);
          }

          if( $role == 'everyone' )
            continue;

          if( $role === 'like_member' ) {
            $role = $ownerList;
          }

        //Here we change isAllowed function for like privacy work only for populate.
          $sitepageAllow = Engine_Api::_()->getApi('allow', 'sitepage');
          if( $form->auth_sspcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sspcreate') ) {
            $form->auth_sspcreate->setValue($roleString);
          }
        // PHOTO PRIVACY WORK
          if( $form->spcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'spcreate') ) {
            $form->spcreate->setValue($roleString);
          }
        // DISCUSSION PRIVACY WORK
          if( $form->sdicreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sdicreate') ) {
            $form->sdicreate->setValue($roleString);
          }
        //SITEPAGEDOCUMENT PRIVACY WORK
          if( $form->sdcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sdcreate') ) {
            $form->sdcreate->setValue($roleString);
          }
        // SITEPAGEVIDEO PRIVACY WORK
          if( $form->svcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'svcreate') ) {
            $form->svcreate->setValue($roleString);
          }
        //START SITEPAGEPOLL PRIVACY WORK
          if( $form->splcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'splcreate') ) {
            $form->splcreate->setValue($roleString);
          }
        //START SITEPAGENOTE PRIVACY WORK
          if( $form->sncreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'sncreate') ) {
            $form->sncreate->setValue($roleString);
          }
        //START SITEPAGEMUSIC PRIVACY WORK
          if( $form->smcreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'smcreate') ) {
            $form->smcreate->setValue($roleString);
          }
        //START SITEPAGEEVENT PRIVACY WORK
          if( $form->secreate && 1 == $sitepageAllow->isAllowed($sitepage, $role, 'secreate') ) {
            $form->secreate->setValue($roleString);
          }
        }

        if( Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable() ) {
          if( !empty($sitepage->networks_privacy) ) {
            $form->networks_privacy->setValue(explode(',', $sitepage->networks_privacy));
          } else {
            $form->networks_privacy->setValue(array(0));
          }
        }
        return;
      }
      if( !$form->isValid($this->getRequest()->getPost()) ) {
        $values['category_id'] = $this->view->category_id = $sitepage->category_id;
        $values['subcategory_id'] = $this->view->subcategory_id = $sitepage->subcategory_id;
        $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitepage->subsubcategory_id;
        $form->populate($values);
        return;
      }

    // handle save for tags
      $values = $form->getValues($values);

        // set header name as title, it header name is not set
        if(empty($values['page_header_name']) && !empty($values['title'])){
            $values['page_header_name'] = $values['title'];
        }

      if( !empty($sitepageUrlEnabled) && !empty($show_url) && !empty($edit_url) ) {
        $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
        $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
        $sitepageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $selectTable = $sitepageTable->select()->where('page_id != ?', $page_id)
        ->where('page_url = ?', $values['page_url']);
        $resultSitepageTable = $sitepageTable->fetchAll($selectTable);
        if( count($resultSitepageTable) || (in_array(strtolower($values['page_url']), $urlArray)) && (!empty($change_url)) ) {
          $form->addError(Zend_Registry::get('Zend_Translate')->_('This URL has been restricted by our automated system. Please choose another URL.'));
          return;
        }
      } elseif( !empty($sitepageUrlEnabled) && empty($show_url) ) {
        $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
        $table = Engine_Api::_()->getDbtable('pages', 'sitepage');
        $resultPageTable = $table->select()->where('title =?', $values['title'])->from($table, 'title')
        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $count_index = count($resultPageTable);
        $resultPageUrl = $table->select()->where('page_url =?', $values['title'])->from($table, 'page_url')
        ->query()->fetchAll(Zend_Db::FETCH_COLUMN);
        $count_index_url = count($resultPageUrl);

        if( empty($count_index) ) {
          $values['page_url'] = trim(preg_replace('/-+/', '-', preg_replace('/[^a-z0-9-]+/i', '-', strtolower($values['title']))), '-');
          if( !empty($count_index) || !empty($count_index_url) ) {
            $values['page_url'] = $values['page_url'] . '-' . $page_id;
          } else {
            $values['page_url'] = $values['page_url'];
          }
          if( in_array(($values['page_url']), $urlArray) ) {

            $form->addError(Zend_Registry::get('Zend_Translate')->_('This Page title has been blocked by our automated system. Please choose another title.', array('escape' => false)));
            return;
          }
        }
      }

      $is_error = 0;
      Engine_Api::_()->getItemtable('sitepage_package')->setPackages();
      if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) && !empty($sitepage->category_id) ) {
        $values['category_id'] = $this->view->category_id = $sitepage->category_id;
        $values['subcategory_id'] = $this->view->subcategory_id = $sitepage->subcategory_id;
        $values['subsubcategory_id'] = $this->view->subsubcategory_id = $sitepage->subsubcategory_id;
        $form->populate($values);
      }
      if( Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) ) {
        if( isset($values['category_id']) && empty($values['category_id']) ) {
          $is_error = 1;
          $this->view->category_id = 0;
          $this->view->subsubcategory_id = 0;
          $this->view->subcategory_id = 0;
        }
      }

      if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) ) {
        if( isset($values['category_id']) && empty($values['category_id']) ) {
          $is_error = 1;
          $this->view->category_id = 0;
          $this->view->subsubcategory_id = 0;
          $this->view->subcategory_id = 0;
        }
      }

    //set error message
      if( $is_error == 1 ) {
        $this->view->status = false;
        $error = Zend_Registry::get('Zend_Translate')->_('Page Category * Please complete this field - it is required.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      if( $sitepageFormEnabled ) {
        $sitepageform_form = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
        $quetion = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
        $select_quetion = $quetion->select()->where('page_id = ?', $page_id);
        $result_quetion = $quetion->fetchRow($select_quetion);
        $this->view->option_id = $result_quetion->option_id;
        $table_option = Engine_Api::_()->fields()->getTable('sitepageform', 'options');
        $table_option->update(array('label' => $values['title']), array('option_id = ?' => $result_quetion->option_id));
      }

      $tags = preg_split('/[,]+/', $values['tags']);
      $tags = array_filter(array_map("trim", $tags));

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        if( Engine_Api::_()->getApi('subCore', 'sitepage')->pageBaseNetworkEnable() ) {

          if( isset($values['networks_privacy']) && !empty($values['networks_privacy']) ) {
            if( in_array(0, $values['networks_privacy']) ) {
              $values['networks_privacy'] = new Zend_Db_Expr('NULL');
              $form->networks_privacy->setValue(array(0));
            } else {
              $values['networks_privacy'] = (string) ( is_array($values['networks_privacy']) ? join(",", $values['networks_privacy']) : $netowrkIds );
            }
          }
        }
        $sitepage->setFromArray($values);
        $sitepage->modified_date = date('Y-m-d H:i:s');

        $sitepage->tags()->setTagMaps($viewer, $tags);
        $sitepage->save();

        $location = $sitepage->location;

//       if ($previous_location && (empty($previous_location) || ($location !== $previous_location))) {
//         $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
//         $locationTable->delete(array('page_id =?' => $page_id, 'location =?' => $previous_location));
//       }
//
//       if (!empty($location) && $location !== $previous_location) {
//         $sitepage->setLocation();
//       }


      /* else {
        $locationTable->delete(arr ay(
        'page_id =?' => $page_id
        ));
      } */
      //CREATE AUTH STUFF HERE
      $auth = Engine_Api::_()->authorization()->context;
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if( !empty($sitepagememberEnabled) ) {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      $oldViewPrivacy = 'owner';
      foreach( $roles as $role ) {
        if( 1 == $auth->isAllowed($sitepage, $role, 'view') ) {
          $oldViewPrivacy = $role;
        }
      }

      $values = $form->getValues();
      if( $values['auth_view'] )
        $auth_view = $values['auth_view'];
      else
        $auth_view = "everyone";
      $viewMax = array_search($auth_view, $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($sitepage, $role, 'view', ($i <= $viewMax));
      }

      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if( !empty($sitepagememberEnabled) ) {
        $roles = array('owner', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      if( $values['auth_comment'] )
        $auth_comment = $values['auth_comment'];
      else
        $auth_comment = "everyone";
      $commentMax = array_search($auth_comment, $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($sitepage, $role, 'comment', ($i <= $commentMax));
      }

      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if( !empty($sitepagememberEnabled) ) {
        $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      } else {
        $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
      }

      //START WORK FOR SUB PAGE.
      if( $values['auth_sspcreate'] )
        $subpage = $values['auth_sspcreate'];
      else
        $subpage = "owner";
      $subpageMax = array_search($subpage, $roles);

      foreach( $roles as $i => $role ) {
        if( $role === 'like_member' ) {
          $role = $ownerList;
        }
        $auth->setAllowed($sitepage, $role, 'sspcreate', ($i <= $subpageMax));
      }
      //END WORK FOR SUBPAGE
      //START DISCUSSION PRIVACY WORK
      $sitepagediscussionEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
      if( $sitepagediscussionEnabled ) {
        if( $values['sdicreate'] )
          $photo = $values['sdicreate'];
        else
          $photo = "registered";
        $photoMax = array_search($photo, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'sdicreate', ($i <= $photoMax));
        }
      }
      //END DISCUSSION PRIVACY WORK
      //START PHOTO PRIVACY WORK
      $sitepagealbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
      if( $sitepagealbumEnabled ) {
        if( $values['spcreate'] )
          $photo = $values['spcreate'];
        else
          $photo = "registered";
        $photoMax = array_search($photo, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'spcreate', ($i <= $photoMax));
        }
      }
      //END PHOTO PRIVACY WORK
      //START SITEPAGEDOCUMENT WORK
      $sitepageDocumentEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
      if( $sitepageDocumentEnabled || (Engine_Api::_()->hasModuleBootstrap('document') && Engine_Api::_()->getDbtable('modules', 'document')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {

        if( $values['sdcreate'] )
          $sdcreate = $values['sdcreate'];
        else
          $sdcreate = "registered";

        $sdcreateMax = array_search($sdcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'sdcreate', ($i <= $sdcreateMax));
        }
      }
      //END SITEPAGEDOCUMENT WORK
      //START SITECROWDFUNDING WORK
      $sitecrowdfundingEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
      if( $sitecrowdfundingEnabled && (Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
        if( $values['sprcreate'] )
          $sprcreate = $values['sprcreate'];
        else
          $sprcreate = "registered";
        $sprcreateMax = array_search($sprcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'sprcreate', ($i <= $sprcreateMax));
        }
      }
      //END SITECROWDFUNDING WORK

      //START SITEMUSIC WORK
      $sitemusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
      if( $sitemusicEnabled && (Engine_Api::_()->hasModuleBootstrap('sitemusic') && Engine_Api::_()->getDbtable('modules', 'sitemusic')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
        if( $values['samcreate'] )
          $samcreate = $values['samcreate'];
        else
          $samcreate = "registered";
        $samcreateMax = array_search($samcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'samcreate', ($i <= $samcreateMax));
        }
      }
      //END SITEMUSIC WORK

      //START SITEPAGEVIDEO WORK
      $sitepageVideoEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
      if( $sitepageVideoEnabled || (Engine_Api::_()->hasModuleBootstrap('sitevideo') && Engine_Api::_()->getDbtable('modules', 'sitevideo')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
        if( $values['svcreate'] )
          $svcreate = $values['svcreate'];
        else
          $svcreate = "registered";
        $svcreateMax = array_search($svcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'svcreate', ($i <= $svcreateMax));
        }
      }
      //END SITECROWDFUNDING WORK
      //START SITEPAGEPOLL WORK
      $sitepagePollEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
      if( $sitepagePollEnabled ) {
        if( $values['splcreate'] )
          $splcreate = $values['splcreate'];
        else
          $splcreate = "registered";
        $splcreateMax = array_search($splcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'splcreate', ($i <= $splcreateMax));
        }
      }
      //END SITEPAGEPOLL WORK
      //START SITEPAGENOTE WORK
      $sitepageNoteEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
      if( $sitepageNoteEnabled ) {
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if( !empty($sitepagememberEnabled) ) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }
        if( $values['sncreate'] )
          $sncreate = $values['sncreate'];
        else
          $sncreate = "registered";
        $sncreateMax = array_search($sncreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'sncreate', ($i <= $sncreateMax));
        }
      }
      //END SITEPAGENOTE WORK
      //START SITEPAGEMUSIC WORK
      $sitepageMusicEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
      if( $sitepageMusicEnabled ) {
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if( !empty($sitepagememberEnabled) ) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }

        if( $values['smcreate'] )
          $smcreate = $values['smcreate'];
        else
          $smcreate = "registered";
        $smcreateMax = array_search($smcreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'smcreate', ($i <= $smcreateMax));
        }
      }
      //END SITEPAGENOTE WORK
      //START SITEPAGEEVENT WORK
      //START SITEPAGEREVIEW CODE
      $sitepageeventEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
      if( $sitepageeventEnabled || (Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) ) {
        $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if( !empty($sitepagememberEnabled) ) {
          $roles = array('owner', 'like_member', 'member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        } else {
          $roles = array('owner', 'like_member', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');
        }
        if( $values['secreate'] )
          $secreate = $values['secreate'];
        else
          $secreate = "registered";
        $secreateMax = array_search($secreate, $roles);

        foreach( $roles as $i => $role ) {
          if( $role === 'like_member' ) {
            $role = $ownerList;
          }
          $auth->setAllowed($sitepage, $role, 'secreate', ($i <= $secreateMax));
        }
      }
      //END SITEPAGEEVENT WORK
      //START SITEPAGEREVIEW CODE
      $sitepageReviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
      if( $sitepageReviewEnabled && $previous_category_id != $sitepage->category_id ) {
        Engine_Api::_()->getDbtable('ratings', 'sitepagereview')->editPageCategory($sitepage->page_id, $previous_category_id, $sitepage->category_id);
      }
      //END SITEPAGEREVIEW CODE
      //START SITEPAGEMEMBER CODE
      $sitepagememberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
      if( $sitepagememberEnabled && $previous_category_id != $sitepage->category_id ) {
        $db->query("UPDATE `engine4_sitepage_membership` SET `role_id` = '0' WHERE `engine4_sitepage_membership`.`page_id` = " . $sitepage->page_id . ";");
      }
      //END SITEPAGEMEMBER CODE
      //START PROFILE MAPPING WORK IF CATEGORY IS EDIT
      if( $previous_category_id != $sitepage->category_id ) {
        Engine_Api::_()->getDbtable('profilemaps', 'sitepage')->editCategoryMapping($sitepage);
      }

      //END PROFILE MAPPING WORK IF CATEGORY IS EDIT
      //INSERT ACTIVITY IF PAGE IS JUST GETTING PUBLISHED
      $action = Engine_Api::_()->getDbtable('actions', 'activity')->getActionsByObject($sitepage);
      if( count($action->toArray()) <= 0 && isset($values['draft']) && $values['draft'] == '1' && empty($sitepage->pending) ) {
        Engine_Api::_()->sitepage()->attachPageActivity($sitepage);
      }
      $db->commit();

      if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) && !empty($sitepage->category_id) ) {

        $this->view->category_id = $sitepage->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitepage->subcategory_id;
        $table = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $categoriesName = $table->info('name');

        $select = $table->select()->from($categoriesName, 'category_name')
        ->where("(category_id = $subcategory_id)");

        $row = $table->fetchRow($select);
        if( !empty($row->category_name) ) {
          $this->view->subcategory_name = $row->category_name;
        }
        $form->getElement('category_id')
        ->setIgnore(true)
        ->setAttrib('disable', true)
        ->clearValidators()
        ->setRequired(false)
        ->setAllowEmpty(true)
        ;
      } else {
        $this->view->category_id = $sitepage->category_id;
        $this->view->subcategory_id = $subcategory_id = $sitepage->subcategory_id;
        $this->view->subsubcategory_id = $subsubcategory_id = $sitepage->subsubcategory_id;
        $table = Engine_Api::_()->getDbtable('categories', 'sitepage');
        $categoriesName = $table->info('name');
        $select = $table->select()->from($categoriesName, 'category_name')
        ->where("(category_id = $subcategory_id)");

        $row = $table->fetchRow($select);
        if( !empty($row->category_name) ) {
          $this->view->subcategory_name = $row->category_name;
        }
      }
      $this->view->form = $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $db->beginTransaction();
    try {

      // REBUILD ACTION PRIVACY ONLY IF OBJECT'S VIEW PRIVACY HAS CHANGED
      if ($oldViewPrivacy !== $values['auth_view']) {
        $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
        foreach( $actionTable->getActionsByObject($sitepage) as $action ) {
          $actionTable->resetActivityBindings($action);
        }
      }

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR DELETING PAGE
  public function deleteAction()
  {

    //USER VALIDATION
    if( !$this->_helper->requireUser()->isValid() )
      return;

    //NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitepage_main', array(), 'sitepage_main_manage');

    //GET PAGE ID AND OBJECT
    $page_id = $this->_getParam('page_id');
    $this->view->sitepage = $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'delete');
    if( empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    if( $this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true ) {

      //START SUB PAGE WORK
      $getSubPageids = Engine_Api::_()->getDbTable('pages', 'sitepage')->getsubPageids($page_id);
      foreach( $getSubPageids as $getSubPageid ) {
        Engine_Api::_()->sitepage()->onPageDelete($getSubPageid['page_id']);
      }
      //END SUB PAGE WORK

      Engine_Api::_()->sitepage()->onPageDelete($page_id);
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
    }
  }

  //ACTION: CLOSE / OPEN PAGE
  public function closeAction()
  {

    //USER VALIDATION
    if( !$this->_helper->requireUser()->isValid() )
      return;

    //GET PAGE OBJECT
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->_getParam('page_id'));

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if( empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $sitepage->closed = $this->_getParam('closed');
    $sitepage->save();

    $check = $this->_getParam('check');
    if( !empty($check) ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'my-pages'), 'sitepage_manageadmins', true);
    } else {
      return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'sitepage_general', true);
    }
  }

  //ACTION FOR CONSTRUCT TAG CLOUD
  public function tagsCloudAction()
  {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
    ->getNavigation('sitepage_main');

    //GENERATE TAG-CLOULD HIDDEN FROM
    $this->view->form = $form = new Sitepage_Form_Searchtagcloud();
    $category_id = $this->_getParam('category_id', 0);

    //CONSTRUCTING TAG CLOUD
    $tag_array = array();
    $tag_cloud_array = Engine_Api::_()->getDbtable('pages', 'sitepage')->getTagCloud('', $category_id, 0);
    $tag_id_array = array();
    foreach( $tag_cloud_array as $vales ) {
      $tag_array[$vales['text']] = $vales['Frequency'];
      $tag_id_array[$vales['text']] = $vales['tag_id'];
    }

    if( !empty($tag_array) ) {
      $max_font_size = 18;
      $min_font_size = 12;
      $max_frequency = max(array_values($tag_array));
      $min_frequency = min(array_values($tag_array));
      $spread = $max_frequency - $min_frequency;

      if( $spread == 0 ) {
        $spread = 1;
      }

      $step = ($max_font_size - $min_font_size) / ($spread);

      $tag_data = array('min_font_size' => $min_font_size, 'max_font_size' => $max_font_size, 'max_frequency' => $max_frequency, 'min_frequency' => $min_frequency, 'step' => $step);

      $this->view->tag_data = $tag_data;
      $this->view->tag_id_array = $tag_id_array;
    }
    $this->view->tag_array = $tag_array;
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subcategoryAction()
  {

    $category_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('category_id_temp');
    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($category_id_temp);
    if( !empty($row->category_name) ) {
      $categoryname = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($row->category_name);
    }
    $data = array();
    $this->view->subcats = $data;
    if( empty($category_id_temp) )
      return;
    $results = Engine_Api::_()->getDbTable('categories', 'sitepage')->getSubCategories($category_id_temp);
    foreach( $results as $value ) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
      $content_array['category_id'] = $value->category_id;
      $content_array['categoryname_temp'] = $categoryname;
      $data[] = $content_array;
    }
    $this->view->subcats = $data;
  }

  //ACTION FOR PAGE PUBLISH
  public function publishAction()
  {

    //CHECK USER VALIDATION
    if( !$this->_helper->requireUser()->isValid() )
      return;

    //SMOOTHBOX
    if( null == $this->_helper->ajaxContext->getCurrentContext() ) {
      $this->_helper->layout->setLayout('default-simple');
    } else {
      //NO LAYOUT
      $this->_helper->layout->disableLayout(true);
    }

    $page_id = $this->view->page_id = $this->_getParam('page_id');

    if( !$this->getRequest()->isPost() )
      return;

    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if( empty($isManageAdmin) ) {
      return $this->_forwardCustom('requireauth', 'error', 'core');
    }
    //END MANAGE-ADMIN CHECK

    $this->view->permission = true;
    $this->view->success = false;
    $db = Engine_Api::_()->getDbtable('pages', 'sitepage')->getAdapter();
    $db->beginTransaction();
    if( !empty($_POST['search']) )
      $search = 1;
    else
      $search = 0;
    try {
      $sitepage->modified_date = new Zend_Db_Expr('NOW()');
      $sitepage->draft = 1;
      $sitepage->search = $search;
      $sitepage->save();
      $db->commit();
      if( !empty($sitepage->draft) && empty($sitepage->pending) ) {
        Engine_Api::_()->sitepage()->attachPageActivity($sitepage);
      }
      $this->view->success = true;
    } catch( Exception $e ) {
      $db->rollback();
      throw $e;
    }

    $this->_forwardCustom('success', 'utility', 'core', array(
      'smoothboxClose' => 10,
      'parentRefresh' => 10,
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Successfully Published !'))
    ));
  }

  //ACTION FOR PAGE URL VALIDATION AT PAGE CREATION TIME
  public function pageurlvalidationAction()
  {

    $page_url = $this->_getParam('page_url');
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $sitepageUrlEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageurl');
    if( !empty($sitepageUrlEnabled) ) {
      $urlArray = Engine_Api::_()->sitepage()->getBannedUrls();
    }
    if( empty($page_url) ) {
      $text = Zend_Registry::get('Zend_Translate')->_("URL not valid.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => '<span style="color:red;"><img src="' . $view->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/cross.png"/>' . $text . '</span>'));
      exit();
    }

    $url_lenght = strlen($page_url);
    if( $url_lenght < 3 ) {
      $error_msg1 = Zend_Registry::get('Zend_Translate')->_("URL component should be atleast 3 characters long.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/>$error_msg1</span>"));
      exit();
    } elseif( $url_lenght > 255 ) {
      $error_msg2 = Zend_Registry::get('Zend_Translate')->_("URL component should be maximum 255 characters long.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/>$error_msg2</span>"));
      exit();
    }

    $change_url = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1);
    $check_url = $this->_getParam('check_url');
    if( !empty($check_url) ) {
      $pageId = $this->_getParam('page_id');
      $page_id = Engine_Api::_()->sitepage()->getPageId($page_url, $pageId);
    } else {
      $page_id = Engine_Api::_()->sitepage()->getPageId($page_url);
    }
    if( !empty($sitepageUrlEnabled) ) {
      if( !empty($page_id) || (in_array(strtolower($page_url), $urlArray)) ) {
        $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
        echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/>$error_msg3</span>"));
        exit();
      }
    } else {
      if( !empty($page_id) ) {
        $error_msg3 = Zend_Registry::get('Zend_Translate')->_("URL not available.");
        echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/>$error_msg3</span>"));
        exit();
      }
    }



    if( !preg_match("/^[a-zA-Z0-9-_]+$/", $page_url) ) {
      $error_msg4 = Zend_Registry::get('Zend_Translate')->_("URL component can contain alphabets, numbers, underscores & dashes only.");
      echo Zend_Json::encode(array('success' => 0, 'error_msg' => "<span style='color:red;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/cross.png'/>$error_msg4</span>"));
      exit();
    } else {
      $error_msg5 = Zend_Registry::get('Zend_Translate')->_("URL Available!");
      echo Zend_Json::encode(array('success' => 1, 'success_msg' => "<span style='color:green;'><img src='" . $view->layout()->staticBaseUrl . "application/modules/Sitepage/externals/images/tick.png'/>$error_msg5</span>"));
      exit();
    }
  }

  //ACITON FOR LISTING PAGES AT HOME PAGE
  public function ajaxHomeListAction()
  {
    $params = array();
    $this->view->category_id = $params['category_id'] = $category_id = $this->_getParam('category_id', 0);
    $tab_show_values = $this->_getParam('tab_show', null);
    $this->view->list_view = $this->_getParam('list_view', 0);
    $this->view->grid_view = $this->_getParam('grid_view', 0);
    $this->view->map_view = $this->_getParam('map_view', 0);
    $this->view->defaultView = $this->_getParam('defaultView', 0);
    $this->view->active_tab_list = $list_limit = $this->_getParam('list_limit', 0);
    $this->view->active_tab_image = $grid_limit = $this->_getParam('grid_limit', 0);
    $this->view->columnHeight = $this->_getParam('columnHeight', 350);
    $this->view->columnWidth = $this->_getParam('columnWidth', 188);
    $this->view->showfeaturedLable = $this->_getParam('showfeaturedLable', 1);
    $this->view->showsponsoredLable = $this->_getParam('showsponsoredLable', 1);
    $this->view->showlocation = $this->_getParam('showlocation', 1);
    $this->view->showprice = $this->_getParam('showprice', 1);
    $this->view->showpostedBy = $this->_getParam('showpostedBy', 1);
    $this->view->showdate = $this->_getParam('showdate', 1);
    $this->view->turncation = $this->_getParam('turncation', 15);
    $this->view->listview_turncation = $this->_getParam('listview_turncation', 15);
    $this->view->showlikebutton = $this->_getParam('showlikebutton', 1);

    $this->view->detactLocation = $params['detactLocation'] = $this->_getParam('detactLocation', 0);
    if( $this->view->detactLocation ) {
      $this->view->defaultLocationDistance = $params['defaultLocationDistance'] = $this->_getParam('defaultLocationDistance', 1000);
      $this->view->latitude = $params['latitude'] = $this->_getParam('latitude', 0);
      $this->view->longitude = $params['longitude'] = $this->_getParam('longitude', 0);
    }

    $this->view->statistics = Zend_Json_Decoder::decode($this->_getParam('statistics'));
    $params['limit'] = $limit = $list_limit > $grid_limit ? $list_limit : $grid_limit;

    $columnsArray = array('page_id', 'title', 'page_url', 'body','owner_id', 'category_id', 'photo_id', 'price', 'location', 'creation_date', 'featured', 'sponsored', 'view_count', 'comment_count', 'like_count', 'follow_count','show_name');
    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') ) {
      $columnsArray[] = 'member_count';
    }
    $columnsArray[] = 'member_title';

    if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') ) {
      $columnsArray[] = 'review_count';
      $columnsArray[] = 'rating';
    }
    $this->view->sitepagesitepage = $sitepage = Engine_Api::_()->getDbTable('pages', 'sitepage')->getListings($tab_show_values, $params, null, null, $columnsArray);

    $this->view->enablePrice = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.price.field', 1);

    $this->view->enableLocation = $checkLocation = Engine_Api::_()->sitepage()->enableLocation();
    if( !empty($this->view->map_view) ) {

      $this->view->flageSponsored = 0;

      if( !empty($checkLocation) ) {
        $ids = array();
        $sponsored = array();
        foreach( $sitepage as $sitepage_page ) {
          $id = $sitepage_page->getIdentity();
          $ids[] = $id;
          $sitepage_temp[$id] = $sitepage_page;
        }
        $values['page_ids'] = $ids;

        $this->view->locations = $locations = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($values);
        foreach( $locations as $location ) {
          if( $sitepage_temp[$location->page_id]->sponsored ) {
            $this->view->flageSponsored = 1;
            break;
          }
        }
        $this->view->sitepage = $sitepage_temp;
      }
    }
    // Rating enable /disable
    $this->view->ratngShow = $ratingShow = (int) Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
  }

  public function checkLevelHost($object, $itemType)
  {
    $length = 7;
    $encodeorder = 0;
    $obj_length = strlen($object);
    if( $length > $obj_length )
      $length = $obj_length;
    for( $i = 0; $i < $length; $i++ ) {
      $encodeorder += ord($object[$i]);
    }
    $req_mode = $encodeorder % strlen($itemType);
    $encodeorder += ord($itemType[$req_mode]);
    $isEnabled = Engine_Api::_()->sitepage()->isEnabled();
    if( empty($isEnabled) ) {
      return 0;
    } else {
      return $encodeorder;
    }
  }

  public function checkPackageHost($strKey)
  {
    $str = explode("-", $strKey);
    $str = $str[2];
    $char_array = array();
    for( $i = 0; $i < 6; $i++ )
      $char_array[] = $str[$i];
    $key = array();
    foreach( $char_array as $value ) {
      $v_a = ord($value);
      if( $v_a > 47 && $v_a < 58 )
        continue;
      $possition = 0;
      $possition = $v_a % 10;
      if( $possition > 5 )
        $possition -= 4;
      $key[] = $char_array[$possition];
    }
    $isEnabled = Engine_Api::_()->sitepage()->isEnabled();
    if( empty($isEnabled) ) {
      return 0;
    } else {
      return $getStr = implode($key);
    }
  }

  public function setPhpIniMemorySize()
  {
    $memory_size = ini_get('memory_limit');
    $memory_Size_int_array = explode("M", $memory_size);
    $memory_Size_int = $memory_Size_int_array[0];
    if( $memory_Size_int <= 32 )
      ini_set('memory_limit', '64M');
  }

  //ACTION FOR GETTING THE PAGES WHICH PAGES CAN BE SEARCH
  public function getSearchPagesAction()
  {

    $usersitepages = Engine_Api::_()->getDbtable('pages', 'sitepage')->getDayItems($this->_getParam('text'), $this->_getParam('limit', 10), $this->_getParam('category_id'));
    $data = array();
    $mode = $this->_getParam('struct');
    $count = count($usersitepages);
    if( $mode == 'text' ) {
      $i = 0;
      foreach( $usersitepages as $usersitepage ) {
        $page_url = $this->view->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($usersitepage->page_id)), 'sitepage_entry_view', true);
        $i++;
        $content_photo = $this->view->itemPhoto($usersitepage, 'thumb.icon');
        $data[] = array(
          'id' => $usersitepage->page_id,
          'label' => $usersitepage->title,
          'photo' => $content_photo,
          'page_url' => $page_url,
          'total_count' => $count,
          'count' => $i
        );
      }
    } else {
      $i = 0;
      foreach( $usersitepages as $usersitepage ) {
        $page_url = $this->view->url(array('page_url' => Engine_Api::_()->sitepage()->getPageUrl($usersitepage->page_id)), 'sitepage_entry_view', true);
        $content_photo = $this->view->itemPhoto($usersitepage, 'thumb.icon');
        $i++;
        $data[] = array(
          'id' => $usersitepage->page_id,
          'label' => $usersitepage->title,
          'photo' => $content_photo,
          'page_url' => $page_url,
          'total_count' => $count,
          'count' => $i
        );
      }
    }
    if( !empty($data) && $i >= 1 ) {
      if( $data[--$i]['count'] == $count ) {
        $data[$count]['id'] = 'stopevent';
        $data[$count]['label'] = $this->_getParam('text');
        $data[$count]['page_url'] = 'seeMoreLink';
        $data[$count]['total_count'] = $count;
      }
    }
    return $this->_helper->json($data);
  }

  //ACTION FOR FETCHING SUB-CATEGORY
  public function subsubcategoryAction()
  {

    $subcategory_id_temp = Zend_Controller_Front::getInstance()->getRequest()->getParam('subcategory_id_temp');

    $row = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategory($subcategory_id_temp);
    if( !empty($row->category_name) ) {
      $categoryname = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategorySlug($row->category_name);
    }
    $data = array();
    $this->view->subsubcats = $data;
    if( empty($subcategory_id_temp) )
      return;

    $results = Engine_Api::_()->getDbTable('categories', 'sitepage')->getSubCategories($subcategory_id_temp);

    foreach( $results as $value ) {
      $content_array = array();
      $content_array['category_name'] = Zend_Registry::get('Zend_Translate')->_($value->category_name);
      $content_array['category_id'] = $value->category_id;
      $content_array['categoryname_temp'] = $categoryname;
      $data[] = $content_array;
    }
    $this->view->subsubcats = $data;
  }

  //ACTION FOR SHOWING LOCAITON IN MAP WITH GET DIRECTION
  public function viewMapAction()
  {

    $this->_helper->layout->setLayout('default-simple');
    $value['id'] = $this->_getParam('id');
    if( !$this->_getParam('id') )
      return $this->_forwardCustom('notfound', 'error', 'core');

    $locationTable = Engine_Api::_()->getDbtable('locations', 'sitepage');
    $select = $locationTable->select();
    $select->where('page_id = ?', $value['id']);
    $item = $locationTable->fetchRow($select);

    $params = (array) $item->toArray();
    if( is_array($params) ) {
      $this->view->checkin = $params;
    } else {
      return $this->_forwardCustom('notfound', 'error', 'core');
    }
  }

  protected function renderUserWidgetCustom()
  {
    if( !Engine_Api::_()->seaocore()->isSitemobileApp() )
      return;
    $content_id = $this->_getParam('tab');
    //  $params = $this->_getAllParams();
    // Render by content row
    if( null !== $content_id ) {
      $view = $this->_getParam('view');
      $show_container = $this->_getParam('container', true);
      $contentTable = Engine_Api::_()->getDbtable('mobileContent', 'sitepage');
      $row = $contentTable->find($content_id)->current();

      if( null !== $row ) {
        // Build full structure from children
        $mobilecontentpage_id = $row->mobilecontentpage_id;
        $pageTable = Engine_Api::_()->getDbtable('mobileContentpages', 'sitepage');
        $content = $contentTable->fetchAll($contentTable->select()->where('mobilecontentpage_id = ?', $mobilecontentpage_id));
        $structure = $pageTable->createElementParams($row);
        $children = $pageTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;
        if( $this->getContainerContent($structure, $show_container) )
          return true;
      } else {
        $contentTable = Engine_Api::_()->getDbtable('mobileadmincontent', 'sitepage');
        $row = $contentTable->find($content_id)->current();

        $page_id = Engine_Api::_()->sitepage()->getMobileWidgetizedPage()->page_id;
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $contentTable->createElementParams($row);
        $children = $contentTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = $view;
        if( $this->getContainerContent($structure, $show_container) )
          return true;
      }
    }
  }

}
