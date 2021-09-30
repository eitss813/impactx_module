<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');
?>
<div class="sitealbum_quick_specs sitealbum_side_widget">
	<?php echo $this->FieldValueLoopQuickInfoSitealbum($this->sitealbum, $this->fieldStructure) ?>
</div>


