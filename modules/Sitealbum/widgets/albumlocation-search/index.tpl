<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css');

//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php if ($this->form): ?>
  <div class="sitealbum_advanced_search sitealbum_advanced_member_search global_form_box">
    <?php echo $this->form->render($this) ?>
  </div>
  <div class="" id="sitealbumlocation_location_pops_loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif' />
  </div>
<?php endif ?>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array()); ?>

<script type="text/javascript">

  en4.core.runonce.add(function() {
    $$('#filter_form input[type=text]').each(function(f) {
      if (f.value == '' && f.id.match(/\min$/)) {
        new OverText(f, {'textOverride': 'min', 'element': 'span'});
      }
      if (f.value == '' && f.id.match(/\max$/)) {
        new OverText(f, {'textOverride': 'max', 'element': 'span'});
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
      if (nextEl.get('class') == 'browse-separator-wrapper') {
        lastSep = nextEl;
        nextEl = false;
      } else {
        allHidden = allHidden && (nextEl.getStyle('display') == 'none');
      }
    } while (nextEl);
    if (lastSep) {
      lastSep.setStyle('display', (allHidden ? 'none' : ''));
    }
  });

  var flag = '<?php echo $this->advanced_search; ?>';
  var mapGetDirection;
  var myLatlng;
  window.addEvent('domready', function() {
    if (document.getElementById('album_location').value == '') {
      submiForm();
    }

    if ($$('.browse-separator-wrapper')) {
      $$('.browse-separator-wrapper').setStyle("display", 'none');
    }

    $('sitealbumlocation_location_pops_loding_image').injectAfter($('done-element'));

    locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'album_location', 'album_city');

    var params = {
      'detactLocation': <?php echo $this->locationDetection; ?>,
      'fieldName': 'album_location',
      //'noSendReq':1,
      'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
    };
    params.callBack = function() {
      submiForm();
      advancedSearchEvents(flag);
    };
    en4.seaocore.locationBased.startReq(params);
  });

  function submiForm() {

    if ($('category_id')) {
      if ($('category_id').options[$('category_id').selectedIndex].value == 0) {
        $('category_id').value = 0;
      }
    }
    var formElements = document.getElementById('album_filter_form');
//    var url = en4.core.baseUrl + 'widget/index/mod/sitealbum/name/bylocation-album';
    var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
    var parms = formElements.toQueryString();

    var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html';
    document.getElementById('sitealbumlocation_location_pops_loding_image').style.display = '';
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': url,
      'data': param,
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('sitealbumlocation_location_pops_loding_image').style.display = 'none';
        $('eventlocation_map_container_topbar').style.display = 'block';
        document.getElementById('albumlocation_location_map_anchor').getParent().innerHTML = responseHTML;
        setMarker();
        en4.core.runonce.trigger();
        $('eventlocation_map_container').style.visibility = 'visible';
        if ($('seaocore_browse_list')) {
          var elementStartY = $('eventlocation_map').getPosition().x;
          var offsetWidth = $('eventlocation_map_container').offsetWidth;
          var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
        }
      }
    }), {
      "force": true
    });
  }

  function locationAlbum() {
    var album_location = document.getElementById('album_location');

    if (document.getElementById('Latitude').value) {
      document.getElementById('Latitude').value = 0;
    }

    if (document.getElementById('Longitude').value) {
      document.getElementById('Longitude').value = 0;
    }
  }

  function locationSearch() {

    var formElements = document.getElementById('album_filter_form');
    formElements.addEvent('submit', function(event) {
      event.stop();
      submiForm();
    });
  }

  function advancedSearchEvents() {

    if (flag == 0) {
      if ($('fieldset-grp2'))
        $('fieldset-grp2').style.display = 'none';
      if ($('fieldset-grp1'))
        $('fieldset-grp1').style.display = 'none';
      flag = 1;
      $('advanced_search').value = 0;
      if ($('album_street'))
        $('album_street').value = '';
      if ($('album_country'))
        $('album_country').value = '';
      if ($('album_state'))
        $('album_state').value = '';
      if ($('album_city'))
        $('album_city').value = '';
      if ($('profile_type'))
        $('profile_type').value = '';
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

  window.addEvent('domready', function() {
    var search_category_id = '<?php echo $this->category_id ?>';
    if (search_category_id != 0) {
      var search_subcategory_id = '<?php echo $this->subcategory_id ?>';
      subcategories(search_category_id, search_subcategory_id, 1);
    }
  });

  var profile_type = 0;
  var previous_mapped_level = 0;
  var sitealbum_categories_slug = <?php echo json_encode($this->categories_slug); ?>;

  function showFields(cat_value, cat_level) {
    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value);
      if (profile_type == 0) {
        profile_type = '';
      } else {
        previous_mapped_level = cat_level;
      }
      $('profile_type').value = profile_type;
      changeFields($('profile_type'));
    }
  }

  var getProfileType = function(category_id) {
    var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitealbum')->getMapping(array('category_id', 'profile_type'))); ?>;
    for (i = 0; i < mapping.length; i++) {
      if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
    }
    return 0;
  }

  var subcategories = function(category_id, subcategory_id, domready)
  {

    if (domready == 0) {
      $('subcategory_id' + '-wrapper').style.display = 'none';
      clear('subcategory_id');
      $('subcategory_id').value = 0;
      $('categoryname').value = sitealbum_categories_slug[category_id];
    }

    if (category_id <= 0)
      return;

    var url = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'sub-category', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';

    en4.core.request.send(new Request.JSON({
      url: url,
      data: {
        format: 'json',
        category_id_temp: category_id,
      },
      onSuccess: function(responseJSON) {
        clear('subcategory_id');
        var subcatss = responseJSON.subcats;

        addOption($('subcategory_id'), " ", '0');
        for (i = 0; i < subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = subcategory_id;
          sitealbum_categories_slug[subcatss[i]['category_id']] = subcatss[i]['category_slug'];
        }

        if (category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        }
      }
    }), {'force': true});
  };

  function clear(ddName)
  {
    for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
    {
      document.getElementById(ddName).options[ i ] = null;
    }
  }

  function addOption(selectbox, text, value)
  {
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;

    if (optn.text != '' && optn.value != '') {
      $('subcategory_id').style.display = 'inline-block';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'inline-block';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'inline-block';
      selectbox.options.add(optn);
    } else {
      $('subcategory_id').style.display = 'none';
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'none';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
      selectbox.options.add(optn);
    }
  }
</script>

<div id="sitealbumlocation_location_map_none" style="display: none;"></div>