<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: compose.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    Messages
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>

<script type="text/javascript">

	sm4.core.runonce.add(function() {
		 $('#toValues-wrapper').css('display', 'none');
		 showprofileOption(0);
  });

  function showprofileOption(options) {
		if(options == 0) {
			$('#user_ids-wrapper').css('display', 'none');
		}
		else if(options == 1) {
			$('#user_ids-wrapper').css('display', 'block');
		}
  }
 var feedURL = '<?php echo $this->url() ?>';
  var composeInstance;
  //sm4.core.runonce.add(function() {
 
    // @todo integrate this into the composer
    if ( '<?php 
         $id = Engine_Api::_()->user()->getViewer()->level_id;
         echo Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('messages', $id, 'editor');
         ?>' == 'plaintext' ) { 
          //sm4.activity.initialize($('#body'), false);
          //photo upload URL:
         var requestOptions = {
            'photourl'  : sm4.core.baseUrl + 'album/album/compose-upload/type/wall',
            'videourl'  : sm4.core.baseUrl + 'video/index/compose-upload/format/json/c_type/wall',
            'videodeleteurl'  : sm4.core.baseUrl + 'video/index/delete'
            
            }
            sm4.activity.composer.init(requestOptions);
    }
//     $('#user_ids').remove();

  //});
   
</script>


<?php echo $this->form->render($this) ?>

<?php //ATTACH THE LINKS ALSO. ?>
<div id="activitypost-container-message">
  <div id="composer-options">
    <div id="smactivityoptions-popup" class="sm-post-composer-options">
      <ul class="share-item">
        <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
          <li>
            <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'photo');" class="ui-link-inherit">
              <i class="cm-icons cm-icon-photo"></i>
              <span><?php echo $this->translate('Add Photo'); ?></span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (Engine_Api::_()->sitemobile()->enableComposer('video')) : ?>
          <li>
            <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'video');" class="ui-link-inherit">
              <i class="cm-icons cm-icon-video"></i>
              <span><?php echo $this->translate('Add Video') ?></span>
            </a>
          </li>
        <?php endif; ?>
        <?php if (Engine_Api::_()->sitemobile()->enableComposer('link')) : ?>
          <li>
            <a href="javascript:void(0);" onclick="return sm4.activity.composer.showPluginForm(this, 'link');" class="ui-link-inherit">
              <i class="cm-icons cm-icon-link"></i>
              <span><?php echo $this->translate('Add Link'); ?></span>
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>     
  </div>
</div>

<script type="text/javascript">
  
  // Populate data
  var maxRecipients = <?php echo sprintf("%d", $this->maxRecipients) ?> || 10;
  var to = {
    id : false,
    type : false,
    guid : false,
    title : false
  };
  var isPopulated = false;

  <?php if( !empty($this->isPopulated) && !empty($this->toObject) ): ?>
    isPopulated = true;
    to = {
      id : <?php echo sprintf("%d", $this->toObject->getIdentity()) ?>,
      type : '<?php echo $this->toObject->getType() ?>',
      guid : '<?php echo $this->toObject->getGuid() ?>',
      title : '<?php echo $this->string()->escapeJavascript($this->toObject->getTitle()) ?>'
    };
  <?php endif; ?>

  sm4.core.runonce.add(function() {
    $.mobile.activePage.find('#activitypost-container-message').after($.mobile.activePage.find('#submit-wrapper'))    
    if ($.type($('#activitypost-container-message').get(0)) != 'undefined')
      $('#activitypost-container-temp').remove();
    $('#activitypost-container-message').attr('id', 'activitypost-container-temp');
    
    $.mobile.activePage.find('#submit-wrapper').off('click').on('click', function () {
      $.mobile.activePage.find('#activitypost-container-temp').prev().submit();

    });
    sm4.activity.initialize($.mobile.activePage.find('#body'), false);

    if( !isPopulated ) { 
			 var url ='<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest'), 'default', true) ?>';
			 $(this).delay(300).queue(function(){
$.mobile.activePage.find('#toValues-wrapper').css('display', 'none');
sm4.core.Module.autoCompleter.attach("user_ids", url, {'singletextbox': false, 'limit':10, 'minLength': 1, 'showPhoto' : true, 'search' : 'search'}, 'toValues');
       $(this).clearQueue();});
    } else {
				var span = $("<span>").attr('id', 'tospan' + to.id).attr('class', 'tag tag_' + to.type).text(to.title)
				$('#to-element').append(span);
				$('#to-wrapper').css('height', 'auto');
				// Hide to input?
				$('#to').css('display', 'none');
				$.mobile.activePage.find('#toValues-wrapper').css('display', 'none');
    }
    
     $.mobile.activePage.find('#messages_compose').off('submit').on('submit', function () {
       $('#activitypost-container-temp').remove();
    });
    
  });
    
</script>