<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2020 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _activityText.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<?php if( empty($this->actions) ) {
echo $this->translate("The action you are looking for does not exist.");
return;
} else {
$actions = $this->actions;
}
$composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.composer.options');
$attachUserTags = in_array("userTags", $composerOptions);
$hashtagEnabled = in_array("hashtags", $composerOptions);
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer.js');

if ($attachUserTags) {
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer_tag.js');
} ?>

<?php $this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js') ?>

<script type="text/javascript">
  var CommentLikesTooltips;
  var commentComposer = new Hash();
  en4.core.runonce.add(function() {
    // Add hover event to get likes
    $$('.comments_comment_likes').addEvent('mouseover', function(event) {
      var el = $(event.target);
      if( !el.retrieve('tip-loaded', false) ) {
        el.store('tip-loaded', true);
        el.store('tip:title', '<?php echo  $this->string()->escapeJavascript($this->translate('Loading...')) ?>');
        el.store('tip:text', '');
        var id = el.get('id').match(/\d+/)[0];
        // Load the likes
        var url = '<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'get-likes'), 'default', true) ?>';
        var req = new Request.JSON({
          url : url,
          data : {
            format : 'json',
            //type : 'core_comment',
            action_id : el.getParent('li').getParent('li').getParent('li').get('id').match(/\d+/)[0],
            comment_id : id
          },
          onComplete : function(responseJSON) {
            el.store('tip:title', responseJSON.body);
            el.store('tip:text', '');
            CommentLikesTooltips.elementEnter(event, el); // Force it to update the text
          }
        });
        req.send();
      }
    });
    // Add tooltips
    CommentLikesTooltips = new Tips($$('.comments_comment_likes'), {
      fixed : true,
      className : 'comments_comment_likes_tips',
      offset : {
        'x' : 48,
        'y' : 16
      }
    });
    // Enable links in comments
    $$('.comments_body').enableLinks();
  });
</script>

<?php if( !$this->getUpdate ): ?>
<ul class='feed' id="activity-feed">
  <?php endif ?>

  <?php
  foreach( $actions as $action ): // (goes to the end of the file)
    try { // prevents a bad feed item from destroying the entire page
      // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
      if( !$action->getTypeInfo()->enabled ) continue;
  if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
  if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;

  ob_start();
  ?>
  <?php if( !$this->noList ): ?><li id="activity-item-<?php echo $action->action_id ?>" class="activity-item"  data-activity-feed-item="<?php echo $action->action_id ?>"><?php endif; ?>
    <?php $this->commentForm->setActionIdentity($action->action_id) ?>
    <!--




        <?php // User's profile photo ?>
        <div class='feed_item_photo'><?php echo $this->htmlLink($action->getSubject()->getHref(),
          $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())
        ) ?></div>


        <div class='feed_item_body'>

          <?php // Main Content ?>
          <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
            <?php echo $action->getContent() ?>
          </span>

          <?php echo $this->editActivity($action);?>
          <?php // Attachments ?>
          <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
            <div class='feed_item_attachments'>
              <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
                <?php if( count($action->getAttachments()) == 1 &&
                        null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
                  <?php echo $richContent; ?>
                <?php else: ?>
                  <?php foreach( $action->getAttachments() as $attachment ): ?>
                    <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                    <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                    <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                      <div>
                        <?php
                          if ($attachment->item->getType() == "core_link")
                          {
                            $attribs = Array('target'=>'_blank');
                          }
                          else
                          {
                            $attribs = Array();
                          }
                        ?>
                        <?php if( $attachment->item->getPhotoUrl() ): ?>
                          <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                        <?php endif; ?>
                        <div>
                          <div class='feed_item_link_title'>
                            <?php
                              echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                            ?>
                          </div>
                          <div class='feed_item_link_desc'>
                            <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                          </div>
                        </div>
                      </div>
                    <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                      <div class="feed_attachment_photo">
                        <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
                      </div>
                    <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                      <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                    <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                    <?php endif; ?>
                    </span>
                  <?php endforeach; ?>
                  <?php endif; ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>
         -->

    <?php // Icon, time since, action links ?>
    <?php
        $icon_type = 'activity_icon_'.$action->type;
    list($attachment) = $action->getAttachments();
    if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
    $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
    endif;
    $canComment = ( $action->getTypeInfo()->commentable &&
    $this->viewer()->getIdentity() &&
    Engine_Api::_()->authorization()->isAllowed($action->getCommentableItem(), null, 'comment') &&
    !empty($this->commentForm) );
    ?>
    <?php  $comments = $action->getComments($this->viewAllComments); ?>
    <?php  $likes = count($action->likes()->getAllLikesUsers()); ?>
    <?php /*
            <?php if( $likes == 0 ): ?>
    <p class="zero_like"><?php  echo $likes.' likes';?></p>
    <?php endif; ?>
    */ ?>
    <div class="tooltip" style="margin-bottom: 5px;">
      <?php if( $likes != 0 ): ?>
      <p class="hover_like"  onclick="openShare('<?php echo $this->url(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'likemodal',
                            'action_id' => $action->action_id,'format' => 'smoothbox',
                   ), 'default', true) ?>')"><?php  echo ($likes == 1) ? $likes.' like(s)' : $likes.' like(s)';?></p>
      <?php endif; ?>
      <!--  <span class="tooltiptext">
         <?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>

            <?php
          $USERS = $action->likes()->getAllLikesUsers();
          foreach($USERS as $vals){ ?>
                  <a><?php echo $vals->displayname; ?></a><br>
            <?php }
        ?>
            <?php else: ?>
            <p class="no_users"><?php echo $this->translate('No Users'); ?></p>
            <?php endif; ?>
      </span> -->
    </div>
    <div class='feed_item_date'>
      <div class="icon_buttons">
        <ul class="feed_item_ul" style="display: flex;">

          <?php /*
                 <?php echo $this->lastEditedActivity($action) ?>
          */?>
          <?php if( $canComment ): ?>
          <?php if( $action->likes()->isLike($this->viewer()) ): ?>
          <li class="feed_item_option_unlike">
            <i class="far fa-thumbs-down"></i>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.$action->action_id.');','class' => ' button_section')) ?>
          </li>
          <?php else: ?>
          <li class="feed_item_option_like">
            <i class="far fa-thumbs-up"></i>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('class' => 'button_section','onclick'=>'javascript:en4.activity.like('.$action->action_id.');')) ?>
          </li>
          <?php endif; ?>
          <?php if( Engine_Api::_()->getApi('settings', 'core')->core_spam_comment ): // Comments - likes ?>
          <li class="feed_item_option_comment">
            <i class="far fa-comment"></i>
            <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
            'class'=>'smoothbox','class' => ' button_section'
            )) ?>
          </li>
          <?php else: ?>
          <li class="feed_item_option_comment">
            <i class="far fa-comment" onclick="customShowComments('comment_section-<?php echo $action->action_id ?>','<?php echo $action->action_id ?>')"></i>

            <?php         $comments = $action->getComments($this->viewAllComments); echo $this->htmlLink('javascript:void(0);', $this->translate('Comment ('.count($comments).')'), array('class' => 'button_section','onclick'=>'showCommentBody(' . $action->action_id . ')')) ?>

          </li>
          <?php endif; ?>
          <?php if( $this->viewAllComments ): ?>
          <script type="text/javascript">
            en4.core.runonce.add(function() {
              document.getElementById('<?php echo $this->commentForm->getAttrib('id') ?>').style.display = "";
              document.getElementById('<?php echo $this->commentForm->submit->getAttrib('id') ?>').style.display = "block";
              document.getElementById('<?php echo $this->commentForm->body->getAttrib('id') ?>').focus();
            });
          </script>
          <?php endif ?>
          <?php endif; ?>
          <?php if ($action->canEdit()): ?>
          <li class="feed_item_option_edit">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Edit'), array(

            'class' => 'seaocore_icon_edit  button_section'
            )); ?>
          </li>
          <?php endif; ?>
          <?php if( $this->viewer()->getIdentity() && (
          $this->activity_moderate || (
          $this->allow_delete && (
          ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
          ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
          )
          )
          ) ): ?>
          <li class="feed_item_option_delete">
            <i class="far fa-trash-alt"></i>
            <a class="button_section"
               onclick="openShare('<?php echo $this->url(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'delete',
                            'action_id' => $action->action_id,'format' => 'smoothbox'
                   ), 'default', true) ?>')"
            >
              Delete
            </a>


          </li>
          <?php endif; ?>
          <?php // Share ?>
          <?php if( $action->getTypeInfo()->shareable && $this->viewer()->getIdentity() ): ?>
          <?php if( $action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment()) ): ?>
          <li class="feed_item_option_share">
            <i class="fa fa-share-alt" ></i>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'class' => '  button_section','title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 2 ): ?>
          <li class="feed_item_option_share">
            <i class="fa fa-share-alt" ></i>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'class' => '  button_section','title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 3 ): ?>
          <li class="feed_item_option_share">
            <i class="fa fa-share-alt" ></i>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->object_type, 'id' => $action->object_id, 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'class' => '  button_section','title' => 'Share')) ?>
          </li>
          <?php elseif( $action->getTypeInfo()->shareable == 4 ): ?>
          <li class="feed_item_option_share">
            <i class="fa fa-share-alt" ></i>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'class' => '  button_section','title' => 'Share')) ?>
          </li>
          <?php endif; ?>
          <?php endif; ?>

        </ul>
        <div>
          <?php $comments = $action->getComments($this->viewAllComments); ?>
          <?php if(count($comments) > 0):?>

          <?php if($this->action_id == $action->action_id):?>
          <button style="display: none;border: unset !important;background-color: white !important;color: #0e0c0c !important;" id="custom_show_view_comments_btn-<?php echo $action->action_id ?>" onclick="customShowComments('comment_section-<?php echo $action->action_id ?>','<?php echo $action->action_id ?>')"  class="view_comments">
            <i class="fa fa-eye" aria-hidden="true"></i> View Comments  (<?php echo count($comments); ?>)
          </button>
          <button style="display: none;border: unset !important;background-color: white !important;color: #0e0c0c !important;" id="custom_hide_view_comments_btn-<?php echo $action->action_id ?>" onclick="customHideComments('comment_section-<?php echo $action->action_id ?>','<?php echo $action->action_id ?>')"  class="hide_comments">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> Hide Comments  (<?php echo count($comments); ?>)
          </button>
          <?php else: ?>
          <button style="display: none; border: unset !important;background-color: white !important;color: #0e0c0c !important;"
                  id="custom_show_view_comments_btn-<?php echo $action->action_id ?>" onclick="customShowComments('comment_section-<?php echo $action->action_id ?>','<?php echo $action->action_id ?>')"  class="view_comments">
            <i class="fa fa-eye" aria-hidden="true"></i> View Comments  (<?php echo count($comments); ?>)
          </button>
          <button style="display: none; border: unset !important;background-color: white !important;color: #0e0c0c !important;"
                  id="custom_hide_view_comments_btn-<?php echo $action->action_id ?>" onclick="customHideComments('comment_section-<?php echo $action->action_id ?>','<?php echo $action->action_id ?>')"  class="hide_comments">
            <i class="fa fa-eye-slash" aria-hidden="true"></i> Hide Comments  (<?php echo count($comments); ?>)
          </button>
          <?php endif ?>

          <?php endif; ?>
        </div>
      </div>

    </div>
    <?php if( $action->getTypeInfo()->commentable ): // Comments - likes ?>
    <div class='comments' >
      <ul>
        <?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>
        <!--  <li>
            <div class="comments_likes">
              <?php if( $action->likes()->getLikeCount() <= 3 || $this->viewAllLikes ): ?>
                <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers()) )?>

              <?php else: ?>
                <?php echo $this->htmlLink($action->getHref(array('show_likes' => true)),
                  $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()) )
                ) ?>
              <?php endif; ?>
            </div>
          </li> -->
        <?php endif; ?>
        <?php if( $action->comments()->getCommentCount() > 0 ): ?>
        <?php if( $action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
        <li>
          <div></div>
          <div class="comments_viewall">
            <?php if( $action->comments()->getCommentCount() > 2): ?>
            <?php echo $this->htmlLink($action->getHref(array('show_comments' => true)),
            $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
            $this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
            <?php else: ?>
            <?php echo $this->htmlLink('javascript:void(0);',
            $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
            $this->locale()->toNumber($action->comments()->getCommentCount())),
            array('onclick'=>'en4.activity.viewComments('.$action->action_id.');')) ?>
            <?php endif; ?>
          </div>
        </li>
        <?php endif; ?>
        <div class="comment_section-<?php echo $action->action_id ?>" id="comment_section"
        <?php echo ($this->action_id == $action->action_id) ? "style='display: block;'" : "style='display: none;'" ?>
        >
        <?php foreach( $action->getComments($this->viewAllComments) as $comment ): ?>
        <li id="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(),
            $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $action->getSubject()->getTitle())
            ) ?>
          </div>
          <div class="comments_info">
                   <span class='comments_author'>
                     <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
                   </span>
            <div class="time_stamp">
              <?php echo $this->timestamp($comment->creation_date); ?>
            </div>
            <p class="comments_body">
              <?php echo $this->viewMore($comment->body) ?>
            </p>
            <!-- custom  part -->
            <div class="like_activity" style="display: flex;align-items: center">
              <ul class="comments_date">
                <!--  <li class="comments_timestamp">
                    <?php echo $this->timestamp($comment->creation_date); ?>
                  </li>-->
                <?php if ( $this->viewer()->getIdentity() &&
                (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                ($this->viewer()->getIdentity() == $comment->poster_id) ||
                $this->activity_moderate ) ): ?>

                <li class="comments_delete">
                  <i class="far fa-trash-alt"></i>
                  <a class=" button_section"
                     onclick="openShare('<?php echo $this->url(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'delete',
                            'comment_id'=> $comment->comment_id,
                            'action_id' => $action->action_id,'format' => 'smoothbox'
                           ), 'default', true) ?>')"
                  >
                    Delete
                  </a>

                </li>
                <?php endif; ?>
                <?php if( $canComment ):
                    $isLiked = $comment->likes()->isLike($this->viewer());
                ?>

                <li class="comments_like">
                  <?php if( !$isLiked ): ?>
                  <i class="far fa-thumbs-up"></i>
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.activity.like('.sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()).');','class' => ' button_section')) ?>
                  <?php else: ?>
                  <i class="far fa-thumbs-down"></i>
                  <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.sprintf("'%d', %d", $action->getIdentity(), $comment->getIdentity()).');','class' => 'button_section')) ?>
                  <?php endif ?>
                </li>
                <?php endif ?>
                <!--   <?php if( $comment->likes()->getLikeCount() > 0 ): ?>

                     <li class="comments_likes_total">
                       <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">

                           <?php if($comment->likes()->getLikeCount() == 0) : ?>
                           <?php echo ''; ?>
                           <?php elseif($comment->likes()->getLikeCount() == 1) : ?>
                           <?php echo $comment->likes()->getLikeCount().' like'; ?>
                           <?php else: ?>
                           <?php echo $comment->likes()->getLikeCount().' likes'; ?>
                           <?php endif ?>
                       </a>
                     </li>
                   <?php endif ?> -->
                <li>
                  <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                  <div class="tooltip">
                    <!--   <?php if( $comment->likes()->getLikeCount() != 0 ): ?>
                       <?php if($comment->likes()->getLikeCount() == 0) : ?>
                       <?php echo ''; ?>
                       <?php elseif($comment->likes()->getLikeCount() == 1) : ?>
                       <?php echo $comment->likes()->getLikeCount().' like(s)'; ?>
                       <?php else: ?>
                       <?php echo $comment->likes()->getLikeCount().' like(s)'; ?>
                       <?php endif ?>
                       <?php endif; ?> -->

                    <p class="hover_like"  onclick="openShare('<?php echo $this->url(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'likemodal',
                            'action_id' => $action->action_id,
                            'comment_id' => $comment->comment_id,'format' => 'smoothbox',
                           ), 'default', true) ?>')"><?php  echo ($comment->likes()->getLikeCount() == 1) ? $comment->likes()->getLikeCount().' like(s)' : $comment->likes()->getLikeCount().' like(s)';?></p>

                    <!-- <span class="tooltiptext">
                         <?php if(  $comment->likes()->getLikeCount() > 0 ): ?>
                         <?php
                                   $sublikes = $comment->likes()->getAllLikesUsers();
                                   foreach($sublikes as $vals){ ?>
                                   <a><?php echo $vals->displayname; ?></a><br>

                         <?php   } ?>
                         <?php endif ?>
               </span> -->
                  </div>
                  <?php endif ?>
                </li>
              </ul>

            </div>
          </div>
        </li>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</ul>
<!--<?php if( $canComment ) echo $this->commentForm->render() /*
<form>
  <textarea rows='1'>Add a comment...</textarea>
  <button type='submit'>Post</button>
</form>


*/ ?> -->
<?php if( $canComment ): ?>
<?php $actionID =  $action->action_id ; ?>
<div class="feed_item_photo"  style="margin-top: 9px;margin-bottom: 5px;display: flex;">
  <p id="sub_comment-<?php echo $actionID ;?>"  style="display:none;margin-top: 11px;margin-left: 8px;font-size: 16px;font-weight: 500;color: #626262;justify-content: center;align-items: center; ">

    <?php
                         $sender = Engine_Api::_()->user()->getUser($this->viewer()->getIdentity());?>
    <?php echo $this->htmlLink($sender->getHref(), $this->itemPhoto($sender, 'thumb.icon')) ?>
    <?php echo $this->htmlLink($sender->getHref(), $sender->getTitle()) ?>
  </p>
</div>
<?php echo $this->commentForm->render(); ?>

<?php endif; ?>

<script type="text/javascript">
  var attachComment = function(action_id){
    en4.core.runonce.add(function(){
      $('activity-comment-body-' + action_id).autogrow();
      var attachComposerTag = '<?php echo $attachUserTags ?>';
      var composeCommentInstance = new CommentsComposer($('activity-comment-body-' + action_id), {
        'submitCallBack' : en4.activity.comment,
        hashtagEnabled : '<?php echo $hashtagEnabled ?>',
      });
      if (attachComposerTag === '1') {
        composeCommentInstance.addPlugin(new CommentsComposer.Plugin.Tag({
          enabled: true,
          suggestOptions : {
            'url' : '<?php echo $this->url(array(), 'default', true) . '/user/friends/suggest' ?>',
            'data' : {
              'format' : 'json'
            }
          },
          'suggestProto' : 'local',
          'suggestParam' : <?php echo Zend_Json::encode($this->friends()) ?>
      }));
      }
      commentComposer[action_id] = composeCommentInstance;
    });
  };
  var action_id = '<?php echo $action->action_id ?>';
  attachComment(action_id);
  var showCommentBody = function (action_id) {
    $('activity-comment-form-' + action_id).style.display = "";
    $('activity-comment-submit-' + action_id).style.display = "block";
    $('sub_comment-' + action_id).style.display = "flex";
    customShowComments('comment_section-'+action_id,action_id);
    console.log('test');

    commentComposer[action_id].focus();
  };


  function customHideComments(id,actionId){
    document.getElementsByClassName(id)[0].style.display="none";
    document.getElementById('custom_show_view_comments_btn-'+actionId).style.display="block";
    document.getElementById('custom_hide_view_comments_btn-'+actionId).style.display="none";
    if($('sub_comment-' + actionId))
    {
      document.getElementById('sub_comment-'+actionId).style.display="none";
      document.getElementById('activity-comment-form-'+actionId).style.display="none";
    }
  }

  function customShowComments(id,actionId){

    if(  document.getElementsByClassName('comment_section-'+actionId)[0].style.display=="none") {

      document.getElementsByClassName(id)[0].style.display="block";
      document.getElementsByClassName('comment_section-'+actionId)[0].style.display="block";
      // if(document.getElementById('custom_show_view_comments_btn-'+actionId))
      //   document.getElementById('custom_show_view_comments_btn-'+actionId).style.display="none";
      // if(document.getElementById('custom_hide_view_comments_btn-'+actionId))
      //   document.getElementById('custom_hide_view_comments_btn-'+actionId).style.display="block";
      // console.log('iff',actionId);


    }
    else if(document.getElementsByClassName('comment_section-'+actionId)[0].style.display=="block") {

      //   document.getElementsByClassName(id)[0].style.display="none";
      document.getElementsByClassName('comment_section-'+actionId)[0].style.display="none";

      if($('sub_comment-' + actionId))
      {
        document.getElementById('sub_comment-'+actionId).style.display="none";
        document.getElementById('activity-comment-form-'+actionId).style.display="none";
      }
      console.log('else',actionId);


    }
  }
  function openShare(getType) {
    Smoothbox.open(getType);
  }

</script>
<!--
</div>
<?php endif; ?>

</div>
<?php if( !$this->noList ): ?></li><?php endif; ?>

<?php
ob_end_flush();
} catch (Exception $e) {
ob_end_clean();
if( APPLICATION_ENV === 'development' ) {
echo $e->__toString();
}
};
endforeach;
?>

<?php if( !$this->getUpdate ): ?>
</ul>
<?php endif ?>
-->
<style>

  span.feed_item_bodytext {
    line-height: 21px !important;
  }
  .feed_item_link_desc {
    margin-bottom: 8px;
  }
  .feed_item_link_desc strong {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 15px;
  }
  .tooltip {
    /*position: relative;*/
    font-family: Roboto,sans-serif !Important;

    color: #44AEC1;
    font-weight: bold;
    cursor: pointer;
    display: inline-block;
  }

  .tooltip .tooltiptext {
    visibility: hidden;
    width: 145px;
    border: 1px solid #181d20 !important;
    background-color: #181d20;
    border-radius: 3px !important;
    color: white !important;
    padding: 5px 0;
    padding-left: 10px;
    /* Position the tooltip */
    position: absolute;
    z-index: 999;
  }

  .tooltip:hover .tooltiptext {
    visibility: visible;
  }
  .tooltip .hover_like{

  }
  .hover_like {
    font-family: Roboto,sans-serif !Important;
    font-weight: 600;
    font-size: 15px
  }
  .zero_like {
    font-family: Roboto,sans-serif !Important;
    margin-bottom: 5px;
    color: #44AEC1;
    display: inline-block;
    font-weight: 600;
    font-size: 15px;
  }
  span.tooltiptext {
    width: 8%;
    color: #605454 !important;
    font-size: 14px !important;
    font-family: Roboto,sans-serif !Important;
  }
  li.comments_likes_total > a {
    font-weight: 600;
    color: #44AEC1 !important;
    font-size: 15px
  }
  .tooltiptext > a {
    color: white;
    font-size: 14px !important;
    text-decoration: none;
    font-family: fontawesome !important;
  }
  .no_users{
    color: #605454 !important;
    font-size: 14px !important;
    text-decoration: none;
    font-family: fontawesome !important;
  }
  /*.comments_comment_likes_tips:not(:hover){*/
  /*  display:none;*/
  /*}*/
  /*.comments_comment_likes_tips:hover{*/
  /*  display:block;*/
  /*}*/
  div#compose-menu {
    display: block !important;
  }

  .comment_button {
    padding: 7px 12px;
    font-size: 14px;
    /* text-transform: uppercase; */
    background-color: #44AEC1;
    color: #ffffff !important;
    border: 2px solid #44AEC1;
    cursor: pointer;
    outline: none;
    position: relative;
    overflow: hidden;
    -webkit-transition: all 500ms ease 0s;
    -moz-transition: all 500ms ease 0s;
    -o-transition: all 500ms ease 0s;
    transition: all 500ms ease 0s;
    -moz-border-radius: 3px;
    -webkit-border-radius: 3px;
    border-radius: 3px;
    -webkit-box-sizing: border-box;
    -mox-box-sizing: border-box;
    box-sizing: border-box;
    margin-right: 15px;
  }
  .button.button_section {

    padding: 5px 9px!important;
    font-weight: unset !important;
  }
  .comments_date li,
  .feed_item_ul li {
    margin-right: 15px;
    font-size: 14px;
    color: #1E1919;
  }
  .view_comments{
    cursor: pointer;
    position: relative;
    bottom: 4px;
    padding: 5px 9px!important;
  }
  .hide_comments{
    cursor: pointer;
    position: relative;
    bottom: 5px;
    padding: 5px 9px!important;
    display: none;
  }
  ul.comments_date {
    height: 32px;
    display: flex;
    align-items: center;
  }
  .comments > ul {
    margin-top: 16px;
  }
  /*.comment_button:hover {*/
                /*    background: #eaeaea;*/
                /*    color: black !important;*/
                /*}*/
  .feed_item_date{
    background-color: white !important;
  }
  span.timestamp {

  }
  .comments_likes {
    display: none;
  }
  .hashtag_activity_item {

    font-size: 15px;
    font-family: 'fontawesome', Roboto, sans-serif;
  }
  .comments {
    margin-top: 6px;
  }
  .feed_item_body {
    font-family: 'fontawesome', Roboto, sans-serif;
    font-size: 15px;
  }
  .comments_likes {
    font-family: 'fontawesome', Roboto, sans-serif;
    font-size: 15px;
  }
  p.comments_body {
    font-size: 15px;
  }
  img.thumb_icon.item_photo_user {
    border-radius: 50%;
  }
  .comments > ul > li {
    background-color: white !important;
    border-top-width: unset !important;
    border-color: white !important;
  }
  a.comment_button.fa.fa-thumbs-o-down ,
  a.comment_button.fa.fa-thumbs-o-up ,
  a.comment_button.fa.fa-share-square-o ,
  a.comment_button.fa.fa-comment-o  {
    text-decoration: none !important;
  }
  a.comment_button.fa.fa-trash-o {
    text-decoration: none !important;
  }
  a.comment_button.fa-thumbs-o-up {
    text-decoration: none !important;
  }
  a.comment_button.fa-thumbs-o-down {
    text-decoration: none !important;
  }
  .feed_item_date {
    display: flex;
    justify-content: space-between;
  }

  .comments_author_photo > a> img.thumb_icon.item_photo_user {
    margin-top: 5px;
  }
  .fa-thumbs-up:before,
  .fa-thumbs-down:before ,
  .fa-comment-:before,
  .fa-trash-:before,
  .fa-share-square-:before,
  .fa-thumbs-down:before,
  .fa-pencil-square:before{
    margin-right: 5px;
  }
  ul.comments_date {
    display: flex;
    align-items: center;
    margin-bottom: 7px;
  }
  #comment_section > li {
    border-bottom: 1px solid lightgray;
    width: 94%;
    margin-bottom: 10px;
  }
  p.comments_body {
    margin-top: 10px;
    margin-bottom: 10px;
  }

  .sitecrowdfunding_activity_feed_desc {
    margin-left: 0px;
    font-size: 15px;
    font-weight: 500;
    line-height: 1 !important;
    margin-bottom: 10px;
  }

  a.sea_add_tooltip_link, .Sitecrowdfunding_project_goal_amount > p, ul.feed .feed_item_link_desc,
  .feed_item_link_desc{
    font-weight: 400 !important;
    font-size: 15px;
    color: #0f1414 !important;
  }
  ul.feed .feed_item_generated {
    color: #020202 !important;
  }
  ul.comments_date {
    position: relative;
    right: 7px;
  }
  .mbot5 {
    margin-bottom: 0px !important;
  }
  .mtop5{
    margin-top: 0px !important;
  }
  /*hided the img and desc in project page*/
  /*a.aaf-feed-photo {*/
                /*  display: none;*/
                /*}
                /*.sitecrowdfunding_activity_feed_desc {
                /*  display: none;
                /* }*/
  img.thumb_normal {
    height: 150px !important;
    width: 150px !important;
  }
  .comments_viewall {
    display: none;
  }
  .hashtag_activity_item > ul {
    display: none !important;
  }

  a.comment_button.fa.fa-trash-o,
  a.comment_button.fa.fa-thumbs-o-up,
  a.comment_button.fa.fa-thumbs-o-down,
  a.comment_button.fa.fa-pencil-square-o,
  a.comment_button.fa.fa-share-square-o {
    margin-right: 14px;
  }
  .comments .comments_comment_options > a, .comments .comments_info, .comments .comments_likes, .comments .comments_viewall {
    font-size: 14px;
  }
  .comments ul ul > li +li {
    border-top-width: 0px !important;
  }
  .comment_button  p {
    font-family: fontawesome !important;
  }
  .comments_comment_likes{
    font-family: fontawesome !important;
    color: #424141 !important;
    font-size: 16px;
  }
  #comment_section > li:last-child {
    border-bottom: unset !important;
    width:unset !important;
    margin-bottom: unset !important;
  }
  .comments_comment_likes_tips {
    border: 1px solid #4c7590 !important;
    border-radius: 3px !important;
  }
  span.feed_item_bodytext {
    line-height: 37px;
  }
  img.thumb_normal
  {
    max-width: unset !important;
    max-height: unset !important;
    height: 150px !important;
    width: 150px !important;

  }
  .main_time_stamp {
    padding-bottom: 11px;
  }
  .feed_item_body_content_only {
    font-size: 15px;
  }
  .feed_item_body_content_only a{
    color:#44AEC1;
  }
  .comment_button {
    position: relative;
    bottom: 2px;
  }
  ul.feed > li .feed_item_body .sitecrowdfunding_review_rich_content .sitecrowdfunding_activity_feed_img .aaf-feed-photo img {
    height: 150px !important;
    width: 150px !important;
  }
  .icon_buttons{
    display: flex;
    width: 100%;
    justify-content: space-between;
  }
  a#compose-video-activator {
    display: none;
  }
  .feed_item_photo > p > a{
    margin-right:6px;
  }
  @media (max-width: 767px)
  {
    ul.comments_date {
      height: 70px !important;
    }
    .button.button_section , .view_comments , .hide_comments{
      margin-top:5px;
    }
    .feed_item_ul li {
      margin-bottom: 15px;
    }
    .icon_buttons{
      display: unset !important;
      width: unset !important;
      justify-content: unset !important;
      margin-top: 15px;
    }
    .feed_item_date, .feed_item_ul {
      flex-wrap: wrap !important;
    }
    .feed_item_body_content_only {
      margin-left: 8px;
    }
    img.thumb_normal {
      height: 150px !important;
      width: 150px !important;
    }
    ul.comments_date {
      display: flex;
      flex-wrap: wrap !important;
    }
  }
</style>