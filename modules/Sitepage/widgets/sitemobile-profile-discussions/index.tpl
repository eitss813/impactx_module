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
<?php if ($this->paginators->getTotalItemCount() > 0): ?>
<?php if ($this->canPost): ?>
 	<div class="seaocore_add" data-role="controlgroup" data-type="horizontal">
    <?php
    echo $this->htmlLink(array(
        'route' => 'sitepage_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->sitepage->getGuid(),
        'page_id' => $this->page_id,
        'resource_type' => $this->subject->getType(),
        'resource_id' => $this->subject->getIdentity(),
            ), $this->translate('Post New Topic'),
            array(
					'class' => 'buttonlink icon_sitepage_post_new','data-role' => "button", 'data-icon' => "plus", "data-iconpos" => "left", "data-inset" => 'false', 'data-mini' => "true", 'data-corners' => "true", 'data-shadow' => "true"))
    ?>
  </div>
<?php endif; ?>
<div class="sm-content-list" id="profile_sitepagediscussions">
		<ul data-role="listview" data-inset="false" data-icon="false">
		  <?php foreach ($this->paginators as $topic):
          $lastpost = $topic->getLastPost();
          $lastposter = $topic->getLastPoster();
      ?>
			<li data-icon="arrow-r">
				<a href="<?php echo $topic->getHref(); ?>">
					<h3><?php echo $topic->getTitle() ?></h3>
					<p class="ui-li-aside"><strong> <?php echo $this->translate(array('%s reply', '%s replies', $topic->post_count - 1), $this->locale()->toNumber($topic->post_count - 1)) ?></strong></p>
					<p><?php echo $this->translate('Last Post') ?> <?php echo $this->translate('by'); ?> <strong><?php echo $lastposter->getTitle() ?></strong></p>
				</a>
			</li>
      <?php endforeach;?>
		</ul>

		<?php if ($this->paginators->count() > 1): ?>
			<?php
			echo $this->paginationAjaxControl(
							$this->paginators, $this->identity, "profile_sitepagediscussions");
			?>
		<?php endif; ?>
   </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No discussion topics have been posted in this Page yet.'); ?>
      <?php
      if ($this->canPost):
        $show_link = $this->htmlLink(
            array(
        'route' => 'sitepage_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->sitepage->getGuid(),
        'page_id' => $this->page_id,
        'tab' => $this->identity,
        'resource_type' => $this->subject->getType(),
        'resource_id' => $this->subject->getIdentity(),
                ), $this->translate('here'));
        $show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to start a discussion.');
        $show_label = sprintf($show_label, $show_link);
        echo $show_label;
      endif;
      ?>
    </span>
  </div>
<?php endif; ?>


