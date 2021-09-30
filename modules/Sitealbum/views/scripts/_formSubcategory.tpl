<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formSubcategory.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'sitealbum')->defaultProfileId();
$defaultProfileFieldId = "0_0_$defaultProfileFieldId";

$album_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', 0);
$profile_type = 0;

if (!empty($album_id)) {
  $album = Engine_Api::_()->getItem('album', $album_id);
  $profile_type = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getProfileType(array('categoryIds' => null, 'category_id' => $album->category_id));
}

$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'sitealbum')->getCatDependancyArray();
?>

<?php
echo "
	<div id='subcategory_backgroundimage' class='form-wrapper'></div>
	<div id='subcategory_id-wrapper' class='form-wrapper' style='display:none;'>
		<div id='subcategory_id-label' class='form-label'>
		 	<label for='subcategory_id' class='optional'>" . $this->translate('Subcategory') . "</label>
		</div>
		<div id='subcategory_id-element' class='form-element'>
			<select name='subcategory_id' id='subcategory_id' onchange='showFields($(this).value, 2);'>
			</select>
		</div>
	</div>";
?>
<?php
?>
<script type="text/javascript">
  var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';

  var defaultProfileFieldId = '<?php echo $defaultProfileFieldId; ?>';
  var actionType = '<?php echo $album_id; ?>';
  var profile_type = '<?php echo $profile_type ?>';
  var previous_mapped_level = 0;

  function showFields(cat_value, cat_level) {
    if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
      profile_type = getProfileType(cat_value);
      if (profile_type == 0) {
        profile_type = '';
      } else {
        previous_mapped_level = cat_level;
      }
      $(defaultProfileFieldId).value = profile_type;
      changeFields($(defaultProfileFieldId));
    }
  }

  var subcategory_id = '';
  var subcategories = function(category_id, subcategory_id)
  {
    if (cateDependencyArray.indexOf(category_id) == -1 || category_id == 0) {
      if ($('subcategory_id-wrapper'))
        $('subcategory_id-wrapper').style.display = 'none';
      if ($('subcategory_id-label'))
        $('subcategory_id-label').style.display = 'none';
      return;
    }

    if ($('subcategory_backgroundimage'))
      $('subcategory_backgroundimage').style.display = 'inline-block';
    if ($('subcategory_id'))
      $('subcategory_id').style.display = 'none';
    if ($('subcategory_id-label'))
      $('subcategory_id-label').style.display = 'none';

    var url = '<?php echo $this->url(array('module' => 'sitealbum', 'controller' => 'index', 'action' => 'sub-category'), "default", true); ?>';

    if ($('subcategory_backgroundimage'))
      $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/core/loading.gif" alt="" /></div>';

    en4.core.request.send(new Request.JSON({
      url: url,
      data: {
        format: 'json',
        category_id_temp: category_id
      },
      onSuccess: function(responseJSON) {
        $('subcategory_backgroundimage').style.display = 'none';
        clear('subcategory_id');
        var subcatss = responseJSON.subcats;

        addOption($('subcategory_id'), " ", '0');
        for (i = 0; i < subcatss.length; i++) {
          addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
          $('subcategory_id').value = subcategory_id;
        }

        if (category_id == 0) {
          clear('subcategory_id');
          $('subcategory_id').style.display = 'none';
          if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        }
      }
    }));
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