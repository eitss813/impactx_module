<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitealbum
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit-photos.tpl 2011-08-026 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="layout_middle">
<h3>
  <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>
  (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->photos_count),$this->locale()->toNumber($this->album->photos_count)) ?>)
</h3>

<?php if( $this->paginator->count() > 0 ): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
<?php
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitealbum/externals/styles/style_sitealbum.css')
?>
<form action="<?php echo $this->escape($this->form->getAction()) ?>" method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->album_id; ?>
  <ul class='albums_editphotos'>
    <?php foreach( $this->paginator as $photo ): ?>
      <li>
        <div class="albums_editphotos_photo">
          <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
        </div>
        <div class="albums_editphotos_info">
          <?php
            $key = $photo->getGuid();
            echo $this->form->getSubForm($key)->render($this);
          ?>
          <div class="albums_editphotos_cover">
            <input id="main_photo_id_<?php echo $photo->photo_id ?>" type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity() ): ?> checked="checked"<?php endif; ?> />
          </div>
          <div class="albums_editphotos_label">
            <label for="main_photo_id_<?php echo $photo->getIdentity() ?>"><?php echo $this->translate('Main Photo');?></label>
          </div>
            
         <div class="albums_editphotos_cover">
             <input id="change_date_photo_id_<?php echo $photo->photo_id ?>" type="checkbox" name="changedate" value="<?php echo $photo->getIdentity() ?>" onclick="changeDate(<?php echo $photo->photo_id ?>);" />
          </div>
          <div class="albums_editphotos_label">
            <label for="change_date_photo_id_<?php echo $photo->getIdentity() ?>"><?php echo $this->translate('Change Date');?></label>
          </div>
            
         <br />
         <div style="display:none;" class="media_option_add_date" id="photo-add-date-<?php echo $photo->getIdentity() ?>" >
            <select id="year-<?php echo $photo->getIdentity() ?>" name="year-<?php echo $photo->getIdentity() ?>">
                <option label="Year" value="Year" disabled="disabled"><?php echo $this->translate('Year'); ?></option>
                <?php $curYear = date('Y'); ?>
                <?php for ($i = 0; $i <= 110; $i++) : ?>
                    <option label="<?php echo $curYear; ?>" value="<?php echo $curYear; ?>" <?php if ($i == 0): ?> selected="selected" <?php endif; ?>><?php echo $curYear; ?></option>
                    <?php $curYear--; ?>
                <?php endfor; ?>
            </select>
            <a onclick="showMonth(0, <?php echo $photo->getIdentity() ?>);" href="javascript:void(0);" id="addmonth-<?php echo $photo->getIdentity() ?>" style="display:none;"><?php echo $this->translate('+ Add Month'); ?></a>
            <select id="month-<?php echo $photo->getIdentity() ?>" name="month-<?php echo $photo->getIdentity() ?>" onblur="showAddmonth(2, <?php echo $photo->getIdentity() ?>)" onclick="showMonth(1, <?php echo $photo->getIdentity() ?>)" onchange="showAddday(2, <?php echo $photo->getIdentity() ?>)" style="display:block;">
                <option label="Month" value="0"><?php echo $this->translate('Month'); ?></option>
                <?php $curMonth = (int) date('m'); ?>
                <?php for ($k = 1; $k <= 12; $k++): ?>
                    <?php $month = date('F', mktime(0, 0, 0, $k, 1)); ?>
                    <option label="<?php echo $month; ?>" value="<?php echo $k; ?>" <?php if ($k == $curMonth): ?> selected="selected" <?php endif; ?>><?php echo $this->translate($month); ?></option>
                <?php endfor; ?>
            </select>
            <a style="display:none;" id="addday-<?php echo $photo->getIdentity() ?>"  onclick="showDay(0, <?php echo $photo->getIdentity() ?>);" href="javascript:void(0);"><?php echo $this->translate('+ Add Day'); ?></a>
            <select id="day-<?php echo $photo->getIdentity() ?>" name="day-<?php echo $photo->getIdentity() ?>" style="display:block;"></select>
        </div>
   
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  
  <?php echo $this->form->submit->render(); ?>
</form>


<?php if( $this->paginator->count() > 0 ): ?>
  <?php echo $this->paginationControl($this->paginator); ?>
<?php endif; ?>
</div>

<script type="text/javascript">
   
    
    var addDay = 0;
    var addMonth = 0;
    function showMonth(month, ele) {  
        addMonth = month;
        document.getElementById('month-' + ele).style.display = 'block';
        var sel = document.getElementById("month-" + ele);
        var year = document.getElementById("year-" + ele);
        var selectedTextYear = year.options[year.selectedIndex].text;
        var selectedValueYear = year.options[year.selectedIndex].value;
        var currentYear = '<?php echo (int) date("Y"); ?>';
        //get the selected option
        var selectedTextMonth = sel.options[sel.selectedIndex].text;
        var selectedValueMonth = sel.options[sel.selectedIndex].value;

        var selday = document.getElementById("day-" + ele);
        //get the selected option
        selday.options[selday.selectedIndex].text = 0;
        selday.options[selday.selectedIndex].value = 0;

        if (selectedTextMonth != 'Month') {
            if (parseInt(selectedValueMonth) > '<?php echo (int) date("m"); ?>' && (currentYear == parseInt(selectedTextYear))) {
                sel.selectedIndex = "Month";
                document.getElementById('addday-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).value = 0;
            }
            else {
                document.getElementById('addday-' + ele).style.display = 'block';
                document.getElementById('day-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).value = 0;
            }
        } else {
            document.getElementById('addday-' + ele).style.display = 'none';
            document.getElementById('day-' + ele).style.display = 'none';
        }
    }

    function showAddmonth(month, ele) {  
        if (addMonth == 0 || month == 2) {
            addMonth = 0;
            var sel = document.getElementById("month-" + ele);
            //get the selected option
            var selectedText = sel.options[sel.selectedIndex].text;
            if (selectedText == 'Month') {
                document.getElementById('month-' + ele).style.display = 'none';
                document.getElementById('addday-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).style.display = 'none';
                document.getElementById('day-' + ele).value = 0;
            }
        }
    }

    function showDay(day, ele) {
        addDay = day;
        clear('day-' + ele);
        if (document.getElementById('addday-' + ele))
            document.getElementById('addday-' + ele).style.display = 'none';
        if (document.getElementById('day-' + ele))
            document.getElementById('day-' + ele).style.display = 'block';
        if (document.getElementById('day-' + ele))
            addOption($('day-' + ele), '<?php echo $this->translate("Day"); ?>', 0);
        if (document.getElementById('month-' + ele))
            var month_day = document.getElementById('month-' + ele).value;
        if (document.getElementById('year-' + ele))
            var year_day = document.getElementById('year-' + ele).value;
        var num = new Date(year_day, month_day, 0).getDate();
        
<?php $curMonth = (int) date('m'); ?>
        var currentDate = '<?php echo (int) date('d'); ?>';
        if (month_day == '<?php echo (int) date("m"); ?>') {
            for (j = 1; j <= currentDate; j++) {
                if (document.getElementById('day-' + ele))
                    addOption($('day-' + ele), j, j);
            }
        } else {
            for (j = 1; j <= num; j++) {
                if (document.getElementById('day-' + ele))
                    addOption($('day-' + ele), j, j);
            }
        }
    }

    if ($('day-album')) {

        $('day-album').removeEvents().addEvent('blur', function (event) {
            showAddday(2, 'album');
        });

        $('day-album').removeEvents().addEvent('click', function (event) {
            showDay(1, 'album');
        });

        $('day-album').removeEvents().addEvent('change', function (event) {
            showAddday(2, 'album');
        });
    }

    function addOption(selectbox, text, value)
    {
        var optn = document.createElement("OPTION");
        optn.text = text;
        optn.value = value;
        selectbox.options.add(optn);
    }

    function clear(ddName)
    {
        for (var k = (document.getElementById(ddName).options.length - 1); k >= 0; k--)
        {
            document.getElementById(ddName).options[ k ] = null;
        }
    }

    function showAddday(day, ele) {  
        var sel = document.getElementById("day-" + ele);
        if (addDay == 0 || day == 2) {
            addDay = 0;
            //get the selected option
            var selectedText = sel.options[sel.selectedIndex].text;
            var selectedValue = sel.options[sel.selectedIndex].value;
            var selYear = document.getElementById("year-" + ele);
            var currentYear = '<?php echo (int) date("Y"); ?>';
            var selectedTextYear = selYear.options[selYear.selectedIndex].text;
            var selectedYearValue = selYear.options[selYear.selectedIndex].value;
            var selMonth = document.getElementById("month-" + ele);
            var currentMonth = selMonth.options[selMonth.selectedIndex].text;
            var selectedMonthValue = selMonth.options[selMonth.selectedIndex].value;
            if (selectedText == 'Day') {
                document.getElementById('addday-' + ele).style.display = 'block';
                document.getElementById('day-' + ele).style.display = 'none';
            }
            else {
                if (parseInt(selectedValue) > '<?php echo (int) date("d"); ?>' && (currentYear == parseInt(selectedTextYear)) && parseInt(selectedMonthValue) == '<?php echo (int) date("m"); ?>') {
                    sel.selectedIndex = "Day";
                    sel.value = 0;
                }
                else {
                    document.getElementById('addday-' + ele).style.display = 'none';
                    document.getElementById('day-' + ele).style.display = 'block';
                }
            }
        }
    }
    
    function changeDate(id) {
      $('photo-add-date-'+id).toggle();
      showDay(0, id);
      var sel = document.getElementById("day-"+id);
      var currentDate = '<?php echo (int) date("d"); ?>';
      sel.value = currentDate;
    }
</script>
