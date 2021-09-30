<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoretheme
 * @copyright  Copyright 2019-2020 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Subscribermail.php 2019-07-09 15:11:20Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecoretheme_Form_Admin_Subscribermail extends Engine_Form
{

  public function init()
  {
    $description = $this->getTranslator()->translate(
      'Using this form, you will be able to send an email to all of your subscribers. Emails are sent using queuing system, so they will be sent to all subscribers gradually. A confirmation email will be sent to you when all emails have been sent. <br>');

    // Decorators
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $this
      ->setTitle('Send Newsletter to All Subscribers')
      ->setDescription($description);

    $settings = Engine_Api::_()->getApi('settings', 'core')->core_mail;

    if( !@$settings['queueing'] ) {
      $this->addElement('Radio', 'queueing', array(
        'label' => 'Utilize Mail Queue',
        'description' => 'Mail queueing permits the emails to be sent out over time, preventing your mail server
           from being overloaded by outgoing emails.  It is recommended you utilize mail queueing for large email
           blasts to help prevent negative performance impacts on your site.',
        'multiOptions' => array(
          1 => 'Utilize Mail Queue (recommended)',
          0 => 'Send all emails immediately (only recommended for less than 100 recipients).',
        ),
        'value' => 1,
      ));
    }


    $this->addElement('Text', 'from_address', array(
      'label' => 'Sender’s Email Address',
      'value' => (!empty($settings['from']) ? $settings['from'] : 'noreply@' . $_SERVER['HTTP_HOST']),
      'required' => true,
      'allowEmpty' => false,
      'validators' => array(
        'EmailAddress',
      )
    ));
    $this->from_address->getValidator('EmailAddress')->getHostnameValidator()->setValidateTld(false);

    $this->addElement('Text', 'from_name', array(
      'label' => 'Sender’s Name',
      'required' => true,
      'allowEmpty' => false,
      'value' => (!empty($settings['name']) ? $settings['name'] : 'Site Administrator'),
    ));


    $this->addElement('Text', 'subject', array(
      'label' => 'Subject',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Textarea', 'body', array(
      'label' => 'Message',
      'required' => true,
      'allowEmpty' => false,
      'description' => '(HTML or Plain Text)',
    ));
    $this->body->getDecorator('Description')->setOptions(array('placement' => 'APPEND'));


    $this->addElement('Textarea', 'body_text', array(
      'label' => 'Body (text)',
    ));

    $this->addDisplayGroup(array('body_text'), 'advanced', array(
      'decorators' => array(
        'FormElements',
        array('Fieldset', array('style' => 'display:none;')),
      ),
    ));


    // init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Send Emails',
      'type' => 'submit',
      'ignore' => true,
    ));
  }

}