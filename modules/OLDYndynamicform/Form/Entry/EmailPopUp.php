<?php

/**
 * Created by PhpStorm.
 * User: NguyenChiThanh_51203
 * Date: 8/29/2016
 * Time: 11:07 PM
 */
class Yndynamicform_Form_Entry_EmailPopUp extends Engine_Form_Email
{
    protected $_requireEmail;
    public function getRequireEmail()
    {
        return $this -> _requireEmail;
    }
    public function setRequireEmail($requireEmail)
    {
        $this -> _requireEmail = $requireEmail;
        return true;
    }
    public function init()
    {
        $this->setTitle('Email Address')
            ->setDescription("Please input your email address to continue submitting process. 
                                        The email you input will be stored and associated with the submitted entry. 
                                        Later, if you use the same email to register, you will be able to view your
                                        associated submitted entries in \"My Entries\" page.")
            ->setMethod('POST')
            ->setAttrib('class', 'global_form_popup')
        ;
        
        $tabindex = 1;
        // Element: email
        $emailElement = $this->addEmailElement(array(
            'label' => 'Email Address',
            'required' => $this -> getRequireEmail(),
            'allowEmpty' => !$this -> getRequireEmail(),
            'validators' => array(
                array('NotEmpty', true),
                array('EmailAddress', true),
                array('Db_NoRecordExists', true, array(Engine_Db_Table::getTablePrefix() . 'users', 'email'))
            ),
            'filters' => array(
                'StringTrim'
            ),
            // fancy stuff
            'inputType' => 'email',
            'autofocus' => 'autofocus',
            'tabindex' => $tabindex++,
        ));
        $emailElement->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));
        $emailElement->getValidator('NotEmpty')->setMessage('Please enter a valid email address.', 'isEmpty');
        $emailElement->getValidator('Db_NoRecordExists')->setMessage('Someone has already registered this email address, please login with this email to submit entry.', 'recordFound');
        $emailElement->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);
        // Add banned email validator
        $bannedEmailValidator = new Engine_Validate_Callback(array($this, 'checkBannedEmail'), $emailElement);
        $bannedEmailValidator->setMessage("This email address is not available, please login with this email to submit entry.");
        $emailElement->addValidator($bannedEmailValidator);

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Submit',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper'
            ),
        ));
    }

    public function checkBannedEmail($value, $emailElement)
    {
        $bannedEmailsTable = Engine_Api::_()->getDbtable('BannedEmails', 'core');
        if ($bannedEmailsTable->isEmailBanned($value)) {
            return false;
        }
        $isValidEmail = true;
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onCheckBannedEmail', $value);
        foreach ((array)$event->getResponses() as $response) {
            if ($response) {
                $isValidEmail = false;
                break;
            }
        }
        return $isValidEmail;
    }
}