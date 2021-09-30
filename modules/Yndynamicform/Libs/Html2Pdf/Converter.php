<?php
require_once('html2pdf.class.php');
class Html2Pdf_Converter {
	
	private $_html2pdf;
	public function __construct($sens = 'P', $format = 'A4', $langue='fr', $unicode=true, $encoding='UTF-8', $marges = array(5, 5, 5, 8)){
		$this->_html2pdf = new HTML2PDF($sens, $format, $langue, $unicode, $encoding, $marges);
	}
	public function __call($method, array $arguments)
	{
		// Requested method
		if( method_exists($this->_html2pdf, $method) )
		{
			$r = new ReflectionMethod($this->_html2pdf, $method);
			//array_unshift($arguments, $this->_sender);
			$return = $r->invokeArgs($this->_html2pdf, $arguments);
			// Hack to make method chaining work
			if( $return === $this->_html2pdf)
			{
				return $this;
			}
			return $return;
		}

		// __call
		if( method_exists($this->_html2pdf, '__call') )
		{
			//array_unshift($arguments, $this->_sender);
			$return = $this->_html2pdf->__call($method, $arguments);
			// Hack to make method chaining work
			if( $return === $this->_html2pdf )
			{
				return $this;
			}
			return $return;
		}
		throw new Engine_Exception(sprintf('ProxyObject method "%s" does not exist and could not be trapped in __call().', $method));
	}
}
?>