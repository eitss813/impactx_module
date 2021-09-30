<?php
class YounetCore_BootHelper extends Zend_Controller_Action_Helper_Abstract
{
	/**
	* @var Zend_View_Interface
	*/
	public $view;

	/**
	* postDispatch - auto render a view
	*
	* Only autorenders if:
	* - _noRender is false
	* - action controller is present
	* - request has not been re-dispatched (i.e., _forward() has not been called)
	* - response is not a redirect
	*
	* @return void
	*/
	public function postDispatch()
	{
	  	$request = Zend_Controller_Front::getInstance() -> getRequest();
	  	$controller_name = $request -> getControllerName();
	  	if (strpos($controller_name,'admin-') !== false)
	  	{
	  		return;
	  	}
	  	$view  =  Zend_Registry::get('Zend_View');
		// check module responsive file
		$view -> headLink() -> appendStylesheet($view -> layout() -> staticBaseUrl . 'application/css.php?request=application/modules/YounetCore/externals/styles/ynresponsive.css');
		$bootstrap_script = "
			window.addEvent('domready', function(){
				$$('body')[0].addClass('".RESPONSIVE_ACTIVE."');
			});";
		$view->headScript()->appendScript( $bootstrap_script );
	}

}