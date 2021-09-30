<?php
/**
 * SocialEngine
 *
 * @category   Application_Module
 * @package    Siteuseravatar
 * @copyright  Copyright 2017-2018 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Avatars.php 2015-09-10 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Siteuseravatar_Model_Dbtable_Avatars extends Engine_Db_Table
{
  public function get(User_Model_User $user)
  {
    return $this->select()
        ->from($this, 'avatar_id')
        ->where('user_id = ?', $user->getIdentity())
        ->query()
        ->fetchColumn();
  }

  public function add(User_Model_User $user, $name)
  {
    $this->remove($user);
    $this->insert(array(
      'user_id' => $user->getIdentity(),
      'name' => $name,
      'avatar_id' => $user->photo_id,
    ));
    return $this;
  }

  function remove(User_Model_User $user)
  {
    $this->delete(array(
      'user_id = ?' => $user->getIdentity(),
    ));
  }

}
