 <?php

class Sitepage_Form_Admin_Addlayout extends Engine_form
{
	public function init()
	{
		$front = Zend_Controller_Front::getInstance();
	    $baseUrl = $front->getBaseUrl();

	    $this
	      ->setTitle('Create Directory / Pages Package Layout')
	      ->setDescription('You can create a new layout by making copy of any default layout. You can then edit the new layout file by following the given path and customize the design as per your requirement.')
	      ;


	    // Check permissions for the template file and folders
	    $folderPath = APPLICATION_PATH."/application/modules/Sitepage/views/scripts/layouts/";
	    $defaultLayouts = Engine_Api::_()->getDbtable('layouts','sitepage')->getLayouts();

	    $errorMessage = '';

	    if (!is_writable($folderPath)) 
	    	$errorMessage = '"'.$folderPath.'" doesnot have the permissions to create new file. Please assign CHMOD 0777 permission to the folder ('.$folderPath.').';
	    foreach ($defaultLayouts as $layout) {
	    	$filePath = $folderPath.'_plansTemplate_'.$layout['layout_id'].'.tpl';
	    	if (!is_writable($filePath) && !file_exists($filePath)) 
	    	$errorMessage = '"'.$filePath.'"does not exist or you have not assigned permissions to edit this file. Please assign CHMOD 0777 permission to the folder ('.$folderPath.').';
	    }

	    if (!empty($errorMessage)) {
	    	$this->addError($errorMessage);
	    	return;
	    }

	    // Set form fields  
	    $layout = $this->addElement('Text','layout',array(
	        'decorators' => array( array('ViewScript', array(
	            'viewScript' => '_layoutElement.tpl',
	            'value' => null
	        ))
	      ),
	    ));

	    $template_name = $this->addElement('Text','layout_name',array(
	        'label' => 'Layout Name',
	        'description' => 'Name the Layout which will be displayed on the template list.',
	        'required' => true,
	        ));

	    // Form Submit Button 
	    $this->addElement('Button', 'save', array(
	        'label' => 'Save Changes',
	        'type' => 'submit',
	        'ignore' => true,
	    ));
	}
}