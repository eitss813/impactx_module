<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: UserFieldBirthdate.php 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemember_View_Helper_UserFieldBirthdate extends Fields_View_Helper_FieldBirthdate {

  public function userFieldBirthdate($subject, $field, $value) {

    if (empty($value->value))
      return;

    $label = $this->view->locale()->toDate($value->value, array(
        'size' => 'long',
        'timezone' => false,
    ));

    $parts = @explode('-', $value->value);

    // Error if not filled out
    if (count($parts) < 3 || count(array_filter($parts)) < 3) {
      //$this->addError('Please fill in your birthday.');
      return false;
    }
    $value = mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
    // Error if too low
    $date = new Zend_Date($value);
    $age = (int) (- $date->sub(time()) / 365 / 86400);
    return $this->encloseInLink($subject, $field, $age, $label, true);
  }

}