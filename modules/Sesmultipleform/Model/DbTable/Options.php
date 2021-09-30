<?php
/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmultipleform
 * @package    Sesmultipleform
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Options.php 2015-12-31 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmultipleform_Model_DbTable_Options extends Engine_Db_Table {

  protected $_rowClass = 'Sesmultipleform_Model_Option';
  protected $_name = 'sesmultipleform_entry_fields_options';

  public function getOptionsLabel($option_id) {
    return $this->select()
                    ->from($this->info('name'), array('label'))
                    ->where('option_id = ?', $option_id)
                    ->query()
                    ->fetchColumn();
  }

}