<?php

class Sitepage_Form_Admin_Addtemplate extends Engine_form
{
  protected $_layoutId;
  protected $_templateId;

  public function setLayoutId($params) {
    $this->_layoutId = $params;
  }

  public function setTemplateId($params) {
    $this->_templateId = $params;
  }

  public function init()
  {
    $front = Zend_Controller_Front::getInstance();
    $baseUrl = $front->getBaseUrl();

    $this
      ->setTitle('Create Directory / Pages Package Template')
      ->setDescription('You can create a new template here by customizing and configuring the design of the template for various parameters. To get an idea you can view the image by clicking at the eye icon and make the changes accordingly. You can also preview the template to see the final design before saving.')
      ->setEnctype(Zend_Form::ENCTYPE_MULTIPART)
      ->addAttribs(array('id' => 'addTemplateForm'))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
              'module' => 'sitepage',
              'controller' => 'package',
              'action' => 'add-template',
        ), 'admin_default', true))
      ;
    $this->loadDefaultDecorators();
    $this->getDecorator('Description')->setOption('escape', false);

    $count = 0;
    $layout = $this->addElement('Text','layout',array(
        'order' => $count,
        'decorators' => array( array('ViewScript', array(
            'viewScript' => '_templateElement.tpl',
            'order' => $count++,
            'value' => null
        ))
      ),
    ));
    

    $template_name = $this->addElement('Text','template_name',array(
        'label' => 'Template Name',
        'order' => $count++,
        'description' => 'Name the Template which will be displayed on the template list.',
        'required' => true,
        ));

    // Form Submit Button -----------------------------------------------------------------------------------
    $this->addElement('Button', 'save', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Form Submit Button -----------------------------------------------------------------------------------
    $this->addElement('Cancel', 'preview', array(
        'label' => 'Preview Template',
        'onclick' => '_processTemplateForm.preview()',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array(
        'save',
        'preview',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
        'order' => 999,
    ));

    // Add elements to form according to request -------------------------------------------------------------
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $action = $request->getActionName();

    // CASE :: ADD TEMPLATES
    if ($this->_layoutId != null) {
      $fieldsJSON = Engine_Api::_()->getDbtable('templates','sitepage')->getFieldsJSON($this->_layoutId,1);
      $this->layout->getDecorator('ViewScript')->setOption('value',$this->_layoutId);
    }
    // CASE :: EDIT TEMPLATE
    else if ($this->_templateId != null) {
      $template = Engine_Api::_()->getDbtable('templates','sitepage')->getTemplate($this->_templateId);

      // process JSON
      $templateJSON = Engine_Api::_()->getDbtable('templates','sitepage')->getFieldsJSON($this->_templateId,0);
      $layoutJSON = Engine_Api::_()->getDbtable('templates','sitepage')->getFieldsJSON($template['layout'],0);
      $template_array = json_decode($templateJSON,true);
      $layout_array = json_decode($layoutJSON,true);

      foreach ($layout_array as $key => $value) {
        $layout_array[$key]['value'] = $template_array[$key]['value'];
      }
      $fieldsJSON = json_encode($layout_array);

      // Set form attributes
      $this->setDescription('Here, You can edit the styling of an already created template. The layout and template name cannot be changed once the template is created.');
      $this->setTitle('Edit Template : '.$template['template_name']);
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
            'module' => 'sitepage',
            'controller' => 'package',
            'action' => 'edit-template',
            'template_id' => $this->_templateId,
      ), 'admin_default', true));

      $this->addElement('Hidden','template_id',array(
        'value' => $this->_templateId,
        'order' => 100,
      ));

      // Set form elements 
      $this->template_name->setValue($template['template_name']);
      $this->template_name->setAttrib('readonly', 'true');
      $this->removeElement('layout');
    } else
      return;

    $fieldsJSON = json_decode($fieldsJSON,true);

    foreach ($fieldsJSON as $key => $value) {
      $this->addElement($value['type'],$key,$value['options']);
      $this->$key->setValue($value['value']);
      if ($this->$key->getDecorator('ViewScript')) {
        $this->$key->getDecorator('ViewScript')->setOption('value',$value['value']);
      }
    }
  }
}