<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _category.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="form-wrapper" id="subcategory_id-wrapper" style='display:none;'>
    <div class="form-label" id="subcategory_id-label">
        <label class="optional" for="subcategory_id"><?php echo $this->translate('Sub-Category'); ?></label>
    </div>
    <div class="form-element" id="subcategory_id-element">
        <select id="subcategory_id" name="subcategory_id" onchange='setHiddenValues("subcategory_id");'></select>
    </div>
</div>

<script type="text/javascript">

    function setHiddenValues(element_id) {
        $('hidden_' + element_id).value = $(element_id).value;
    }

    var subcategories = function (category_id, subcategory_id, domready)
    {
        if (domready == 0) {
            $('subcategory_id' + '-wrapper').style.display = 'none';
            clear('subcategory_id');
            $('subcategory_id').value = 0;
            $('hidden_subcategory_id').value = 0;
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
            onSuccess: function (responseJSON) {
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

    window.addEvent('domready', function () {
        if ($('hidden_category_id-wrapper')) {
            $('hidden_category_id-wrapper').style.display = 'none';
            if ($('hidden_subcategory_id-wrapper'))
                $('hidden_subcategory_id-wrapper').style.display = 'none';

            if ($("hidden_category_id").value) {
                subcategories($("hidden_category_id").value, $("hidden_subcategory_id").value, 1);
            }
        }
    });

</script>