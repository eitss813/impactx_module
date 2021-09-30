<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Adsettings.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitepage_Form_Admin_Adsettings extends Engine_Form {

  public function init() {

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if (!$enable_ads) {
      $this->addElement('Dummy', 'note', array(
          'description' => '<div class="tip"><span>' . sprintf(Zend_Registry::get('Zend_Translate')->_('This plugin provides deep integration for advertising using the "%1$sAdvertisements / Community Ads Plugin%2$s". Please install and enable this plugin to configure settings for the various ad positions and widgets available. If you do not have this plugin yet, click here to view its details and purchase it: %1$shttp://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin%2$s.'), '<a href="http://www.socialengineaddons.com/socialengine-advertisements-community-ads-plugin" target="_blank">', '</a>') . '</span></div>',
          'decorators' => array(
              'ViewHelper', array(
                  'description', array('placement' => 'APPEND', 'escape' => false)
          ))
      ));
    }

    $enable_ads = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad');
    if ($enable_ads) {
      $this
              ->setTitle('Ad Settings')
              ->setDescription('This plugin provides seamless integration with the "Advertisements / Community Ads Plugin". Attractive advertising can be done using the many available, well designed ad positions in this plugin. Below, you can configure the settings for the various ad positions and widgets.');
      $this->addElement('Radio', 'sitepage_adpreview', array(
          'label' => 'Sample Ad Widget',
           'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show a Sample Ad of a Page on its profile in the Info widget? (This widget will only be visible to page admins and will tempt them to create an Ad for their Page. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitepage/externals/images/ad_preview.png\');"></a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpreview', 1),
      ));
      $this->sitepage_adpreview->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitepage_adcreatelink', array(
          'label' => 'Advertise your Page Widget',
          'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Do you want to show the Advertise your Page widget on Page Profile in the Info widget? (This widget will only be visible to page admins and it displays a catchy phrase to tempt them to create an Ad for their Page. It also has a link to Create an Ad. Click %s to preview this widget.)'), '<a href="javascript:void(0);" onclick="parent.Smoothbox.open(\'application/modules/Sitepage/externals/images/ad_content.png\');">here</a>'),
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adcreatelink', 1),
      ));
      $this->sitepage_adcreatelink->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

      $this->addElement('Radio', 'sitepage_communityads', array(
          'label' => 'Community Ads in this plugin',
          'description' => 'Do you want to show community ads in the various positions available in this plugin? (Below, you will be able to choose for every individual position. If you do not want to show ads in a particular position, then please enter the value "0" for it below.). If you have enabled Packages for Pages from Global Settings, then for each package, you can configure if community ads display should be enabled on their Pages (You can use this to give extra privileges to Pages of special / paid packages by making them free of ads display.).',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showads(this.value)',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1),
      ));

//       if ((Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote')) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
//         $lightbox_photos = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepagealbum.photolightbox.show', 1);
//         if (!empty($lightbox_photos)) {
//           $this->addElement('Radio', 'sitepage_lightboxads', array(
//               'label' => 'Ads in Photos Lightbox',
//               'description' => 'Do you want to show ads in ajax lightbox for viewing photos of albums of pages?',
//               'multiOptions' => array(
//                   1 => 'Yes',
//                   0 => 'No'
//               ),
//               'onclick' => 'showlightboxads(this.value)',
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.lightboxads', 1),
//           ));
// 
//           $this->addElement('Radio', 'sitepage_adtype', array(
//               'label' => 'Type of Ads in Photos Lightbox',
//               'description' => 'Select the type of ads you want to show in the ajax lightbox for viewing photos of albums of pages.',
//               'multiOptions' => array(
//                   3 => 'All',
//                   2 => 'Sponsored Ads',
//                   1 => 'Featured Ads',
//                   0 => 'Both Sponsored and Featured Ads'
//               ),
//               'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtype', 3),
//           ));
//         }
//       }

      $this->addElement('Text', 'sitepage_admylikes', array(
          'label' => 'Pages I Like Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page i like page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admylikes', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagenote') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adnotewidget', array(
          'label' => 'Page Profile Notes Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile notes widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotewidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adnoteview', array(
          'label' => 'Page Notes View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteview', 3),
      ));

      $this->addElement('Text', 'sitepage_adnotebrowse', array(
          'label' => 'Page Notes Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotebrowse', 3),
      ));

      $this->addElement('Text', 'sitepage_adnotecreate', array(
          'label' => 'Page Note\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotecreate', 3),
      ));

      $this->addElement('Text', 'sitepage_adnoteedit', array(
          'label' => 'Page Note\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteedit', 3),
      ));

      $this->addElement('Text', 'sitepage_adnotedelete', array(
          'label' => 'Page Note\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotedelete', 1),
      ));


      $this->addElement('Text', 'sitepage_adnoteaddphoto', array(
          'label' => 'Page Note\'s Add Photos Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s add photos page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteaddphoto', 1),
      ));

      $this->addElement('Text', 'sitepage_adnoteeditphoto', array(
          'label' => 'Page Note\'s Edit Photo Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s edit photo page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnoteeditphoto', 5),
      ));

      $this->addElement('Text', 'sitepage_adnotesuccess', array(
          'label' => 'Page Note\'s Creation Success Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page note\'s creation success page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adnotesuccess', 1),
      ));
    }

    if ((Engine_Api::_()->hasModuleBootstrap('siteevent') && Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitepage_page', 'item_module' => 'sitepage'))) && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adeventwidget', array(
          'label' => 'Page Profile Events Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile events widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adeventview', array(
          'label' => 'Page Event\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventview', 2),
      ));

      $this->addElement('Text', 'sitepage_adeventbrowse', array(
          'label' => 'Page Event\'s Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventbrowse', 2),
      ));

      $this->addElement('Text', 'sitepage_adeventcreate', array(
          'label' => 'Page Event\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventcreate', 2),
      ));

      $this->addElement('Text', 'sitepage_adeventedit', array(
          'label' => 'Page Event\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventedit', 2),
      ));

      $this->addElement('Text', 'sitepage_adeventdelete', array(
          'label' => 'Page Event\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventdelete', 1),
      ));
      
        $this->addElement('Text', 'sitepage_adeventaddphoto', array(
          'label' => 'Page Event\'s Add Photos Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s add photos page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventaddphoto', 1),
      ));

      $this->addElement('Text', 'sitepage_adeventeditphoto', array(
          'label' => 'Page Event\'s Edit Photo Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page event\'s edit photo page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adeventeditphoto', 5),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagealbum') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adalbumwidget', array(
          'label' => 'Page Profile Albums Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile albums widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adalbumview', array(
          'label' => 'Page Album\'s Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page album\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumview', 3),
      ));

      $this->addElement('Text', 'sitepage_adalbumbrowse', array(
          'label' => 'Page Album\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page album\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumbrowse', 3),
      )); 

      $this->addElement('Text', 'sitepage_adalbumcreate', array(
          'label' => 'Page Album\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page album\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumcreate', 2),
      ));

      $this->addElement('Text', 'sitepage_adalbumeditphoto', array(
          'label' => 'Page Album\'s Edit Photos Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page album\'s edit photos page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adalbumeditphoto', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagediscussion') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_addicussionwidget', array(
          'label' => 'Page Profile Discussions Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile discussions widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addicussionwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_addiscussionview', array(
          'label' => 'Page Discussion\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page dicussion\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionview', 2),
      ));

      $this->addElement('Text', 'sitepage_addiscussioncreate', array(
          'label' => 'Page Discussion\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page dicussion\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussioncreate', 2),
      ));

      $this->addElement('Text', 'sitepage_addiscussionreply', array(
          'label' => 'Page Discussion\'s Post Reply Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a dicussion\'s post reply page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addiscussionreply', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagedocument') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_addocumentwidget', array(
          'label' => 'Page Profile Documents Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile documents widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_addocumentview', array(
          'label' => 'Page Document\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Page document\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentview', 2),
      ));

      $this->addElement('Text', 'sitepage_addocumentbrowse', array(
          'label' => 'Page Document\'s Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Page document\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentbrowse', 2),
      ));

      $this->addElement('Text', 'sitepage_addocumentcreate', array(
          'label' => 'Page Document\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Page document\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentcreate', 4),
      ));

      $this->addElement('Text', 'sitepage_addocumentedit', array(
          'label' => 'Page Document\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Page document\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentedit', 4),
      ));

      $this->addElement('Text', 'sitepage_addocumentdelete', array(
          'label' => 'Page Document\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a Page document\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.addocumentdelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagevideo') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_advideowidget', array(
          'label' => 'Page Profile Videos Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile videos widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideowidget', 3),
      ));

      $this->addElement('Text', 'sitepage_advideoview', array(
          'label' => 'Page Video\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page video\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideoview', 2),
      ));

      $this->addElement('Text', 'sitepage_advideobrowse', array(
          'label' => 'Page Video\'s Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page video\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideobrowse', 2),
      ));

      $this->addElement('Text', 'sitepage_advideocreate', array(
          'label' => 'Page Video\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page video\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideocreate', 2),
      ));

      $this->addElement('Text', 'sitepage_advideoedit', array(
          'label' => 'Page Video\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page video\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideoedit', 2),
      ));

      $this->addElement('Text', 'sitepage_advideodelete', array(
          'label' => 'Page Video\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page video\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.advideodelete', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagepoll') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adpollwidget', array(
          'label' => 'Page Profile Polls Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile polls widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adpollview', array(
          'label' => 'Page Poll\'s View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page poll\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollview', 2),
      ));

      $this->addElement('Text', 'sitepage_adpollbrowse', array(
          'label' => 'Page Poll\'s Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page poll\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollbrowse', 2),
      ));

      $this->addElement('Text', 'sitepage_adpollcreate', array(
          'label' => 'Page Poll\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page poll\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpollcreate', 2),
      ));

      $this->addElement('Text', 'sitepage_adpolldelete', array(
          'label' => 'Page Poll\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page poll\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adpolldelete', 1),
      ));
    }
    
    
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_admemberwidget', array(
          'label' => 'Page Profile Members Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile members widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admemberwidget', 3),
      ));
      $this->addElement('Text', 'sitepage_admemberbrowse', array(
          'label' => 'Page Members Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page member\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admemberbrowse', 3),
      ));
    }
    

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagereview') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adreviewwidget', array(
          'label' => 'Page Profile Reviews Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile reviews widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adreviewcreate', array(
          'label' => 'Page Review\'s Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page review\'s create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewcreate', 2),
      ));

      $this->addElement('Text', 'sitepage_adreviewedit', array(
          'label' => 'Page Review\'s Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page review\'s edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewedit', 2),
      ));

      $this->addElement('Text', 'sitepage_adreviewdelete', array(
          'label' => 'Page Review\'s Delete Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page review\'s delete page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewdelete', 1),
      ));

      $this->addElement('Text', 'sitepage_adreviewview', array(
          'label' => 'Page Reviews Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page review\'s browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewview', 2),
      ));

      $this->addElement('Text', 'sitepage_adreviewbrowse', array(
          'label' => 'Page Reviews View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page review\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adreviewbrowse', 2),
      ));  

    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageoffer') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adofferwidget', array(
          'label' => 'Page Profile Offers Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile offers widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adofferwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adofferpage', array(
          'label' => 'Page Offers Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on page offers browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adofferpage', 2),
      ));

      $this->addElement('Text', 'sitepage_adofferlist', array(
          'label' => 'Page Offers List Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on page offers list page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adofferlist', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageform') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adformwidget', array(
          'label' => 'Page Profile Form Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile form widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adformwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adformcreate', array(
          'label' => 'Form Add Question Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on form\'s add question page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adformcreate', 3),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepageinvite') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adinvite', array(
          'label' => 'Invite Friends Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on invite friends page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adinvite', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagebadge') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adbadgeview', array(
          'label' => 'Badges View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a badge\'s view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adbadgeview', 2),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_adlocationwidget', array(
          'label' => 'Page Profile Map Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile map widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adlocationwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adoverviewwidget', array(
          'label' => 'Page Profile Overview Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile overview widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adoverviewwidget', 0),
      ));

      $this->addElement('Text', 'sitepage_adinfowidget', array(
          'label' => 'Page Profile Info Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile info widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adinfowidget', 3),
      ));

      $this->addElement('Text', 'sitepage_adclaimview', array(
          'label' => 'Claim Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on claim page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adclaimview', 2),
      ));

      $this->addElement('Text', 'sitepage_adtagview', array(
          'label' => 'Browse Tags Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on browse tags page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtagview', 1),
      ));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemusic') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Text', 'sitepage_admusicwidget', array(
          'label' => 'Page Profile Music Widget',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown in page profile music widget?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicwidget', 3),
      ));

      $this->addElement('Text', 'sitepage_admusicview', array(
          'label' => 'Page Music View Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page music view page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicview', 3),
      ));

      $this->addElement('Text', 'sitepage_admusicbrowse', array(
          'label' => 'Page Music Browse Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page music browse page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicbrowse', 3),
      ));

      $this->addElement('Text', 'sitepage_admusiccreate', array(
          'label' => 'Page Music Create Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page music create page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusiccreate', 3),
      ));

      $this->addElement('Text', 'sitepage_admusicedit', array(
          'label' => 'Page Music Edit Page',
          'maxlenght' => 3,
          'description' => 'How many ads will be shown on a page music edit page?',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.admusicedit', 3),
      ));
    }

	  //START FOR INRAGRATION WORK WITH OTHER PLUGIN.
    $sitepageintegrationModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitepageintegration');
    if(!empty($sitepageintegrationModEnabled)&& Engine_Api::_()->getDbtable('modules',
      'core')->isModuleEnabled('communityad')) {
			$mixSettingsResults = Engine_Api::_()->getDbtable( 'mixsettings' , 'sitepageintegration'
	    )->getIntegrationItems();
			foreach($mixSettingsResults as $modNameValue) {

// 				if(strstr($modNameValue['resource_type'], 'sitereview_listing')) {
// 					$item_title = 'Products';
// 				} else {
// 					$item_title = $modNameValue["item_title"];
// 				}
        $item_title = $modNameValue["item_title"];
				$this->addElement('Text', "sitepage_ad_" . $modNameValue['resource_type']. '_' .$modNameValue['listingtype_id'], array(
					'label' => "Page Profile " . $item_title . " Widget",
					'maxlenght' => 3,
					'description' => "How many ads will be shown in page profile " .  $item_title . "   widget?",
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("sitepage.ad." . $modNameValue['resource_type'].".".$modNameValue['listingtype_id'] , 3),
				));
			}
	  }
    //END FOR INRAGRATION WORK WITH OTHER PLUGIN.

	  //START AD WORK FOR TWITTER
    $sitepagetwitterModEnabled = Engine_Api::_()->getDbtable('modules',
     'core')->isModuleEnabled('sitepagetwitter');
		if(!empty($sitepagetwitterModEnabled)&& Engine_Api::_()->getDbtable('modules',
				'core')->isModuleEnabled('communityad')) {
			$this->addElement('Text', 'sitepage_adtwitterwidget', array(
					'label' => 'Page Profile Twitter Widget',
					'maxlenght' => 3,
					'description' => 'How many ads will be shown in page profile twitter widget?',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.adtwitterwidget', 3),
			));
		}
		//END AD WORK FOR TWITTER

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
      $this->addElement('Button', 'submit', array(
          'label' => 'Save Changes',
          'type' => 'submit',
          'ignore' => true
      ));
    }
  }

}

?>