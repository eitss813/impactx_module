<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Menus.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Plugin_Menus {

    public function onMenuInitialize_SitecorethemeCoreMiniAdmin($row) {
        // @todo check perms
        if (Engine_Api::_()->getApi('core', 'authorization')->isAllowed('admin', null, 'view')) {
            return array(
                'label' => $row->label,
                'route' => 'admin_default',
                'class' => 'no-dloader',
            );
        }

        return false;
    }

//    public function onMenuInitialize_SitecorethemeLoginSignupPopupAdmin($row) {
//        if(!Engine_Api::_()->hasModuleBootstrap('sitemenu')) {
//            return false;
//        }
//        return true;
//    }

    public function onMenuInitialize_SitecorethemeCoreMiniAuth($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            return array(
                'label' => 'Sign Out',
                'route' => 'user_logout',
                'class' => 'no-dloader',
            );
        } else {
            if ($_SERVER['REQUEST_URI'] == '/net/' || $_SERVER['REQUEST_URI'] == '/network/' ) {
                return array(
                    'label' => 'Sign In',
                    'route' => 'user_login',
                    'params' => array(
                        'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'] . 'members/home'),
                    ),
                );
            }else{
                return array(
                    'label' => 'Sign In',
                    'route' => 'user_login',
                    'params' => array(
                        'return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI']),
                    ),
                );
            }
        }
    }

    public function onMenuInitialize_SitecorethemeCoreMiniSignin($row) {
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!$viewer->getIdentity()) {
            return array(
                'label' => 'Sign In',
                'route' => 'user_login',
                'params' => array(
                    // Nasty hack
                    'return_url' => '64-' . base64_encode($this->url(array('action'=>'home'),"user_general")),
                ),
            );
        }
    }

    public function onMenuInitialize_SitecorethemeSiteeventticketMainTicket() {

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $viewer->getIdentity();

        //RETURN IF VIEWER IS EMPTY
        if (empty($viewer_id)) {
            return false;
        }

        //MUST BE ABLE TO VIEW EVENTS
        if (!Engine_Api::_()->authorization()->isAllowed('siteevent_event', $viewer, "view")) {
            return false;
        }

        if (!Engine_Api::_()->siteevent()->hasTicketEnable()) {
            return false;
        }
        return array(
            'route' => 'siteeventticket_order',
            'params' => array(
                'module' => 'siteeventticket',
                'controller' => 'order',
                'action' => 'my-tickets'
            ),
        );
    }

  public function onMenuInitialize_SitecorethemeAdminSettingsSigninPopup() {
    return true; //Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu');
  }  
}