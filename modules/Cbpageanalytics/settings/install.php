<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
class Cbpageanalytics_Installer extends Engine_Package_Installer_Module {

    public function onInstall() {
        $this->_addGeneral();

        parent::onInstall();
    }

    protected function _addGeneral() {
        $db = $this->getDb();

        // Site Footer
        $pageId = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', 'footer')
                ->limit(1)
                ->query()
                ->fetchColumn();

        //check if widget already not there
        $widget = $db->select()
                ->from('engine4_core_content', 'page_id')
                ->where('name = ?', 'cbpageanalytics.page-analytics')
                ->limit(1)
                ->query()
                ->fetchColumn();

        if ($pageId && !$widget) {
            // Site Footer Container
            $contentId = $db->select()
                    ->from('engine4_core_content', 'content_id')
                    ->where('page_id = ?', $pageId)
                    ->where('type = ?', 'container')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            $order = $db->select()
                    ->from('engine4_core_content', 'order')
                    ->where('page_id = ?', $pageId)
                    ->order('content_id DESC')
                    ->limit(1)
                    ->query()
                    ->fetchColumn();

            // Add widget
            $db->insert('engine4_core_content', array(
                'type' => 'widget',
                'name' => 'cbpageanalytics.page-analytics',
                'page_id' => $pageId,
                'parent_content_id' => $contentId,
                'order' => $order + 1,
            ));
        }
    }

}

?>