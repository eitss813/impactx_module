<?php

/**
 * socialnetworking.solutions
 *
 * @category   Application_Modules
 * @package    Sesblog
 * @copyright  Copyright 2014-2020 Ahead WebSoft Technologies Pvt. Ltd.
 * @license    https://socialnetworking.solutions/license/
 * @version    $Id: create.tpl 2016-07-23 00:00:00 socialnetworking.solutions $
 * @author     socialnetworking.solutions
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/scripts/core.js'); ?> 

<?php if(!$this->typesmoothbox){ ?>
  <?php
    if (APPLICATION_ENV == 'production')
      $this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
    else
      $this->headScript()
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
          ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
  ?>
  <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'); ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js'); ?>

<?php } else { ?>

  <script type="application/javascript">

    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl .'externals/autocompleter/Observer.js'; ?>");
    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.js' ?>");
    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Local.js'; ?>");
    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl .'externals/autocompleter/Autocompleter.Request.js'; ?>");

    Sessmoothbox.css.push("<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesblog/externals/styles/styles.css'; ?>");
    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'; ?>");
    //Sessmoothbox.javascript.push("<?php //echo $this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/sesJquery.js'; ?>");
    Sessmoothbox.javascript.push("<?php echo $this->layout()->staticBaseUrl . 'externals/tinymce/tinymce.min.js'; ?>");

  </script>

<?php } ?>


<?php
$mainPhotoEnable = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog.photo.mandatory', '1');
if ($mainPhotoEnable == 1) {
	$required = true; ?>
  <style type='text/css'>
  .sesblog_create #tabs_form_blogcreate-label label:after{content: '*';color: #F00;}
  </style>
<?php } else {
	$required = false;
}
?>

<script type="text/javascript">

  function removeLastMinus (myUrl) {
    if (myUrl.substring(myUrl.length-1) == "-") {
      myUrl = myUrl.substring(0, myUrl.length-1);
    }
    return myUrl;
  }
  var changeTitle = true;

  en4.core.runonce.add(function() {

    //auto fill custom url value
    sesJqueryObject("#title").keyup(function(){
      var Text = sesJqueryObject(this).val();
      if(!changeTitle)
      return;
      Text = Text.toLowerCase();
      Text = Text.replace(/[^a-zA-Z0-9]+/g,'-');
      Text = removeLastMinus(Text);
      sesJqueryObject("#custom_url").val(Text);
    });
    sesJqueryObject("#title").blur(function(){
      if(sesJqueryObject(this).val()){
        changeTitle = false;
      }
    });
  });

  function showHideHeight(value) {
    if(value == 1) {
      sesJqueryObject('#continue_height-wrapper').show();
    } else {
      sesJqueryObject('#continue_height-wrapper').hide();
    }
  }

  function checkAvailsbility(submitform) {
    var custom_url_value = jqueryObjectOfSes('#custom_url').val();
    if(!custom_url_value && typeof submitform == 'undefined')
    return;
    jqueryObjectOfSes('#sesblog_custom_url_wrong').hide();
    jqueryObjectOfSes('#sesblog_custom_url_correct').hide();
    jqueryObjectOfSes('#sesblog_custom_url_loading').css('display','block');
    jqueryObjectOfSes('#check_custom_url_availability').html('');
    jqueryObjectOfSes.post('<?php echo $this->url(array('controller' => 'index','module'=>'sesblog', 'action' => 'custom-url-check'), 'default', true) ?>',{value:custom_url_value},function(response){
      jqueryObjectOfSes('#sesblog_custom_url_loading').hide();
      jqueryObjectOfSes('#check_custom_url_availability').html('Check Availability');
      response = jqueryObjectOfSes.parseJSON(response);
      if(response.error){
        jqueryObjectOfSes('#sesblog_custom_url_correct').hide();
        jqueryObjectOfSes('#sesblog_custom_url_wrong').css('display','block');
        if(typeof submitform != 'undefined') {
          alert('<?php echo $this->translate("Custom Url is not available. Please select another URL."); ?>');
          var errorFirstObject = jqueryObjectOfSes('#custom_url').parent().parent();
          jqueryObjectOfSes('html, body').animate({
          scrollTop: errorFirstObject.offset().top
          }, 2000);
        }
      } else{
        jqueryObjectOfSes('#custom_url').val(response.value);
        jqueryObjectOfSes('#sesblog_custom_url_wrong').hide();
        jqueryObjectOfSes('#sesblog_custom_url_correct').css('display','block');
        if(typeof submitform != 'undefined') {
          jqueryObjectOfSes('#upload').attr('disabled',true);
          jqueryObjectOfSes('#upload').html('<?php echo $this->translate("Submitting Form ...") ; ?>');
          jqueryObjectOfSes('#submit_check').trigger('click');
        }
      }
    });
  }

  en4.core.runonce.add(function() {

    if(jqueryObjectOfSes('#show_start_time') && jqueryObjectOfSes('input[name="show_start_time"]:checked').val() == '1')
    sesJqueryObject('#event_start_time-wrapper').hide();

    jqueryObjectOfSes('#submit_check-wrapper').hide();

    //function ckeck url availability
    jqueryObjectOfSes('#check_custom_url_availability').click(function(){
      checkAvailsbility();
    });

    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
      'postVar' : 'text',
      'customChoices' : true,
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });

  <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('enableglocation', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sesblog_enable_location', 1)){ ?>
    en4.core.runonce.add(function() {
      jqueryObjectOfSes('#lat-wrapper').css('display' , 'none');
      jqueryObjectOfSes('#lng-wrapper').css('display' , 'none');
      jqueryObjectOfSes('#mapcanvas-element').attr('id','map-canvas');
      jqueryObjectOfSes('#map-canvas').css('height','200px');
      jqueryObjectOfSes('#map-canvas').css('width','500px');
      sesJqueryObject('#mapcanvas-wrapper').hide();
      jqueryObjectOfSes('#ses_location-label').attr('id','ses_location_data_list');
      jqueryObjectOfSes('#ses_location_data_list').html("<?php echo isset($_POST['location']) ? $_POST['location'] : '' ; ?>");
      jqueryObjectOfSes('#ses_location-wrapper').css('display','none');
      initializeSesBlogMap();
    });
  <?php } ?>
</script>
<?php if (($this->current_count >= $this->quota) && !empty($this->quota)):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('You have already uploaded the maximum number of entries allowed.');?>
      <?php echo $this->translate('If you would like to upload a new entry, please <a href="%1$s">delete</a> an old one first.', $this->url(array('action' => 'manage'), 'sesblog_general'));?>
    </span>
  </div>
  <br/>
<?php else:?>
<div class="sesblog_default_form sesbasic_bxs">
  <?php echo $this->form->render($this);?></div>
<?php endif; ?>

<script type="text/javascript">
  $$('.core_main_sesblog').getParent().addClass('active');
</script>

<?php
$defaultProfileFieldId = "0_0_$this->defaultProfileId";
$profile_type = 2;
?>

<?php echo $this->partial('_customFields.tpl', 'sesblog', array()); ?>

<script type="application/javascript">
jqueryObjectOfSes('#rotation-wrapper').hide();
jqueryObjectOfSes('#embedUrl-wrapper').hide();
function enablePasswordFiled(value) {
  if(value == 0)
  jqueryObjectOfSes('#password-wrapper').hide();
  else
  jqueryObjectOfSes('#password-wrapper').show();
}
jqueryObjectOfSes('#password-wrapper').hide();
</script>

<script type="text/javascript">

  var defaultProfileFieldId = '<?php echo $defaultProfileFieldId ?>';
  var profile_type = '<?php echo $profile_type ?>';
  var previous_mapped_level = 0;
  function showFields(cat_value, cat_level,typed,isLoad) {
		if(sesJqueryObject('#custom_fields_enable').length > 0)
			return;
    var categoryId = getProfileType($('category_id').value);
    var subcatId = getProfileType($('subcat_id').value);
    var subsubcatId = getProfileType($('subsubcat_id').value);
    var type = categoryId+','+subcatId+','+subsubcatId;
    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value);
      if (profile_type == 0)
      profile_type = '';
      else
      previous_mapped_level = cat_level;
      $(defaultProfileFieldId).value = profile_type;
      changeFields($(defaultProfileFieldId),null,isLoad,type);
    }
  }

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sesblog')->getMapping(array('category_id', 'profile_type'))); ?>;
    for (i = 0; i < mapping.length; i++) {
      if (mapping[i].category_id == category_id)
      return mapping[i].profile_type;
    }
    return 0;
  }

  en4.core.runonce.add(function() {
    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
     if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
      $(defaultProfileId).setStyle('display', 'none');
    }
  });

  function showSubCategory(cat_id,selectedId) {
    var selected;
    if(selectedId != '')
    var selected = selectedId;
    var url = en4.core.baseUrl + 'sesblog/index/subcategory/category_id/' + cat_id;
    new Request.HTML({
      url: url,
      data: {'selected':selected},
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

	  if ($('subcat_id') && responseHTML) {
	    if ($('subcat_id-wrapper')) {
	      $('subcat_id-wrapper').style.display = "block";
	    }
	    $('subcat_id').innerHTML = responseHTML;
	  } else {
	    if ($('subcat_id-wrapper')) {
	      $('subcat_id-wrapper').style.display = "none";
	      $('subcat_id').innerHTML = '<option value="0"></option>';
	    }
	  }
	  if ($('subsubcat_id-wrapper')) {
	    $('subsubcat_id-wrapper').style.display = "none";
	    $('subsubcat_id').innerHTML = '<option value="0"></option>';
	  }

	showFields(cat_id,1);
      }
    }).send();
  }

  function showSubSubCategory(cat_id,selectedId,isLoad) {
    var categoryId = getProfileType($('category_id').value);
    if(cat_id == 0){

      if ($('subsubcat_id-wrapper')) {
        $('subsubcat_id-wrapper').style.display = "none";
        $('subsubcat_id').innerHTML = '';
        if(typeof document.getElementsByName("0_0_1")[0] != 'undefined')
        document.getElementsByName("0_0_1")[0].value=categoryId;
      }

      showFields(cat_id,1,categoryId);
      return false;
    }

    showFields(cat_id,1,categoryId);
    var selected;
    if(selectedId != '')
    var selected = selectedId;
    var url = en4.core.baseUrl + 'sesblog/index/subsubcategory/subcategory_id/' + cat_id;
    (new Request.HTML({
      url: url,
      data: {'selected':selected},
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

        if ($('subsubcat_id') && responseHTML) {
          if ($('subsubcat_id-wrapper')) {
            $('subsubcat_id-wrapper').style.display = "block";
          }
          $('subsubcat_id').innerHTML = responseHTML;
          // get category id value
          if(isLoad == 'no')
          showFields(cat_id,1,categoryId,isLoad);
        } else {
          if ($('subsubcat_id-wrapper')) {
            $('subsubcat_id-wrapper').style.display = "none";
            $('subsubcat_id').innerHTML = '<option value="0"></option>';
          }
        }
      }
    })).send();
  }

  function showCustom(value,isLoad){

    var categoryId = getProfileType($('category_id').value);
    var subcatId = getProfileType($('subcat_id').value);
    var id = categoryId+','+subcatId;
    showFields(value,1,id,isLoad);
    if(value == 0 && typeof document.getElementsByName("0_0_1")[0] != 'undefined')
    document.getElementsByName("0_0_1")[0].value=subcatId;
    return false;
  }

  function showCustomOnLoad(value,isLoad) {
    <?php if(isset($this->category_id) && $this->category_id != 0){ ?>
      var categoryId = getProfileType(<?php echo $this->category_id; ?>)+',';
    <?php if(isset($this->subcat_id) && $this->subcat_id != 0){ ?>
      var subcatId = getProfileType(<?php echo $this->subcat_id; ?>)+',';
    <?php  }else{ ?>
      var subcatId = '';
    <?php } ?>
    <?php if(isset($this->subsubcat_id) && $this->subsubcat_id != 0){ ?>
      var subsubcat_id = getProfileType(<?php echo $this->subsubcat_id; ?>)+',';
    <?php  }else{ ?>
      var subsubcat_id = '';
    <?php } ?>
      var id = (categoryId+subcatId+subsubcat_id).replace(/,+$/g,"");;
      showFields(value,1,id,isLoad);
    if(value == 0 && typeof document.getElementsByName("0_0_1")[0] != 'undefined')
    document.getElementsByName("0_0_1")[0].value=subcatId;
    return false;
    <?php } ?>
  }

  en4.core.runonce.add(function() {
    var sesdevelopment = 1;
    <?php if(isset($this->category_id) && $this->category_id != 0){ ?>
        <?php if(isset($this->subcat_id)){$catId = $this->subcat_id;}else $catId = ''; ?>
        showSubCategory('<?php echo $this->category_id; ?>','<?php echo $catId; ?>','yes');
     <?php  }else{ ?>
      if($('subcat_id-wrapper'))
        $('subcat_id-wrapper').style.display = "none";
     <?php } ?>

     <?php if(isset($this->subsubcat_id)){ ?>
      if (<?php echo isset($this->subcat_id) && intval($this->subcat_id)>0 ? $this->subcat_id : 'sesdevelopment' ?> == 0) {
       $('subsubcat_id-wrapper').style.display = "none";
      } else {
        <?php if(isset($this->subsubcat_id)){$subsubcat_id = $this->subsubcat_id;}else $subsubcat_id = ''; ?>
        showSubSubCategory('<?php echo $this->subcat_id; ?>','<?php echo $this->subsubcat_id; ?>','yes');
      }
     <?php }else{ ?>
         $('subsubcat_id-wrapper').style.display = "none";
     <?php } ?>
        showCustomOnLoad('','no');
  });

  //prevent form submit on enter
  jqueryObjectOfSes("#sesblogs_create").bind("keypress", function (e) {
    if (e.keyCode == 13 && jqueryObjectOfSes('#'+e.target.id).prop('tagName') != 'TEXTAREA') {
      e.preventDefault();
    }else{
      return true;
    }
  });

  //Ajax error show before form submit
  var error = false;
  var objectError ;
  var counter = 0;
  function validateForm() {
    var errorPresent = false;
    jqueryObjectOfSes('#sesblogs_create input, #sesblogs_create select,#sesblogs_create checkbox,#sesblogs_create textarea,#sesblogs_create radio').each(
      function(index){
	var input = jqueryObjectOfSes(this);
	if(jqueryObjectOfSes(this).closest('div').parent().css('display') != 'none' && jqueryObjectOfSes(this).closest('div').parent().find('.form-label').find('label').first().hasClass('required') && jqueryObjectOfSes(this).prop('type') != 'hidden' && jqueryObjectOfSes(this).closest('div').parent().attr('class') != 'form-elements'){
	  if(jqueryObjectOfSes(this).prop('type') == 'checkbox'){
	    value = '';
	    if(jqueryObjectOfSes('input[name="'+jqueryObjectOfSes(this).attr('name')+'"]:checked').length > 0) {
	      value = 1;
	    };
	    if(value == '')
	    error = true;
	    else
	    error = false;
	  }
	  else if(jqueryObjectOfSes(this).prop('type') == 'select-multiple'){
	    if(jqueryObjectOfSes(this).val() === '' || jqueryObjectOfSes(this).val() == null)
	    error = true;
	    else
	    error = false;
	  }
	  else if(jqueryObjectOfSes(this).prop('type') == 'select-one' || jqueryObjectOfSes(this).prop('type') == 'select' ){
	    if(jqueryObjectOfSes(this).val() === '')
	    error = true;
	    else
	    error = false;
	  }
	  else if(jqueryObjectOfSes(this).prop('type') == 'radio'){
	    if(jqueryObjectOfSes("input[name='"+jqueryObjectOfSes(this).attr('name').replace('[]','')+"']:checked").val() === '')
	    error = true;
	    else
	    error = false;
	  }
	  else if(jqueryObjectOfSes(this).prop('type') == 'textarea' && jqueryObjectOfSes(this).prop('id') == 'body'){

	  }
	  else if(jqueryObjectOfSes(this).prop('type') == 'textarea') {
	    if(jqueryObjectOfSes(this).val() === '' || jqueryObjectOfSes(this).val() == null)
	    error = true;
	    else
	    error = false;
	  }
	  else{
	    if(jqueryObjectOfSes(this).val() === '' || jqueryObjectOfSes(this).val() == null)
	    error = true;
	    else
	    error = false;
	  }
	  if(error){
	    if(counter == 0){
	      objectError = this;
	    }
	    counter++
	  }
	  else{
			if(sesJqueryObject('#tabs_form_blogcreate-wrapper').length && sesJqueryObject('.sesblog_upload_item_photo').length == 0){
				<?php if($required):?>
					objectError = sesJqueryObject('.sesblog_create_form_tabs');
					error = true;
				<?php endif;?>
			}
	  }
	  if(error)
			errorPresent = true;
			error = false;
		}
      }
    );
    return errorPresent ;
  }

en4.core.runonce.add(function() {
  jqueryObjectOfSes('#sesblogs_create').submit(function(e) {
    var validationFm = validateForm();
    if(!validationFm) {
			var lastTwoDigitStart = sesJqueryObject('#sesblog_schedule_time').val().slice('-2');
			var startDate = new Date(sesJqueryObject('#sesblog_schedule_date').val()+' '+sesJqueryObject('#sesblog_schedule_time').val().replace(lastTwoDigitStart,'')+':00 '+lastTwoDigitStart);
			var error = checkDateTime(startDate);
			if(error != ''){
				sesJqueryObject('#event_error_time-wrapper').show();
				sesJqueryObject('#sesblog_schedule_error_time-element').text(error);
			 var errorFirstObject = sesJqueryObject('#event_start_time-label').parent().parent();
			 sesJqueryObject('html, body').animate({
				scrollTop: errorFirstObject.offset().top
			 }, 2000);
				return false;
			}else{
				sesJqueryObject('#event_error_time-wrapper').hide();
			}

		}
    if(validationFm) {
      alert('<?php echo $this->translate("Please fill the red mark fields"); ?>');
      if(typeof objectError != 'undefined'){
				var errorFirstObject = jqueryObjectOfSes(objectError).parent().parent();
				jqueryObjectOfSes('html, body').animate({
				scrollTop: errorFirstObject.offset().top
				}, 2000);
      }
      return false;
    }else if(sesJqueryObject('.sesblog_upload_item_abort').length){
				alert('<?php echo $this->translate("Please wait till all photos uploaded."); ?>');
				var errorFirstObject = jqueryObjectOfSes('#uploadFileContainer-wrapper');
				jqueryObjectOfSes('html, body').animate({
					scrollTop: errorFirstObject.offset().top
				}, 2000);
				return false;
		}
//     else{
//       var avacheckAvailsbility = checkAvailsbility('true');
//       return false;
//     }
  });
});
</script>

<script type="text/javascript">

en4.core.runonce.add(function() {
  jqueryObjectOfSes('#dragdrop-wrapper').show();
  jqueryObjectOfSes('#fromurl-wrapper').hide();
  jqueryObjectOfSes('#file_multi-wrapper').hide();
});

en4.core.runonce.add(function() {
  var sesblog_create_form_tabsSesblog = jqueryObjectOfSes('#sesblog_create_form_tabs li a');
  sesblog_create_form_tabsSesblog.click(function() {
    jqueryObjectOfSes('#dragdrop-wrapper').hide();
    jqueryObjectOfSes('#fromurl-wrapper').hide();
    jqueryObjectOfSes('#file_multi-wrapper').hide();
    if(jqueryObjectOfSes(this).hasClass('drag_drop'))
      jqueryObjectOfSes('#dragdrop-wrapper').show();
    else if(jqueryObjectOfSes(this).hasClass('multi_upload')){
      document.getElementById('file_multi').click();
    }
    else if(jqueryObjectOfSes(this).hasClass('from_url')){
      document.getElementById('fromurl-wrapper').style.display = 'block'
    }
  });
});

en4.core.runonce.add(function()
{
var obj = jqueryObjectOfSes('#dragandrophandler');
obj.on('dragenter', function (e)
{
    e.stopPropagation();
    e.preventDefault();
    jqueryObjectOfSes (this).addClass("sesbd");
});
obj.on('dragover', function (e)
{
     e.stopPropagation();
     e.preventDefault();
});
obj.on('drop', function (e)
{

         jqueryObjectOfSes (this).removeClass("sesbd");
         jqueryObjectOfSes (this).addClass("sesbm");
     e.preventDefault();
     var files = e.originalEvent.dataTransfer.files;
     //We need to send dropped files to Server
     handleFileUpload(files,obj);
});
jqueryObjectOfSes (document).on('dragenter', function (e)
{
    e.stopPropagation();
    e.preventDefault();
});
jqueryObjectOfSes (document).on('dragover', function (e)
{
  e.stopPropagation();
  e.preventDefault();
});
	jqueryObjectOfSes (document).on('drop', function (e)
	{
			e.stopPropagation();
			e.preventDefault();
	});
});
var rowCount=0;
jqueryObjectOfSes(document).on('click','div[id^="abortPhoto_"]',function(){
		var id = jqueryObjectOfSes(this).attr('id').match(/\d+/)[0];
		if(typeof jqXHR[id] != 'undefined'){
				jqXHR[id].abort();
				delete filesArray[id];
				execute = true;
				jqueryObjectOfSes(this).parent().remove();
				executeupload();
		}else{
				delete filesArray[id];
				jqueryObjectOfSes(this).parent().remove();
		}
});
function createStatusbar(obj,file)
{
     rowCount++;
     var row="odd";
     if(rowCount %2 ==0) row ="even";
		  var checkedId = jqueryObjectOfSes("input[name=cover]:checked");
			this.objectInsert = jqueryObjectOfSes('<div class="sesblog_upload_item sesbm '+row+'"></div>');
			this.overlay = jqueryObjectOfSes("<div class='overlay sesblog_upload_item_overlay'></div>").appendTo(this.objectInsert);
			this.abort = jqueryObjectOfSes('<div class="abort sesblog_upload_item_abort" id="abortPhoto_'+countUploadSes+'"><span><?php echo $this->translate("Cancel Uploading"); ?></span></div>').appendTo(this.objectInsert);
			this.progressBar = jqueryObjectOfSes('<div class="overlay_image progressBar"><div></div></div>').appendTo(this.objectInsert);
			this.imageContainer = jqueryObjectOfSes('<div class="sesblog_upload_item_photo"></div>').appendTo(this.objectInsert);
			this.src = jqueryObjectOfSes('<img src="'+en4.core.baseUrl+'application/modules/Sesblog/externals/images/blank-img.gif">').appendTo(this.imageContainer);
			this.infoContainer = jqueryObjectOfSes('<div class=sesblog_upload_photo_info sesbasic_clearfix"></div>').appendTo(this.objectInsert);
			this.size = jqueryObjectOfSes('<span class="sesblog_upload_item_size sesbasic_text_light"></span>').appendTo(this.infoContainer);
			this.filename = jqueryObjectOfSes('<span class="sesblog_upload_item_name"></span>').appendTo(this.infoContainer);
			this.option = jqueryObjectOfSes('<div class="sesblog_upload_item_options clear sesbasic_clearfix"><span class="sesblog_upload_item_radio"><input type="radio" id="main_photo_id'+rowCount+'" name="cover"><label for="main_photo_id'+rowCount+'"><?php echo $this->translate("Main Photo"); ?></label></span><a class="edit_image_upload" href="javascript:void(0);"><i class="fa fa-edit"></i></a><a class="delete_image_upload" href="javascript:void(0);"><i class="fa fa-trash"></i></a></div>').appendTo(this.objectInsert);
		  var objectAdd = jqueryObjectOfSes(this.objectInsert).appendTo('#show_photo');
			jqueryObjectOfSes(this.objectInsert).css('width', widthSetImageContainer+'px');
		if (1) {
			if(jqueryObjectOfSes('#show_photo').children('div').length == 1) {
				var idPhoto = jqueryObjectOfSes('#show_photo').eq(0).find('.sesblog_upload_item_radio').find('input').attr('id');
				jqueryObjectOfSes('#'+idPhoto).prop('checked', true);
			}else{
				jqueryObjectOfSes(checkedId).prop('checked', true);
			}
		}
    this.setFileNameSize = function(name,size)
    {
				if(typeof size != 'undefined'){
					var sizeStr="";
					var sizeKB = size/1024;
					if(parseInt(sizeKB) > 1024)
					{
							var sizeMB = sizeKB/1024;
							sizeStr = sizeMB.toFixed(2)+" MB";
					}
					else
					{
							sizeStr = sizeKB.toFixed(2)+" KB";
					}
					this.size.html(sizeStr);
				}
					this.filename.html(name);
    }
    this.setProgress = function(progress)
    {
        var progressBarWidth =progress*this.progressBar.width()/ 100;
        this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
        if(parseInt(progress) >= 100)
        {
						jqueryObjectOfSes(this.progressBar).remove();
        }
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.objectInsert;

        this.abort.click(function()
        {
            jqxhr.abort();
            sb.hide();
						executeupload();
        });
    }
}
var widthSetImageContainer = 180;
jqueryObjectOfSes(document).ready(function(){
calculateWidthOfImageContainer();
});
function calculateWidthOfImageContainer(){
	var widthOfContainer = jqueryObjectOfSes('#uploadFileContainer-element').width();
	if(widthOfContainer>=740){
		widthSetImageContainer = 	(widthOfContainer/4)-12;
	}else if(widthOfContainer>=570){
			widthSetImageContainer = (widthOfContainer/3)-12;
	}else if(widthOfContainer>=380){
			widthSetImageContainer = (widthOfContainer/2)-12;
	}else {
			widthSetImageContainer = (widthOfContainer/1)-12;
	}
}
var selectedFileLength = 0;
var statusArray =new Array();
var filesArray = [];
var countUploadSes = 0;
var fdSes = new Array();
var checkUploadPhoto = false;
var myuploadphotocounter = 0;
function handleFileUpload(files,obj)
{
	 if(checkUploadPhoto)
	 	return;
	 var check = false;
	 if(sesJqueryObject('#photo_count').length && sesJqueryObject('#photo_count').val() == 0){
		 	checkUploadPhoto = true;
			return false;
	 }
	 if(sesJqueryObject('#photo_count').length){
			 check = true;
			 var  count = sesJqueryObject('#photo_count').val();
		}
	 selectedFileLength = files.length;
   for (var i = 0; i < files.length; i++)
   {
			var url = files[i].name;
    	var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
			if((ext == "png" || ext == "jpeg" || ext == "jpg" || ext == 'PNG' || ext == 'JPEG' || ext == 'JPG' || ext == 'gif'  || ext == 'GIF')){
				var status = new createStatusbar(obj,files[i]); //Using this we can set progress.
				status.setFileNameSize(files[i].name,files[i].size);
				statusArray[countUploadSes] =status;
				filesArray[countUploadSes] = files[i];
				countUploadSes++;
				myuploadphotocounter++;
				if(check && parseInt(count) <= (myuploadphotocounter)){
					checkUploadPhoto = true;
					break;
				}
			}
   }
	 executeupload();
}
var execute = true;
function executeupload(){
	if(Object.keys(filesArray).length == 0 && jqueryObjectOfSes('#show_photo').html() != ''){
		jqueryObjectOfSes('#submit-wrapper').show();
	}
	if(execute == true){
	 for (var i in filesArray) {
		if (filesArray.hasOwnProperty(i))
    {
     	sendFileToServer(filesArray[i],statusArray[i],filesArray[i],'upload',i);
			break;
    }
	 }
	}
}
var jqXHR = new Array();
function sendFileToServer(formData,status,file,isURL,i)
{
		execute = false;
		var formData = new FormData();
		formData.append('Filedata', file);
		if(isURL == 'upload'){
			var reader = new FileReader();
			reader.onload = function (e) {
				status.src.attr('src', e.target.result);
			}
			reader.readAsDataURL(file);
			var urlIs = '';
		}else{
			status.src.attr('src', file);
			var urlIs = true;
		}
		jqueryObjectOfSes('#show_photo_container').addClass('iscontent');
		var url = '&isURL='+urlIs;
    var uploadURL = en4.core.baseUrl + 'sesblog/photo/upload' + '?ul=1'+url; //Upload URL
    var extraData ={}; //Extra Data.
    jqXHR[i]=jqueryObjectOfSes.ajax({
		xhr: function() {
		var xhrobj = jqueryObjectOfSes.ajaxSettings.xhr();
		if (xhrobj.upload) {
				xhrobj.upload.addEventListener('progress', function(event) {
						var percent = 0;
						var position = event.loaded || event.position;
						var total = event.total;
						if (event.lengthComputable) {
								percent = Math.ceil(position / total * 100);
						}
						//Set progress
						status.setProgress(percent);
				}, false);
		}
		return xhrobj;
		},
    url: uploadURL,
    type: "POST",
    contentType:false,
    processData: false,
		cache: false,
		data: formData,
		success: function(response){
		                        response = jqueryObjectOfSes.parseJSON(response);

					execute = true;
					delete filesArray[i];
					//jqueryObjectOfSes('#submit-wrapper').show();
					if (response.status) {
							var fileids = document.getElementById('fancyuploadfileids');
							fileids.value = fileids.value + response.photo_id + " ";
							status.option.find('.sesblog_upload_item_radio').find('input').attr('value',response.photo_id);
							status.src.attr('src',response.url);
							status.option.attr('data-src',response.photo_id);
							status.overlay.css('display','none');
							status.setProgress(100);
							status.abort.remove();
					}else
							status.abort.html('<span>Error In Uploading File</span>');
					executeupload();
       }
    });
}
function readImageUrl(input) {
	handleFileUpload(input.files,jqueryObjectOfSes('#dragandrophandler'));
}

var dragandrophandlerSesblog = sesJqueryObject('#dragandrophandler');
dragandrophandlerSesblog.click(function(){
	document.getElementById('file_multi').click();
});

var isUploadUrl = false;

var upload_from_url = sesJqueryObject('#upload_from_url');
upload_from_url.click(function(e) {
	e.preventDefault();

	if(checkUploadPhoto || (parseInt(sesJqueryObject('#show_photo').children().length) <= (myuploadphotocounter) && myuploadphotocounter) || (sesJqueryObject('#photo_count').length && sesJqueryObject('#photo_count').val() == 0)){
		myuploadphotocounter++;
		checkUploadPhoto = true;
		return false;
  }
	var url = jqueryObjectOfSes('#from_url_upload').val();

	var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
	var name = url.split('/').pop();
	name = name.substr(0, name.lastIndexOf('.'));
	console.log('Test');
		if((ext == "png" || ext == "jpeg" || ext == "jpg" || ext == 'PNG' || ext == 'JPEG' || ext == 'JPG' || ext == 'gif'  || ext == 'GIF')){
			var status = new createStatusbar(jqueryObjectOfSes('#dragandrophandler'),url,'url'); //Using this we can set progress.
			var fd = new FormData();
			fd.append('Filedata', url);
			status.setFileNameSize(name);
			isUploadUrl = true;
			jqueryObjectOfSes('#loading_image').html('uploading ...');
			sendFileToServer(fd,status,url,'url');
			isUploadUrl = false;

			jqueryObjectOfSes('#loading_image').html('');
			jqueryObjectOfSes('#from_url_upload').val('');
   }
	 return false;
});

jqueryObjectOfSes(document).on('click','.edit_image_upload',function(e){
  e.preventDefault();
  var photo_id = jqueryObjectOfSes(this).closest('.sesblog_upload_item_options').attr('data-src');
  if(photo_id){
    editImage(photo_id);
  }else
    return false;
});

jqueryObjectOfSes(document).on('click','.delete_image_upload',function(e){
	e.preventDefault();
	jqueryObjectOfSes(this).parent().parent().find('.sesblog_upload_item_overlay').css('display','block');
	var sesthat = this;
	var isCover = jqueryObjectOfSes(this).closest('.sesblog_upload_item_options').find('.sesblog_upload_item_radio').find('input').prop('checked');
	var photo_id = jqueryObjectOfSes(this).closest('.sesblog_upload_item_options').attr('data-src');
	if(photo_id){
		request = new Request.JSON({
    'format' : 'json',
    'url' : '<?php echo $this->url(Array('module' => 'sesblog', 'controller' => 'index', 'action' => 'remove'), 'default') ?>',
    'data': {
      'photo_id' : photo_id
    },
   'onSuccess' : function(responseJSON) {
			jqueryObjectOfSes(sesthat).parent().parent().remove();
			var fileids = document.getElementById('fancyuploadfileids');
			jqueryObjectOfSes('#fancyuploadfileids').val(fileids.value.replace(photo_id + " ",''));
		//if ($('album').get('value') == 0) {
			if(isCover){
				var idPhoto = jqueryObjectOfSes('#show_photo').eq(0).find('.sesblog_upload_item_radio').find('input').attr('id');
				jqueryObjectOfSes('#'+idPhoto).prop('checked', true);
			}
		//}
			if(jqueryObjectOfSes('#show_photo').html() == ''){
				jqueryObjectOfSes('#submit-wrapper').hide();
				jqueryObjectOfSes('#show_photo_container').removeClass('iscontent');
			}
			checkUploadPhoto = false;
			myuploadphotocounter--;
     return false;
    }
    });
    request.send();
	}else
		return false;
});

<?php if(isset($_POST['file']) && $_POST['file'] != ''){ ?>
		jqueryObjectOfSes('#fancyuploadfileids').val("<?php echo $_POST['file'] ?>");
<?php } ?>
  function editImage(photo_id) {
    var url = '<?php echo $this->url(Array('module' => 'sesblog', 'controller' => 'index', 'action' => 'edit-photo'), 'default') ?>' + '/photo_id/'+ photo_id;
    Smoothbox.open(url);
  }

  function showPreview(value) {
    if(value == 1)
    en4.core.showError('<a class="icon_close"><i class="fa fa-times"></i></a> <p class="popup_design_title">'+en4.core.language.translate("Design 1")+'</p><img class="popup_img" src="./application/modules/Sesblog/externals/images/layout_1.jpg" alt="" />');
    else if(value == 2)
    en4.core.showError('<a class="icon_close"><i class="fa fa-times"></i></a> <p class="popup_design_title">'+en4.core.language.translate("Design 2")+'</p><img src="./application/modules/Sesblog/externals/images/layout_2.jpg" alt="" />');
    else if(value == 3)
    en4.core.showError('<a class="icon_close"><i class="fa fa-times"></i></a> <p class="popup_design_title">'+en4.core.language.translate("Design 3")+'</p><img src="./application/modules/Sesblog/externals/images/layout_3.jpg" alt="" />');
    else if(value == 4)
    en4.core.showError('<a class="icon_close"><i class="fa fa-times"></i></a> <p class="popup_design_title">'+en4.core.language.translate("Design 4")+'</p><img src="./application/modules/Sesblog/externals/images/layout_4.jpg" alt="" />');
    return;
  }
  $$('.core_main_sesblog').getParent().addClass('active');
  jqueryObjectOfSes(document).on('click','.icon_close',function(){
    Smoothbox.close();
  });

  function showStartDate(value) {
    if(value == '1')
    jqueryObjectOfSes('#event_start_time-wrapper').hide();
    else
    jqueryObjectOfSes('#event_start_time-wrapper').show();
  }

</script>


<script type="text/javascript">
jqueryObjectOfSes('body').click(function(event) {
  if(event.target.id == 'custom_url') {
    jqueryObjectOfSes('#suggestion_tooltip').show();
  }
  else {
    jqueryObjectOfSes('#suggestion_tooltip').hide();
  }
});
</script>


<?php if($this->typesmoothbox) { ?>
	<script type="application/javascript">
	executetimesmoothboxTimeinterval = 200;
	executetimesmoothbox = true;
	function showHideOptionsSesblog(display){
		var elem = sesJqueryObject('.sesblog_hideelement_smoothbox');
		for(var i = 0 ; i < elem.length ; i++){
				sesJqueryObject(elem[i]).parent().parent().css('display',display);
		}
	}
	function checkSettingSesblog(first){
		var hideShowOption = sesJqueryObject('#advanced_sesblogoptions').hasClass('active');
			if(hideShowOption){
					showHideOptionsSesblog('none');
					if(typeof first == 'undefined'){
						sesJqueryObject('#advanced_sesblogoptions').html("<i class='fa fa-plus-circle'></i><?php echo $this->translate('Show Advanced Settings') ?>");
					}
					sesJqueryObject('#advanced_sesblogoptions').removeClass('active');
			}else{
					showHideOptionsSesblog('block');
					sesJqueryObject('#advanced_sesblogoptions').html("<i class='fa fa-minus-circle'></i><?php echo $this->translate('Hide Advanced Settings') ?>");
						sesJqueryObject('#advanced_sesblogoptions').addClass('active');
			}
	}
	en4.core.runonce.add(function()
  {
		sesJqueryObject('#advanced_sesblogoptions').click(function(e){
			checkSettingSesblog();
		});
		sesJqueryObject('#advanced_sesblogoptions').html("<i class='fa fa-plus-circle'></i><?php echo $this->translate('Show Advanced Settings') ?>");
		checkSettingSesblog('true');

		tinymce.init({
			mode: "specific_textareas",
			plugins: "table,fullscreen,media,preview,paste,code,image,textcolor,jbimages,link",
			theme: "modern",
			menubar: false,
			statusbar: false,
			toolbar1:  "undo,redo,removeformat,pastetext,|,code,media,image,jbimages,link,fullscreen,preview",
			toolbar2: "fontselect,fontsizeselect,bold,italic,underline,strikethrough,forecolor,backcolor,|,alignleft,aligncenter,alignright,alignjustify,|,bullist,numlist,|,outdent,indent,blockquote",
			toolbar3: "",
			element_format: "html",
			height: "225px",
      content_css: "bbcode.css",
      entity_encoding: "raw",
      add_unload_trigger: "0",
      remove_linebreaks: false,
			convert_urls: false,
			language: "<?php echo $this->language; ?>",
			directionality: "<?php echo $this->direction; ?>",
			upload_url: "<?php echo $this->url(array('module' => 'sesbasic', 'controller' => 'index', 'action' => 'upload-image'), 'default', true); ?>",
			editor_selector: "tinymce"
		});
	});
  </script>
<?php	die; 	} ?>
