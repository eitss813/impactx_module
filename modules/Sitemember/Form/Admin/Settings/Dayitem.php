<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dayitem.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Form_Admin_Settings_Dayitem extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $this->setMethod('post');
    $this->setTitle('Member of the Day')
            ->setDescription('Displays Member of the day as selected by you from below. You can use this widget to highlight any member posted at your site using the auto-suggest box below.');

    $this->addElement('Text', 'member_title', array(
        'label' => 'Member',
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '/application/modules/Sitemember/views/scripts/admin-settings/add-day-item.tpl',
                    'class' => 'form element')))
    ));

    $this->addElement('text', 'user_id', array());


     $this->addElement('Radio', 'circularImage', array(
        'label' => 'Do you want to show circular images instead of square images?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
    ));

    $tempOtherInfoElement = array(
        "featuredLabel" => "Featured Label",
        "sponsoredLabel" => "Sponsored Label",
        "location" => "Location",
        "directionLink" => "Get Direction Link (Dependent on Location)",
        "viewCount" => "Views",
        "likeCount" => "Likes",
        "memberCount" => "Friends",
        "mutualFriend" => "Mutual Friends",
        "memberStatus" => "Member Status (Online)",
        "joined" => "Joined (Duration after signed up)",
        "networks" => "Networks",
        'profileField' => 'Profile Fields'
    );

    //IF SITEVERIFY PLUGIN IS INSTALLED
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteverify')) {
      $tempOtherInfoElement['verifyLabel'] = "Verify Icon";
      $tempOtherInfoElement['verifyCount'] = "Verifies";
    }

    $this->addElement('MultiCheckbox', 'memberInfo', array(
        'label' => $view->translate('Choose the options that you want to be displayed for the members in this block.'),
        'multiOptions' => $tempOtherInfoElement,
        'value' => array("featuredLabel", "sponsoredLabel", "location", "directionLink", "viewCount", "likeCount", "memberCount", "mutualFriend", "memberStatus", "joined", "networks", "profileField")
    ));


    $this->addElement('Text', 'customParams', array(
        'label' => $view->translate("Custom Profile Fields"),
        'Description' => $view->translate('(number of fields to show.)'),
        'required' => true,
        'value' => 5,
    ));

    $this->addElement('Radio', 'custom_field_title', array(
        'label' => 'Do you want to show "Title" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ));

    $this->addElement('Radio', 'custom_field_heading', array(
        'label' => 'Do you want to show "Heading" of custom field?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => '0'
            )
    );

    // Start time
    $start = new Engine_Form_Element_CalendarDateTime('starttime');
    $start->setLabel("Start Time");
    $start->setAllowEmpty(false);
    $this->addElement($start);

    // End time
    $end = new Engine_Form_Element_CalendarDateTime('endtime');
    $end->setLabel("End Time");
    $end->setAllowEmpty(false);
    $this->addElement($end);

    //SHOW PREFIELD START AND END DATETIME
    $httpReferer = $_SERVER['HTTP_REFERER'];
    if (!empty($httpReferer) && strstr($httpReferer, '?page=')) {
      $httpRefererArray = explode('?page=', $httpReferer);
      $page_id = (int) $httpRefererArray['1'];
      if (!empty($page_id) && is_numeric($page_id)) {

        //GET CONTENT TABLE
        $tableContent = Engine_Api::_()->getDbtable('content', 'core');
        $tableContentName = $tableContent->info('name');

        //GET CONTENT
        $params = $tableContent->select()
                ->from($tableContentName, array('params'))
                ->where('page_id = ?', $page_id)
                ->where('name = ?', 'sitemember.item-sitemember')
                ->query()
                ->fetchColumn();

        if (!empty($params)) {
          $params = Zend_Json_Decoder::decode($params);
          if (isset($params['starttime']) && !empty($params['starttime'])) {
            $start->setValue($params['starttime']);
          }

          if (isset($params['endtime']) && !empty($params['endtime'])) {
            $end->setValue($params['endtime']);
          }

          $this->memberInfo->setValue($params['memberInfo']);
        }
      }
    }
  }
}