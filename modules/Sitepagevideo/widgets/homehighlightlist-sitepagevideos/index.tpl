<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<ul class="sitepage_sidebar_list">
  <?php foreach ($this->paginator as $sitepagevideo): ?>      
      <?php $this->sitepage_subject = Engine_Api::_()->getItem('sitepage_page', $sitepagevideo->page_id);?>
      <?php  $this->partial()->setObjectKey('sitepagevideo');
        echo $this->partial('application/modules/Sitepagevideo/views/scripts/partialWidget.tpl', $sitepagevideo);
		  ?>
          <?php
	          $truncation_limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.title.truncation', 18);
	          $tmpBody = strip_tags($sitepagevideo->page_title);
	          $page_title = ( Engine_String::strlen($tmpBody) > $truncation_limit ? Engine_String::substr($tmpBody, 0, $truncation_limit) . '..' : $tmpBody );
          ?>
          <?php echo $this->translate("in ") . $this->htmlLink(Engine_Api::_()->sitepage()->getHref($sitepagevideo->page_id, $sitepagevideo->owner_id, $sitepagevideo->getSlug()), $page_title, array('title' => $sitepagevideo->page_title)) ?> 
        </div>
        <div class="sitepage_sidebar_list_details">  
        	<?php echo $this->translate(array('%s comment', '%s comments', $sitepagevideo->comment_count), $this->locale()->toNumber($sitepagevideo->comment_count)) ?>,
					<?php echo $this->translate(array('%s view', '%s views', $sitepagevideo->view_count), $this->locale()->toNumber($sitepagevideo->view_count)) ?>
				</div>	
      </div>
    </li>
  <?php endforeach; ?>
  <li class="sitepage_sidebar_list_seeall">
		<a href='<?php echo $this->url(array('highlightedvideo'=> 1), 'sitepagevideo_browse', true) ?>'><?php echo $this->translate('See All');?> &raquo;</a>
	</li>
</ul>