

<?php
	$getFormUrl = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
					'module' => 'sitepage',
					'controller' => 'package',
	      'action' => 'add-template',
	      ), 'admin_default', true);
?>

<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if( count($this->subnavigation) ): ?>
<div class='seaocore_admin_tabs clr'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->subnavigation)->render();
    ?>
</div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/back_1.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sitepage', 'controller' => 'package', 'action' => 'subscription-templates'), $this->translate('Back to Manage Templates'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br /><br />

<div class="settings">
  <?php echo $this->form->render($this); ?>
</div>

<script src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/scripts/mooRainbow.js" type="text/javascript"></script>

<?php
  $this->headLink()
      ->prependStylesheet($this->layout()->staticBaseUrl.'application/modules/Seaocore/externals/styles/mooRainbow.css');
?>

<script type="text/javascript">

_processTemplateForm = {
	flag: false,
	previewFlag : false,
	writePreviewFileUrl:  '<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'package', 'action'=>'write-preview-file', 'previewtype' => 'styles'), 'admin_default', true) ?>',
	sb_url: '<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'package', 'action'=>'preview', 'previewtype' => 'styles'), 'admin_default', true) ?>',
	hexcolorTonumbercolor: function(hexcolor) {
		var hexcolorAlphabets = "0123456789ABCDEF";
	  var valueNumber = new Array(3);
	  var j = 0;
	  if(hexcolor.charAt(0) == "#")
	    hexcolor = hexcolor.slice(1);
	  hexcolor = hexcolor.toUpperCase();
	  for(var i=0;i<6;i+=2) {
	    valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i+1));
	    j++;
	  }
	  return(valueNumber);
	},
	attachClickEvent: function() {
		$$('#layout-element').addEvent('change',function(e){
			var payload = "layout_id=" + e.target.value+"&ajax=true";

			if (_processTemplateForm.flag === false) {
				_processTemplateForm.flag = true;
				_processTemplateForm.setLoader(_processTemplateForm.flag);
				var formRequest = new Request.HTML({
					'format': 'html',
		      		'method': 'post',
					'url': '<?php echo $getFormUrl; ?>',
					'data': payload,
					onError: function(text,error) {
						console.warn(error);
					},
					onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
						var parser = new DOMParser;
						htmlDOM = parser.parseFromString(responseHTML,'text/html');
						replaceText = htmlDOM.getElementsByClassName('settings')[0].innerHTML;
						$$(".settings").set('html',replaceText);
						_processTemplateForm.attachClickEvent();
						_processTemplateForm.initializeColorPicker();
						_processTemplateForm.attachLoader();
						_processTemplateForm.flag = false;
						_processTemplateForm.setLoader(_processTemplateForm.flag);
					},
				});
				formRequest.send();
			}
		});
	},
	initializeColorPicker: function() {
		var elements = new Array();
	  $$('.colorPickerElement').each(function(item, index){
		    var elementExists = $('myDemo'+item.id);
				if(elementExists) 
					elementExists.remove();
				new MooRainbow('myRainbow'+item.id, { 
		      id: 'myDemo'+item.id,
		      'startColor': _processTemplateForm.hexcolorTonumbercolor($('inputbox'+item.id).value),
		      'onChange': function(color) {
		      	var inputBoxId = 'inputbox'+this.options.id.substr(6);
		        if($(inputBoxId))
		        	$('inputbox'+this.options.id.substr(6)).value = color.hex;
		      }
		    });
		});
	},
	preview: function() {
		var formData = $('addTemplateForm').toQueryString().parseQueryString();
		if (typeof(formData.layout) == 'undefined') {
			console.warn('Choose layout before previewing template.')
			return;
		}

		delete formData['colorPickerElement'];
		var content = 'content='+JSON.stringify(formData);
	    if (_processTemplateForm.previewFlag == false) {
	    	_processTemplateForm.previewFlag = true;
	    	_processTemplateForm.setPreviewLoader(_processTemplateForm.previewFlag);
	    	var request = new Request.JSON({
		      'url' : _processTemplateForm.writePreviewFileUrl,
		      'data' : content,
		      onError: function(text,error) {
						console.warn(error);
					},
		      onSuccess : function(responseJSON) {
		      	_processTemplateForm.previewFlag = false;
		      	_processTemplateForm.setPreviewLoader(_processTemplateForm.previewFlag);
		        if (responseJSON.return == '0') 
		          alert(responseJSON.message);
		        else
		          Smoothbox.open(_processTemplateForm.sb_url+'/template_id/'+formData.layout
		          	// ,{width : 980, height : 400,}
		          	);
		      }
		    });
		    request.send();
	    }
	},
	attachLoader: function() {
		// attach template loader
		var loader_container = document.createElement('div');
		loader_container.style.display = 'inline-block';
		loader_container.innerHTML = '&nbsp;&nbsp; <span id="loader" style="display: none;"> Loading template ... &nbsp;  &nbsp; <img length="20px" width="20px" style="vertical-align: middle;" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/loader.gif'; ?>" ></span>';
		document.getElementById('layout-wrapper').appendChild(loader_container);

		// attach preview loader
		$$('preview').setStyle('display','inline-block');
		var preview_loader_container = document.createElement('div');
		preview_loader_container.style.display = 'inline-block';
		preview_loader_container.innerHTML = '&nbsp;&nbsp; <span id="preview_loader" style="display: none;"> Creating preview ... &nbsp;  &nbsp; <img length="20px" width="20px" style="vertical-align: middle;" src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Sitepage/externals/images/loader.gif'; ?>" ></span>';
		document.getElementById('buttons-element').appendChild(preview_loader_container);
	},
	setLoader: function(flag) {
		if (flag == true) {
			$$('button[id=save]').set('disabled',true);
			$$('#loader').setStyle('display','block');
		} else {
			$$('button[id=save]').set('disabled',false);
			$$('#loader').setStyle('display','none');
		}
	},
	setPreviewLoader: function(flag) {
		if (flag == true)
			$$('#preview_loader').setStyle('display','inline-block');
		else
			$$('#preview_loader').setStyle('display','none');
	}
}

_processTemplateForm.attachClickEvent();
_processTemplateForm.initializeColorPicker();
_processTemplateForm.attachLoader();

</script>


<style type="text/css">
.settings div.form-element .description {
    min-width: 100%;
}
</style>