<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: TagController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_ShorturlController extends Core_Controller_Action_Standard
{

    public function getLinkAction()
    {



        $actual_link = $this->_getParam('subject');
        $host = $_SERVER['HTTP_HOST'];
        if($host == 'stage.impactx.co'){
            $host = $host . '/network/';
        }else{
            $host = $host . '/net/';
        }
        $subject = null;
        $url = null;
        if (strpos($actual_link, 'sitepage_initiative') !== false) {
            $linkArrTemp = explode("?", $actual_link);
            $linkArr = explode("_", $linkArrTemp[0]);
            $actual_link = 'https://' . $host . '/organizations/initiatives/landing-page/page_id/' . $linkArr[2] . '/initiative_id/' . $linkArr[3];
            $url = $actual_link;
            $sub = $linkArr[0].'_'.$linkArr[1].'_'.$linkArr[3] ;
            $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($sub);


        }else {
            $this->view->subject = $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        }
        $this->view->url = Engine_Api::_()->getApi('Shorturl', 'core')->generateShorturl($subject,$url);
    }

}