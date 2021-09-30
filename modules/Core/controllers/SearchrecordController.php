<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: SearchController.php 9906 2013-02-14 02:54:51Z shaun $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Core_SearchController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        // $searchApi = Engine_Api::_()->getApi('search', 'core');

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        if (!$require_check) {
            if (!$this->_helper->requireUser()->isValid()) {
                return;
            }
        }
        echo "Hi";

    }
}
