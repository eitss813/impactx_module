<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: EmailSettings.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Model_DbTable_EmailSettings extends Engine_Db_Table
{
    /**
     * Gets all enabled email types for a user
     *
     * @param User_Model_User $user
     * @return array An array of enabled email types
     */
    public function getEnabledEmails(User_Model_User $user)
    {
        $emailTypes = Engine_Api::_()->getDbTable('mailTemplates', 'core')->getEmailTypes();

        $select = $this->select()
            ->where('user_id = ?', $user->getIdentity());
        $rowset = $this->fetchAll($select);

        $enabledTypes = array();
        foreach( $emailTypes as $type )
        {
            $row = $rowset->getRowMatching('type', $type->type);
            if( null === $row || $row->email == true )
            {
                $enabledTypes[] = $type->type;
            }
        }

        return $enabledTypes;
    }


    public function getDisabledEmails(User_Model_User $user)
    {
        $emailTypes = Engine_Api::_()->getDbTable('mailTemplates', 'core')->getEmailTypes();

        $select = $this->select()
            ->where('user_id = ?', $user->getIdentity());
        $rowset = $this->fetchAll($select);

        $enabledTypes = array();
        foreach( $rowset as $type )
        {

                $enabledTypes[] = $type->type;

        }

        return $enabledTypes;
    }


    /**
     * Set enabled email types for a user
     *
     * @param User_Model_User $user
     * @param array $emailTypes
     * @return Activity_Api_Emails
     */
    public function setEnabledEmails(User_Model_User $user, array $enabledTypes)
    {
        $emailTypes = Engine_Api::_()->getDbTable('mailTemplates', 'core')->getEmailTypes();
        $emailSettingsDetails = Engine_Api::_()->getDbtable('emailSettings', 'user')->getDisabledEmails($user);

        $select = $this->select()
            ->where('user_id = ?', $user->getIdentity());
        $rowset = $this->fetchAll($select);
        $finaArrr= array();
        foreach( $emailTypes as $type )
        {
            array_push($finaArrr,$type->type);
        }

        $result=array_diff($finaArrr,$enabledTypes);

        foreach( $result as $type )
        {

          $row = $rowset->getRowMatching('type', $type->type);
            $value = in_array($type, $emailSettingsDetails);


            if( $value  )
            {
//                $this->delete(array(
//                    'user_id = ?' =>  $user->getIdentity(),
//                    'type = ?' =>  $type
//                ));

            }
            else if( !$value )
            {

                $this->delete(array(
                    'user_id = ?' =>  $user->getIdentity(),
                    'type = ?' =>  $type
                ));
//                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
//
//                  $db->query('INSERT IGNORE INTO `engine4_user_emailsettings` (`user_id`, `type`, `email`) VALUES(".$user->getIdentity(), $type,(bool) $value.");');
                $row = $this->createRow();
                $row->user_id = $user->getIdentity();
                $row->type = $type;
                $row->email = (bool) $value;
                $row->save();
            }
        }
        foreach( $enabledTypes as $type )
        {
            $this->delete(array(
                'user_id = ?' =>  $user->getIdentity(),
                'type = ?' =>  $type
            ));
        }


        return $this;
    }

    /**
     * Check if a email is enabled
     *
     * @param User_Model_User $user User to check for
     * @param string $type Email type
     * @return bool Enabled
     */
    public function checkEnabledEmail(User_Model_User $user, $type)
    {
        $select = $this->select()
            ->where('user_id = ?', $user->getIdentity())
            ->where('type IN (?)', array('notify_'.$type, $type))
            ->limit(1);

        $row = $this->fetchRow($select);

        if( null === $row )
        {
            return true;
        }

        return (bool) $row->email;
    }
}
