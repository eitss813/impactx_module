<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: notification.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/seaomooscroll/SEAOMooVerticalScroll.js'); ?>

<?php $temp_flag = 0; ?>
<div class='seocore_pulldown_item_list'>
  <?php if( $this->notifications->getTotalItemCount() > 0 ): $temp_flag = 1; ?>
    <div id="seaocore_notification_main_right_content_area">
      <div id="seaocore_notification_scroll_main_right_content" class="seaocore_scroll_content">
        <ul class='notifications' id="notifications_main">
          <?php
          foreach( $this->notifications as $notification ):
            ob_start();
            try {
              ?>
              <li  onmouseover="this.style.backgroundColor='#f4f1f1';" onmouseout="this.style.backgroundColor='white';" id="<?php echo 'redirect_'.$notification->getIdentity(); ?>"  <?php if( !$notification->read ): ?>class="notifications_unread clr"<?php endif; ?> value="<?php echo $notification->getIdentity(); ?>" style="overflow: hidden;">
                <span  onclick="redirect('<?php echo $notification->getIdentity(); ?>')" class="notification_item_general aaf_update_pulldown">
                  <?php $item = $notification->getSubject() ?>      
                  <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', $item->getTitle())) ?>

                  <span class="aaf_update_pulldown_content">

                      <?php $post = $notification->params['label'] ; ?>
                      <?php if( $notification->type == 'commented' && $post !='post'): ?>
                          <?php  $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $notification->object_id); ?>
                            <span class="aaf_update_pulldown_content_title fleft">
                              <a class="feed_item_username" href="<?php echo $item->getHref(); ?>"><?php echo $item->displayname; ?></a> has commented on your
                              <a class="feed_item_username" href="<?php echo $notification->params['url1']; ?>"> <?php echo $project->title; ?></a>.
                            </span>
                      <?php endif; ?>
                      <?php if( $notification->type != 'commented' || ($notification->type == 'commented' && $post =='post' )  ): ?>

                            <?php if($notification->type != 'commented'  && $notification->type != 'commented_commented' && $notification->type != 'friend_request' ): ?>
                                  <span class="aaf_update_pulldown_content_title fleft"><?php echo $notification->__toString() ?></span>
                            <?php endif; ?>
                            <?php if($notification->type == 'commented' && $post =='post' ): ?>
                            <?php  $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $notification->object_id); ?>
                                   <span class="aaf_update_pulldown_content_title fleft">
                                      <a class="feed_item_username" href="<?php echo $item->getHref(); ?>"><?php echo $item->displayname; ?></a> has commented on your
                                      <a class="feed_item_username" href="<?php echo $notification->params['comment_id']; ?>"> <?php echo 'post'; ?></a>.
                                    </span>
                            <?php endif; ?>

                          <?php if( $notification->type == 'friend_request'): ?>
                              <span class="aaf_update_pulldown_content_title fleft">
                                <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
                                   <a class="feed_item_username" href="<?php echo $item->getHref(); ?>"><?php echo $item->displayname; ?></a> requested to be your friend.
                                   <a class="feed_item_username" href="<?php echo $viewer->getHref().'?screen=friends'; ?>"><?php echo 'Accept request'; ?></a>
                              </span>
                          <?php endif; ?>

                          <?php if( $notification->type == 'commented_commented'): ?>
                                  <span class="aaf_update_pulldown_content_title fleft">
                                    <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
                                       <a class="feed_item_username" href="<?php echo $item->getHref(); ?>"><?php echo $item->displayname; ?></a> has commented on a post you commented on.
                                       <a class="feed_item_username" href="<?php echo $notification->params['comment_id']; ?>"><?php echo 'post'; ?></a>
                                  </span>
                          <?php endif; ?>

                      <?php endif; ?>


                    <span class="aaf_update_pulldown_content_stat notification_type_<?php echo $notification->type ?>"> 
                      <?php echo $this->timestamp(strtotime($notification->date)) ?>
                    </span>
                  </span>
                </span>
              </li>
              <?php
            } catch( Exception $e ) {
              ob_end_clean();
              if( APPLICATION_ENV === 'development' ) {
                echo $e->__toString();
              }
              continue;
            }
            ob_end_flush();
          endforeach;
          ?>
        </ul>
      </div>
    </div>
  <?php else: $temp_flag = 0; ?>
    <div class="seaocore_pulldown_nocontent_msg">
      <?php echo $this->translate("You have no notifications.") ?>
    </div>
  <?php endif; ?>
</div>
<?php if( !empty($temp_flag) ): ?>
  <div class="seocore-pulldown-footer">
    <a href="<?php echo $this->url(array(), 'recent_activity', true) ?>" class="ui-link">
      <?php echo $this->translate('View All Notifications') ?>
    </a>
    <a href="javascript:void(0)" onclick="en4.seaocore.miniMenu.notifications.markAsRead();" class="fright ui-link">
      <?php echo $this->translate('Mark as Read') ?>
    </a>
  </div>
<?php endif; ?>
<script type="text/javascript">
  en4.core.runonce.add(function () {
    new SEAOMooVerticalScroll('seaocore_notification_main_right_content_area', 'seaocore_notification_scroll_main_right_content', {});
    $('notifications_main').addEvent('click', function (event) {
      event.stop(); //Prevents the browser from following the link.
      en4.seaocore.miniMenu.notifications.onClick(event);
    });
  });
  function redirect(val) {
    document.getElementById('redirect_'+val).style.backgroundColor = "#f4f1f1";
  }

</script>
<style>
  ul.notifications > li {
    overflow: hidden;
    clear: both;
    margin-bottom: unset !important;
  }
  .layout_seaocore_menu_mini .seaocore_mini_menu_items .seocore_pulldown_item_list {
     padding: 1px !important;
  }
</style>