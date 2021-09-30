<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php  
$url = $this->url(array('module'=>'sitemember','controller'=>'compliment','action' => 'create','subject_id'=>$this->subject->getIdentity(),'subject_type'=>$this->subject->getType()), 'default', true);

?>
<a href="<?php echo $url; ?>" class="sitemember_compliment_me seao_smoothbox"><?php echo $this->translate((!empty($this->compliment_button_title)) ? $this->compliment_button_title : "Compliment Me !") ?> 
        </a>
   