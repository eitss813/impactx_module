<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Photo.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class User_Form_Admin_Signup_Location extends Engine_Form
{
    public function init()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');

        // Get step and step number
        $stepTable = Engine_Api::_()->getDbtable('signup', 'user');
        $stepSelect = $stepTable->select()->where('class = ?', str_replace('_Form_Admin_', '_Plugin_', get_class($this)));
        $step = $stepTable->fetchRow($stepSelect);
        $stepNumber = 1 + $stepTable->select()
                ->from($stepTable, new Zend_Db_Expr('COUNT(signup_id)'))
                ->where('`order` < ?', $step->order)
                ->query()
                ->fetchColumn()
        ;
        $stepString = $this->getView()->translate('Step %1$s', $stepNumber);
        $this->setDisableTranslator(true);


        // Custom
        $this->setTitle($this->getView()->translate('%1$s: Add Your Location', $stepString));

        // Element: enable
        $this->addElement('Radio', 'enable', array(
            'label' => 'User location',
            'description' => 'Do you want your users to add their location upon signup?',
            'multiOptions' => array(
                '1' => 'Yes, give users the option to add location upon signup.',
                '0' => 'No, do not allow users to add location upon signup.',
            ),
        ));

        // Element: submit
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));

        // Populate
        $this->populate(array(
            'enable' => $step->enable,
        ));
    }
}