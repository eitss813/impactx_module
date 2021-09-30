<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepage
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>

<?php 
$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitepage')->getCatDependancyArray();
$subCateDependencyArray  = Engine_Api::_()->getDbTable('categories', 'sitepage')->getSubCatDependancyArray();
  if(!empty($this->sitepageurlenabled) && !empty($this->show_url) && !empty($this->edit_url)):
?>
	<script type="text/javascript">

		window.addEvent('domready', function() { 
		ShowUrlColumn("<?php echo $this->page_id; ?>");
		});

    en4.core.language.addData({
      "Check Availability":"<?php echo $this->string()->escapeJavascript($this->translate("Check Availability"));?>"
   });

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
<?php endif;?>

<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>

<?php if (empty($this->is_ajax)) : ?>
<?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>

	<div class="layout_middle">
		<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
		<?php echo $this->partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array( 'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Edit Info', 'sectionDescription' => '')); ?>
		<?php
		$this->headScript()
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
						->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
		?>
		<?php
			/* Include the common user-end field switching javascript */
			echo $this->partial('_jsSwitch.tpl', 'fields', array(
				//'topLevelId' => (int) @$this->topLevelId,
				//'topLevelValue' => (int) @$this->topLevelValue
			))
		?>

		<div class="sitepage_edit_content">
			<div id="show_tab_content">
		<?php endif; ?>

<?php  echo $this->form->render(); ?>
<?php if (empty($this->is_ajax)) : ?>	
	    </div>
	  </div>	
  </div>
<?php endif; ?>


<script type="text/javascript">

	var subcatid = '<?php echo $this->subcategory_id; ?>';
  
  var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';  

  var submitformajax = 0;
	var show_subcat = 1;
	var cateDependencyArray = new Array(); 
	var subCateDependencyArray = new Array(); 
	<?php foreach($cateDependencyArray as $cat) : ?>
	cateDependencyArray.push(<?php echo $cat ?>);
	<?php endforeach; ?>
	<?php foreach($subCateDependencyArray as $cat) : ?>
	subCateDependencyArray.push(<?php echo $cat ?>);
	<?php endforeach; ?>
	<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.category.edit', 0) && !empty($this->sitepage->category_id)) :?>
		 show_subcat = 0;
	<?php endif;	?>
	
	  en4.core.runonce.add(function()
	  {
       checkDraft();

	    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('controller' => 'tag', 'action' => 'suggest'), 'default', true) ?>', {
	      'postVar' : 'text',	
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
	  var subcategory = function(category_id, subcatid, subcatname,subsubcatid)
		{
      
			if(subcatid > 0) {
				changesubcategory(subcatid, subsubcatid);
      }      
			if(!in_array(cateDependencyArray, category_id)) {
				if($('subcategory_id-wrapper'))
					$('subcategory_id-wrapper').style.display = 'none';
				if($('subcategory_id-label'))
					$('subcategory_id-label').style.display = 'none';  
				if($('buttons-wrapper')) {
					$('buttons-wrapper').style.display = 'block';
				}   
				return;
			}
      if($('subsubcategory_backgroundimage'))
      $('subcategory_backgroundimage').style.display = 'block';
      if($('subcategory_id'))
      $('subcategory_id').style.display = 'none';
      if($('subsubcategory_id'))
      $('subsubcategory_id').style.display = 'none';
      if($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
        if($('subcategory_backgroundimage'))
        $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" /></center></div>';
  
      
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      			if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'none';
			if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'none';
			var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitepage_general', true);?>';
			en4.core.request.send(new Request.JSON({      	
		    url : url,
		    data : {
		      format : 'json',
		      category_id_temp : category_id
				},
		    onSuccess : function(responseJSON) { 
		    	if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          if($('subcategory_backgroundimage'))
          $('subcategory_backgroundimage').style.display = 'none';
          
		    	clear('subcategory_id');
		    	var  subcatss = responseJSON.subcats;
		      addOption($('subcategory_id')," ", '0');
          for (i=0; i< subcatss.length; i++) {
            addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
            if(show_subcat == 0) {
              if($('subcategory_id'))
              $('subcategory_id').disabled = 'disabled';
              if($('subsubcategory_id'))
              $('subsubcategory_id').disabled = 'disabled';
            }
            if($('subcategory_id')) {
              $('subcategory_id').value = subcatid;
            }
          }
						
          if(category_id == 0) {
            clear('subcategory_id');
            if($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
            if($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
          }
		    }
			  }), {
          "force":true
        });
		};
function in_array(ArrayofCategories, value) {
	for(var i=0;i<ArrayofCategories.length;i++) {
		if(ArrayofCategories[i] == value) {
			return true;
		}
	}
	return false;
}
    var changesubcategory = function(subcatid, subsubcatid)
		{
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
      
			if(!in_array(subCateDependencyArray, subcatid)) {
				if($('subsubcategory_id-wrapper'))
					$('subsubcategory_id-wrapper').style.display = 'none';
				if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';   
				if($('buttons-wrapper')) {
					$('buttons-wrapper').style.display = 'block';
				}   
				return;
			}
      if($('subsubcategory_backgroundimage'))
      $('subsubcategory_backgroundimage').style.display = 'block';
      if($('subsubcategory_id'))   
      $('subsubcategory_id').style.display = 'none';
      if($('subsubcategory_id-label'))
        $('subsubcategory_id-label').style.display = 'none';
        if($('subsubcategory_backgroundimage'))
        $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/loading.gif" /></center></div>';
  
      
			if($('buttons-wrapper')) {
		  	$('buttons-wrapper').style.display = 'none';
			}
			var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitepage_general', true);?>';
   		var request = new Request.JSON({
		    url : url,
		    data : {
		      format : 'json',
		      subcategory_id_temp : subcatid
				},
		    onSuccess : function(responseJSON) {
		    	if($('buttons-wrapper')) {
				  	$('buttons-wrapper').style.display = 'block';
					}
          if($('subsubcategory_backgroundimage'))
          $('subsubcategory_backgroundimage').style.display = 'none';

		    	clear('subsubcategory_id');
		    	var  subsubcatss = responseJSON.subsubcats;   
          if($('subsubcategory_id')){
						addSubOption($('subsubcategory_id')," ", '0');
						for (i=0; i< subsubcatss.length; i++) {
							addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
							if($('subsubcategory_id')) {
								$('subsubcategory_id').value = subsubcatid;
							}
						}
          }
		    }
			});
      request.send();
		};

		function clear(ddName)
		{ 
			if(document.getElementById(ddName)) {
			   for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
			   { 
			      document.getElementById(ddName).options[ i ]=null; 			      
			   } 
			}
		}
    function addOption(selectbox,text,value )
    {
			if($('subcategory_id')) {
				var optn = document.createElement("OPTION");
				optn.text = text;
				optn.value = value;
		
				if(optn.text != '' && optn.value != '') {
					if($('subcategory_id'))
					$('subcategory_id').style.display = 'block';
					if($('subcategory_id-wrapper'))
						$('subcategory_id-wrapper').style.display = 'block';
					if($('subcategory_id-label'))
						$('subcategory_id-label').style.display = 'block';
					selectbox.options.add(optn);
				} else {
					if($('subcategory_id'))
					$('subcategory_id').style.display = 'none';
					if($('subcategory_id-wrapper'))
						$('subcategory_id-wrapper').style.display = 'none';
					if($('subcategory_id-label'))
						$('subcategory_id-label').style.display = 'none';
					selectbox.options.add(optn);
				}
	    }
    }
	
		var cat = '<?php echo $this->category_id ?>';
		if(cat != '') {		
			 subcatid = '<?php echo $this->subcategory_id; ?>';
       subsubcatid = '<?php echo $this->subsubcategory_id; ?>';
			 var subcatname = '<?php echo $this->subcategory_name; ?>';			 
			 subcategory(cat, subcatid, subcatname,subsubcatid);
		}

		window.addEvent('domready', function() {      
			//var e4 = $('page_url_msg-wrapper');
			if($('page_url_msg-wrapper'))
			$('page_url_msg-wrapper').setStyle('display', 'none');
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


    function addSubOption(selectbox,text,value )
    {
      if($('subsubcategory_id')) {
				var optn = document.createElement("OPTION");
				optn.text = text;
				optn.value = value;
				if(optn.text != '' && optn.value != '') {
					if($('subsubcategory_id'))
					$('subsubcategory_id').style.display = 'block';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'block';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'block';
					selectbox.options.add(optn);
				} else {
					if($('subsubcategory_id'))
					$('subsubcategory_id').style.display = 'none';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'none';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'none';
					selectbox.options.add(optn);
				}
      }
    }
    
		window.addEvent('domready', function() { 
				if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
					var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
					<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
				}
		});    
</script>