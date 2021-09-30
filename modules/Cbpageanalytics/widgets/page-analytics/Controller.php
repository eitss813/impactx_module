<?php

/**
 * SocialEngine
 *
 * @category   Application_Extra
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */

/**
 * @category   Application_Extra
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 */
class Cbpageanalytics_Widget_PageAnalyticsController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $viewer = Engine_Api::_()->user()->getViewer();
        $pagesTable = Engine_Api::_()->getItemTable('core_page');

        $settings = Engine_Api::_()->getApi('settings', 'core')->getSetting('cbpageanalytics.allow.plugin', 1);
        if (!$settings) {
            return $this->setNoRender();
        }

        $viewerID = ($viewer->getIdentity()) ? $viewer->getIdentity() : NULL;

        $page_url = $request->getPathInfo();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        $name = $module . '_' . $controller . '_' . $action;

        $page = $pagesTable->fetchRow($pagesTable->select()->where('name = ?', $name));
        $referrer = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);

        $pageData = array(
            'page_url' => $page_url,
            'user_id' => $viewerID,
            'page_name' => $name,
            'page_module' => $module,
            'page_controller' => $controller,
            'page_action' => $action,
            'request' => 1
        );

        if (count($page)) {
            $pageData['page_original_id'] = $page->getIdentity();
            $pageData['page_title'] = $page->getTitle();
        } else {
            if(isset($this->view->headTitle()[0]) && !empty($this->view->headTitle()[0])){
                $title = $this->view->headTitle()[0];
            } else {
                $title = ''; 
            }
            
            $pageData['page_title'] = $title;
        }

        if (Engine_Api::_()->core()->hasSubject()) {
            $subject = Engine_Api::_()->core()->getSubject();
            $pageData['page_subject_type'] = $subject->getType();
            $pageData['page_subject_id'] = $subject->getIdentity();
            $pageData['page_subject_name'] = $subject->getTitle();
        }

        if ($referrer != NULL) {
            $pageData['referrer_page'] = $referrer;
        }

        $this->view->pageData = $pageData;
    }

}
