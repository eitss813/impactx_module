<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<script type="text/javascript">
  var notificationPageCount = <?php echo sprintf('%d', $this->notifications->count()); ?>;
  var notificationPage = <?php echo sprintf('%d', $this->notifications->getCurrentPageNumber()); ?>;
  var loadMoreNotifications = function() {
    notificationPage++;
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : notificationPage
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('notifications_loading_main').setStyle('display', 'none');
        if( '' != responseHTML.trim() && notificationPageCount > notificationPage ) {
          $('notifications_viewmore').setStyle('display', '');
        }
        $('notifications_main').innerHTML += responseHTML;
      }
    }).send();
  };
  en4.core.runonce.add(function(){
    if($('notifications_viewmore_link')){
      $('notifications_viewmore_link').addEvent('click', function() {
        $('notifications_viewmore').setStyle('display', 'none');
        $('notifications_loading_main').setStyle('display', '');
        loadMoreNotifications();
      });
    }

    if($('notifications_markread_link_main')){
      $('notifications_markread_link_main').addEvent('click', function() {
        $('notifications_markread_main').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->translate("0 Updates");?>');
      });
    }

    $('notifications_main').addEvent('click', function(event){
      event.stop(); //Prevents the browser from following the link.

      var current_link = event.target;
      let len = current_link.getElementsByTagName('a');
      let lenth = len.length;
      var final_link = current_link.getElementsByTagName('a')[lenth-1].getAttribute("href");
      console.log('link1', final_link);
      console.log('link2', current_link.get('href'));
      var notification_li = $(current_link).getParent('li');
      if(final_link){
        en4.core.request.send(new Request.JSON({
          url : en4.core.baseUrl + 'activity/notifications/markread',
          data : {
            format     : 'json',
            'actionid' : notification_li.get('value')
          },
          onSuccess : window.location.href = final_link
        }));


      }
    });

  });
</script>

<div class='notifications_layout'>

  <div class='notifications_leftside'>


    <h3 class="sep">
      <?php  $itemCount = $this->requests->getTotalItemCount(); ?>
      <span><?php echo $this->translate(array("My Request (%d)","My Requests (%d)", $itemCount), $itemCount) ?></span>
    </h3>
    <ul class='requests' >
      <?php if( $this->requests->getTotalItemCount() > 0 ): ?>
      <?php foreach( $this->requests as $notification ): ?>
      <?php
          try {
            $parts = explode('.', $notification->getTypeInfo()->handler);
      echo $this->action($parts[2], $parts[1], $parts[0], array('notification' => $notification));
      } catch( Exception $e ) {
      if( APPLICATION_ENV === 'development' ) {
      echo $e->__toString();
      }
      continue;
      }
      ?>
      <?php endforeach; ?>
      <?php else: ?>
      <li style="display: flex;justify-content: center;width: 100%;">
        <div class="" style="width: 100%">
          <div class="tip">
                 <span>
                  <?php echo $this->translate("You have no requests.") ?>
                 </span>
          </div>
        </div>
      </li>
      <?php endif; ?>
    </ul>
  </div>

  <div class='notifications_rightside'>
    <h3 class="sep">
      <span><?php echo $this->translate("Recent Updates") ?></span>
    </h3>
    <ul class='notifications' id="notifications_main">
      <?php if( $this->notifications->getTotalItemCount() > 0 ): ?>
      <?php
          foreach( $this->notifications as $notification ):
      ob_start();
      try { ?>
      <li<?php if( !$notification->read ): ?> class="notifications_unread"<?php $this->hasunread = true; ?> <?php endif; ?> value="<?php echo $notification->getIdentity();?>">
      <?php // removed onclick event onclick="javascript:en4.activity.markRead($notification->getIdentity() ?>


            <?php if( $notification->type == 'yndynamicform_user_assign_form' ): ?>
                 <span style="display:none;"><?php echo $notification->__toString() ?></span>
                 <?php
                     $yndform = Engine_Api::_() -> getItem('yndynamicform_form', $notification->object_id);
                     $form_name = $yndform ?  $yndform->getTitle() : null;
                     $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $notification->subject_id);
                     $project_name = $project->getTitle();
                ?>
                <?php if( $form_name ): ?>
                  <span>
                        Form <a  class="feed_item_username" href="/dynamic-form/entry/create/1/form_id/<?php echo $yndform->getIdentity(); ?>/project_id/<?php echo $project->getIdentity(); ?>" >
                        <?php echo $form_name; ?> </a> 
                    is assigned to your project <a  class="feed_item_username" href="<?php echo $project->getHref(); ?>" >
                        <?php echo $project_name; ?> </a> .Posted <?php echo $this->timestamp($notification->date); ?>
                  </span>

                        <span style="display:none;">   </span>

                  <?php else: ?>
                     <span> Form assigned to you does not exist now.</span>
                  <?php endif; ?>
          <?php else: ?>
                <span style="cursor:pointer" class="notification_item_general notification_type_<?php echo $notification->type ?>">
                 <?php echo $notification->__toString(), $this->translate(' Posted %1$s', $this->timestamp($notification->date)) ?>

          <?php endif; ?>


             <?php $post = $notification->params['label'] ; ?>
             <?php if( $notification->type == 'commented' && $post !='post'): ?>
             <?php  $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $notification->object_id); ?>
                           <span style="display:none;">
                              <a class="feed_item_username" href="<?php echo $notification->params['url1']; ?>"> <?php echo $project->title; ?></a>.
                            </span>
             <?php endif; ?>
             <?php if( $notification->type != 'commented' || ($notification->type == 'commented' && $post =='post' )  ): ?>

                 <?php if($notification->type != 'commented'  && $notification->type != 'commented_commented' && $notification->type != 'friend_request' ): ?>
                                     <span style="display:none;"><?php echo $notification->__toString() ?></span>
                 <?php endif; ?>
                 <?php if($notification->type == 'commented' && $post =='post' ): ?>
                 <?php  $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $notification->object_id); ?>
                             <span style="display:none;">
                                <a class="feed_item_username" href="<?php echo $notification->params['comment_id']; ?>"> <?php echo 'post'; ?></a>.
                              </span>
                 <?php endif; ?>

                 <?php if( $notification->type == 'friend_request'): ?>
                                 <span style="display:none;">
                              <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
                                 <a class="feed_item_username" href="<?php echo $viewer->getHref().'?screen=friends'; ?>"><?php echo 'Accept request'; ?></a>
                            </span>
                 <?php endif; ?>

                 <?php if( $notification->type == 'commented_commented'): ?>
                             <span style="display:none;">
                            <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
                               <a class="feed_item_username" href="<?php echo $notification->params['comment_id']; ?>"><?php echo 'post'; ?></a>
                          </span>
                 <?php endif; ?>

             <?php endif; ?>


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
      <?php else: ?>
      <li>
        <?php echo $this->translate("You have no notifications.") ?>
      </li>
      <?php endif; ?>
    </ul>

    <div class="notifications_options">
      <?php if( $this->hasunread ): ?>
      <div class="notifications_markread" id="notifications_markread_main">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
        'id' => 'notifications_markread_link_main',
        'class' => 'buttonlink notifications_markread_link'
        )) ?>
      </div>
      <?php endif; ?>
      <?php if( $this->notifications->getTotalItemCount() > 10 ): ?>
      <div class="notifications_viewmore" id="notifications_viewmore">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => 'notifications_viewmore_link',
        'class' => 'buttonlink icon_viewmore'
        )) ?>
      </div>
      <?php endif; ?>
      <div class="notifications_viewmore" id="notifications_loading_main" style="display: none;">
        <i class="fa fa-spinner fa-spin" style=' margin-right: 5px;'></i>
        <?php echo $this->translate("Loading ...") ?>
      </div>
    </div>




  </div>

</div>
<style>
  .user-widget-request {
    border-width: 1px;
    /* float: left; */
    display: inline-block;
    margin: 0 2% 2% 0;
    overflow: hidden;
     width: unset !important;
    /* height: 98px; */
    vertical-align: top;
    box-sizing: border-box;
  }
  a.feed_item_username {
    color: #44AEC1;
  }
  @media (max-width: 767px) {
    div.notifications_layout > div.notifications_leftside {
      float: left;
      width: 100% !important;
      overflow: hidden;
    }
  }


</style>

