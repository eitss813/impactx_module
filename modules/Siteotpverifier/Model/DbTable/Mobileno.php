<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteotpverifier
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Mobileno.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteotpverifier_Model_DbTable_Mobileno extends Engine_Db_Table
{
  public function gc()
  {
    // Delete everything older than <del>6</del> 24 hours
    $this->delete(array(
      'creation_date < ?' => date('Y-m-d H:i:s', time() - (3600 * 24)),
    ));
  }
}