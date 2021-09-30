<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 10128 2014-01-24 18:47:54Z lucas $
 * @author     John
 */
?>
<?php        
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/scripts/core.js');
?>

<?php //include_once APPLICATION_PATH . '/application/modules/Sitemember/views/scripts/infotooltip.tpl'; ?>

<?php $this->headLink()
->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemember/externals/styles/style_sitemember.css')
?>

<?php if ($this->loaded_by_ajax): ?>
  <script type="text/javascript">
    var params = {
      requestParams:<?php echo json_encode($this->params) ?>,
      responseContainer: $$('.layout_sitemember_profile_followers_sitemember')
    }
    en4.sitemember.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
  </script>
<?php endif; ?>

<?php if ($this->showContent): ?>
  <script type="text/javascript">

    function getNextMembers() {

      if($('follower_noviewmore')) {
        $('follower_noviewmore').style.display = 'none';
      }
      if($('follower_loading')) {
        $('follower_loading').style.display = 'block';
      }    
      if($('follower_viewmore_link')) {
        $('follower_viewmore_link').style.display = 'none';
      }

      if($('follower_viewmore')) {
        $('follower_viewmore').style.display = 'none';
      }
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->followers->getCurrentPageNumber() + 1) ?>,
          isAjax : 1,
          search: '<?php echo $this->search;?>',
          loaded_by_ajax: 1,
          showContent: 1,
          is_ajax_load: 1
        },
        onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
          Elements.from(responseHTML).inject($('sitemember_profile_followers'));
          en4.core.runonce.trigger();
        }
      }));
    }  

    en4.core.runonce.add(function() {

      $('sitemember_followers_search_input').addEvent('keypress', function(e) {

        if( e.key != 'enter' ) return;

        if($('follower_noviewmore')) {
          $('follower_noviewmore').style.display = 'none';
        }
        if($('follower_loading')) {
          $('follower_loading').style.display = 'none';
        }    
        if($('follower_viewmore_link')) {
          $('follower_viewmore_link').style.display = 'none';
        }
        
        if($('follower_viewmore')) {
          $('follower_viewmore').style.display = 'none';
        }
        getMembersResults(this.value);
      });
    });  

    function getMembersResults(search) {


      $('sitemember_profile_followers').innerHTML = '<div class="seaocore_content_loader"></div>';
      var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
      en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : search,
          isAjax: 1,
          loaded_by_ajax: 1,
          showContent: 1,
          is_ajax_load: 1
        }, onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
         $('sitemember_profile_followers').innerHTML = responseHTML;
       }
     }));
    }
  </script>
  <?php if(empty($this->isAjax)):?>
    <?php if ($this->followers->getTotalItemCount() > 0 ) :?>

      <div class="sitemember_friends_search b_medium mbot10">
        <div class="sitemember_friends_search_right fright">
          <input id="sitemember_followers_search_input" type="text" value="<?php echo $this->search;?>" onfocus="$(this).store('over', this.value);this.value = '';" onblur="this.value = $(this).retrieve('over');" placeholder="<?php echo $this->translate('Search Followers');?>">
        </div>

      </div><?php endif;?>
    <?php endif;?> 

    <?php if($this->search && $this->isAjax && $this->followers->getTotalItemCount() > 0): ?>
      <div class="mleft10"> <?php echo $this->translate(array('%1$s result for: %2$s', '%1$s results for: %2$s)', $this->followers->getTotalItemCount(), $this->search), '<b>' . $this->followers->getTotalItemCount(). '</b>', '<b>' . $this->search. '</b>'); ?></div>
    <?php endif;?>

    <?php if(!$this->isAjax):?>

      <ul class='profile_followers <?php if(!empty($this->circularImage)):?> sitemember_circular_container <?php endif;?>' id="sitemember_profile_followers">
      <?php endif;?> 
      <?php if ($this->followers->getTotalItemCount() > 0) :?>
        <?php foreach( $this->followers as $follower ):?>
          <?php $sitemember = Engine_Api::_()->getItem('user', $follower['poster_id']); ?>
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
              </div>
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
           <?php echo $this->translate("There are no followers.");?>
         </span>
       </div>
     <?php endif;?>
     <?php if(!$this->isAjax):?>
     </ul>
   <?php endif;?>

   <?php  if ($this->followers->count() > 1 && $this->followers->count() > $this->page && empty($this->isAjax)): ?>
    <div id="pagination_container">
      <div class="seaocore_view_more" id="follower_viewmore" style="display: none;">
        <?php
        echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => 'follower_viewmore_link',
          'class' => 'buttonlink icon_viewmore'
          ))
          ?>
        </div>

        <div id="follower_loading" style="display: none;" class="seaocore_view_more">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
          <?php echo $this->translate("Loading ...") ?>
        </div>
        <div class="seaocore_view_more" id="follower_noviewmore" style="display: none;">
          <?php echo $this->translate('There are no more followers.'); ?>
        </div>
      </div>
    <?php endif; ?>

    <script type="text/javascript">  

     en4.core.runonce.add(function() {
      <?php if ($this->followers->count() > 1 && $this->followers->count() > $this->page): ?>
      if ($('follower_viewmore')) {
        window.onscroll = doOnScrollLoadFollowers;
        $('follower_viewmore').style.display = '';
                //$('feed_viewmore').style.display = 'none';
                $('follower_loading').style.display = 'none';
                $('follower_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                  event.stop();
                  getNextMembers();
                });
              }

            <?php else: ?>
            window.onscroll = '';
            <?php if ($this->page > 1) : ?>
            $('follower_noviewmore').style.display = 'block';
            $('follower_loading').style.display = 'none';
            $('follower_viewmore').style.display = 'none';
          <?php endif; ?>
        <?php endif; ?>
      }); 

     var doOnScrollLoadFollowers = function()
     {
      if ($('follower_viewmore')) {
        if (typeof($('follower_viewmore').offsetParent) != 'undefined') {
          var elementPostionY = $('follower_viewmore').offsetTop;
        } else {
          var elementPostionY = $('follower_viewmore').y;
        }
        if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
          getNextMembers();
        }
      }
    }
  </script>
<?php endif;?>