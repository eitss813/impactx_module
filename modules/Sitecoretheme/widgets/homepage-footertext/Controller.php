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
class Sitecoretheme_Widget_HomepageFootertextController extends Engine_Content_Widget_Abstract {

  public function indexAction() {
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();

    if (!empty($viewer_id)) {
      return $this->setNoRender();
    }
  }

}