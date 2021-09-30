<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecrowdfunding
 * @copyright  Copyright 2017-2021 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChangePhoto.php 2017-03-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitecrowdfunding_Form_ChangePhoto extends Engine_Form {

    public function init() {

        $project_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('project_id', null);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);

        $this->setTitle("Edit Profile Picture")
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'EditPhoto');

        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(
                array('ViewScript', array(
                        'viewScript' => '_formEditImage.tpl',
                        'class' => 'form_element',
                        'testing' => 'testing'
                    )
                ),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'form-label')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'form-wrapper sitecrowdfunding_profile_picture_wrapper' , 'id' => 'current-wrapper')),
            ),
        ));
        // Engine_Form::addDefaultDecorators($this->current);

          $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();',
			'id' => 'file_browse',
            'decorators' => array(
                'File',
                array('HtmlTag', array('tag' => 'div' , 'id' => 'file_browse_wrapper' )),
                array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'form-label')),
                array('HtmlTag2', array('tag' => 'div', 'class' => 'form-wrapper file-box' , 'id' => 'Filedata-wrapper')),
            ),
        ));

        $this->addElement('Hidden', 'coordinates', array(
            'filters' => array(
                'HtmlEntities',
            )
        ));

        $this->addElement('Hidden', 'form_type', array('value' => 'edit_profile_pic','order' => 800000));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'remove-photo', 'project_id' => $project_id), "sitecrowdfunding_dashboard", true);
        $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_id);
        if ($project->photo_id != 0) {

            $this->addElement('Button', 'remove', array(
                'label' => 'Remove Photo',
                'onclick' => "removePhotoProject('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $url = $view->url(array('project_id' => $project->project_id, 'slug' => $project->getSlug()), "sitecrowdfunding_entry_view", true);

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' ' . Zend_Registry::get('Zend_Translate')->_('or') . ' ',
                'link' => true,
                'onclick' => "removePhotoProject('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
        }
    }

}
