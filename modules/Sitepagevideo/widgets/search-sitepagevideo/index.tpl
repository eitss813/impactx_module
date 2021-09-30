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
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var searchSitepagevideos = function() {
    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {  
      $('filter_form').submit();
    }
  }
  en4.core.runonce.add(function(){
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride':'min','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride':'max','element':'span'});
        //f.set('class', 'integer_field_unselected');
      }
    });
  });
  window.addEvent('onChangeFields', function() {
    var firstSep = $$('li.browse-separator-wrapper')[0];
    var lastSep;
    var nextEl = firstSep;
    var allHidden = true;
    do {
      nextEl = nextEl.getNext();
      if( nextEl.get('class') == 'browse-separator-wrapper' ) {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && ( nextEl.getStyle('display') == 'none' );
      }
    } while( nextEl );
    if( lastSep ) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });
</script>
<div class="seaocore_search_criteria">
<?php if ($this->sitepage_post == 'enabled') {
	echo $this->form->render($this);
} else {
	return;
} ?>
</div>
<?php if (in_array("4", $this->showTabArray)):?>
	<script type="text/javascript">
		var form;

		var subcategoryies = function(category_id, sub, subcatname, subsubcat)
		{    
			if($('filter_form')) {
				form=document.getElementById('filter_form');
			} else if($('filter_form_category')){
				form=$('filter_form_category');
			}
				
			if($('category_id') && form.elements['category_id']){
				form.elements['category_id'].value = '<?php echo $this->category_id?>';
			}
			if($('category') && form.elements['category']){
				form.elements['category'].value = '<?php echo $this->category?>';
			}
			if($('subcategory_id') && form.elements['subcategory_id']){
				form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id?>';
			}
			if($('subcategory') && form.elements['subcategory']){
				form.elements['subcategory'].value = '<?php echo $this->subcategory?>';
			}
			if($('subsubcategory_id') && form.elements['subsubcategory_id']){
				form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id?>';
			}
			if(category_id != '' && form.elements['category_id']){
				form.elements['category_id'].value = category_id;
			}
			if(category_id != 0) {
				if(sub == '')
				subsubcat = 0;
				changesubcategory(sub, subsubcat);
			}
			
			var url = '<?php echo $this->url(array('action' => 'subcategory'), 'sitepage_general', true);?>';
			en4.core.request.send(new Request.JSON({      	
				url : url,
				data : {
					format : 'json',
					category_id_temp : category_id
				},
				onSuccess : function(responseJSON) {
					clear('subcategory_id');
					var  subcatss = responseJSON.subcats;        
					addOption($('subcategory_id')," ", '0');
					for (i=0; i< subcatss.length; i++) {
						addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);  
						$('subcategory_id').value = sub;
						form.elements['subcategory'].value = $('subcategory_id').value;
						form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
						form.elements['category'].value = category_id;
						form.elements['subcategory_id'].value = $('subcategory_id').value;
						if(form.elements['subsubcategory'])
						form.elements['subsubcategory'].value = subsubcat;
						if(form.elements['subsubcategory_id'])
						form.elements['subsubcategory_id'].value = subsubcat;
					}

					if(subcatss.length == 0) {
						form.elements['categoryname'].value = 0;
					}
					
					if(category_id == 0) {
						clear('subcategory_id');
						clear('subsubcategory_id');
						$('subcategory_id').style.display = 'none';
						$('subcategory_id-label').style.display = 'none';
						$('subsubcategory_id').style.display = 'none';
						$('subsubcategory_id-label').style.display = 'none';
					}
				}
			}));    
		};
		function clear(ddName)
		{ 
			for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
			{ 
				document.getElementById(ddName).options[ i ]=null; 	      
			} 
		}
		function addOption(selectbox,text,value )
		{
			var optn = document.createElement("OPTION");
			optn.text = text;
			optn.value = value;			
			if(optn.text != '' && optn.value != '') {
				$('subcategory_id').style.display = 'block';
				$('subcategory_id-label').style.display = 'block';
				selectbox.options.add(optn);
			} 
			else {
				$('subcategory_id').style.display = 'none';
				$('subcategory_id-label').style.display = 'none';
				selectbox.options.add(optn);
			}
		}
		
			var changesubcategory = function(subcatid, subsubcat)
			{
				var url = '<?php echo $this->url(array('action' => 'subsubcategory'), 'sitepage_general', true);?>';
				var request = new Request.JSON({
					url : url,
					data : {
						format : 'json',
						subcategory_id_temp : subcatid
					},
					onSuccess : function(responseJSON) {
						clear('subsubcategory_id');
						var  subsubcatss = responseJSON.subsubcats;
						addSubOption($('subsubcategory_id')," ", '0');
						for (i=0; i< subsubcatss.length; i++) {
							addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
							$('subsubcategory_id').value = subsubcat;
							if(form.elements[' subsubcategory_id'])
							form.elements[' subsubcategory_id'].value = $('subsubcategory_id').value;
							if(form.elements[' subsubcategory'])
							form.elements['subsubcategory'].value = $('subsubcategory_id').value;
							if($('subsubcategory_id')) {
								$('subsubcategory_id').value = subsubcat;
							}
						}

						if(subcatid == 0) {
							clear('subsubcategory_id');
							if($('subsubcategory_id-label'))
							$('subsubcategory_id-label').style.display = 'none';
						}
					}
				});
				request.send();
			};

			function addSubOption(selectbox,text,value )
			{
				var optn = document.createElement("OPTION");
				optn.text = text;
				optn.value = value;
				if(optn.text != '' && optn.value != '') {
					$('subsubcategory_id').style.display = 'block';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'block';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'block';
					selectbox.options.add(optn);
				} else {
					$('subsubcategory_id').style.display = 'none';
					if($('subsubcategory_id-wrapper'))
						$('subsubcategory_id-wrapper').style.display = 'none';
					if($('subsubcategory_id-label'))
						$('subsubcategory_id-label').style.display = 'none';
					selectbox.options.add(optn);
				}
			}

		var cat = '<?php echo $this->category_id ?>';

		if(cat != '') {
			var sub = '<?php echo $this->subcategory_id; ?>';
			var subcatname = '<?php echo $this->subcategory_name; ?>';
			var subsubcat = '<?php echo $this->subsubcategory_id; ?>';
			subcategoryies(cat, sub, subcatname,subsubcat);
		}
		
		function show_subcat(cat_id) 
		{		
			if(document.getElementById('subcat_' + cat_id)) {
				if(document.getElementById('subcat_' + cat_id).style.display == 'block') {		
					document.getElementById('subcat_' + cat_id).style.display = 'none';
					document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
				} 
				else if(document.getElementById('subcat_' + cat_id).style.display == '') {			
					document.getElementById('subcat_' + cat_id).style.display = 'none';
					document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/plus16.gif';
				}
				else {			
					document.getElementById('subcat_' + cat_id).style.display = 'block';
					document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitepage/externals/images/icons/minus16.gif';
				}		
			}
		}
		
	</script>
<?php endif;?>

<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    var contentAutocomplete = new Autocompleter.Request.JSON('title', '<?php echo $this->url(array('module' => 'sitepage', 'controller' => 'items', 'action' => 'getitem'), 'admin_default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'searchbox_autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);

      }
    });

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      document.getElementById('resource_id').value = selected.retrieve('autocompleteChoice').id;
    });

  });
</script>