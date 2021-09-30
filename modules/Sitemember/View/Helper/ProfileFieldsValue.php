<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: MemberInfo.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_View_Helper_ProfileFieldsValue extends Zend_View_Helper_Abstract {

    public function profileFieldsValue($sitemember, $fieldName) {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $view->addHelperPath(APPLICATION_PATH . '/application/modules/Sitemember/View/Helper', 'Sitemember_View_Helper');

        $metaTable = Engine_Api::_()->fields()->getTable('user', 'meta');
        $valueTable = Engine_Api::_()->fields()->getTable('user', 'values');

        $stmt = $metaTable->select()
          ->where("type = ? ", $fieldName)
          ->query();
        $fieldIds = array();
        foreach( $stmt->fetchAll() as $field ) {
          $fieldIds[] = $field['field_id'];
        }

        if( empty($fieldIds) ) {
          return '';
        }
        $stmt2 = $valueTable->select()
          ->where("item_id = ? ", $sitemember->user_id)
          ->where("field_id in (?) ", $fieldIds)
          ->query();

        foreach( $stmt2->fetchAll() as $value ) {
          $fieldValue = $value['value'];
        }
        return $fieldValue;
    }

}
?>
