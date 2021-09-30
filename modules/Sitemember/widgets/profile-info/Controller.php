<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_Widget_ProfileInfoController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
        if (!$subject->authorization()->isAllowed($viewer, 'view')) {
            return $this->setNoRender();
        }

        // Member type
        $subject = Engine_Api::_()->core()->getSubject();
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);

        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            if ($optionId) {
                $optionObj = Engine_Api::_()->fields()
                        ->getFieldsOptions($subject)
                        ->getRowMatching('option_id', $optionId->value);
                if ($optionObj) {
                    $this->view->memberType = $optionObj->label;
                }
            }
        }

    $widgetSettings = array("lastLoginDate", "lastUpdateDate", "inviteeName", "profileType", "memberLevel", "profileViews", "joinedDate", "friendsCount");
    $isAdminAllow = array("lastLoginShow", "lastUpdateShow", "inviteeShow", "profileTypeShow", "memberLevelShow", "profileViewsShow", "joinedDateShow", "friendsCountShow");
    $isAtleastOne = false;

    foreach ($widgetSettings as $key => $value) {
      $userSetting = $subject->toArray();
      if ((
        $subject->authorization()->isAllowed($viewer, $value) ||
        $viewer->isAdmin() || 
        (array_key_exists($value, $userSetting) && $userSetting[$value] == "everyone")) 
        && 
        ($subject->isAllowed('user', $isAdminAllow[$key]) || $viewer->isAdmin())
      ) {
        $this->view->{$value} = true;
        $isAtleastOne = true;
      }
    }

    if (empty($isAtleastOne))
      return $this->setNoRender();
  
        // Networks
        $select = Engine_Api::_()->getDbtable('membership', 'network')->getMembershipsOfSelect($subject)
                ->where('hide = ?', 0);
        $this->view->networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll($select);

        // Friend count
        $this->view->friendCount = $subject->membership()->getMemberCount($subject);
    }

}
