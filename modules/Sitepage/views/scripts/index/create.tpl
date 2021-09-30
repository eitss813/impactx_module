<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<?php if(!empty($this->sitepageUrlEnabled) && !empty($this->show_url)):?>
	<script type="text/javascript">

		window.addEvent('domready', function() { 
		var e4 = $('page_url_msg-wrapper');
		$('page_url_msg-wrapper').setStyle('display', 'none');
		
				var pageurlcontainer = $('page_url-element');
				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
				var newdiv = document.createElement('div');
				newdiv.id = 'url_varify';
				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
				checkDraft();
				
				if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
					var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
					<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
				}
		});


		function checkDraft(){
			if($('draft')){
				if($('draft').value==0) {
					$("search-wrapper").style.display="none";
					$("search").checked= false;
				} else{
					$("search-wrapper").style.display="block";
					$("search").checked= true;
				}
			}
		}


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
			var url = '<?php echo $this->url(array('action' => 'pageurlvalidation' ), 'sitepage_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					page_url : $('page_url').value,
          check_url : 0,
          page_id : 0,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					//$('page_url_msg-wrapper').setStyle('display', 'block');
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

	//<![CDATA[
		window.addEvent('load', function()
		{
		  var url = '<?php echo $this->translate('PAGE-NAME');?>';
		  if($('page_url_address')) {
				$('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text"><?php echo $this->translate('	PAGE-NAME');?></span>');
			}
      
      $('short_page_url_address').innerHTML = $('short_page_url_address').innerHTML.replace(url, '<span id="short_page_url_address_text"><?php echo $this->translate('PAGE-NAME');?></span>');

			$('page_url').addEvent('keyup', function()
			{
				var text = url;
				if( this.value != '' )
				{
					text = this.value;
				}
				if($('page_url_address_text')) {
					$('page_url_address_text').innerHTML = text;
				}
        $('short_page_url_address_text').innerHTML = text;
			});
			// trigger on page-load
			if ($('page_url').value.length)
					$('page_url').fireEvent('keyup');
		});
	//]]>
	</script>
<?php elseif(empty($this->sitepageUrlEnabled)):?>
  <script type="text/javascript">

		window.addEvent('domready', function() { 
		var e4 = $('page_url_msg-wrapper');
		$('page_url_msg-wrapper').setStyle('display', 'none');
		
				var pageurlcontainer = $('page_url-element');
				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
				var newdiv = document.createElement('div');
				newdiv.id = 'url_varify';
				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='PageUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

				pageurlcontainer.insertBefore(newdiv, pageurlcontainer.childNodes[2]);
				checkDraft();
				
				if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))){
				  var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
					<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
				}
		});

		function checkDraft(){
			if($('draft')){
				if($('draft').value==0) {
					$("search-wrapper").style.display="none";
					$("search").checked= false;
				} else{
					$("search-wrapper").style.display="block";
					$("search").checked= true;
				}
			}
		}


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
			var url = '<?php echo $this->url(array('action' => 'pageurlvalidation' ), 'sitepage_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					page_url : $('page_url').value,
          check_url : 0,
          page_id : 0,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					//$('page_url_msg-wrapper').setStyle('display', 'block');
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

	//<![CDATA[
		window.addEvent('load', function()
		{ 
		var url = '<?php echo $this->translate('PAGE-NAME');?>';
		  if($('page_url_address')) {
				$('page_url_address').innerHTML = $('page_url_address').innerHTML.replace(url, '<span id="page_url_address_text"><?php echo $this->translate('PAGE-NAME');?></span>');
			}

			$('page_url').addEvent('keyup', function()
			{
				var text = url;
				if( this.value != '' )
				{
					text = this.value;
				}
				
				if($('page_url_address_text')) {
					$('page_url_address_text').innerHTML = text;
				}
			});
			// trigger on page-load
			if ($('page_url').value.length)
					$('page_url').fireEvent('keyup');
		});
	//]]>
	</script>
<?php endif;?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>  
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitepage_page'), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
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
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
<div class='layout_middle sitepage_create_wrapper clr' id="show">
	<?php if(Engine_Api::_()->getApi('settings', 'core')
						->getSetting('sitepage.category.enable', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)): ?>
		<div class="sitepage_change_category">
			<div class="_sitepage_change_vategory_label">Want to change category? </div>
			<button id="sitepage_change" onclick="changeCategory()">Click here</button>
		</div>
	<?php endif;?>
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tips.enable',1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tip',0)) :?>
		<div class="tips">
			<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.tip'); ?>
		</div>
	<?php endif;?>
	<?php if ($this->current_count >= $this->quota  && !empty($this->quota)): ?>
	  <div class="tip">
	  	<span><?php echo $this->translate('You have already created the maximum number of pages allowed.'); ?></span> 
	  </div>
	  <br/>
	<?php else: ?>
	  <?php if($this->sitepage_render == 'sitepage_form') { ?>
    <?php if(!empty($this->package)):?>
	<h3><?php echo $this->translate("Create New Page") ?></h3>
	<p><?php echo $this->translate("Create a page using these quick, easy steps and get going.");?></p>	
    <h4 class="sitepage_create_step"><?php echo $this->translate('2. Configure your page based on the package you have chosen.'); ?></h4>
	  <div class='sitepagepage_layout_right'>      
    	<div class="sitepage_package_page p5">          
        <ul class="sitepage_package_list">
        	<li class="p5">
          	<div class="sitepage_package_list_title">
              <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
            </div>           
            <div class="sitepage_package_stat"> 
              <span>
								<b><?php echo $this->translate("Price"). ": "; ?> </b>
								<?php if($this->package->price > 0):echo Engine_Api::_()->sitepage()->getPriceWithCurrency($this->package->price); else: echo $this->translate('FREE'); endif; ?>
             	</span>
             	<span>
                <b><?php echo $this->translate("Billing Cycle"). ": "; ?> </b>
                <?php echo $this->package->getBillingCycle() ?>
              </span>
              <span style="width: auto;">
              	<b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration"). ": ": $this->translate("Duration"). ": "; ?> </b>
               	<?php echo $this->package->getPackageQuantity() ; ?>
             	</span>
              <br />
              <span>
              	<b><?php echo $this->translate("Featured"). ": "; ?> </b>
               	<?php
                	if ($this->package->featured == 1)
                		echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
                ?>
             	</span>
              <span>
              	<b><?php echo $this->translate("Sponsored"). ": "; ?> </b>
               	<?php
                	if ($this->package->sponsored == 1)
                  	echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
             	 	?>
             	</span>
              <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
                <span>
                  <b><?php echo $this->translate("Ads Display"). ": "; ?> </b>
                   <?php
                    if ($this->package->ads == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.communityads', 1))
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>            
              
             	<span>
              	<b><?php echo $this->translate("Tell a friend"). ": "; ?> </b>
               	<?php
                  if ($this->package->tellafriend == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                ?>
             	</span>
              <span>
                <b><?php echo $this->translate("Print"). ": "; ?> </b>
                 <?php
                  if ($this->package->print == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
             	<span>
               <b><?php echo $this->translate("Rich Overview"). ": "; ?> </b>
               <?php
                if ($this->package->overview == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Map"). ": "; ?> </b>
               	<?php
                if ($this->package->map == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Insights"). ": "; ?> </b>
               	<?php
                if ($this->package->insights == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
                ?>
             	</span>
              <span>
                  <b><?php echo $this->translate("Contact Details"). ": "; ?> </b>
                   <?php
                    if ($this->package->contact_details == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
              </span>
              <span>
                <b><?php echo $this->translate("Send an Update"). ": "; ?> </b>
                 <?php
                  if ($this->package->sendupdate == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
<!--              <span>
                <b><?php echo $this->translate("Save To Foursquare Button"). ": "; ?> </b>
                 <?php
                  if ($this->package->foursquare == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span> -->
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagetwitter')) :?>
                <span>
                  <b><?php echo $this->translate("Display Twitter Updates"). ": "; ?> </b>
                  <?php
                    if ($this->package->twitter == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>
							<?php  $module= unserialize($this->package->modules);
               if(!empty($module)):
                    $subModuleStr=$this->package->getSubModulesString();
             		if(!empty($this->package->modules) && !empty ($subModuleStr)):?>
				        <span class="sitepage_package_stat_apps">
				           <b><?php echo $this->translate("Apps available"). ": "; ?> </b>
				           <?php echo $subModuleStr; ?>
				        </span>
				      <?php endif; ?>
              <?php endif; ?>
						</div>
						<div class="sitepage_list_details">
							<?php echo $this->translate($this->package->description); ?>
		        </div>
          	<div class="sitepage_create_link mtop10 clr">
           		<a href="<?php echo $this->url(array('action'=>'index'), 'sitepage_packages', true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
          	</div>
          </li>
        </ul>
      </div>
    </div>
    <div class="sitepagepage_layout_left">
  <?php endif; ?>
  <?php echo $this->form->render($this); ?>
  <?php if(!empty($this->package)):?>
  	</div>
  <?php endif; ?>
  <?php } else { echo $this->sitepage_formrender; } ?>
  <?php endif; ?> 
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')
						->getSetting('sitepage.category.enable', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)): ?>
<script type="text/javascript">
	function category(item) {
		document.getElementById("category").style.display = "none";
		document.getElementById('category_id-wrapper').style.display = "none";
		document.getElementById("show").style.display = "block";
		$('category_id').value = item;
		if($('0_0_1')) {
			var profile_type = getProfileType(item);
			if(profile_type == 0) profile_type = '';
			$('0_0_1').value = profile_type;
			changeFields($('0_0_1'));
			subcategory(item, '', '');
		}
	}
	function changeCategory() {
		document.getElementById("show").style.display = "none";
		document.getElementById("category").style.display = "table";
	}
</script>
<?php endif;?>
<script type="text/javascript">
	<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.enable', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)): ?>
		document.getElementById("show").style.display = "none";
	<?php endif;?>
</script>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.enable', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.feature.extension', 0)): ?>
<div id="category" class="categories_grid" style="display: table">
	<h3><?php
		$data = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCategories();
		echo "Select Category "; ?>
		<?php foreach ($data as $value) :
		?></h3>
	<div class="categories_box">
		<div class="category_box_container">
			<div class="_icon">
				<?php if (!empty($value['file_id'])): ?>
                        <?php
                        $temStorage = Engine_Api::_()->storage()->get($value['file_id'], '');
                        if (!empty($temStorage)):
                            ?>
                           <img src="<?php echo $temStorage->getPhotoUrl(); ?>">
                        <?php endif;
                    else:
                        ?>
							<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/categories/<?php echo str_replace(' ', '', $value['category_name'])?>.png">
                        <?php endif; ?>
			</div>
			<div class="_title">
				<h3><?php echo $value["category_name"]; ?></h3>
			</div>
			<button class="_select_category" value="<?php echo $value['category_id']; ?>" onclick="category(this.value)" >Select</button>
		</div>
	</div>
	<?php endforeach;?>
</div>
<?php endif;?>
<?php if($this->res == 1) : ?>
<script type="text/javascript">
	category(<?php echo $this->cat_id; ?>);
</script>
<?php endif;?>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.profile.fields', 1)): ?>
	<?php
		/* Include the common user-end field switching javascript */
		echo $this->partial('_jsSwitch.tpl', 'fields', array( 
		))
	?>
	<script type="text/javascript">

		var getProfileType = function(category_id) {
			var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'sitepage')->getMapping()); ?>;
			for(i = 0; i < mapping.length; i++) {
				if(mapping[i].category_id == category_id)
					return mapping[i].profile_type;
			}
			return 0;
		}

		var defaultProfileId = '<?php echo '0_0_1' ?>'+'-wrapper';
		if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') { 
			$(defaultProfileId).setStyle('display', 'none');
		}
	</script>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
     
    if('<?php echo $this->quick;?>') {
       SmoothboxSEAO.active = true;
    }
 
        if (SmoothboxSEAO.active && $('sitepages_create_quick')) {   
            if($('sitepages_create_quick').getElements('.sp_quick_advanced').length > 0){
            var toogleAdvancedView = function(el) {
                if (el.retrieve('activeHideAdvanced', false)) {
                $('sitepages_create_quick').getElements('.sp_quick_advanced').getParent('.form-wrapper').removeClass('dnone');
                    el.store('activeHideAdvanced', false);
                    el.innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Hide Advanced Options'));?>';
                    el.addClass('seaocore_icon_minus');
                } else {
                    $('sitepages_create_quick').getElements('.sp_quick_advanced').getParent('.form-wrapper').addClass('dnone');
                    el.store('activeHideAdvanced', true);
                    el.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Show Advanced Options')); ?>';
                    el.removeClass('seaocore_icon_minus');
                }
            };
            var el= new Element('a', {
      'class': 'buttonlink seaocore_icon_add sp_create_more'
    }).inject($('sitepages_create_quick').getElementById('buttons-element'),'top');
                el.addEvent('click', function() {
            toogleAdvancedView(el);
            });
            toogleAdvancedView(el);
            }
        }
    });
    var cat = '<?php echo $this->category_id ?>';
    if(cat != '') { 
      sub = '<?php echo $this->subcategory_id; ?>';
      subcatname = '<?php echo $this->subcategory_name; ?>';
      subcategory(cat, sub, subcatname);
    }
</script>

<style type="text/css">

.sp_create_more{
  display: block !important;
  margin-bottom: 10px;
}
</style>