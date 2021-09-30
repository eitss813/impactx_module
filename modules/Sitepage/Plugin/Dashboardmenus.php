<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_Plugin_Dashboardmenus {

  public function onMenuInitialize_SitepageDashboardGetstarted($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
      'label' => $row->label,
      'route' => 'sitepage_dashboard',
      'action' => 'get-started',
      'params' => array(
          'page_id' => $sitepage->getIdentity()
      ),
    );
  }

    public function onMenuInitialize_SitepageDashboardPrivacy($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'privacy',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardProjectNotifications($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'project-privacy',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardMetric($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'metrics',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardManageMetric($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'controller'=>'metrics',
            'action' => 'manage-metrics',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardManageapi($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_api',
            'controller' => 'manageapi',
            'action' => 'manage',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardManageforms($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_api',
            'controller' => 'manageforms',
            'action' => 'manage',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardPartnerOrganization($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_extended',
            'controller' => 'partner',
            'action' => 'manage-partner',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }
    public function onMenuInitialize_SitepageDashboardSettings($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'settings',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }

  public function onMenuInitialize_SitepageDashboardServices($row) {

    // check extension installed or not
    $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
    if (!$featureExtension) {
      return false;
    }
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    //check service is enable or not
    $servicePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'service');
    if (empty($servicePrivacy)) {
      return false;
    }
    return array(
      'label' => $row->label,
      'route' => 'sitepage_dashboard',
      'action' => 'service',
      'params' => array(
          'page_id' => $sitepage->getIdentity()
      ),
    );
  }

  public function onMenuInitialize_SitepageDashboardLinkpages($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    // check extension installed or not
    $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
    if (!$featureExtension) {
      return false;
    }
    //FOR SHOW ADD FAVOURITE LINK ON THE PAGE PROFILE PAGE
    $show_link = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addfavourite.show', 0);
    if (empty($show_link)) {
      return false;
    }

    return array(
      'label' => $row->label,
      'route' => 'sitepage_dashboard',
      'action' => 'linkpages',
      'params' => array(
          'page_id' => $sitepage->getIdentity()
      ),
    );
  }

  public function onMenuInitialize_SitepageDashboardTiming($row) {
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    $timing_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.operating.hours.enable', 0);
    if ($sitepage->getType() !== 'sitepage_page' || (!$timing_enable)) {
      return false;
    }
    // check extension installed or not
    $featureExtension = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0);
    if (!$featureExtension) {
      return false;
    }
    return array(
      'label' => $row->label,
      'route' => 'sitepage_dashboard',
      'action' => 'timing',
      'params' => array(
          'page_id' => $sitepage->getIdentity()
      ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditinfo($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_edit',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardProfilepicture($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'profile-picture',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardOverview($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    $overviewPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
    if (empty($overviewPrivacy)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'overview',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

    public function onMenuInitialize_SitepageDashboardManageprojects($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'manage-projects',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );
    }

  public function onMenuInitialize_SitepageDashboardContact($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $contactPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'contact');
    if (empty($contactPrivacy)) {
      return false;
    }
    
    $contactSpicifyFileds = 0;
    $pageOwner = Engine_Api::_()->user()->getUser($sitepage->owner_id);
    $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('sitepage_page', $pageOwner, 'contact_detail');
    $availableLabels = array('phone' => 'Phone', 'website' => 'Website', 'email' => 'Email',);
    $options_create = array_intersect_key($availableLabels, array_flip($view_options));
    if (!empty($options_create)) {
      $contactSpicifyFileds = 1;
    }
    
    if (empty($contactSpicifyFileds)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'contact',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardManagememberroles($row) {

    $sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if (empty($sitepageMemberEnabled)) {
      return false;
    }
    
    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagemember.category.settings', 1) == 1) {
      return false;
    }
    
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'manage-member-category',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardAnnouncements($row) {
    
    //$sitepageMemberEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')) {
      return false;
    }
    
    $pageannoucement = Engine_Api::_()->getApi('settings', 'core')->getSetting('pagemember.announcement', 1);
    if (empty($pageannoucement)) {
      return false;
    }
    
    $sitepagememberGetAnnouucement = Zend_Registry::isRegistered('sitepagememberGetAnnouucement') ? Zend_Registry::get('sitepagememberGetAnnouucement') : null;
    if (empty($sitepagememberGetAnnouucement)) {
      return false;
    }
    
    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $allowPage = Engine_Api::_()->sitepage()->allowInThisPage($sitepage, "sitepagemember", 'smecreate');
    if (empty($allowPage)) {
        return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'announcements',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardAlllocation($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);
    if (empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->enableLocation()) {
      return false;
    }
    
    $mapPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'all-location',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditlocation($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }
    
    $multipleLocation = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.multiple.location', 0);
    if (!empty($multipleLocation)) {
      return false;
    }
    
    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->enableLocation()) {
      return false;
    }
    $mapPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
    if (empty($mapPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'edit-location',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardProfiletype($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $profileTypePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'profile');
    if (empty($profileTypePrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'profile-type',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'profile_type' => $sitepage->profile_type
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardApps($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->sitepage()->getEnabledSubModules()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'app',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardMarketing($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'marketing',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardNotificationsettings($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'notification-settings',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardInsights($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_insights',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'action' => 'index',
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardReports($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $insightPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'insight');
    if (empty($insightPrivacy)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_reports',
        'params' => array(
            'page_id' => $sitepage->getIdentity(),
            'action'  => 'export-report',
            'active' => false
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardBadge($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    $sitepageBadgeEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge');
    if (empty($sitepageBadgeEnabled)) {
      return false;
    }
    
    if (!empty($sitepageBadgeEnabled)) {
      $badgePrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'badge');
      if (!empty($badgePrivacy)) {
        $badgeCount = Engine_Api::_()->sitepagebadge()->badgeCount();
      }
    }
    if (empty($badgeCount)) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepagebadge_request',
        //	'action' => 'edit-style',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardManageadmins($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }

    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_manageadmins',
        'action' => 'index',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardFeaturedowners($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1)) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'featured-owners',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditstyle($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitepage()->allowStyle()) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_dashboard',
        'action' => 'edit-style',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardEditlayout($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->getApi('settings', 'core')->sitepage_layoutcreate) {
      return false;
    }
    
    return array(
        'label' => $row->label,
        'route' => 'sitepage_layout',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardUpdatepackages($row) {

    //GET PAGE ID AND SITEPAGE OBJECT
    $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
    $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
    if ($sitepage->getType() !== 'sitepage_page') {
      return false;
    }

    $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    if (empty($editPrivacy)) {
      return false;
    }
    
    if (!Engine_Api::_()->sitepage()->hasPackageEnable()) {
      return false;
    }
    return array(
        'label' => $row->label,
        'route' => 'sitepage_packages',
        'action' => 'update-package',
        'class' => 'ajax_dashboard_enabled',
        'params' => array(
            'page_id' => $sitepage->getIdentity()
        ),
    );
  }

  public function onMenuInitialize_SitepageDashboardProjects($row) { 
  
      //GET PAGE ID AND SITEPAGE OBJECT
      $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage->getType() !== 'sitepage_page') {
          return false;
      } 
      $viewer = Engine_Api::_()->user()->getViewer();
      $editPrivacy = $sitepage->authorization()->isAllowed($viewer, "edit");
      if (empty($editPrivacy)) {
          return false;
      }
      $crowdfundingEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding'); 
      if (!($crowdfundingEnable && Engine_Api::_()->hasModuleBootstrap('sitecrowdfundingintegration') && Engine_Api::_()->getDbtable('modules', 'sitecrowdfunding')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage')))) {
          return false;
      }
      //DO NOT SHOW THE PROJECTS TAB IF ADMIN HAVE SELECTED PROJECT FOR THE PAGE PROFILE PAGE
      $adminSelectedProject = Engine_Api::_()->sitecrowdfunding()->adminSelectedProject('sitepage_index_view');
      if(!empty($adminSelectedProject)) {
          return false;
      }  
      if (Engine_Api::_()->sitepage()->hasPackageEnable()) {
          $allowProject = Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", 'sitecrowdfunding') ? 1 : 0; 
      } else {
          //permission to create project in above modules(Not Available Yet )
          $allowProject = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sprcreate') ? 1 : 0;
      } 
      if (empty($allowProject)) {
          return false;
      } 
      return array(
          'label' => $row->label,
          'route' => 'sitepage_dashboard', 
          'action' => 'choose-project',
          'class' => 'ajax_dashboard_enabled',
          'params' => array(
              'page_id' => $sitepage->getIdentity()
          ),
      ); 
  }

  public function onMenuInitialize_SitepageDashboardPartner($row) {

      //GET PAGE ID AND SITEPAGE OBJECT
      $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
      $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
      if ($sitepage->getType() !== 'sitepage_page') {
          return false;
      }

      $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
      if (empty($editPrivacy)) {
          return false;
      }

      return array(
          'label' => $row->label,
          'route' => 'sitepage_extended',
          'controller' => 'partner',
          'action' => 'manage-partner',
          'params' => array(
              'page_id' => $sitepage->getIdentity()
          ),
      );

  }

    public function onMenuInitialize_SitepageDashboardManagemembers($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'manage-members',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }
    public function onMenuInitialize_SitepageDashboardManagePrivacy($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_dashboard',
            'action' => 'manage-privacy',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }
    public function onMenuInitialize_SitepageDashboardInitiatives($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_initiatives',
            'action' => 'list',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }

    public function onMenuInitialize_SitepageDashboardGettransactions($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_transaction',
            'action' => 'get-transactions',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }

    // Payment
    public function onMenuInitialize_SitepageDashboardSetPaymentForProjects($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_projectpayment',
            'action' => 'set-payment',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }

    // Payment
    public function onMenuInitialize_SitepageDashboardSetStripePaymentForProjects($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_projectpayment',
            'action' => 'set-stripe-payment',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }

    // Payment Settings
    public function onMenuInitialize_SitepageDashboardSetPaymentSettings($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_projectpayment',
            'action' => 'set-payment-settings',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }

    //Donaction Receipt
    public function onMenuInitialize_SitepageDashboardSetDonationReceipt($row) {

        //GET PAGE ID AND SITEPAGE OBJECT
        $page_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('page_id', null);
        $sitepage = Engine_Api::_()->getItem('sitepage_page', $page_id);
        if ($sitepage->getType() !== 'sitepage_page') {
            return false;
        }

        $editPrivacy = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'sitepage_projectpayment',
            'action' => 'set-donate-receipt',
            'params' => array(
                'page_id' => $sitepage->getIdentity()
            ),
        );

    }
}