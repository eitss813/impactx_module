<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: InstallController.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_InstallController extends Core_Controller_Action_Standard
{

  protected $_token;

  public function init()
  {
    $this->_helper->contextSwitch->initContext();
    $settingsTable = Engine_Api::_()->getDbtable('settings', 'core');
    $row = $settingsTable->fetchRow($settingsTable->select()
        ->where('name = ?', 'sitecoretheme.install.ssotoken'));
    $this->_token = $row ? $row->value : null;    
    $token = $this->_getParam('key', false);
    if( $token != $this->_token ) {
      exit(0);
    }
  }

  public function postInstallAction()
  {
    //CHANGE LAYOUT OF HEADER,FOOTER,SIGNIN,SIGNUP page
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setHeaderLayout(array('sitecoretheme_header_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setFooterLayout(array('sitecoretheme_footer_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInPageLayout(array('sitecoretheme_login_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignInRequiredPageLayout(array('sitecoretheme_login_required_page_layout' => 1));
    Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setSignUpPageLayout(array('sitecoretheme_signup_page_layout' => 1));
		Engine_Api::_()->getApi('pagelayouts', 'sitecoretheme')->setDefaultLayout(array('sitecoretheme_landing_page_layout' => 1));
    $settingsTable = Engine_Api::_()->getDbtable('settings', 'core');
    $settingsTable->delete(array('name = ?' => 'sitecoretheme.install.ssotoken'));
    exit(0);
  }

}