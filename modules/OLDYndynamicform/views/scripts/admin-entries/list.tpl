
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');

$hasFileUpload = $this->yndform->hasFileUpload();
?>

<h2>
    <?php echo $this->translate("Dynamic Form Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->yndform->getTitle() . ' &#187; ' . $this->translate('Manage entries') ?></h3>

<br />

<p>
    <?php echo $this->translate("YNDYNAMICFORM_VIEWS_SCRIPTS_ADMINENTRIES_LIST_DESCRIPTION") ?>
</p>

<div class="yndform_manage_form_admin_search clearfix">
    <?php echo $this->search_form->render($this); ?>
</div>

<input type="hidden" id="advsearch_text" name="advsearch_text">

<?php if( count($this->paginator) ): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url();?>">
        <table class='admin_table'>
            <thead>
            <tr>
                <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th field="entry_id">
                    <a href="javascript:void(0);" onclick="changeOrder('entry_id', 'ASC')">
                        <?php echo $this->translate("ID") ?>
                    </a>
                </th>
                <th>
                </th>
                <th>
                    <?php echo $this->translate("Submitted By") ?>
                </th>
                <th field="creation_date">
                    <a href="javascript:void(0);" onclick="changeOrder('creation_date', 'ASC')">
                        <?php echo $this->translate("Submission Time") ?>
                    </a>
                </th>

            <?php if($hasFileUpload): ?>
                <th>
                    <?php echo $this->translate("Attached Files") ?>
                </th>
            <?php endif; ?>
                <th>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->paginator as $entry): ?>
                <tr>
                    <td><input type='checkbox' class='checkbox' name='delete_<?php echo $entry->entry_id; ?>' value='<?php echo $entry->entry_id; ?>' /></td>
                    <td>
                        <?php
                        echo $this->htmlLink(array(
                            'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries', 'action' =>'view',
                            'entry_id' => $entry->getIdentity()),
                            '#'.$entry->getIdentity())
                        ?>
                    </td>
                    <td>
                        <?php if(!$entry->isViewed())
                            echo '<span class="yndform_span">' . $this->translate("NEW") . '</span>';
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($entry->owner_id) {
                            echo $entry->getOwner();
                        } else if ($entry->user_email) {
                            echo "<a href='mailto:$entry->user_email'>" . $entry->user_email . "</a>";
                        } else {
                            echo $this->translate('Anonymous');
                        }
                        ?>
                    </td>
                    <td>
                        <?php $options = array();
                        $options['format'] = 'H:m a, F';
                        echo $this->locale()->toDateTime($entry->creation_date, $options)?>
                    </td>
                <?php if($hasFileUpload): ?>
                    <td>
                        <?php if ($filesCount = $entry->getFilesCount())
                            echo '<i class="ynicon yn-paperclip-o"> </i>' . $filesCount;
                        ?>
                    </td>
                <?php endif; ?>
                    <td>
                    <ul>
                        <li><?php
                        echo $this->htmlLink(array(
                            'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries', 'action' =>'view',
                            'entry_id' => $entry->getIdentity()), $this->translate('View'))
                        ?></li>
                        <li class="yndform_border"><?php
                        echo $this->htmlLink(array(
                            'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries', 'action' =>'edit',
                            'entry_id' => $entry->getIdentity()), $this->translate('Edit'))
                        ?></li>
                        <li><?php
                        echo $this->htmlLink(array(
                            'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries', 'action' =>'delete',
                            'entry_id' => $entry->getIdentity()), $this->translate('Delete'),
                            array('class' => 'smoothbox'))
                        ?></li>
                    </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <br />

        <div class='buttons'>
            <button type='button' onclick="multiDelete()"><?php echo $this->translate("Delete Selected") ?></button>
        </div>
        <a href="<?php echo $this->url(array('module'=>'yndynamicform', 'controller' => 'entries', 'action' => 'export-csv', 'form_id' => $this->yndform->getIdentity()),'yndynamicform_entry_general'); ?>" onclick="javascript:void(0);">
            <button type="button"><span class="ynicon yn-downloads"></span><?php echo $this->translate('Export to CSV') ?></button>
        </a>
    </form>

    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
<?php else: ?>
    <div class="tip">
            <span>
                <?php echo $this->translate("No entries found.") ?>
            </span>
    </div>
<?php endif; ?>

<div id="yndform_hidden_form_wrapper" style="display: none;">
    <div id="yndform_hidden_form">
        <?php echo $this->hidden_form->render($this); ?>
    </div>
</div>

<script type="text/javascript">

    $('advsearch_text').set('value', '<?php echo !empty($this->params['conditional_logic']) ? addslashes(json_encode($this->params['conditional_logic'])) : '' ?>');

    var ynDynamicFormCalendar= {
        currentText: '<?php echo $this->string()->escapeJavascript($this->translate('Today')) ?>',
        monthNames: ['<?php echo $this->string()->escapeJavascript($this->translate('January')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('February')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('March')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('April')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('June')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('July')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('August')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('September')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('October')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('November')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('December')) ?>',
        ],
        monthNamesShort: ['<?php echo $this->string()->escapeJavascript($this->translate('Jan')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Feb')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mar')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Apr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('May')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jun')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Jul')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Aug')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sep')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Oct')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Nov')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Dec')) ?>',
        ],
        dayNames: ['<?php echo $this->string()->escapeJavascript($this->translate('Sunday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Monday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tuesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Wednesday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Thursday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Friday')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Saturday')) ?>',
        ],
        dayNamesShort: ['<?php echo $this->translate('Su') ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        dayNamesMin: ['<?php echo $this->string()->escapeJavascript($this->translate('Su')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Mo')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Tu')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('We')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Th')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Fr')) ?>',
            '<?php echo $this->string()->escapeJavascript($this->translate('Sa')) ?>',
        ],
        firstDay: 0,
        isRTL: <?php echo $this->layout()->orientation == 'right-to-left'? 'true':'false' ?>,
        showMonthAfterYear: false,
        yearSuffix: ''
    };

    jQuery(document).ready(function(){
        jQuery.datepicker.setDefaults(ynDynamicFormCalendar);
        jQuery('#start_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '',
        });

        jQuery('#to_date').datepicker({
            firstDay: 1,
            dateFormat: 'yy-mm-dd',
            showOn: "button",
            buttonImage:'<?php echo $this->baseUrl() ?>/application/modules/Yndynamicform/externals/images/calendar.png',
            buttonImageOnly: true,
            buttonText: '<?php echo $this -> translate("Select date")?>'
        });

    });

    function multiDelete()
    {
        var selected = $('multidelete_form').getElements('.checkbox:checked').length;
        if (!selected) {
            alert('<?php echo $this->translate("Please select at least one entry to delete")?>');
            return false;
        }
        Smoothbox.open($('yndform_hidden_form'));
        return false;
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }

    function submitForm() {
        Smoothbox.close();
        $('multidelete_form').submit();
    }

    function changeOrder(listby, default_direction){
        var currentOrder = '<?php echo $this->params['fieldOrder'] ?>';
        var currentOrderDirection = '<?php echo $this->params['direction'] ?>';

        // Just change direction
        if( listby == currentOrder ) {
            $('direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
        } else {
            $('fieldOrder').value = listby;
            $('direction').value = default_direction;
        }
        $('filter_form').submit();
    }
</script>