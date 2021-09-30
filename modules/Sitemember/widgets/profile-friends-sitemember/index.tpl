<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemember
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-07-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js');
?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl'; ?>

<?php $this->headLink()
->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
?>

<?php if ($this->loaded_by_ajax): ?>
<script type="text/javascript">
    var params = {
        requestParams:<?php echo json_encode($this->params) ?>,
    responseContainer: $$('.layout_sitemember_profile_friends_sitemember')
    }
    en4.sitemember.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
</script>
<?php endif; ?>

<?php if ($this->showContent): ?>
<script type="text/javascript">
    var toggleFriendsPulldown = function(event, element, user_id) {
        event = new Event(event);
        if( $(event.target).get('tag') != 'a' ) {
            return;
        }

        $$('.profile_friends_lists').each(function(otherElement) {
            if( otherElement.id == 'sitemember_friend_lists_' + user_id ) {
                return;
            }
            var pulldownElement = otherElement.getElement('.pulldown_active');
            if( pulldownElement ) {
                pulldownElement.addClass('pulldown').removeClass('pulldown_active');
            }
        });
        if( $(element).hasClass('pulldown') ) {
            element.removeClass('pulldown').addClass('pulldown_active');
        } else {
            element.addClass('pulldown').removeClass('pulldown_active');
        }
        OverText.update();
    }
    var handleFriendList = function(event, element, user_id, list_id) {
        new Event(event).stop();
        if( !$(element).hasClass('friend_list_joined') ) {
            // Add
            en4.user.friends.addToList(list_id, user_id);
            element.addClass('friend_list_joined').removeClass('friend_list_unjoined');
        } else {
            // Remove
            en4.user.friends.removeFromList(list_id, user_id);
            element.removeClass('friend_list_joined').addClass('friend_list_unjoined');
        }
    }
    var createFriendList = function(event, element, user_id) {
        var list_name = element.value;
        element.value = '';
        element.blur();
        var request = en4.user.friends.createList(list_name, user_id);
        request.addEvent('complete', function(responseJSON) {
            if( responseJSON.status ) {
                var topRelEl = element.getParent();
                $$('.profile_friends_lists ul').each(function(el) {
                    var relEl = el.getElement('input').getParent();
                    new Element('li', {
                        'html' : '\n\
<span><a href="javascript:void(0);" onclick="deleteFriendList(event, ' + responseJSON.list_id + ');">x</a></span>\n\
<div>' + list_name + '</div>',
                        'class' : ( relEl == topRelEl ? 'friend_list_joined' : 'friend_list_unjoined' ) + ' user_profile_friend_list_' + responseJSON.list_id,
                        'onclick' : 'handleFriendList(event, $(this), \'' + user_id + '\', \'' + responseJSON.list_id + '\');'
                    }).inject(relEl, 'before');
                });
                OverText.update();
            } else {
                //alert('whoops');
            }
        });
    }
    var deleteFriendList = function(event, list_id) {
        event = new Event(event);
        event.stop();

        // Delete
        $$('.user_profile_friend_list_' + list_id).destroy();

        // Send request
        en4.user.friends.deleteList(list_id);
    }

    function getNextFriends() {

        if($('friend_noviewmore')) {
            $('friend_noviewmore').style.display = 'none';
        }
        if($('friend_loading')) {
            $('friend_loading').style.display = 'block';
        }
        if($('friend_viewmore_link')) {
            $('friend_viewmore_link').style.display = 'none';
        }

        if($('friend_viewmore')) {
            $('friend_viewmore').style.display = 'none';
        }
        en4.core.request.send(new Request.HTML({
            url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
            format : 'html',
                subject : en4.core.subject.guid,
                page : <?php echo sprintf('%d', $this->friends->getCurrentPageNumber() + 1) ?>,
            isAjax : 1,
                search: '<?php echo $this->search;?>',
                mutual: '<?php echo $this->mutual;?>',
                loaded_by_ajax: 1,
                showContent: 1,
                is_ajax_load: 1
        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
            Elements.from(responseHTML).inject($('sitemember_profile_friends'));
            en4.core.runonce.trigger();
        }
    }));
    }

    en4.core.runonce.add(function(){
        $$('.profile_friends_lists input').each(function(element) { new OverText(element); });
        $$('.friends_lists_menu_input input').each(function(element){
            element.addEvent('blur', function() {
                this.getParents('.drop_down_frame')[0].style.visibility = "hidden";
            });
        });
    });

    en4.core.runonce.add(function() {

        $('sitemember_friends_search_input').addEvent('keypress', function(e) {

            if( e.key != 'enter' ) return;

            if($('friend_noviewmore')) {
                $('friend_noviewmore').style.display = 'none';
            }
            if($('friend_loading')) {
                $('friend_loading').style.display = 'none';
            }
            if($('friend_viewmore_link')) {
                $('friend_viewmore_link').style.display = 'none';
            }

            if($('friend_viewmore')) {
                $('friend_viewmore').style.display = 'none';
            }
            getFriendsResults(this.value, '<?php echo $this->mutual;?>');
        });
    });

    function getFriendsResults(search, mutual) {


        $('sitemember_profile_friends').innerHTML = '<div class="seaocore_content_loader"></div>';
        var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
        en4.core.request.send(new Request.HTML({
            'url' : url,
            'data' : {
                'format' : 'html',
                'subject' : en4.core.subject.guid,
                'search' : search,
                mutual: mutual,
                isAjax: 1,
                loaded_by_ajax: 1,
                showContent: 1,
                is_ajax_load: 1
            }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('sitemember_profile_friends').innerHTML = responseHTML;
                if(mutual == 0) {
                    if($('select_mutual'))
                        $('select_mutual').removeClass('bold');
                    if($('select_all'))
                        $('select_all').removeClass('bold').addClass('bold');
                } else {
                    if($('select_all'))
                        $('select_all').removeClass('bold');
                    if($('select_mutual'))
                        $('select_mutual').removeClass('bold').addClass('bold');
                }
            }
        }));
    }
</script>

<?php if(empty($this->isAjax)):?>
<?php if ($this->friends->getTotalItemCount() > 1 ) :?>
<div class="sitemember_friends_search b_medium mbot10">

    <?php if($this->subject->getIdentity() != $this->viewer->getIdentity() && $this->viewer->getIdentity() && $this->mutualFriendsCount):?>
    <div class="sitemember_friends_search_filters fleft">
        <a href="javascript:void(0);" onclick="getFriendsResults('<?php echo $this->search;?>', 0);" id='select_all' <?php if ($this->mutual == 0) echo 'class="bold"'; ?>><?php echo $this->translate('All (%s)', $this->friendsCount) ?></a> |
        <a href="javascript:void(0);" onclick="getFriendsResults('<?php echo $this->search;?>', 1);" id='select_mutual'<?php if ($this->mutual == 1) echo 'class="bold"'; ?>><?php echo $this->translate('Mutual Friends (%s)', $this->mutualFriendsCount); ?></a>
    </div>
    <?php endif;?>

    <div class="sitemember_friends_search_right fright">
        <input id="sitemember_friends_search_input" type="text" value="<?php echo $this->search;?>" onfocus="$(this).store('over', this.value);this.value = '';" onblur="this.value = $(this).retrieve('over');" placeholder="<?php echo $this->translate('Search Friends');?>">
    </div>

</div><?php endif;?>
<?php endif;?>

<?php if($this->search && $this->isAjax && $this->friends->getTotalItemCount() > 0): ?>
<div class="mleft10"> <?php echo $this->translate(array('%1$s result for: %2$s', '%1$s results for: %2$s)', $this->friends->getTotalItemCount(), $this->search), '<b>' . $this->friends->getTotalItemCount(). '</b>', '<b>' . $this->search. '</b>'); ?></div>
<?php endif;?>

<?php if(!$this->isAjax):?>
<div class='notifications_rightside'>
    <h3 class="sep">
        <?php  $itemCount = $this->requests ? $this->requests->getTotalItemCount() : '0'; ?>
        <!--  <span><?php echo $this->translate(array("My Request (%d)","My Requests (%d)", $itemCount), $itemCount) ?></span> -->
        <span><?php echo 'My Pending Requests ('.$itemCount.')'; ?></span>
    </h3>
    <ul class='requests' style="display: flex;flex-wrap: wrap">
        <?php if($this->requests && $this->requests->getTotalItemCount() > 0 ): ?>
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
<div class="notifications_rightside">
    <h3 class="sep">
        <span><?php echo 'My Friends ('.$this->friends->getTotalItemCount().')'; ?></span>
    </h3>
    <ul class='profile_friends <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>' id="sitemember_profile_friends">
        <?php endif;?>
        <?php if ($this->friends->getTotalItemCount() > 0) :?>
        <?php foreach( $this->friends as $sitemembership ):

        if(!$this->mutual) {
        if( !isset($this->friendUsers[$sitemembership->resource_id]) ) continue;
        $sitemember = $this->friendUsers[$sitemembership->resource_id];
        } else {
        $sitemember = Engine_Api::_()->getItem('user', $sitemembership['user_id']);
        }


        ?>

        <li id="sitemember_friend">

            <?php $rel = 'user' . ' ' . $sitemember->user_id; ?>
            <?php
          $url = $sitemember->getPhotoUrl('thumb.profile');
            if (empty($url)): $url = $this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/images/nophoto_user_thumb_normal.png';
            endif;
            ?>
            <?php if($this->circularImage):?>
            <a href="<?php echo $sitemember->getHref() ?>" class ="sitemember_thumb sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                <span style="background-image: url(<?php echo $url; ?>);"></span>
            </a>
            <?php else:?>

            <a href="<?php echo $sitemember->getHref() ?>" class ="sea_add_tooltip_link" rel="<?php echo $rel ?>" >
                <span style="background-image: url(<?php echo $url; ?>);"></span>
            </a>
            <?php endif;?>

            <div class='profile_friends_body'>
                <div class='profile_friends_status'>
          <span>
            <?php echo $this->htmlLink($sitemember->getHref(), $sitemember->getTitle(), array('class' => 'sea_add_tooltip_link', 'rel' => $sitemember->getType() . ' ' . $sitemember->getIdentity())) ?>
          </span>
                    <?php if(isset($sitemembership->member_count)):?>
                    <div class="seaocore_txt_light">
                        <?php echo $this->translate(array('%s friend', '%s friends', $sitemembership->member_count), $this->locale()->toNumber($sitemembership->member_count));?>
                    </div>
                    <?php endif;?>
                </div>

                <?php if( $this->viewer()->isSelf($this->subject()) && Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.lists') && !$this->mutual): // BEGIN LIST CODE ?>
                <div class='profile_friends_lists' id='sitemember_friend_lists_<?php echo $sitemember->user_id ?>'>

            <span class="pulldown" style="display:inline-block;" onClick="toggleFriendsPulldown(event, this, '<?php echo $sitemember->user_id ?>');">
              <div class="pulldown_contents_wrapper">
                <div class="pulldown_contents">
                  <ul>
                    <?php foreach( $this->lists as $list ):
                      $inList = in_array($list->list_id, (array)@$this->listsByUser[$sitemember->user_id]);
                      ?>
                      <li class="<?php echo ( $inList !== false ? 'friend_list_joined' : 'friend_list_unjoined' ) ?> user_profile_friend_list_<?php echo $list->list_id ?>" onclick="handleFriendList(event, $(this), '<?php echo $sitemember->user_id ?>', '<?php echo $list->list_id ?>');">
                        <span>
                          <a href="javascript:void(0);" onclick="deleteFriendList(event, <?php echo $list->list_id ?>);">x</a>
                        </span>
                        <div>
                          <?php echo $list->title ?>
                        </div>
                      </li>
                      <?php endforeach; ?>
                      <li>
                      <input type="text" title="<?php echo $this->translate('New list...') ?>" onclick="new Event(event).stop();" onkeypress="if( new Event(event).key == 'enter' ) { createFriendList(event, $(this), '<?php echo $sitemember->user_id ?>'); }" />
                    </li>
                  </ul>
                </div>
              </div>
              <a href="javascript:void(0);"><?php echo $this->translate('add to list') ?></a>
            </span>

                </div>

                <?php endif; // END LIST CODE ?>
            </div>

            <?php if($this->userFriendship($sitemember)):?>
            <div class='sitemember_action_link_options'>
                <span><?php echo $this->userFriendship($sitemember) ?></span>
            </div>
            <?php endif;?>
        </li>

        <?php endforeach ?>
        <?php else :?>
        <div class="tip">
      <span>
         <?php
             if(Engine_Api::_()->getApi('settings', 'core')->user_friends_direction){
                echo $this->translate("There are no friends.");
             }else{
                echo $this->translate("There are no followers.");
             }
         ?>
      </span>
        </div>
        <?php endif;?>
        <?php if(!$this->isAjax):?>
    </ul>
</div>

<?php endif;?>

<?php  if ($this->friends->count() > 1 && $this->friends->count() > $this->page && empty($this->isAjax)): ?>
<div id="pagination_container">
    <div class="seaocore_view_more" id="friend_viewmore" style="display: none;">
        <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => 'friend_viewmore_link',
        'class' => 'buttonlink icon_viewmore'
        ))
        ?>
    </div>

    <div id="friend_loading" style="display: none;" class="seaocore_view_more">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' alt="Loading" />
        <?php echo $this->translate("Loading ...") ?>
    </div>
    <div class="seaocore_view_more" id="friend_noviewmore" style="display: none;">
        <?php echo $this->translate('There are no more friends.'); ?>
    </div>
</div>
<?php endif; ?>

<script type="text/javascript">

    en4.core.runonce.add(function() {
    <?php if ($this->friends->count() > 1 && $this->friends->count() > $this->page): ?>
        if ($('friend_viewmore')) {
            window.onscroll = doOnScrollLoadFriends;
            $('friend_viewmore').style.display = '';
            //$('feed_viewmore').style.display = 'none';
            $('friend_loading').style.display = 'none';
            p
            $('friend_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                event.stop();
                getNextFriends();
            });
        }

    <?php else: ?>
        window.onscroll = '';
    <?php if ($this->page > 1) : ?>
        $('friend_noviewmore').style.display = 'block';
        $('friend_loading').style.display = 'none';
        $('friend_viewmore').style.display = 'none';
    <?php endif; ?>
    <?php endif; ?>
    });

    var doOnScrollLoadFriends = function()
    {
        if ($('friend_viewmore')) {
            if (typeof($('friend_viewmore').offsetParent) != 'undefined') {
                var elementPostionY = $('friend_viewmore').offsetTop;
            } else {
                var elementPostionY = $('friend_viewmore').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                getNextFriends();
            }
        }
    }
</script>
<?php endif;?>
<style>
    h3.sep {
        display: flex;
        justify-content: center;
        font-weight: 600;
    }
</style>