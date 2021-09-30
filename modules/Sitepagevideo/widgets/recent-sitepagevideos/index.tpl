<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<ul class="sitepage_sidebar_list">
	<?php foreach ($this->paginator as $sitepagevideo): ?>
    <?php  $this->partial()->setObjectKey('sitepagevideo');
        echo $this->partial('application/modules/Sitepagevideo/views/scripts/partialWidget.tpl', $sitepagevideo);
		?>		       
            <?php echo $this->translate(array('%s view', '%s views', $sitepagevideo->view_count), $this->locale()->toNumber($sitepagevideo->view_count)) ?>
    					|
            <?php echo $this->translate(array('%s comment', '%s comments', $sitepagevideo->comment_count), $this->locale()->toNumber($sitepagevideo->comment_count)) ?>	
          </div>
        </div>
      </li>
    <?php endforeach; ?>
</ul>	