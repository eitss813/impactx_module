<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagevideo
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
  //include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl';
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';

	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitepagevideo/externals/styles/style_sitepagevideo.css')
?>
<?php if( !$this->video || ($this->video->status!=1)): ?>
	<div class="tip">
		<span>
			<?php echo $this->translate('The video you are looking for does not exist or has not been processed yet.');?>
		</span>
	</div>		
	<?php return; // Do no render the rest of the script in this mode
endif; ?>

<?php if ( $this->video->type == 3 && $this->video_extension == 'mp4' ):
    $this->videoPlayerJs();
?>
<?php endif; ?>
<?php if( $this->video->type == 3 && $this->video_extension == 'flv' ):
    $this->videoPlayerJs();
  ?>
<?php
                                             
       $flowplayerSwf = Engine_Api::_()->sitepagevideo()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('core')->version, '4.8.10') ? 'flowplayer-3.1.5.swf' : 'flowplayer-3.2.18.swf';
       ?>
  <script type='text/javascript'>
    flashembed("video_embed",
    {
      src: "<?php echo $this->layout()->staticBaseUrl ?>externals/flowplayer/<?php echo $flowplayerSwf;?>",
      width: 480,
      height: 386,
      wmode: 'transparent'
    },
    {
      config: {
        clip: {
          url: "<?php echo $this->video_location;?>",
          autoPlay: false,
          duration: "<?php echo $this->video->duration ?>",
          autoBuffering: true
        },
        plugins: {
          controls: {
            background: '#000000',
            bufferColor: '#333333',
            progressColor: '#444444',
            buttonColor: '#444444',
            buttonOverColor: '#666666'
          }
        },
        canvas: {
          backgroundColor:'#000000'
        }
      }
    });
    
  /*var flowplayer = "<?php echo $this->layout()->staticBaseUrl ?>/externals/flowplayer/flowplayer-3.1.5.swf";
  var video_player = new Swiff(flowplayer, {
    width:  320,
    height: 240,
    vars: {
      clip: {
        url: '/engine4/public/video/1000000/1000/68/53.flv',
        autoPlay: false,
        autoBuffering: true
      },
      plugins: {
        controls: {
          background: '#000000',
          bufferColor: '#333333',
          progressColor: '#444444',
          buttonColor: '#444444',
          buttonOverColor: '#666666'
        }
      },
      canvas: {
        backgroundColor:'#000000'
      }
    }
  });
  en4.core.runonce.add(function(){video_player.inject($('video_embed'))});*/

  </script>
<?php endif;?>

<script type="text/javascript">
  var page_id = <?php echo $this->video->page_id;?>;
  var pre_rate = <?php echo $this->video->rating;?>;
  var rated = '<?php echo $this->rated;?>';
  var video_id = <?php echo $this->video->video_id;?>;
  var total_votes = <?php echo $this->rating_count;?>;
  var viewer = <?php echo $this->viewer_id;?>;
  <?php if(empty($this->rating_count)): ?>
  var rating_var =  '<?php echo $this->string()->escapeJavascript($this->translate(" rating")) ?>';
  <?php else: ?>
  var rating_var =  '<?php echo $this->string()->escapeJavascript($this->translate(" ratings")) ?>';
   <?php endif; ?>
   var check_rating = 0;
	var current_total_rate;
  function rating_over(rating) {
    if (rated == 1){
      $('rating_text').innerHTML = "<?php echo $this->translate('you already rated');?>";
      //set_rating();
    }
    else if (viewer == 0){
      $('rating_text').innerHTML = "<?php echo $this->translate('please login to rate');?>";
    }
    else{
      $('rating_text').innerHTML = "<?php echo $this->translate('click to rate');?>";
      for(var x=1; x<=5; x++) {
        if(x <= rating) {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
        } else {
          $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
        }
      }
    }
  }
  function rating_out() {
    $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
    if (pre_rate != 0){
      set_rating();
    }
    else {
      for(var x=1; x<=5; x++) {
        $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
      }
    }
  }

  function set_rating() {
    var rating = pre_rate;
     if(check_rating == 1) {
      if(current_total_rate == 1) {
    	  $('rating_text').innerHTML = current_total_rate+rating_var;
      }
      else {
		  	$('rating_text').innerHTML = current_total_rate+rating_var;
    	}
	  }
	  else {
    	$('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
	  }
    for(var x=1; x<=parseInt(rating); x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big');
    }
    

    for(var x=parseInt(rating)+1; x<=5; x++) {
      $('rate_'+x).set('class', 'rating_star_big_generic rating_star_big_disabled');
    }

    var remainder = Math.round(rating)-rating;
    if (remainder <= 0.5 && remainder !=0){
      var last = parseInt(rating)+1;
      $('rate_'+last).set('class', 'rating_star_big_generic rating_star_big_half');
    }
  }
  
  function rate(rating) {
    $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating!');?>";
    for(var x=1; x<=5; x++) {
      $('rate_'+x).set('onclick', '');
    }
    (new Request.JSON({
      'format': 'json',
      'url' : '<?php echo $this->url(array('module' => 'sitepagevideo', 'controller' => 'index', 'action' => 'rate'), 'default', true) ?>',
      'data' : {
        'format' : 'json',
        'rating' : rating,
        'video_id': video_id,
        'page_id' : page_id
      },
      'onRequest' : function(){
        rated = 1;
        total_votes = total_votes+1;
        pre_rate = (pre_rate+rating)/total_votes;
        set_rating();
      },
      'onSuccess' : function(responseJSON, responseText)
      {
         $('rating_text').innerHTML = responseJSON[0].total+rating_var;
         current_total_rate =  responseJSON[0].total;
         check_rating = 1;
      }
    })).send();
    
  }
  
  var tagAction =function(tag, url){
    $('tag').value = tag;
    window.location.href = url;
  }

  en4.core.runonce.add(set_rating);
  

</script>


<form id='filter_form' class='global_form_box' method='get' style='display:none;'>
  <input type="hidden" id="tag" name="tag" value=""/>
</form>

<div class="sitepage_viewpages_head">
	<?php echo $this->htmlLink($this->sitepage->getHref(), $this->itemPhoto($this->sitepage, 'thumb.icon', '', array('align' => 'left'))) ?>
	<h2>
	  <?php echo $this->sitepage->__toString() ?>
	  <?php echo $this->translate('&raquo;');?>
     <?php echo $this->htmlLink($this->sitepage->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Videos')) ?>
	  <?php echo $this->translate('&raquo;');?>
	  <?php echo $this->video->title ?>
	</h2>
</div>

<div class="sitepagevideo_view">
  <h3>
    <?php echo $this->video->title;?>
  </h3>

  <div class="sitepagevideo_date">
    <?php echo $this->translate('Posted by') ?>
    <?php echo $this->htmlLink($this->video->getOwner(), $this->video->getOwner()->getTitle()) ?>
  </div>
  <div class="video_desc">
    <?php echo nl2br($this->video->description);?>
  </div>
    <!--FACEBOOK LIKE BUTTON START HERE-->
    
     <?php  $fbmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('facebookse');
      if (!empty ($fbmodule)) :
        $enable_facebookse = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('facebookse'); 
        if (!empty ($enable_facebookse) && !empty($fbmodule->version)) :
          $fbversion = $fbmodule->version; 
          if (!empty($fbversion) && ($fbversion >= '4.1.5')) { ?>
             <div class="sitepagevideo_fb_like">
                <script type="text/javascript">
                    var fblike_moduletype = 'sitepagevideo_video';
		                var fblike_moduletype_id = '<?php echo $this->video->video_id ?>';
                 </script>
                <?php echo Engine_Api::_()->facebookse()->isValidFbLike(); ?>
              </div>
          
          <?php } ?>
        <?php endif; ?>
   <?php endif; ?>
  <?php if( $this->video->type == 3): ?>
		<div id="video_embed" class="sitepagevideo_embed">
       <?php if ($this->video_extension !== 'flv'): ?>
      <video id="sitepagevideo" controls preload="auto" width="480" height="386">
        <source type='video/mp4;' src="<?php echo $this->video_location ?>">
      </video>
    <?php endif ?>
		</div>
  <?php else: ?>
		<div class="sitepagevideo_embed">
			<?php echo $this->videoEmbedded;?>
		</div>
  <?php endif; ?>
  <div class="sitepagevideo_date">
    <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($this->video->creation_date) ?>
     <span class="video_views">- <?php echo $this->translate(array('%s comment', '%s ', $this->video->comments()->getCommentCount()),$this->locale()->toNumber($this->video->comments()->getCommentCount())) ?>	-  
			<?php echo $this->translate(array('%s view', '%s views', $this->video->view_count ), $this->locale()->toNumber($this->video->view_count )) ?>
     - <?php echo $this->translate(array('%s like', '%s likes', $this->video->likes()->getLikeCount()),$this->locale()->toNumber($this->video->likes()->getLikeCount())) ?>
     </span>

    <?php if ($this->category): ?>
      - <?php echo $this->translate('Filed in');?>
      <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'>
          <?php echo $this->translate($this->category->category_name) ?>
      </a>
    <?php endif; ?>

    <?php if (count($this->videoTags )):?>
      -
      <?php  foreach ($this->videoTags as $tag): ?>
       <a href='javascript:void(0);' onclick="javascript:tagAction('<?php echo $tag->getTag()->tag_id; ?>', '<?php echo $this->url(array('tag' => $tag->getTag()->tag_id), 'sitepagevideo_browse', true); ?>');">
        <?php if(!empty($tag->getTag()->text)):?>#<?php endif;?><?php echo $tag->getTag()->text?></a>&nbsp;
     <?php endforeach; ?>
    <?php  endif; ?>
  </div>
  <div id="video_rating" class="rating" onmouseout="rating_out();">
    <span id="rate_1" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(1);"<?php endif; ?> onmouseover="rating_over(1);"></span>
    <span id="rate_2" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
    <span id="rate_3" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
    <span id="rate_4" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
    <span id="rate_5" class="rating_star_big_generic" <?php if (!$this->rated && $this->viewer_id):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
    <span id="rating_text" class="rating_text"><?php echo $this->translate('click to rate');?></span>
  </div>

  <div class='sitepage_video_options'>
			<!--  Start: Suggest to Friend link show work -->
			<?php if( !empty($this->videoSuggLink) && !empty($this->video->search) && !empty($this->video->status) ): ?>				
				<?php echo $this->htmlLink(array('route' => 'default', 'module' => 'suggestion', 'controller' => 'index', 'action' => 'popups', 'sugg_id' => $this->video->video_id, 'sugg_type' => 'page_video'), $this->translate('Suggest to Friends'), array(
					'class'=>'buttonlink  icon_page_friend_suggestion smoothbox')); ?> &nbsp; | &nbsp;			
			<?php endif; ?>					
			<!--  End: Suggest to Friend link show work -->
  	<?php if($this->can_create):?>
				<a href='<?php echo $this->url(array('page_id' => $this->sitepage->page_id,'tab' => $this->tab_selected_id),'sitepagevideo_create', true) ?>' class='buttonlink icon_type_sitepagevideo_new'><?php echo $this->translate('Add Video');?></a>&nbsp; | &nbsp;
		<?php endif; ?>   
		<?php if($this->video->owner_id == $this->viewer_id || $this->can_edit == 1): ?>
			<?php echo $this->htmlLink(array('route' => 'sitepagevideo_edit', 'video_id' => $this->video->video_id,'page_id'=>$this->sitepage->page_id,'tab'=>$this->tab_selected_id), $this->translate('Edit Video'), array(
						'class' => 'buttonlink icon_type_sitepagevideo_edit'
					)) ?>&nbsp; | &nbsp;

			<?php  echo $this->htmlLink(array('route' => 'sitepagevideo_delete', 'video_id' => $this->video->video_id,'page_id'=> $this->sitepage->page_id,'tab'=> $this->tab_selected_id), $this->translate('Delete Video'), array(
							'class' => 'buttonlink icon_type_sitepagevideo_delete'
						));?> &nbsp; | &nbsp;
    <?php elseif($this->can_create):?>
    <?php endif; ?>
    <?php echo $this->htmlLink(Array('module'=> 'activity', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => 'sitepagevideo_video', 'id' => $this->video->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox buttonlink icon_type_sitepagevideo_share')); ?>&nbsp; | &nbsp;

    <?php if( $this->can_embed ): ?>
		<?php echo $this->htmlLink(Array('module'=> 'sitepagevideo', 'controller' => 'video', 'action' => 'embed', 'route' => 'default', 'id' => $this->video->getIdentity(), 'format' => 'smoothbox'), $this->translate("Embed"), array('class' => 'smoothbox buttonlink icon_type_sitepagevideo_embed')); ?>&nbsp; | &nbsp; 
	  <?php endif ?>
    
		<?php if($this->allowView ): ?>    
			<?php echo $this->htmlLink(array('route' => 'default','module'=> 'sitepagevideo', 'controller'=>'index','action' => 'add-video-of-day', 'video_id' => $this->video->video_id, 'format' => 'smoothbox'), $this->translate('Make Video of the Day'), array(
			'class' => 'buttonlink smoothbox item_icon_sitepagevideo_video'
		)) ?>
      &nbsp; | &nbsp;
		<?php endif;?>

   <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' =>  $this->video->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox buttonlink icon_type_sitepagevideo_report')); ?>
  </div>

	<?php 
        include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/_listComment.tpl';
    ?>

</div>

<script type="text/javascript">
  function featured(video_id)
  {
    en4.core.request.send(new Request.HTML({
      method : 'post',
      'url' : en4.core.baseUrl + 'sitepage/index/featured',
      'data' : {
        format : 'html',
        'video_id' : video_id
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {       
       if($('featured_sitepagevideo').style.display=='none'){
        $('featured_sitepagevideo').style.display="";
        $('un_featured_sitepagevideo').style.display="none";
       }else{
          $('un_featured_sitepagevideo').style.display="";
        $('featured_sitepagevideo').style.display="none";
       }
      }
    }));

    return false;

  }
</script>
