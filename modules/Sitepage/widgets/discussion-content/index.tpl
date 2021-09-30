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
<?php 
  include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagediscussion/externals/styles/style_sitepagediscussion.css')
?>
<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitepage->__toString() ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Discussions')) ?>
	  <?php echo $this->translate('&raquo; ');?>
	  <?php echo $this->topic->getTitle() ?>
	</h2>
   <?php if(($resource=$this->topic->getResource())!=null):?>
      <span>
     <?php echo $this->translate("In ".$resource->getMediaType().":") ?>
     <?php echo $this->htmlLink($resource->getHref(), $resource->getTitle()) ?>
      </span>
  <?php endif;?>
</div>

<!--FACEBOOK LIKE BUTTON START HERE-->
 <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
        if (!empty ($fbmodule)) :
          $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
          if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
            $fbversion = $fbmodule->version; 
            if (!empty($fbversion) && (!empty(Engine_Api::_()->seaocore()->checkVersion($fbversion, '4.1.5')))) { ?>
               <div class="mbot10">
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>

            <?php } ?>
          <?php endif; ?>
   <?php endif; ?>

<div class="sitepage_discussion_view">

	<?php $this->placeholder('sitepagetopicnavi')->captureStart(); ?>
	<div class="sitepage_sitepages_thread_options">
	  <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'topic', 'action' => 'index', 'page_id' => $this->sitepage->getIdentity(), 'tab' => $this->tab_selected_id), $this->translate('Back to Topics'), array(
	    'class' => 'buttonlink icon_back'
	  )) ?>
	  <?php if( ($this->canPost)  && (!$this->topic->closed)): ?>
	    <?php echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
	      'class' => 'buttonlink icon_sitepage_post_reply'
	    )) ?>
	  <?php endif; ?>
	  <?php if( $this->viewer->getIdentity() ): ?>
	    <?php if( !$this->isWatching ): ?>
	      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
	        'class' => 'buttonlink icon_sitepage_topic_watch'
	      )) ?>
	    <?php else: ?>
	      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
	        'class' => 'buttonlink icon_sitepage_topic_unwatch'
	      )) ?>
	    <?php endif; ?>
	  <?php endif; ?>
	  <?php if( $this->canEdit || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
	    <?php if( !$this->topic->sticky ): ?>
	      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
	        'class' => 'buttonlink icon_sitepage_post_stick'
	      )) ?>
	    <?php else: ?>
	      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
	        'class' => 'buttonlink icon_sitepage_post_unstick'
	      )) ?>
    <?php endif; ?>
  	<?php if( !$this->topic->closed ): ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
        'class' => 'buttonlink icon_sitepage_post_close'
      )) ?>
 	  <?php else: ?>
      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
        'class' => 'buttonlink icon_sitepage_post_open'
      )) ?>
    <?php endif; ?>
    <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
      'class' => 'buttonlink smoothbox icon_sitepage_post_rename'
    )) ?>
    <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
      'class' => 'buttonlink smoothbox icon_sitepage_post_delete'
    )) ?>
	<?php elseif( $this->canEdit == 0): ?>
    <?php if( $this->topic->closed ): ?>
      <div class="sitepage_sitepages_thread_options_closed">
        <?php echo $this->translate('This topic has been closed.');?>
      </div>
    <?php endif; ?>
	<?php endif; ?>
	</div>
	<?php $this->placeholder('sitepagetopicnavi')->captureEnd(); ?>
	<?php echo $this->placeholder('sitepagetopicnavi') ?>
	<?php echo $this->paginationControl(null, null, null, array(
	  'params' => array(
	    'post_id' => null // Remove post id
	  )
	)) ?>

<script type="text/javascript">
	var quotePost = function(user, href, body) {
		if( $type(body) == 'element' ) {
			body = $(body).getParent('li').getElement('.sitepage_sitepages_thread_body_raw').get('html').trim();
		}
		var tinyMCEEditor = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tinymceditor', 1);?>';
		if(tinyMCEEditor == 1) {
			tinyMCE.activeEditor.setContent('[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n'); 
		} else {
				$('body').value = '[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n';
		}
		$("body").focus();
		$("body").scrollTo(0, $("body").getScrollSize().y);
	}
</script>

	<ul class='sitepage_sitepages_thread'>
	  <?php foreach( $this->paginator as $post ): ?>
	  <li>
	    <div class="sitepage_sitepages_thread_photo">
	      <?php
	        $user = $this->item('user', $post->user_id);
	        echo $this->htmlLink($user->getHref(), $user->getTitle());
	        echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));
	      ?>
	    </div>
	    <div class="sitepage_sitepages_thread_info">
	      <div class="sitepage_sitepages_thread_details">
	        <div class="sitepage_sitepages_thread_details_options">
	          <?php if( $this->form ): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'sitepage_extended',
                'action' => 'post',
                'controller' => 'topic',
                'topic_id'=>$this->topic->getIdentity(),
                'quote_id'=>$post->getIdentity(),
              ), $this->translate('Quote'), array(
                'class' => 'buttonlink icon_sitepage_post_quote',
              )) ?>
	          <?php endif; ?>

	          <?php if( $post->user_id == $this->viewer()->getIdentity() || $this->canEdit == 1 || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
	            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox', 'page_id' => $this->sitepage->getIdentity()), $this->translate('Edit'), array(
	              'class' => 'buttonlink smoothbox icon_sitepage_post_edit'
	            )) ?>
	            <?php echo $this->htmlLink(array('route' => 'sitepage_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox', 'page_id' => $this->sitepage->getIdentity()), $this->translate('Delete'), array(
	              'class' => 'buttonlink smoothbox icon_sitepage_post_delete'
	            )) ?>
	          <?php endif; ?>
	        </div>
	        <div class="sitepage_sitepages_thread_details_date">
	          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
	        </div>
	      </div>
	      <div class="sitepage_sitepages_thread_body">
          <?php
            $body = $post->body;
            $doNl2br = false;
            if( strip_tags($body) == $body ) {
              $body = nl2br($body);
            }
            if( !$this->decode_html && $this->decode_bbcode ) {
              $body = $this->BBCode($body, array('link_no_preparse' => true));
            }
            echo $body;
          ?>
	      </div>
	      <span class="sitepage_sitepages_thread_body_raw" style="display: none;">
          <?php
            $body = $post->body;
            $doNl2br = false;
            if( strip_tags($body) == $body ) {
              $body = nl2br($body);
            }
            if( !$this->decode_html && $this->decode_bbcode ) {
              $body = $this->BBCode($body, array('link_no_preparse' => true));
            }
            echo $body;
          ?>
	      </span>
	    </div>
	  </li>
	  <?php endforeach; ?>
	</ul>
	<?php if($this->paginator->getCurrentItemCount() > 4): ?>
	  <?php echo $this->paginationControl(null, null, null, array(
	    'params' => array(
	      'post_id' => null // Remove post id
	    )
	  )) ?>
	  <br />
	  <?php echo $this->placeholder('sitepagetopicnavi') ?>
	<?php endif; ?>
	
	<br />
	<?php if( $this->form ): ?>
	  <a name="reply"> </a>
	  <?php echo $this->form->setAttrib('id', 'sitepage_topic_reply')->render($this) ?>
	<?php endif; ?>
</div>