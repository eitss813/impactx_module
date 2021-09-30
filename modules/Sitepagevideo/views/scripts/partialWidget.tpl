<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: partialWidget.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<li> 
	<?php
	echo $this->htmlLink(
					$this->sitepagevideo->getHref(), $this->itemPhoto($this->sitepagevideo, 'thumb.icon', $this->sitepagevideo->getTitle()), array('class' => 'list_thumb', 'title' => $this->sitepagevideo->getTitle())
	)
	?>
	<div class='sitepage_sidebar_list_info'>
		<div class='sitepage_sidebar_list_title'>
			<?php echo $this->htmlLink($this->sitepagevideo->getHref(), Engine_Api::_()->sitepagevideo()->truncation($this->sitepagevideo->getTitle()), array('title' => $this->sitepagevideo->getTitle(),'class'=>'sitepagevideo_title')); ?> 	
		</div>
		<div class='sitepage_sidebar_list_details'>