<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestaticpage
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-02-16 5:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php /*We have used the javascript code in tpl file because we are using many php variable and loops in javascript code, this code will be executed only admin side not on user side.*/  ?>

<div class="tip" id="message"></div>
<script type="text/javascript">

  var multiLanguage = '<?php echo $this->multiLanguage; ?>';
  var languageCount = '<?php echo $this->languageCount; ?>';

  window.addEvent('domready', function() {
    var e4 = $('page_url_msg-wrapper');
    $('page_url_msg-wrapper').setStyle('display', 'none');
    var pageurlcontainer = $('page_url-element');
    var language = '<?php echo $this->string()->escapeJavascript("Check Availability") ?>';
    var newdiv = document.createElement('div');
    newdiv.id = 'url_varify';
    newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>" + language + "</a> <br />";
    pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);

    var bodyParent = $('<?php echo $this->add_show_hide_link; ?>').getParent().getParent();
    if (multiLanguage == 1 && languageCount > 1) {
      $('multiLanguageLinkShow').inject(bodyParent, 'after');
      $('multiLanguageLinkHide').inject(bodyParent, 'after');
			<?php
				foreach ($this->languageData as $value):
				if ($value == 'en')
				$value = '';
				else 
				$value = "_$value";
			?>
			var bodycontainer = $('body' + '<?php echo $value; ?>' + '-element');
			var language1 = '<?php echo $this->string()->escapeJavascript("Embed a Form (Get Embed Code)"); ?>';
			var newdiv1 = document.createElement('div');
			newdiv1.id = 'embedded' + '<?php echo $value; ?>' + '_code';
			newdiv1.innerHTML = "<a href='<?php echo $this->url(array('action' => 'form-list'), 'sitestaticpage_manageadmins') ?>' name='get_embeded' id='get_embeded' class= 'smoothbox sitestaticpage_icon_embed buttonlink'>" + language1 + "</a> <br />";
			bodycontainer.insertBefore(newdiv1, bodycontainer.childNodes[3]);

			<?php endforeach; ?>
      multiLanguageOption(0);
    }
    else {
			var bodycontainer = $('body-element');
			var language1 = '<?php echo $this->string()->escapeJavascript("Embed a Form (Get Embed Code)") ?>';
			var newdiv1 = document.createElement('div');
			newdiv1.id = 'embedded_code';
			newdiv1.innerHTML = "<a href='<?php echo $this->url(array('action' => 'form-list'), 'sitestaticpage_manageadmins') ?>' name='get_embeded' id='get_embeded' class= 'smoothbox  sitestaticpage_icon_embed buttonlink'>" + language1 + "</a> <br />";
			bodycontainer.insertBefore(newdiv1, bodycontainer.childNodes[3]);
    }
  });

  function PageUrlBlur() {

		if ($('page_url_alert') == null) {
				var pageurlcontainer = $('page_url-element');
				var newdiv = document.createElement('span');
				newdiv.id = 'page_url_alert';
				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" />';
				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[3]);
		}
		else {
				$('page_url_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" />';
	  }
    var url = "<?php echo $this->url(array('action' => 'pageurlvalidation' ), 'sitestaticpage_manageadmins', true);?>";
    var page_id = "<?php echo $this->staticpage_id;?>";
      en4.core.request.send(new Request.JSON({
        url: url,
        method: 'get',
        data: {
          page_url: $('page_url').value,
          check_url: 1,
          page_id: page_id,
          format: 'html'
        },        

      onSuccess: function(responseJSON) {
        
        if (responseJSON.success == 0) {
          $('page_url_alert').innerHTML = responseJSON.error_msg;
          if ($('page_url_alert')) {
            $('page_url_alert').innerHTML = responseJSON.error_msg;
          }
        }
        else {
          $('page_url_alert').innerHTML = responseJSON.success_msg;
          if ($('page_url_alert')) {
            $('page_url_alert').innerHTML = responseJSON.success_msg;
          }
        }
      }
    }));
  }
  
  window.addEvent('load', function()
  {
   $('title').addEvent('keyup', function()
    {
			var link_text;
			if(this.value != '') {
				link_text = this.value;
			}
    });
    // trigger on page-load
    if ($('title').value.length) {
      $('title').fireEvent('keyup');
    }
  });
  
  window.addEvent('load', function()
{
		$('menu-3').addEvent('click', function(){
				$('link_title-wrapper').setStyle('display', ($(this).get('value') == '3'?'none':'block'));
		});
                $('menu-0').addEvent('click', function(){
				$('link_title-wrapper').setStyle('display', 'block');
		});
                $('menu-1').addEvent('click', function(){
				$('link_title-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
		});
                $('menu-2').addEvent('click', function(){
				$('link_title-wrapper').setStyle('display', ($(this).get('value') == '2'?'block':'none'));
		});
		window.addEvent('domready', function() {
			$('link_title-wrapper').setStyle('display', ($('menu-3').checked ?'none':'block'));
		});
});

  window.addEvent('load', function()
  {
    $('short_url-1').addEvent('click', function(){
      if($(this).get('value') == '1')
        {
           var url = '<?php echo 'URL-COMPONENT'; ?>';
           var page_url = $('page_url').value;
           $('page_url_address').innerHTML = 'http://'+'<?php echo $_SERVER['HTTP_HOST'];?>' + '<?php echo $this->baseUrl();?>' + '/' + url;
          if ($('page_url_address')) {
                $('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text">pag_url</span>');
           }
           $('page_url').addEvent('keyup', function() {
      var text = url;
      if (this.value != '')
      {
        text = this.value;
      }
      if ($('page_url_address_text')) {
        $('page_url_address_text').innerHTML = text;
      }
    });
    // trigger on page-load
    if ($('page_url').value.length) {
      $('page_url').fireEvent('keyup');
    }
        }
        
  });
  $('short_url-0').addEvent('click', function(){
      if($(this).get('value') == '0')
        {
           var url = '<?php echo 'URL-COMPONENT'; ?>';
           var page_url = $('page_url').value;
          $('page_url_address').innerHTML = 'http://'+'<?php echo $_SERVER['HTTP_HOST'];?>' + '<?php echo $this->baseUrl();?>' + '/' + '<?php echo $this->default_url;?>' + '/' + url;
          if ($('page_url_address')) {
                $('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text">page_url</span>');
           }
           $('page_url').addEvent('keyup', function() {
      var text = url;
      if (this.value != '')
      {
        text = this.value;
      }
      if ($('page_url_address_text')) {
        $('page_url_address_text').innerHTML = text;
      }
    });
    // trigger on page-load
    if ($('page_url').value.length) {
      $('page_url').fireEvent('keyup');
    }
        }
        
  });
  window.addEvent('domready', function() {
    if($('short_url-1').checked)
      {
         var url = '<?php echo 'URL-COMPONENT'; ?>';
         var page_url = $('page_url').value;
           $('page_url_address').innerHTML = 'http://'+'<?php echo $_SERVER['HTTP_HOST'];?>'  + '<?php echo $this->baseUrl();?>' + '/' +  url;
          if ($('page_url_address')) {
                $('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text">page_url</span>');
           }
           $('page_url').addEvent('keyup', function() {
      var text = url;
      if (this.value != '')
      {
        text = this.value;
      }
      if ($('page_url_address_text')) {
        $('page_url_address_text').innerHTML = text;
      }
    });
    // trigger on page-load
    if ($('page_url').value.length) {
      $('page_url').fireEvent('keyup');
    }
      }
      else{
      var url = '<?php echo 'URL-COMPONENT'; ?>';
       var page_url = $('page_url').value;
           $('page_url_address').innerHTML = 'http://'+'<?php echo $_SERVER['HTTP_HOST'];?>' + '<?php echo $this->baseUrl();?>' + '/' +'<?php echo $this->default_url;?>'  +  '/'+ url;
    if ($('page_url_address')) {
      $('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text">page_url</span>');
    }
    $('page_url').addEvent('keyup', function() {
      var text = url;
      if (this.value != '')
      {
        text = this.value;
      }
      if ($('page_url_address_text')) {
        $('page_url_address_text').innerHTML = text;
      }
    });
    // trigger on page-load
    if ($('page_url').value.length) {
      $('page_url').fireEvent('keyup');
    }
      }
		});
  });
  
   window.addEvent('load',function()
  {
    $('type').addEvent('change', function(){
    if($('type').value == 1)
      {
        $('short_url-wrapper').style.display = 'none';
        $('page_url-wrapper').style.display = 'none';
        $('menu-wrapper').style.display = 'none';
        $('page_widget-wrapper').style.display = 'none';
        $('search-wrapper').style.display = 'none';
        $('page_title-wrapper').style.display = 'none';
        $('page_description-wrapper').style.display = 'none';
        $('keywords-wrapper').style.display = 'none';
      }
      else{
        $('short_url-wrapper').style.display = 'block';
        $('page_url-wrapper').style.display = 'block';
        $('menu-wrapper').style.display = 'block';
        $('page_widget-wrapper').style.display = 'block';
        $('search-wrapper').style.display = 'block';
        $('page_title-wrapper').style.display = 'block';
        $('page_description-wrapper').style.display = 'block';
        $('keywords-wrapper').style.display = 'block';
      }
    });
    window.addEvent('domready', function(){
       if($('type').value == 1)
      {
        $('short_url-wrapper').style.display = 'none';
        $('page_url-wrapper').style.display = 'none';
        $('menu-wrapper').style.display = 'none';
        $('page_widget-wrapper').style.display = 'none';
        $('search-wrapper').style.display = 'none';
        $('page_title-wrapper').style.display = 'none';
        $('page_description-wrapper').style.display = 'none';
        $('keywords-wrapper').style.display = 'none';
      }
      else{
        $('short_url-wrapper').style.display = 'block';
        $('page_url-wrapper').style.display = 'block';
        $('menu-wrapper').style.display = 'block';
        $('page_widget-wrapper').style.display = 'block';
        $('search-wrapper').style.display = 'block';
        $('page_title-wrapper').style.display = 'block';
        $('page_description-wrapper').style.display = 'block';
        $('keywords-wrapper').style.display = 'block';
      }
    });
  });
  </script>
<h2><?php echo 'Static Pages, HTML Blocks and Multiple Forms Plugin'; ?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div>
  <?php echo $this->htmlLink(array('route' => 'admin_default','module' =>'sitestaticpage', 'controller' =>'manage', 'action' => 'index'), 'Back to Manage Static Pages', array('class' => 'seaocore_icon_back buttonlink')) ?>
</div>

<br />
<div class='clear seaocore_settings_form sitestaticpage_form'>
  <div class='settings'>
      <?php echo $this->form->render($this); ?>
      <?php if ($this->languageCount > 1 && $this->multiLanguage): ?>
      <div id="multiLanguageLinkShow" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageOption(2);" style="text-decoration: none;"><b><?php echo "+ Create Static Page in the multiple languages supported by this website." ?></b></a>
        </div>
      </div>

      <div id="multiLanguageLinkHide" class="form-wrapper">
        <div class="form-label">&nbsp;</div>
        <div class="form-element">
          <a href="javascript: void(0);" onclick="return multiLanguageOption(1);" style="text-decoration: none;"><b><?php echo "- Create Static Page in the primary language of this website." ?></b></a>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
  <script type="text/javascript">
  var multiLanguageOption = function(show) {

<?php
foreach ($this->languageData as $value):
  if ($this->defaultLanguage == $value) {
    continue;
  }
  if ($value == 'en') {
    $value = '';
  } else {
    $value = "_$value";
  }
  ?>
	if (show == 1) {
		$('body' + '<?php echo $value; ?>' + '-wrapper').setStyle('display', 'none');
		$('multiLanguageLinkShow').setStyle('display', 'block');
		$('multiLanguageLinkHide').setStyle('display', 'none');
	}
	else {
		$('body' + '<?php echo $value; ?>' + '-wrapper').setStyle('display', 'block');
		$('multiLanguageLinkShow').setStyle('display', 'none');
		$('multiLanguageLinkHide').setStyle('display', 'block');
	}
<?php endforeach; ?>
  }
  </script>