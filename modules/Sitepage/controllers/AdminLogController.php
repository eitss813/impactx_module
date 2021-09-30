<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLogController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepage_AdminLogController extends Core_Controller_Action_Admin {

	//ACTION FOR SHOWING THE LOG HISTORY OF IMPORT
  public function indexAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('sitepage_admin_main', array(), 'sitepage_admin_main_import');

		//LOG FILE PATH
    $logPath = APPLICATION_PATH . '/temporary/log';

    //GET ALL EXISTING IMPORT HISTORY FILES
    $logFiles = array();
    foreach (scandir($logPath) as $file) {
      if ($file == 'CSVToPageImport.log' || $file == 'ListingToPageImport.log') {
        if (strtolower(substr($file, -4)) == '.log') {
          $logFiles[] = substr($file, 0, -4);
        }
      }
    }

    //NO FILES
    $this->view->logFiles = $logFiles;
    if (empty($logFiles)) {
      $this->view->error = 'There are no log files to view.';
      return;
    }

    //MAKE FORM
		$csvImportVar = $this->view->translate('Page Import From a CSV File');
		$listingImportVar = $this->view->translate('Page Import From Listings');
    $labels = array(
        'CSVToPageImport' => $csvImportVar,
        'ListingToPageImport' => $listingImportVar
    );
    $multiOptions = array_combine($logFiles, $logFiles);
    $labels = array_intersect_key($labels, $multiOptions);
    $multiOptions = array_diff_key($multiOptions, $labels);
    $multiOptions = array_merge($labels, $multiOptions);

    $this->view->formFilter = $formFilter = new Sitepage_Form_Admin_Import_Log();
    $formFilter->getElement('file')->addMultiOptions($multiOptions);

    $values = array_merge(array(
                'length' => 50,
                    ), $this->_getAllParams());

    if ($formFilter->isValid($values)) {
      $values = $formFilter->getValues();
    } else {
      $values = array('length' => 50);
    }

    //MAKE SURE PARAM IS IN EXISTING LOG FILES
    $logName = @$values['file'];
    $logFile = $logName . '.log';
    if (empty($logName) ||
            !in_array($logName, $logFiles) ||
            !file_exists($logPath . DIRECTORY_SEPARATOR . $logFile)) {
      $logName = null;
      $logFile = null;
    }

    //EXIT IF NO VALID LOG FILE
    if (!$logFile) {
			$error = $this->view->translate('Please select a file to view.');
      $this->view->error = $error;
      return;
    }

    //CLEAR LOG IF REQUESTED
    if ($this->getRequest()->isPost() && $this->_getParam('clear', false)) {
      if (($fh = fopen($logPath . DIRECTORY_SEPARATOR . $logFile, 'w'))) {
        ftruncate($fh, 0);
        fclose($fh);
      }
      return $this->_helper->redirector->gotoRoute(array());
    }

    //GET LOG LENGHT
    $this->view->logFile = $logFile;
    $this->view->logSize = $logSize = filesize($logPath . DIRECTORY_SEPARATOR . $logFile);
    $this->view->logLength = $logLength = $values['length'];
    $this->view->logOffset = $logOffset = $this->_getParam('offset', $logSize);

    //TAIL THE FILE
    $endOffset = 0;
    try {
      $lines = $this->_tail($logPath . DIRECTORY_SEPARATOR . $logFile, $logLength, $logOffset, true, $endOffset);
    } catch (Exception $e) {
      $this->view->error = $e->getMessage();
      return;
    }

    $this->view->logText = $lines;
    $this->view->logEndOffset = $endOffset;
  }

	//FUNCTION FOR FINDING THE FILE END
  protected function _tail($file, $length = 10, $offset = 0, $whence = true, &$endOffset = null) {

    //CHECK STUFF
    if (!file_exists($file)) {
      throw new Exception('File does not exist.');
    }
    if (0 === ($size = filesize($file))) {
      throw new Exception('File is empty.');
    }
    if (!($fh = fopen($file, 'r'))) {
      throw new Exception('Unable to open file.');
    }

    //PROCESS ARGS
    if (abs($offset) > $size) {
      throw new Exception('Reached end of file.');
    }
    if (!in_array($whence, array(SEEK_SET, SEEK_END))) {
      throw new Exception('Unknown whence.');
    }

    //SEEK TO REQUESTED POSITION
    fseek($fh, $offset, SEEK_SET);

    // Read in chunks of 512 bytes
    $position = $offset;
    $break = false;
    $lines = array();
    $chunkSize = 32;
    $buffer = '';

    do {

      //GET NEXT POSITION
      $position += ( $whence ? -$chunkSize : $chunkSize );
      fseek($fh, $position, SEEK_SET);

      //WHOOPS WE RAN OUT OF STUFF
      if ($position < 0 || $position > $size) {
        $break = true;
        break;
      }

      //READ A CHUNK
      $chunk = fread($fh, $chunkSize);
      if ($whence) {
        $buffer = $chunk . $buffer;
      } else {
        $buffer .= $chunk;
      }

      //PARESE CHUNK INTO LINES
      $bufferLines = preg_split('/\r\n?|\n/', $buffer);

      //PUT THE LAST (PROBABLY INCOMPLETE) ONE BACK
      if ($whence) {
        $buffer = array_shift($bufferLines);
      } else {
        $buffer = array_pop($bufferLines);
      }

      //ADD TO EXISTING LINES
      if ($whence) {
        $lines = array_merge($bufferLines, $lines);
      } else {
        $lines = array_merge($lines, $bufferLines);
      }

      //ARE WE DONE?
      if (count($lines) >= $length) {
        $break = true;
      }
    } while (!$break);

    $endOffset = $position;

    //ADD REMAINING LENGTH IN BUFFER
    $endOffset += strlen($buffer);

    return trim(join(PHP_EOL, $lines), "\n\r");
  }
}
?>