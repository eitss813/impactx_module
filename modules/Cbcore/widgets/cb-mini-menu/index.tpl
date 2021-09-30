<?php
/**
 * SocialEngine
 *
 * @category   Module
 * @package    Consecutive Bytes Core
 * @copyright  Copyright 2015 - 2017 Consecutive Bytes
 * @license    http://www.consecutivebytes.com/license/
 * @version    4.9.0
 * @author     Consecutive Bytes
 */
 ?>

<div id="qc_logo">
<?php
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$logo  = $this->logo;
$route = $this->viewer()->getIdentity()
             ? array('route'=>'user_general', 'action'=>'home')
             : array('route'=>'default');

echo ($logo)
     ? $this->htmlLink($route, $this->htmlImage($logo, array('alt'=>$title)))
     : $this->htmlLink($route, $title);
?>
</div>
<div id='core_menu_mini_menu'>
  <?php
    // Reverse the navigation order (they're floating right)
    $count = count($this->navigation);
    foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
  ?>
  <div id="qc_right_panel" title="Mini Menu">
  
  </div>
  <ul>
    <?php if( $this->viewer->getIdentity()) :?>
    <li id='core_menu_mini_menu_update'>
      <span onclick="toggleUpdatesPulldown(event, this, '4');" style="display: inline-block;" class="updates_pulldown">
        <div class="pulldown_contents_wrapper">
          <div class="pulldown_contents">
            <ul class="notifications_menu" id="notifications_menu">
              <div class="notifications_loading" id="notifications_loading">
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
                <?php echo $this->translate("Loading ...") ?>
              </div>
            </ul>
          </div>
          <div class="pulldown_options">
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
               $this->translate('View All Updates'),
               array('id' => 'notifications_viewall_link')) ?>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
              'id' => 'notifications_markread_link',
            )) ?>
          </div>
        </div>
        <a href="javascript:void(0);" id="updates_toggle" <?php if( $this->notificationCount ):?> class="new_updates"<?php endif;?>>
        <p class="notification_count"><?php echo $this->translate($this->locale()->toNumber($this->notificationCount)) ?></p>
        </a>
      </span>
    </li>
    <?php endif; ?>
    <?php foreach( $this->navigation as $item ): ?>
      <li<?php if( !$this->viewer->getIdentity() ):?> class="hide_before_login"<?php endif;?> title="<?php echo $item->label;?>"><?php echo $this->htmlLink($item->getHref(), $this->translate(''), array_filter(array(
        'class' => ( !empty($item->class) ? $item->class : null ),
        'alt' => ( !empty($item->alt) ? $item->alt : null ),
        'target' => ( !empty($item->target) ? $item->target : null ),
      ))) ?></li>
    <?php endforeach; ?>
    <?php if($this->search_check):?>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
        </form>
      </li>
    <?php endif;?>
      <?php if( !$this->viewer->getIdentity()) :?>
    	<li id="qc_login">
        	<a href="javascript:void();" class="menu_core_mini core_mini_login" title="Login"></a>
            <div id="qc_login_form">
            	<?php echo $this->content()->renderWidget('user.login-or-signup'); ?>
            </div>
            <div id="qc_login_overlay">
            </div>
        </li>
        <li id="qc_signup">
        	<a href="<?php echo $this->baseUrl(); ?>/signup" class="menu_core_mini   core_mini_signup" title="Sign Up"></a>
        </li>
    <?php endif; ?>
  </ul>

</div>


<script type='text/javascript'>
  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }

    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>
  });


  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }

  var showNotifications = function() {
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };

  /*
  function focusSearch() {
    if(document.getElementById('global_search_field').value == 'Search') {
      document.getElementById('global_search_field').value = '';
      document.getElementById('global_search_field').className = 'text';
    }
  }
  function blurSearch() {
    if(document.getElementById('global_search_field').value == '') {
      document.getElementById('global_search_field').value = 'Search';
      document.getElementById('global_search_field').className = 'text suggested';
    }
  }
  */
</script>
<script type="text/javascript">
	$$('#qc_login a').addEvent('click',function(){
		$$('#qc_login_form').set('tween', {transition: Fx.Transitions.linear,duration:'short'});
		$$('#qc_login_form').tween('right', 65);
			$$('#qc_login').toggleClass('qc_login_active');		
		});
		$$('#qc_login_overlay').addEvent('click',function(){
			$$('#qc_login_form').set('tween', {transition: Fx.Transitions.linear,duration:'short'});
			$$('#qc_login_form').tween('right', -400);
			$$('#qc_login').toggleClass('qc_login_active');
		});
	$$('#qc_right_panel').addEvent('click',function(){
		$$('#core_menu_mini_menu').toggleClass('qc_right_panel_active');
	});
	$$('#core_menu_mini_menu_update > span > a').addEvent('click',function(){
		$$('#core_menu_mini_menu').toggleClass('qc_updates_active');
	});
</script>