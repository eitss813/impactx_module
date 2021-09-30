<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Page.php 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestaticpage_Model_Page extends Core_Model_Item_Abstract {

    /**
     *
     * @return string $flage
     */
    protected $_serializedColumns = array('params', 'meta_info');

    public function isViewableByNetwork() {

        $viewer = Engine_Api::_()->user()->getViewer();
        $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
        $viewerNetworkIds = $networkMembershipTable->getMembershipsOfIds($viewer);

        if ($this->networks) {
            if (!empty($viewerNetworkIds)) {
                $commanIds = array_intersect($this->networks, $viewerNetworkIds);
                if (empty($commanIds))
                    $flage = false;
                else
                    $flage = true;
            } else {
                $flage = false;
            }
        }
        return $flage;
    }

    public function getDescription() {

        //RETURN VALUE
        $body = $this->getFullDescription();
        $tmpBody = strip_tags($body);
        return ( Engine_String::strlen($tmpBody) > 255 ? Engine_String::substr($tmpBody, 0, 252) . '...' : $tmpBody );
    }

    /**
     * Convert description text in to current selected language
     *
     */
    public function getFullDescription() {

        //$body = Engine_Api::_()->sitestaticpage()->getLanguageColumn('body');
        $body = 'body';
        if (empty($this->$body)) {
            return $this->body;
        }

        //RETURN VALUE
        return $this->$body;
    }

    /**
     * Gets an absolute URL to the page to view this item
     *
     * @return string
     */
    public function getHref($params = array()) {

        $slug = $this->getSlug();
        $params = array_merge(array(
            'route' => "sitestaticpage_index_index_staticpageid_$this->page_id",
            'reset' => true,
            'staticpage_id' => $this->page_id,
            'slug' => $slug,
                ), $params);
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);

        return Zend_Controller_Front::getInstance()->getRouter()
                        ->assemble($params, $route, $reset);
    }

    /**
     * Return slug
     * */
    public function getSlug($str = null, $maxLength = 64) {
        if (null === $str) {
            $str = $this->title;
        }
        return Engine_Api::_()->seaocore()->getSlug($str, 225);
    }

    public function _delete() {
        parent::_delete();
    }

}
