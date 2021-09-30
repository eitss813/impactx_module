<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    Sitepage
* @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $manageAdminEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.manageadmin', 1);?>
<?php $pageilike = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.mylike.show', 1);?>
<?php $claimEnabled = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.claimlink', 1);?>
<div class="quicklinks sitepage_manage_admin_link">
    <ul>
        <?php if($this->showPageAdmin && !empty($manageAdminEnabled) && !empty($this->manageadmin_count)): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-pages'), 'sitepage_manageadmins', true) ?>' class="icon_sitepages_page-owner buttonlink"> <?php echo $this->translate("Pages I Admin") ?></a>
        </li>
        <?php endif; ?>
        <?php if($this->showPageClaimed && !empty($claimEnabled) && !empty($this->showClaimLink)): ?>			
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-pages'), 'sitepage_claimpages', true) ?>' class="icon_sitepages_claim buttonlink"> <?php echo $this->translate("Pages I've Claimed") ?></a>
        </li>				
        <?php endif; ?>
        <?php if($this->showPageLiked && !empty($pageilike)): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'mylikes'), 'sitepage_like', true) ?>' class="icon_sitepages_like buttonlink"> <?php echo $this->translate("Pages I Like") ?></a>
        </li>
        <?php endif; ?>
        <?php if($this->showPageJoined && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember')): ?>
        <li>
            <a href='<?php echo $this->url(array('action' => 'my-joined'), 'sitepage_like', true) ?>' class="icon_sitepage_join buttonlink"> <?php echo $this->translate("Pages I've Joined") ?></a>
        </li>
        <?php endif; ?>
    </ul>		
</div>	