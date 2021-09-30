<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/styles/custom-activity.css');?>

<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer.js')
->appendFile($this->layout()->staticBaseUrl . 'application/modules/Activity/externals/scripts/core.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flowplayer-3.2.13.min.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/html5media/html5media.min.js');

$composerOptions = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity.composer.options');
$attachUserTags = in_array("userTags", $composerOptions);
$hashtagEnabled = in_array("hashtags", $composerOptions);

$actions = $this->actions;

if ($attachUserTags) {
  $this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/comments_composer_tag.js');
}
?>


<?php if( (!empty($this->actions)) && (count($this->actions) > 0) ):?>

  <?php $actions = $this->actions;?>
  <ul class="activity-list-box-container" id="activity-feed">
    <?php foreach( $actions as $action ): ?>

      <?php if( $action->getTypeInfo()->enabled ): ?>

        <?php if(
            ( $action->getSubject() || !$action->getSubject()->getIdentity() ) ||
            ( $action->getObject() || !$action->getObject()->getIdentity() )
            ):?>

            <?php $shareableItem = $action->getShareableItem();?>
            <?php $likesCount = $action->likes()->getLikeCount();?>
            <?php $commentsCount = $action->comments()->getCommentCount();?>

            <li class="activity-item-container" id="activity-item-<?php echo $action->action_id ?>">
              <div class="activity-item-header">
                <div class="activity-item-avatar">
                  <?php echo $this->htmlLink($action->getSubject()->getHref(),
                  $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())
                  ) ?>
                </div>
                <div class="activity-item-header-details">
                  <p>
                    <a class="activity-item-header-user-details" href="<?php echo $action->getSubject()->getHref();?>"><?php echo $action->getSubject()->getTitle();?></a>
                    <?php //echo $action->getObject()->getType();?> ->
                    <a class="activity-item-header-action-details" href="<?php echo $action->getObject()->getHref();?>"><?php echo $action->getObject()->getTitle();?></a>
                  </p>
                  <p class="activity-date"><?php echo $this->timestamp($action->getTimeValue()) ?></p>
                </div>
              </div>
              <div class="activity-item-content">
                eeeeee
              </div>
              <div class="activity-item-options">

                <!-- Show icons for logged persons -->
                <?php if($this->viewer()->getIdentity()):?>

                  <?php if( $action->likes()->isLike($this->viewer()) ): ?>
                      <div class="generic-button activity-item-unlike-option">
                        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('onclick'=>'javascript:en4.activity.unlike('.$action->action_id.');')) ?>
                      </div>
                  <?php else: ?>
                      <div class="generic-button activity-item-like-option">
                          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('onclick'=>'javascript:en4.activity.like('.$action->action_id.');')) ?>
                      </div>
                  <?php endif; ?>

                  <?php if(
                      $this->viewer()->getIdentity() && ( $this->activity_moderate ||
                      (
                        ($this->viewer()->getIdentity() == $this->activity_group) ||
                        ($this->allow_delete &&
                          (
                            ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id)
                            ||
                            ('user' == $action->object_type && $this->viewer()->getIdentity()  == $action->object_id)
                          )
                        )
                    ))): ?>
                    <div class="generic-button activity-item-delete-option">
                      <?php echo $this->htmlLink(array(
                      'route' => 'default',
                      'module' => 'activity',
                      'controller' => 'index',
                      'action' => 'delete',
                      'action_id' => $action->action_id
                      ), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
                    </div>
                  <?php endif; ?>

                  <?php if ($action->canEdit()): ?>
                    <div class="generic-button activity-item-edit-option">
                      <?php echo $this->htmlLink(array(
                      'route' => 'default',
                      'module' => 'activity',
                      'controller' => 'index',
                      'action' => 'delete',
                      'action_id' => $action->action_id
                      ), $this->translate('Delete'), array('class' => 'smoothbox')) ?>
                    </div>
                  <?php endif; ?>

                  <div class="generic-button activity-item-comment-option">
                    <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'viewcomment','action_id'=>$action->getIdentity(),'format'=>'smoothbox'), $this->translate('Comment'), array(
                    'class'=>'smoothbox',
                    )) ?>
                  </div>

                  <?php if($likesCount > 0):?>
                    <div class="generic-button activity-item-list-likes-option">
                      <a href="javascript:void(0);"
                         onclick="openSmoothbox('<?php echo $this->url(array(
                            'route' => 'default',
                            'module' => 'activity',
                            'controller' => 'index',
                            'action' => 'likemodal',
                            'action_id' => $action->action_id,
                            'format' => 'smoothbox',
                            ), 'default', true) ?>')">
                        <?php echo $likesCount.' Like(s)';?>
                      </a>
                    </div>
                  <?php endif; ?>

                  <?php if($commentsCount > 0):?>
                    <div class="generic-button activity-item-view-comments-option">
                      <?php echo $commentsCount.' Comment(s)';?>
                    </div>
                  <?php endif; ?>

                  <?php if( $shareableItem && $this->viewer()->getIdentity() ): ?>
                    <div class="generic-button activity-item-share-option">
                      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $shareableItem->getType(), 'id' => $shareableItem->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
                    </div>
                  <?php endif; ?>

                <?php else:?>

                  <div class="generic-button activity-item-like-option">
                    <a href="javascript:void(0);">Like</a>
                  </div>

                  <div class="generic-button activity-item-comment-option">
                    <a href="javascript:void(0);">Comment</a>
                  </div>

                  <div class="generic-button activity-item-list-likes-option">
                    <?php echo $this->htmlLink('javascript:void(0);',
                    $this->translate(array('%s Like(s)', '%s Like(s)', $action->likes()->getLikeCount()),
                    $this->locale()->toNumber($action->likes()->getLikeCount())),
                    array()) ?>
                  </div>

                  <div class="generic-button activity-item-view-comments-option">
                    <?php echo $this->htmlLink('javascript:void(0);',
                    $this->translate(array('%s Comment(s)', '%s Comment(s)', $action->comments()->getCommentCount()),
                    $this->locale()->toNumber($action->comments()->getCommentCount())),
                    array()) ?>
                  </div>

                <?php endif; ?>
              </div>
            </li>
        <?php endif; ?>

      <?php endif; ?>

    <?php endforeach;?>
  </ul>
<?php endif; ?>

<script type="text/javascript">
  function openSmoothbox(url) {
    Smoothbox.open(url);
  }
</script>