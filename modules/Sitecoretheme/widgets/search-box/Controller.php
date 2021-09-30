<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Widget_SearchBoxController extends Engine_Content_Widget_Abstract {

  public function indexAction() {

    $this->view->searchbox_width = $this->_getParam('sitecoretheme_search_width', 240);

    $this->view->sitecoretheme_search_box_width_for_nonloggedin = $this->_getParam('sitecoretheme_search_box_width_for_nonloggedin', 275);
    $this->view->isSiteadvsearchEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch');
  }

}