<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelogin
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Core.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteloginconnect_Api_Core extends Core_Api_Abstract {

	public function getProfileFieldMaps() {

		$metaUserTable = Engine_Api::_()->fields()->getTable('user','meta');
		$optionUserTable = Engine_Api::_()->fields()->getTable('user','options');
		$mapUserTable = Engine_Api::_()->fields()->getTable('user','maps');

		$db = Engine_Db_Table::getDefaultAdapter();

		$selectprofileTypeMeta = $db->select()
									->from( $metaUserTable->info('name'), array('field_id'))
									->where( "type = ?", "profile_type" );
		
		$profileTypeMetaId_sql = $selectprofileTypeMeta->__toString();
		
		$selectProfileTypes = $db->select()->from( $optionUserTable->info('name'), array("label", "option_id") )->where("field_id = ({$profileTypeMetaId_sql})");

		$profileTypes = $db->fetchAll($selectProfileTypes);

		$profileTypesIds = array_column( $profileTypes, "option_id");
		$allFieldMaps = [];

		foreach ( $profileTypesIds as $profileTypeId ) {

			$fieldsSelect = $db->select()
							   ->from( $mapUserTable->info("name"), array( "profile_type"=>"option_id", "field_id"=>"child_id" ))
							   ->join( $metaUserTable->info("name"), "`{$metaUserTable->info("name")}`.`field_id` = `{$mapUserTable->info("name")}`.`child_id`", array("label"))
							   ->where("`{$mapUserTable->info("name")}`.`field_id` = ({$profileTypeMetaId_sql})")
							   ->where("option_id = ? ", $profileTypeId)
							   ->where("`{$metaUserTable->info("name")}`.`type` <> ?", "heading")
							   ->where("option_id NOT IN (SELECT field_id FROM {$metaUserTable->info('name')} WHERE type = 'heading' )");
			
			$fieldData = $db->fetchAll( $fieldsSelect );
			$fieldsMap[ $profileTypeId ] = $fieldData;
			foreach( $fieldData as $data ) {
				$childSelect = $db->select()
							   ->from( $mapUserTable->info("name"), array('field_id','child_id','option_id','order'))
							   ->join( $metaUserTable->info("name"), "`{$metaUserTable->info("name")}`.`field_id` = `{$mapUserTable->info("name")}`.`child_id`", array("label"))
							   ->join( $optionUserTable->info("name"), "`{$optionUserTable->info("name")}`.`option_id` = `{$mapUserTable->info("name")}`.`option_id`", array("label as option_label","order as option_order"))
							   ->where("`{$mapUserTable->info("name")}`.`field_id` = ({$data["field_id"]})")
							   ->where("`{$metaUserTable->info("name")}`.`type` <> ?", "heading")
							   ->order("option_order")
							   ->order("{$mapUserTable->info("name")}.order");
							   
				$childDataresult=$db->fetchAll( $childSelect );
				if($childDataresult){
					foreach ($childDataresult as $key => $value) {
						$option_array[$value['option_id']][$value['child_id']]['label']=$value['label'];
						$option_array[$value['option_id']]['option_label']=$value['option_label'];
					}
					$childData[$data["field_id"]]=$option_array;	
				}
				$allFieldMaps[ $data["field_id"] ] = $data;
			}
		}

		return array(	
					"profile_types" => $profileTypes,
					"fieldsMap" => $fieldsMap,
					"allFieldMaps"=> $allFieldMaps,
					"childData"=>$childData,
				);
	}

	public function getSocialSiteFields() {
		return array(
				"linkedin" => array(
					"" => "Select",
					"emailAddress"=>"emailAddress",
					"firstName"=>"firstName",
					"lastName" =>"lastName",
					"location_country_code" => "country code",
					"location_country_name" => "country name",
					"industry" => "industry",
					"headline" => "headline",
				),
				"twitter" => array(
					""=>"Select",
					"name"=>"name",
					"screen_name"=>"screen_name",
					"location"=>"location",
					"description" => "description",
					"url"=>"url",
					"lang" => "lang",
				),
				"facebook" => array(
					""=>"Select",
					"name" => "name",
					"gender" => "gender",
					"locale" => "locale",
					"age_range" => "age_range",
					"about" => "about",
					"website" => "website",
					"email" => "email",
					"birthday" => "birthday",
					"education" => "education",
					"hometown" => "hometown",
					"location" => "location",
					"religion" => "religion",
					"political" => "politial",
					"address" => "address",
					"first_name" => "first_name",
					"interested_in" => "interested_in",
					"relationship_status" => "relationship_status",
					"hometown" => "hometown",
					"likes" => "likes",
					"tagged_places" => "tagged_places",
					"friends" => "friends"
				),
				"instagram" => array(
					""=>"Select",
					"username" => "username",
					"full_name" => "full_name",
					"bio" => "bio",
					"website" => "website",
				)
		);
	}

	public function getRedirectUrl($site) {
		$baseParentUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $domainUrl = (_ENGINE_SSL ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'];
        if (isset($baseParentUrl) && !empty($baseParentUrl)) {
            $domainUrl = $domainUrl . $baseParentUrl;
        }
        if($site=='facebook')
       		$href = $domainUrl . "/user/auth/facebook";
       	else
       		$href = $domainUrl . "/siteloginconnect/link/".$site;

        return $href;
        
	}
	
	public function getfacebookButton($connect_text="Integrate with my Facebook") {
		$href = $this->getRedirectUrl('facebook');
	    return '<div style="max-width:120px;" class="social-btn btn-facebook social-btn-lg social-btn-square facebook-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Facebook">
            <i class="fa fa-facebook icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function gettwitterButton($connect_text="Integrate with my Twitter") {
		$href = $this->getRedirectUrl('twitter');
	    return '<div style="max-width:120px;" class="social-btn btn-twitter social-btn-lg social-btn-square twitter-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Twitter">
            <i class="fa fa-twitter icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function getlinkedinButton($connect_text="Integrate with my LinkedIn") {
		$href = $this->getRedirectUrl('linkedin');
	    return '<div style="max-width:120px;" class="social-btn btn-linkedin social-btn-lg social-btn-square linkedin-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="LinkedIn">
            <i class="fa fa-linkedin icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>';    
	}

	public function getinstagramButton($connect_text="Integrate with my Instagram") {
		$href = $this->getRedirectUrl('instagram');
	    return '<div style="max-width:120px;" class="social-btn btn-instagram social-btn-lg social-btn-square instagram-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Instagram">
            <i class="fa fa-instagram icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function getgoogleButton($connect_text="Integrate with my Google") {
		$href = $this->getRedirectUrl('google');
	    return '<div style="max-width:120px;" class="social-btn btn-google social-btn-lg social-btn-square google-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Google">
            <i class="fa fa-google icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function getflickrButton($connect_text="Integrate with my Flickr") {
		$href = $this->getRedirectUrl('flickr');
	    return '<div style="max-width:120px;" class="social-btn btn-flickr social-btn-lg social-btn-square flickr-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Flickr">
            <i class="fa fa-flickr icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function getoutlookButton($connect_text="Integrate with my Outlook") {
		$href = $this->getRedirectUrl('outlook');
	    return '<div style="max-width:120px;" class="social-btn btn-hotmail social-btn-lg social-btn-square hotmail-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Outlook">
            <i class="fa fa-hotmail icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}	

	public function getpinterestButton($connect_text="Integrate with my Pinterest") {
		$href = $this->getRedirectUrl('pinterest');
	    return '<div style="max-width:120px;" class="social-btn btn-pinterest social-btn-lg social-btn-square pinterest-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Pinterest">
            <i class="fa fa-pinterest icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}

	public function getyahooButton($connect_text="Integrate with my Yahoo") {
		$href = $this->getRedirectUrl('yahoo');
	    return '<div style="max-width:120px;" class="social-btn btn-yahoo social-btn-lg social-btn-square yahoo-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Yahoo">
            <i class="fa fa-yahoo icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}	

	public function getvkButton($connect_text="Integrate with my Vkontakte") {
		$href = $this->getRedirectUrl('vk');
	    return '<div style="max-width:120px;" class="social-btn btn-vk social-btn-lg social-btn-square vk-seboxshadow seboxshadow">
        <a href="'.$href.'" aria-label="Vkontakte">
            <i class="fa fa-vk icon-with-bg" aria-hidden="true" title="'.$connect_text.'"></i>
            <span>'.$connect_text.'</span>
        </a>
    	</div>'; 
	}	

	public function _getUserLogin($id = 0, $type = NULL) {
        if (!empty($type) && !empty($id)) {
            $column_name = $type . '_id';
            $siteTable = Engine_Api::_()->getDbtable($type, 'sitelogin');
            $user_id = $siteTable->select()
                    ->from($siteTable, 'user_id')
                    ->where("$column_name = ?", $id)
                    ->query()
                    ->fetchColumn();
            return $user_id;
        }
        return false;
    }

}