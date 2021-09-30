<?php

class Sesblog_Form_Import extends Engine_Form {
 
  public function init() {
  
    $this->setTitle('Import Blogs')->setDescription('')->setAttrib('name', 'blog_import')->setAttrib('enctype', 'multipart/form-data');
    
    $this->addElement('Select', 'import_type', array(
      'label' => 'Type of Import',
      'multiOptions' => array("0"=>"","1"=>"Blogger", "2"=>"WordPress","3"=>"Tumblr"),
      'description' => 'Choose a site to import.',
      'onchange' => "showImportOption(this.value);"
    ));

    $this->addElement('File', 'file_data', array(
      'label' => 'Blog XML File',
      'description' => 'Choose a corresponding site file XML to import.' 
    ));
    $this->file_data->addValidator('Extension', false, 'xml');
    
    
    $this->addElement('Text', 'user_name', array(
      'label' => 'Tumblr User Name',
      'description' => 'Please put here your tumblr account user name to import blogs.',
      'filters' => array(
        new Engine_Filter_Censor(),
      )
    ));
    $this->user_name->getDecorator("Description")->setOption("placement", "append");
    

    $this->addElement('Button', 'submit', array(
      'label' => 'Start Importing',
      'type' => 'submit',
    ));
  
  }
}
