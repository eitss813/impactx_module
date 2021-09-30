 <?php

 class Sitepage_Form_Admin_Definedlayout_Create extends Engine_form
 {
 	public function init()
 	{
 		$this->setTitle('Layout Details')
 		->setDescription('Enter details related to layout')
 		->setAttrib('class', 'global_form_popup');

 		$this->addElement('Text', 'name', array(
 			'label' => 'Page Name',
 			'required' => true,
             ));

 		$this->addElement('Text', 'title', array(
 			'label' => 'Layout Title',
 			'required' => true,
 			));

 		$this->addElement('File', 'photo', array(
 			'label' => 'Layout Image',
            'accept' => 'image/*',
            'required' => true,
 			));
 		$this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('Textarea', 'style', array(
        'label' => 'Custom Advanced Page Style',
        'description' => 'Add your own CSS code above to give your page a more personalized look.',
        'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
        $this->style->getDecorator('Description')->setOption('placement', 'APPEND');

        $this->addElement('Radio', 'status', array(
 			'label' => 'Enable Layout',
 			'multiOptions' => array(
 				'1' => 'Enable',
 				'0' => 'Disable',
 				),
 			'value' => '1',
 			));
 		$pageTable = Engine_Api::_()->getDbtable('pages', 'core');
 		$pageSelect = $pageTable->select();
 		$pageList = $pageTable->fetchAll($pageSelect);

 		if (count($pageList) != 0) {
            $pageListAssoc[0] = "";
            foreach ($pageList as $page) {
                $pageListAssoc[$page->page_id] = $page->displayname;
            }
            $pageListAssoc[0] = "No, create a blank page";
            $this->addElement('Select', 'duplicate', array(
                'label' => 'Duplicate Existing Page?',
                'multiOptions' => $pageListAssoc,
                'required' => true,
                ));

            $this->addElement('Button', 'submit', array(
                'label' => 'Save Details',
                'type' => 'submit',
                'ignore' => true,
                ));
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'href' => '',
                'onClick' => 'javascript:parent.Smoothbox.close();',
                'decorators' => array(
                    'ViewHelper'
                    )
                ));
            $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
            $button_group = $this->getDisplayGroup('buttons');
        }
    }
}