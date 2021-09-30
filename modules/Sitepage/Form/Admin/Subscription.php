<?php 

class Sitepage_Form_Admin_Subscription extends Engine_Form
{
  protected $_isSignup = true;
  
  protected $_packages;
  
  public function setIsSignup($flag)
  {
    $this->_isSignup = (bool) $flag;
  }

  public function getUserProfileMap($user_id = null)
  {
    if ($user_id == null) 
      return;

    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('user');
    if( count($topStructure) ==  1 && $topStructure[0]->getChild()->type ==  'profile_type' ) {
      $profileTypeField = $topStructure[0]->getChild();
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $map = $db->select()
              ->from('engine4_user_fields_values')
              ->where('field_id = ?',$profileTypeField->field_id)
              ->where('item_id = ?',$user_id)
              ->query()
              ->fetchAll(); 

    if (empty($map)) 
      return '0';

    else
      return (int) $map[0]['value'];
  }
  
  public function init()
  {
    $this
      ->setTitle('')
      ->setDescription('')
      ;

    // Get available subscriptions
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'sitepage');
    $tableName  = $packagesTable->info('name');
    
    // Get the package order 
    $packageOrder = array();
    $packageOrderTable = Engine_Api::_()->getDbtable('packageorder','sitepage');
    $packageOrderTableName = $packageOrderTable->info('name');
    $packageOrder = $packageOrderTable->select()->query()->fetchAll();

    $db = Zend_Db_Table::getDefaultAdapter();
    $select = $db->select();
    $select->from($tableName);

    if (!empty($packageOrder)) {
      $select->joinLeft($packageOrderTableName,"`$tableName`.package_id = `$packageOrderTableName`.package_id",array("$packageOrderTableName.order" ));
      $select->order("$packageOrderTableName.order ASC");
    }
      $select->where('enabled = ?', true);

    $dummySelect = $select->query()->fetchAll();

    $this->_packages = array(); 

    foreach ($dummySelect as $dummyPackage) {
      $this->_packages[$dummyPackage['package_id']] = $packagesTable->find($dummyPackage['package_id'])->current();
    }
    
    // Remove the package from package list for upgrading packages
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $action = $request->getActionName();
    $controller = $request->getControllerName();
    $profileMapAllowed = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.mapping', '0');

    $this->_packages = (object) $this->_packages;

    // Insert Form elements
    foreach( $this->_packages as $package ) {
      $multiOptions[$package->package_id] = $package->title
        . ' (' . $package->getPackageDescription() . ')'
        ;
    }
    // Element: package_id
    if( count($multiOptions) >= 1 ) {
      $this->addElement('Select', 'package_id', array(
        'label' => 'Choose Plan:',
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $multiOptions,
      ));
    }
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
  
  public function getPackages()
  {
    return $this->_packages;
  }
  
  public function setPackages($packages)
  {
    $this->_packages = $packages;
  }
}