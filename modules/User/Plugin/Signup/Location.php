<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Account.php 10099 2013-10-19 14:58:40Z ivan $
 * @author     John
 */

class User_Plugin_Signup_Location extends Core_Plugin_FormSequence_Abstract
{
    protected $_name = 'location';

    protected $_formClass = 'User_Form_Signup_Location';
    protected $_script = array('signup/form/location.tpl', 'user');

    protected $_adminFormClass = 'User_Form_Admin_Signup_Location';

    protected $_adminScript = array('admin-signup/location.tpl', 'user');


    public $email = null;

    public function onView()
    {

    }

    public function onProcess()
    {
        $user = $this->_registry->user;
        $user_id = $user->user_id;

        // Get form values
        $form = $this->getForm();
        $values = $form->getValues();

        // GET LOCATION TABLE
        $locationTable = Engine_Api::_()->getDbtable('locations', 'user');

        $loctionV['location'] = $values['location'];
        $loctionV['latitude'] = $values['latitude'];
        $loctionV['longitude'] = $values['longitude'];
        $loctionV['formatted_address'] = $values['formatted_address'];
        $loctionV['country'] = $values['country'];
        $loctionV['state'] = $values['state'];
        $loctionV['zipcode'] = $values['zipcode'];
        $loctionV['city'] = $values['city'];
        $loctionV['address'] = $values['address'];
        $loctionV['zoom'] = 16;
        $loctionV['user_id'] = $user_id;

        $locationRow = $locationTable->createRow();
        $locationRow->setFromArray($loctionV);
        $locationRow->save();

    }


    public function onAdminProcess($form)
    {
        $step_table = Engine_Api::_()->getDbtable('signup', 'user');
        $step_row = $step_table->fetchRow($step_table->select()->where('class = ?', 'User_Plugin_Signup_Location'));
        $step_row->enable = $form->getValue('enable');
        $step_row->save();

        $settings = Engine_Api::_()->getApi('settings', 'core');
        $values = $form->getValues();
        $form->addNotice('Your changes have been saved.');
    }

}
