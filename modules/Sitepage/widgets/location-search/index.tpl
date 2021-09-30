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
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl';
?>
<?php
//if(empty($this->sitepage_post)){return;}
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
<?php if( $this->form ): ?>
  <div class="global_form_box sitepage_advanced_search_form">
    <?php echo $this->form->render($this) ?>
  </div>
  <div class="" id="page_location_pops_loding_image" style="display: none;">
	<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
	<?php //echo $this->translate("Loading ...") ?>
</div>
<?php endif ?>

<script type="text/javascript">
  var flag = '<?php echo $this->advanced_search; ?>';
  var mapGetDirection;
  var myLatlng;
	window.addEvent('domready', function() {
	  
	  if(document.getElementById('sitepage_location')) {
			if(document.getElementById('sitepage_location').value == '') {
				submiForm();
			} 
		} else {
			submiForm();
		}
		
		if ($$('.browse-separator-wrapper')) {
			$$('.browse-separator-wrapper').setStyle("display",'none');
		}
	
	  $('page_location_pops_loding_image').injectAfter($('done-element'));
		new google.maps.places.Autocomplete(document.getElementById('sitepage_location'));
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position){
			var lat = position.coords.latitude;
			var lng = position.coords.longitude;
			
			var myLatlng = new google.maps.LatLng(lat,lng);
			
			var myOptions = {
				zoom: 8 ,
				center: myLatlng,
				navigationControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}

			mapGetDirection = new google.maps.Map(document.getElementById("sitepage_location_map_none"), myOptions);
    
        if(!position.address) {
          var service = new google.maps.places.PlacesService(mapGetDirection);
          var request = {
            location: new google.maps.LatLng(lat,lng), 
            radius: 500
          };
          
          service.search(request, function(results, status) { 
            if (status  ==  'OK') {
              var index = 0;
              var radian = 3.141592653589793/ 180;
              var my_distance = 1000; 
              for (var i = 0; i < results.length; i++){
              var R = 6371; // km
              var lat2 = results[i].geometry.location.lat();
              var lon2 = results[i].geometry.location.lng(); 
              var dLat = (lat2-lat) * radian;
              var dLon = (lon2-lng) * radian;
              var lat1 = lat * radian;
              var lat2 = lat2 * radian;

              var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2); 
              var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
              var d = R * c;
              if(d < my_distance) {
                index = i;
                my_distance = d;
              }
            }      
           
              document.getElementById('sitepage_location').value = (results[index].vicinity) ? results[index].vicinity :'';
              document.getElementById('Latitude').value = lat;
              document.getElementById('Longitude').value = lng;
              document.getElementById('locationmiles').value = 1000;
              
              //form submit by ajax
              submiForm();
            } 
          });
        } else {
          var delimiter = (position.address && position.address.street !=  '' && position.address.city !=  '') ? ', ' : '';
          var location = (position.address) ? (position.address.street + delimiter + position.address.city) : '';
          document.getElementById('sitepage_location').value = location;
					document.getElementById('Latitude').value = lat;
					document.getElementById('Longitude').value = lng;
					document.getElementById('locationmiles').value = 1000;
          //form submit by ajax
          submiForm();
        }
      });
    } else {
			submiForm();
		}

		advancedSearchSitepages(flag);
		
	});

	function submiForm() {
	
		if ($('category_id')) {
			if ($('category_id').options[$('category_id').selectedIndex].value == 0) { 
				$('category').value = 0;
			}
		}
		var  formElements = document.getElementById('filter_form');
		var url = en4.core.baseUrl + 'widget/index/mod/sitepage/name/browselocation-sitepage'; 
		var parms = formElements.toQueryString(); 

		var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html';
		document.getElementById('page_location_pops_loding_image').style.display ='';
		en4.core.request.send(new Request.HTML({
			method : 'post',
			'url' : url,
			'data' : param,
			onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
				document.getElementById('page_location_pops_loding_image').style.display ='none';
				$('sitepage_map_container_topbar').style.display ='block';
				document.getElementById('sitepage_location_map_anchor').getParent().innerHTML = responseHTML;
				setMarker();
			  en4.core.runonce.trigger();
				$('sitepage_map_container').style.visibility = 'visible'; 
				if ($('seaocore_browse_list')) {
					var elementStartY = $('sitepagelocation_map').getPosition().x ;
					var offsetWidth = $('sitepage_map_container').offsetWidth;
					var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
				}
			}
		}), {
         "force":true
    });
	}

	function locationPage() {
		var  sitepage_location = document.getElementById('sitepage_location');
		
// 		if (document.getElementById('sitepage_location').value) {
// 			document.getElementById('sitepage_location') = 0; 
// 	  }
	  
		if (document.getElementById('Latitude').value) {
			document.getElementById('Latitude').value = 0;
		}
		
		if(document.getElementById('Longitude').value) {
			document.getElementById('Longitude').value = 0;
		}
	}
	
	function locationSearch() {

	  var  formElements = document.getElementById('filter_form');
    formElements.addEvent('submit', function(event) { 
      event.stop();
      submiForm();
    });
  }

	function advancedSearchSitepages() {
	
		if (flag == 0) {
		  if ($('fieldset-grp2'))
				$('fieldset-grp2').style.display = 'none';
				
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'none';
				
			flag = 1;
			$('advanced_search').value = 0;
			if ($('sitepage_street'))
			$('sitepage_street').value = '';
// 			if ($('sitepage_postalcode'))
// 			$('sitepage_postalcode').value = '';
			if ($('sitepage_country'))
			$('sitepage_country').value = '';
			if ($('sitepage_state'))
			$('sitepage_state').value = '';
			if ($('sitepage_city'))
			$('sitepage_city').value = '';
			if ($('profile_type'))
			$('profile_type').value = '';
			changeFields($('profile_type'));
			if ($('orderby'))
			$('orderby').value = '';
			if ($('category_id'))
			$('category_id').value = 0;

		} else {
		  if ($('fieldset-grp2'))
				$('fieldset-grp2').style.display = 'block';
				
			if ($('fieldset-grp1'))
				$('fieldset-grp1').style.display = 'block';
				
			flag = 0;
			$('advanced_search').value = 1;
		}
  }
</script>

<?php //$row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('sitepage', 'category_id');
//if(!empty($row->display)):
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
    
  var form;

  var location_subcategoryies = function(category_id, sub, subcatname, subsubcat) {

	  if($('filter_form')) {
	    form=document.getElementById('filter_form');
	  } else if($('filter_form_category')){
			form=$('filter_form_category');
		}

    if($('category_id') && form.elements['category_id']){
      form.elements['category_id'].value = '<?php echo $this->category_id;?>';
    }

    if($('subcategory_id') && form.elements['subcategory_id']){
      form.elements['subcategory_id'].value = '<?php echo $this->subcategory_id;?>';
    }

    if($('subsubcategory_id') && form.elements['subsubcategory_id']){
      form.elements['subsubcategory_id'].value = '<?php echo $this->subsubcategory_id;?>';
    }
    
    if(category_id != '' && form.elements['category_id']){
      form.elements['category_id'].value = category_id;
    }

    if(category_id != 0) {
      if(sub == '') {
				sub=0;
				subsubcat = 0;
      }
      changesubcategory(sub, subsubcat, subcatname);
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
          //$('subcategory_id').value = sub;
          //form.elements['subcategory'].value = sub;
        	form.elements['categoryname'].value = subcatss[i]['categoryname_temp'];
          form.elements['category'].value = category_id;
          form.elements['subcategory_id'].value = sub;
          //form.elements['subcategory'].value = sub;
//           if(form.elements['subsubcategory'])
//           form.elements['subsubcategory'].value = subsubcat;
//           if(form.elements['subsubcategory_id'])
//           form.elements['subsubcategory_id'].value = subsubcat;
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

  function clear(ddName) {
    for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) {
      document.getElementById(ddName).options[ i ]=null; 	      
    }
  }

  function addOption(selectbox,text,value ) {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if(optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    }
    else {
      $('subcategory_id').style.display = 'none';
      $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
  
	var changesubcategory = function(subcatid, subsubcat, subcatname) {
	
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
					if(form.elements['subsubcategory_id'])
					form.elements['subsubcategory_id'].value = subsubcat;
					if(form.elements['subsubcategory'])
					form.elements['subsubcategory'].value = subsubcat;
					if($('subsubcategory_id')) {
						$('subsubcategory_id').value = subsubcat;
					}
				}
				form.elements['subcategory'].value = subcatid;
				form.elements['subcategoryname'].value = subcatname;

				if(subcatid == 0) {
					clear('subsubcategory_id');
					if($('subsubcategory_id-label'))
					$('subsubcategory_id-label').style.display = 'none';
				}
			}
		});
		request.send();
	};

	function addSubOption(selectbox,text,value ) {
		var optn = document.createElement("OPTION");
		optn.text = text;
		optn.value = value;
		
		if(optn.text != '' && optn.value != '') {
			$('subsubcategory_id').style.display = 'block';
				if($('subsubcategory_id-wrapper'))
				$('subsubcategory_id-wrapper').style.display = 'inline-block';
				if($('subsubcategory_id-label'))
				$('subsubcategory_id-label').style.display = 'inline-block';
			selectbox.options.add(optn);
		} 
		else {
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

    location_subcategoryies(cat, sub, subcatname,subsubcat);
  }
</script>

<?php //endif;?>
<div id="sitepage_location_map_none" style="display: none;"></div>

<script type="text/javascript">
//   en4.core.runonce.add(function() {
//     var contentAutocomplete = new Autocompleter.Request.JSON('sitepage_location', '<?php //echo $this->url(array('module' => 'sitepagemember', 'controller' => 'widgets', 'action' => 'getitem'), 'admin_default', true) ?>', {
//       'postVar' : 'text',
//       'minLength': 1,
//       'selectMode': 'pick',
//       'autocompleteType': 'tag',
//       'className': 'seaocore-autosuggest',
//       'customChoices' : true,
//       'filterSubset' : true,
//       'multiple' : false,
//       'injectChoice': function(token){
//         var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id':token.label});
//         new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice1'}).inject(choice);
//         this.addChoiceEvents(choice).inject(this.choices);
//         choice.store('autocompleteChoice', token);
// 
//       }
//     });
//     contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
//       document.getElementById('location').value = selected.retrieve('autocompleteChoice').id;
//     });
// 
//   });
</script>