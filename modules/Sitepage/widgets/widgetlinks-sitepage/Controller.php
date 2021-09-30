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
class Sitepage_Widget_WidgetlinksSitepageController extends Engine_Content_Widget_Abstract
{

  //ACTION FOR GETTING THE LINKS OF THE WIDGETS ON PAGE PROFILE PAGE (MEANS WITHOUT TAB LINKS)
  public function indexAction()
  {

    //DON'T RENDER IF SUNJECT IS NOT THERE
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    //GET SITEPAGE SUBJECT
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('sitepage_page');

    //GET PAGE ID
    $page_id = $subject->page_id;

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($subject, 'view');
    if( empty($isManageAdmin) ) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET CORE CONTENT TABLE
    $contentTable = Engine_Api::_()->getDbtable('content', 'core');

    //GET CORE PAGES TABLE
    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');

    //SELECT PAGE
    $selectPage = $pageTable->select()->from($pageTable->info('name'), array('page_id'))->where('name =?', 'sitepage_index_view')->limit(1);

    //GET PAGE INFO
    $pageInfo = $pageTable->fetchRow($selectPage);
    $resource_type = '';
    //HOW MAY LINK SHOULD BE SHOW IN THE WIDGET LINK BEFORE MORE LINK
    $this->view->linklimit = $linklimit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showmore', 8);

    if( !empty($pageInfo) ) {
      $selectContent = $contentTable->select()->from($contentTable->info('name'), array('page_id'))->where('name =?', 'core.container-tabs')->where('page_id =?', $pageInfo->page_id)->limit(1);
      $contentinfo = $contentTable->fetchRow($selectContent);
      if( !empty($contentinfo) && !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0) ) {
        return $this->setNoRender();
      }
    }

    //GET TAB ID
    $this->view->tab_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', null);
    if( !Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.layoutcreate', 0) ) {
      $pageAdminContentTable = Engine_Api::_()->getDbtable('content', 'core');
      $selectPageAdmin = $pageAdminContentTable->select()
        ->from($pageAdminContentTable->info('name'), array('content_id', 'params', 'name'))
        ->where('page_id =?', $pageInfo->page_id)
        ->where("name IN ('sitepage.overview-sitepage', 'sitepage.photos-sitepage',
'sitepage.discussion-sitepage', 'sitepagenote.profile-sitepagenotes', 'sitepagepoll.profile-sitepagepolls',
'sitepageevent.profile-sitepageevents', 'sitepagevideo.profile-sitepagevideos',
'sitepageoffer.profile-sitepageoffers', 'sitepagereview.profile-sitepagereviews',
'sitepagedocument.profile-sitepagedocuments', 'sitepageform.sitepage-viewform','sitepage.info-sitepage',
'seaocore.feed', 'activity.feed','advancedactivity.home-feeds','sitepage.location-sitepage', 'core.profile-links',
'sitepagemusic.profile-sitepagemusic','sitepageintegration.profile-items', 'sitepagemember.profile-sitepagemembers','sitepagetwitter.feeds-sitepagetwitter', 'siteevent.contenttype-events', 'sitevideo.contenttype-videos', 'sitecrowdfunding.contenttype-projects')");
      $pageAdminresult = $pageAdminContentTable->fetchAll($selectPageAdmin);
      $contentWigentLinks = array();
      if( !empty($pageAdminresult) ) {
        foreach( $pageAdminresult as $key => $value ) {
          if( isset($value->params['resource_type']) ) {
            $resource_type = $value->params['resource_type'];
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type) ) {
              $pieces = explode("_", $resource_type);
              $listingTypeId = $pieces[2];
              $count = Engine_Api::_()->getDbtable('contents', 'sitepageintegration')->getCountResults('sitereview_listing', $listingTypeId);
              if( $count == 0 )
                continue;
            }
          }
          $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
          if( !empty($content) ) {
            $contentWigentLinks[$key]['content_id'] = $value->content_id;
            $contentWigentLinks[$key]['content_name'] = $content[0];
            $contentWigentLinks[$key]['content_class'] = $content[1];
            if( isset($content[2]) )
              $contentWigentLinks[$key]['content_resource'] = $content[2];
          }
        }
        $this->view->contentWigentLinks = $contentWigentLinks;
      }
    } else {
      $row = Engine_Api::_()->getDbtable('contentpages', 'sitepage')->getContentPageId($page_id);
      if( !empty($row) ) {
        $contentpage_id = $row->contentpage_id;
        $pageAdminresult = Engine_Api::_()->getDbtable('content', 'sitepage')->getContents($contentpage_id);
      } else {
        $contentpage_id = Engine_Api::_()->sitepage()->getWidgetizedPage()->page_id;
        $pageAdminresult = Engine_Api::_()->getDbtable('admincontent', 'sitepage')->getContents($contentpage_id);
      }


      $contentWigentLinks = array();

      if( !empty($pageAdminresult) ) {
        foreach( $pageAdminresult as $key => $value ) {
          if( isset($value->params['resource_type']) ) {
            $resource_type = $value->params['resource_type'];
            if( Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration') && strstr($resource_type, 'sitereview_listing') && !empty($resource_type) ) {
              $pieces = explode("_", $resource_type);
              $listingTypeId = $pieces[2];
              $count = Engine_Api::_()->getDbtable('contents', 'sitepageintegration')->getCountResults('sitereview_listing', $listingTypeId);
              if( $count == 0 )
                continue;
            }
          }

          $content = $this->getContentName($value->name, $value->params['title'], $resource_type);
          if( !empty($content) ) {
            if( isset($value->content_id) ) {
              $contentWigentLinks[$key]['content_id'] = $value->content_id;
            } else {
              $contentWigentLinks[$key]['content_id'] = $value->admincontent_id;
            }
            $contentWigentLinks[$key]['content_name'] = $content[0];
            $contentWigentLinks[$key]['content_class'] = $content[1];
            if( isset($content[2]) )
              $contentWigentLinks[$key]['content_resource'] = $content[2];
          }
        }
        $this->view->contentWigentLinks = $contentWigentLinks;
      }
    }
  }

  public function getContentName($name, $widgettitle, $resource_type)
  {
    $sitepage = Engine_Api::_()->core()->getSubject('sitepage_page');
    $page_id = $sitepage->page_id;
    $can_edit = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
    $content_array = array();

    //GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();
    $viewer_id = $viewer->getIdentity();
    if( !empty($viewer_id) ) {
      $level_id = $viewer->level_id;
    } else {
      $level_id = 0;
    }
    switch( $name ) {
      case 'advancedactivity.home-feeds':
        $content_array = array($widgettitle, 'icon_sitepage_update');
        break;
      case 'seaocore.feed':
        $content_array = array($widgettitle, 'icon_sitepage_update');
        break;
      case 'activity.feed':
        $content_array = array($widgettitle, 'icon_sitepage_update');
        break;
      case 'sitepage.info-sitepage':
        $content_array = array($widgettitle, 'icon_sitepage_info');
        break;
      case 'core.profile-links':
        $linkTable = Engine_Api::_()->getDbtable('links', 'core');
        $linkTableresult = $linkTable->fetchAll($linkTable->select()->where('parent_id =?', $page_id))->toarray();
        if( !empty($linkTableresult) ) {
          $content_array = array($widgettitle, 'icon_sitepage_page_link');
        }
        break;
      case 'sitepage.photos-sitepage':
        $enable_albums = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum');
        if( $enable_albums ) {

          //TOTAL ALBUMS
          $albumCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'albums');
          $photoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'spcreate');
          if( empty($photoCreate) && empty($albumCount) ) {
            break;
          }

          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagealbum") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'spcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepage_photo_view');
        }
        break;
      case 'sitepage.discussion-sitepage':
        $enable_discussions = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion');
        if( $enable_discussions ) {
          //TOTAL TOPICS
          $discussionsCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'topics');
          $topicComment = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdicreate');
          if( empty($topicComment) && empty($discussionsCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagediscussion") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdicreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepages_discussion');
        }
        break;
      case 'sitepagenote.profile-sitepagenotes':
        $enable_notes = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote');
        if( $enable_notes ) {
          //TOTAL NOTES
          $notesCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagenote', 'notes');
          $noteCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sncreate');
          if( empty($noteCreate) && empty($notesCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagenote") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sncreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepagenote_note');
        }
        break;
      case 'sitepageevent.profile-sitepageevents':
        $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageevent');
        if( $enable_events ) {
          //TOTAL EVENTS
          $eventCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepageevent', 'events');
          $eventCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
          if( empty($eventCreate) && empty($eventCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepageevent');
        }
        break;
      case 'siteevent.contenttype-events':
        $enable_events = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent');
        if( $enable_events ) {
          $eventCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'siteevent', 'events');
          $eventCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'secreate');
          if( empty($eventCreate) && empty($eventCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageevent") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'secreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepageevent');
        }
        break;
      case 'sitepagepoll.profile-sitepagepolls':
        $enable_polls = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll');
        if( $enable_polls ) {
          //TOTAL POLLS
          $pollCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagepoll', 'polls');
          $pollCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'splcreate');
          if( empty($pollCreate) && empty($pollCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagepoll") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'splcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'item_icon_sitepagepoll');
        }
        break;
      case 'sitepagevideo.profile-sitepagevideos':
        $enable_videos = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo');
        if( $enable_videos ) {
          //TOTAL VIDEOS
          $videoCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagevideo', 'videos');
          $videoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
          if( empty($videoCreate) && empty($videoCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_type_sitepagevideo');
        }
        break;
      case 'document.contenttype-documents':
        $enable_documents = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('document');
        if( $enable_documents ) {
          $documentCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'document', 'documents');
          $documentCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
          if( empty($documentCreate) && empty($documentCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepagedocument');
        }
        break;
      case 'sitevideo.contenttype-videos':
        $enable_videos = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo');
        if( $enable_videos ) {
          $videoCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitevideo', 'videos');
          $videoCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'svcreate');
          if( empty($videoCreate) && empty($videoCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagevideo") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'svcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepagevideo');
        }
        break;
      case 'sitecrowdfunding.contenttype-projects':
        $enable_projects = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecrowdfunding');
        if( $enable_projects ) {
          $projectCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitecrowdfunding', 'projects');
          $projectCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sprcreate');
          if( empty($projectCreate) && empty($projectCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitecrowdfunding") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sprcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitecrowdfunding');
        }
        break;
      case 'sitemusic.contenttype-musics':
        $enableMusicAlbum = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemusic');
        if( $enableMusicAlbum ) {
          $musicAlbumCount = Engine_Api::_()->sitepage()->getTotalCount($sitepage->page_id, 'sitemusic', 'music');
          $musicAlbumCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'samcreate');
          if( empty($musicAlbumCreate) && empty($musicAlbumCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitemusic") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitmusic, 'samcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitemusic');
        }
        break;
      case 'sitepagedocument.profile-sitepagedocuments':
        $enable_documents = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument');
        if( $enable_documents ) {
          //TOTAL DOCUMENTS
          $documentCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagedocument', 'documents');
          $documentCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'sdcreate');
          if( empty($documentCreate) && empty($documentCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagedocument") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'sdcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'item_icon_sitepagedocument_detail');
        }
        break;
      case 'sitepagereview.profile-sitepagereviews':
        $enable_reviews = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview');
        if( $enable_reviews ) {
          //TOTAL REVIEW
          $reviewCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagereview', 'reviews');
          $level_allow = Engine_Api::_()->authorization()->getPermission($level_id, 'sitepagereview_review', 'create');
          if( empty($level_allow) && empty($reviewCount) ) {
            break;
          }
          $content_array = array($widgettitle, 'icon_sitepages_review');
        }
        break;
      case 'sitepage.location-sitepage':
        $check_location = Engine_Api::_()->sitepage()->enableLocation();
        $value['id'] = $page_id;
        $location = Engine_Api::_()->getDbtable('locations', 'sitepage')->getLocation($value);
        $isManageAdmin = 0;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'map');
        if( $check_location && $location && !empty($isManageAdmin) ) {
          $content_array = array($widgettitle, 'icon_sitepages_map');
        }
        break;
      case 'sitepage.overview-sitepage':
        $isManageAdmin = 0;
        $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'overview');
        if( empty($isManageAdmin) || empty($can_edit) && empty($sitepage->overview) )
          break;
        $content_array = array($widgettitle, 'icon_sitepages_overview');
        break;
      case 'sitepageform.sitepage-viewform':
        $enable_forms = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform');
        if( $enable_forms ) {
          $quetion = Engine_Api::_()->getDbtable('pagequetions', 'sitepageform');
          $result_quetion = $quetion->fetchRow($quetion->select()->where('page_id = ?', $page_id));
          $option_id = $result_quetion->option_id;
          $itepageforms_table = Engine_Api::_()->getDbtable('sitepageforms', 'sitepageform');
          $select_sitepageform_result = $itepageforms_table->fetchRow($itepageforms_table->select()->where('page_id = ?', $page_id));
          if( !empty($option_id) ) {
            if( $select_sitepageform_result->status == 0 || $select_sitepageform_result->pageformactive == 0 ) {
              break;
            }
            //PACKAGE BASE PRIYACY START
            if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
              if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageform") ) {
                break;
              }
            } else {
              $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'form');
              if( empty($isPageOwnerAllow) ) {
                break;
              }
            }
            //PACKAGE BASE PRIYACY END
            $content_array = array($widgettitle, 'icon_sitepage_form');
          }
        }
        break;
      case 'sitepageoffer.profile-sitepageoffers':
        $enable_offers = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer');
        if( $enable_offers ) {

          $can_edit = 1;
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'edit');
          if( empty($isManageAdmin) ) {
            $can_edit = 0;
          }

          $can_offer = 1;
          $isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'offer');

          if( empty($isManageAdmin) ) {
            $can_offer = 0;
          }

          $can_create_offer = '';

          //OFFER CREATION AUTHENTICATION CHECK
          if( $can_edit == 1 && $can_offer == 1 ) {
            $this->view->can_create_offer = $can_create_offer = 1;
          }

          //TOTAL OFFER
          $offerCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepageoffer', 'offers');
          if( empty($can_create_offer) && empty($offerCount) ) {
            break;
          }

          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepageoffer") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'offer');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'sitepageoffer_type_offer');
        }
        break;
      case 'sitepagemusic.profile-sitepagemusic':
        $enable_music = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic');
        if( $enable_music ) {
          //TOTAL MUSIC
          $musicCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepagemusic', 'playlists');
          $musicCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smcreate');
          if( empty($musicCreate) && empty($musicCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemusic") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smcreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepagemusic_music');
        }
        break;

      case 'sitepagemember.profile-sitepagemembers':
        $enable_member = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember');
        if( $enable_member ) {
          //TOTAL MEMBER
          $memberCount = Engine_Api::_()->sitepage()->getTotalCount($page_id, 'sitepage', 'membership');
          $memberCreate = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'smecreate');
          if( empty($memberCreate) && empty($memberCount) ) {
            break;
          }
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", "sitepagemember") ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, 'smecreate');
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }
          //PACKAGE BASE PRIYACY END
          $content_array = array($widgettitle, 'icon_sitepage_member');
        }
        break;
      case 'sitepageintegration.profile-items':
        $pieces = explode("_", $resource_type);
        $resourceType = $pieces[0] . '_' . $pieces[1];
        $listingTypeId = $pieces[2];

        $enable_sitepageintegration = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageintegration');
        if( $enable_sitepageintegration ) {
          //PACKAGE BASE PRIYACY START
          if( Engine_Api::_()->sitepage()->hasPackageEnable() ) {
            if( !Engine_Api::_()->sitepage()->allowPackageContent($sitepage->package_id, "modules", $resourceType . '_' . $listingTypeId) ) {
              break;
            }
          } else {
            $isPageOwnerAllow = Engine_Api::_()->sitepage()->isPageOwnerAllow($sitepage, $resourceType . '_' . $listingTypeId);
            if( empty($isPageOwnerAllow) ) {
              break;
            }
          }

          //PACKAGE BASE PRIYACY END
          if( $resourceType == 'list_listing' ) {
            $content_array = array($widgettitle, "item_icon_list", $resourceType);
          } elseif( $resourceType == 'sitebusiness_business' ) {
            $content_array = array($widgettitle, "item_icon_sitebusiness", $resourceType);
          } else {
            $content_array = array($widgettitle, "item_icon_sitereview_listtype_$listingTypeId", $resourceType);
          }
        }


        break;
//         case 'sitepagetwitter.feeds-sitepagetwitter':
//
// 				$enable_sitepagetwitter = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter');
// 				if ($enable_sitepagetwitter) {
// 					$isManageAdmin = Engine_Api::_()->sitepage()->isManageAdmin($sitepage, 'twitter');
// 					if (empty($isManageAdmin)) {
// 						break;
// 					}
//
// 					$content_array = array($widgettitle, 'icon_sitepagemusic_music');
// 				}
    }

    return $content_array;
  }

}

?>