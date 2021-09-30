<?php

class Sitepage_Model_DbTable_Layouts extends Engine_Db_Table
{
	protected $_rowClass = 'Sitepage_Model_Layout';

	public function getLayoutStructureType($layout_id) {
		$row = $this->select()->where('`layout_id` = ?',$layout_id)->query()->fetch();
		return $row['structure_type'];
	}

	public function getLayouts()
	{
		$rows = $this->select()
					->query()
					->fetchAll();

		return $rows;
	}

	public function getLayout($id)
	{
		$rows = $this->select()->where('`layout_id` = ?',$id)->query()->fetch();
		return $rows;
	}

	public function createLayout($params = null)
	{
		if (empty($params)) 
			return null;

		$layouts = $this->createRow();

		$db = $this->getAdapter();
        $db->beginTransaction();

        try {
            $layout = $this->createRow();
            $layout->setFromArray($params);
            $layout->save();
            $db->commit();
        } catch( Exception $e ) {
          $db->rollBack();
          throw $e;
        }

        return $layout->getIdentity();
	}
        
        public function changeLable($labelfield,$relatedvalue,$structuretype)
	{
            $packageLabelArray = array('Featured Package' => array('0'=>'Is not Featured','1'=>'Is Featured'), 'Sponsored Package' => array('0'=>'Is not Sponsored','1'=>'Is Sponsored'), 'Tell a friend' => array('0'=>'Not Provided','1'=>'Tell a Friend Feature Available'), 'Print Page Information' => array('0'=>'Nothing','1'=>'Can Print Page Information'), 'Rich Overview' => array('0'=>'Rich Overview Unavailable','1'=>'Rich Overview Available'), 'Map' => array('0'=>'Not Available','1'=>'Location Map Feature Available'), 
                'Insights' => array('0'=>'Page Insights Unavailable','1'=>'Page Insights Available'), 'Contact Details' => array('0'=>'Add contact details feature unavailable','1'=>'Can add contact details to page'), 'Send Updates to Page Users' => array('0'=>'Send updates feature unavailable','1'=>'Can send updates to page users'));
            if($structuretype == 'type1') {
                if( ($labelfield != 'Billing Cycle' && $labelfield != 'Package Expires In' && $labelfield != 'Description') && ($relatedvalue == 1)) {
                   return $newlable = 'yes';

                } 
                elseif  ( ($labelfield != 'Billing Cycle' && $labelfield != 'Package Expires In' && $labelfield != 'Description') && ($relatedvalue == 0) ) {
                   return  $newlable = 'no';

                }
                else {
                    return  $newlable = $relatedvalue;
                }
            }
            else {
                if($labelfield == 'Description') {
                  return $newlable = $relatedvalue;
                }
                else {
                     //$PackageExpiresIn = array('Forever'=>'Package Never Expires');       
                     if($labelfield == 'Package Expires In') {
                       if($relatedvalue == 'Forever')   {
                           return $newlable = 'Package Never Expires';
                       }
                       else {
                           return $newlable = 'Package Expires In '.$relatedvalue;
                       }
                         
                     }
                     elseif($labelfield == 'Billing Cycle') {
                        if($relatedvalue == 'One-time')   {
                            return $newlable = $relatedvalue.' Charges';
                        }
                        elseif($relatedvalue == 'N/A')   {
                            return $newlable = 'Charges '.$relatedvalue;
                        }
                        else {
                            return $newlable = 'Charges every '.$relatedvalue;
                        } 
                     }
                         
                    foreach ($packageLabelArray as $key => $field) {
                        if($labelfield == $key) {
                            //echo "Detail=>".$key;
                        foreach ($field as $key1 => $field1) {
                            if($relatedvalue == $key1){
                                $newlable = $field1;
                                break;
                            }
                            else {
                                continue;
                            }
//                            echo "Key=>".$key1;
//                            echo "Value=>".$field1."<br>";
                            
                        }                        
                      }
                      continue;
                    }
                    
                    return $newlable;
                }
            }
	}
}