<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->show_phone && isset($this->options_create['phone']) && $this->options_create['phone'] == 'Phone') : ?>    
	<?php if (empty($this->sitepage->phone) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('phone');" onmouseout="hideImage('phone');">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/mobile.png" alt="Phone" />
			<div class="sitepage_contect_det">        
				<input type="text" name="phone" value="<?php if(empty($this->sitepage->phone)): ?><?php echo $this->translate("Contact Number");?><?php else:?><?php echo $this->sitepage->phone ?><?php endif;?>" id="phone" onblur="saveContactDetails('phone')" onclick="onFocus('phone')" />   
				<div id="showPhoneNumber" style="display:none;"></div> 
			</div>  
			<?php if ($this->can_edit) : ?>	
				<div class="edit_icon" id="phoneimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('phone')"></a>
				</div>	
			<?php endif; ?>
		</div>   
	<?php elseif (!empty($this->sitepage->phone)) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('phone');" onmouseout="hideImage('phone');">
			<img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Sitepage/externals/images/icons/mobile.png" alt="Phone" />
			<div class="sitepage_contect_det">
				<input type="text" name="phone" value="<?php echo $this->sitepage->phone ?>" id="phone" onblur="saveContactDetails('phone')" style="display:none;" onclick="onFocus('phone')" />  
				<div id="showPhoneNumber" style="display:block;"><?php echo $this->sitepage->phone ?></div>     
			</div>
			<?php if ($this->can_edit) : ?>	
				<div class="edit_icon" id="phoneimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('phone')"></a>
				</div>	
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->show_email && isset($this->options_create['email']) && $this->options_create['email'] == 'Email') : ?>   
	<?php if (empty($this->sitepage->email) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('email');" onmouseout="hideImage('email');">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/invite.png" alt="E-mail" />
			<div class="sitepage_contect_det">
				<input type="text" name="email" value="<?php if(empty($this->sitepage->email)): ?><?php echo $this->translate("Email ID");?><?php else:?><?php echo $this->sitepage->email ?><?php endif;?>" id="email" onblur="saveContactDetails('email')" onclick="onFocus('email')" />
				<div id="showEmailAddress" style="display:none;"></div>
			</div>
			<?php if ($this->can_edit) : ?>
				<div id="showerrormessage" style="display:none;" class="sitepage_contect_error">  </div> 
				<div class="edit_icon" id="emailimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('email')"></a>
				</div>  
			<?php endif; ?>
		</div>
	<?php elseif (!empty($this->sitepage->email)) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('email');" onmouseout="hideImage('email');">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/invite.png" alt="E-mail" />
			<div class="sitepage_contect_det">
				<input type="text" name="email" value="<?php echo $this->sitepage->email ?>" id="email" onblur="saveContactDetails('email')"  style="display:none;" onclick="onFocus('email')" />
				<div id="showEmailAddress">
				  <?php if (empty($this->emailme)) :?>
					<a href='mailto:<?php echo $this->sitepage->email ?>'><?php echo $this->translate('Email Me') ?></a>
			    <?php else: ?>
					<a href="javascript:void(0);" onclick="showSmoothBox('<?php echo $this->escape($this->url(array('route' => 'sitepage_profilepage', 'module' => 'sitepage', 'controller' => 'profile', 'action' => 'email-me', "id" => $this->sitepage->page_id), 'default' , true)); ?>'); return false;"><?php echo $this->translate('Email Me') ?></a>
					<?php endif; ?>
					
				</div>
			</div>        
			<?php if ($this->can_edit) : ?>
				<div id="showerrormessage" style="display:none;" class="sitepage_contect_error">  </div> 
				<div class="edit_icon" id="emailimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('email')"></a>
				</div>  
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ($this->show_website && isset($this->options_create['website']) && $this->options_create['website'] == 'Website') : ?>    
	<?php if (empty($this->sitepage->website) && $this->viewer()->getIdentity() && $this->can_edit) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('website');" onmouseout="hideImage('website');">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/web.png" alt="Website" />
			<div class="sitepage_contect_det">          
				<input type="text" name="website" value="<?php if(empty($this->sitepage->website)): ?><?php echo $this->translate("Website");?><?php else:?><?php echo $this->sitepage->website ?><?php endif;?>" id="website" onblur="saveContactDetails('website')" onclick="onFocus('website')" />        
				<div id="showWebsite" style="display:none;"></div>
			</div>
			<?php if ($this->can_edit) : ?>
				<div class="edit_icon" id="websiteimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('website')"></a>
				</div>
			<?php endif; ?>
		</div>
	<?php elseif (!empty($this->sitepage->website) ) : ?>
		<div class="sitepage_contect_field" onmouseover="displayImage('website');" onmouseout="hideImage('website');">
			<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/web.png" alt="Website" />
			<div class="sitepage_contect_det">
				<input type="text" name="website" value="<?php echo $this->sitepage->website ?>" id="website" onblur="saveContactDetails('website')"  style="display:none;" onclick="onFocus('website')" />          
				<div id="showWebsite">   
					<?php if (strstr($this->sitepage->website, 'http://') || strstr($this->sitepage->website, 'https://')): ?>
						<a href='<?php echo $this->sitepage->website ?>' target="_blank" title='<?php echo $this->sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
					<?php else: ?>
						<a href='http://<?php echo $this->sitepage->website ?>' target="_blank" title='<?php echo $this->sitepage->website ?>' ><?php echo $this->translate(''); ?> <?php echo $this->translate('Visit Website') ?></a>
					<?php endif; ?>
				</div>
			</div>
			<?php if ($this->can_edit) : ?>
				<div class="edit_icon" id="websiteimage" style="display:none;" title="<?php echo $this->translate('edit');?>">
					<a href="javascript:void(0);" onclick="onFocus('website')"></a>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>    
<?php endif;?>

<script type="text/javascript">
 
  function displayImage(showimage) {
    if(showimage == 'phone') {
      if($('phoneimage'))
        $('phoneimage').style.display = "block";      
    } 
    else if(showimage == 'email') {
      if($('emailimage'))
        $('emailimage').style.display = "block";
    } 
    else if(showimage == 'website') {
      if($('websiteimage'))
        $('websiteimage').style.display = "block";
    }
  }
  
  function hideImage(hideImage) {
    if(hideImage == 'phone') {
      if($('phoneimage'))
        $('phoneimage').style.display = "none";
    } 
    else if(hideImage == 'email') {
      if($('emailimage'))
        $('emailimage').style.display = "none";
    } 
    else if(hideImage == 'website') {
      if($('websiteimage'))
        $('websiteimage').style.display = "none";
    }
  }
  
  function onFocus(focusid){
    if(focusid == 'phone') {  
      if($('showPhoneNumber'))
        $('showPhoneNumber').style.display = "none";
      if($('phone')) {
        if($('phone').value == '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>') {
          $('phone').value="";
        }
        $('phone').style.display = "block";
        $('phone').focus();
      }
    }
    else if(focusid == 'email') {
      if($('email')) {
        if($('email').value == '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>') {
          $('email').value="";
        }
        $('email').style.display = "block";
        $('email').focus();  
      }
      if($('showEmailAddress'))
        $('showEmailAddress').style.display = "none";
    }
    else if(focusid == 'website') {
      if($('website')) {
        if($('website').value == '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>') {
          $('website').value="";
        }
        $('website').style.display = "block";
        $('website').focus();  
      }
      if($('showWebsite'))
        $('showWebsite').style.display = "none";      
    }
  }
  
  function validateEmail(email) { 
    if(email != '') {
      var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return filter.test(email);
    } else {
      return true;
    }
  }  

  function saveContactDetails(blurid) {
    if(blurid == 'phone') {     
      if($('phoneimage'))
        $('phoneimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/edit.png";
    } 
    else if(blurid == 'email') {     
      if(validateEmail($('email').value)){
        if($('emailimage'))
          $('emailimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/edit.png";
      } else {
        if($('showerrormessage')) {
          $('showerrormessage').style.display = "block";
          $('showerrormessage').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("Please enter a valid email address.")) ?>';
        }
        return false;
      }      
    } 
    else if(blurid == 'website') {
      if($('websiteimage')) {
        $('websiteimage').src = "<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/edit.png";
      }      
    }
    
    if($('phone')) {
      var phone = $('phone').value;        
      if($('phone').value == '') { 
        $('phone').value = '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>';
      }
  
      if(phone == '<?php echo $this->string()->escapeJavascript($this->translate("Contact Number")) ?>') {
        phone="";
      }      
    }
    if($('email')) {
      var email = $('email').value;
      if($('email').value == '') { 
        $('email').value = '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>';        
      }
      
      if(email == '<?php echo $this->string()->escapeJavascript($this->translate("Email ID")) ?>') {
        email="";
      } 
    }   
   
    if($('website')) {
      var website = $('website').value;
      if($('website').value == '') { 
        $('website').value = '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>';       
      }
      
      if(website == '<?php echo $this->string()->escapeJavascript($this->translate("Website")) ?>') {
        website="";
      } 
    }    

    en4.core.request.send(new Request.HTML({
      url :'<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'profile', 'action' => 'contact-detail'), 'default', true) ?>',
      data : {
        format : 'html',
        phone : phone,
        email : email,
        website : website,
        page_id : '<?php echo $this->sitepage->page_id ?>'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {  

        if(blurid == 'phone') {
          if($('showPhoneNumber')) {
            $('showPhoneNumber').style.display = "block";
            $('showPhoneNumber').innerHTML = phone;            
          }
          if($('phone') && phone != '')
            $('phone').style.display = "none";   
          if($('phoneimage')) 
            $('phoneimage').style.display = "none";             

        } 
        else if(blurid == 'email') {          
          if($('showEmailAddress')) {    
            $('showEmailAddress').style.display = "block";
            $('showEmailAddress').innerHTML = '<a href="mailto:'+$('email').value+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Email Me")) ?>'+'</a>';
          }
          if($('email') && email != '')
            $('email').style.display = "none";
          if($('showerrormessage'))
            $('showerrormessage').style.display = "none";
          if($('emailimage'))
            $('emailimage').style.display = "none";
        } else if(blurid == 'website') {
          if($('showWebsite')) { 
            $('showWebsite').style.display = "block";
            var str = website;
            var splitFromHttp = str.split("http://"); 
            var splitFromHttps = str.split("https://");  
            if(typeof splitFromHttp != 'undefined' && splitFromHttp[0] == '') {
              $('showWebsite').innerHTML = '<a href="http://'+splitFromHttp[1]+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            } 
            else if(typeof splitFromHttps != 'undefined' && splitFromHttps[0] == ''){
              $('showWebsite').innerHTML = '<a href="https://'+splitFromHttps[1]+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            }
            else {
              $('showWebsite').innerHTML = '<a href="http://'+str+'" target="_blank" title="'+website+'">'+'<?php echo $this->string()->escapeJavascript($this->translate("Visit Website")) ?>'+'</a>';
            }            
          }
          if($('website') && website != '')
            $('website').style.display = "none";   
          if($('websiteimage'))
            $('websiteimage').style.display = "none";
        }
      }
    }));
  }
</script>  
<script type="text/javascript">

function showSmoothBox(url)
{
  Smoothbox.open(url);
  parent.Smoothbox.close;
}
</script>
