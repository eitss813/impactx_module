<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Controller.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Widget_SampleController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $this->view->title='Sample Page';
        // Do not show if logged in
        //if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
        //   $this->setNoRender();
        //    return;
       //  }



    }

    public function getCacheKey()
    {
        return false;
    }
}