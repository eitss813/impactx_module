
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');
?>

<script type="text/javascript">
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
            buttonImage:'',
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
    var confirm_delete = false;

    function multiDelete() {
        if (!confirm_delete) {
            Smoothbox.open('<?php echo $this->url(array('module' => 'yndynamicform', 'controller' => 'manage', 'action' => 'multi-delete-confirm'), 'admin_default', true); ?>');
            return false;
        } else {
            return true;
        }
    }

    function submitForm() {
        confirm_delete = true;
        $('yndform_manage_form_table').submit();
    }

    function selectAll() {
        var i;
        var multidelete_form = $('yndform_manage_form_table');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                if ($(inputs[i]).hasClass('checkbox')) {
                    inputs[i].checked = inputs[2].checked;
                }
            }
        }
    }

    function changeOrder(listby, ele){
        var cellEle = ele.getParent();
        if ($(cellEle).hasClass('yndynamicform_order_asc')) {
            $(cellEle).removeClass('yndynamicform_order_asc');
            $(cellEle).addClass('yndynamicform_order_desc');
        } else {
            $(cellEle).removeClass('yndynamicform_order_desc');
            $(cellEle).addClass('yndynamicform_order_asc');
        }
        var direction = "ASC"
        if ($(cellEle).hasClass('yndynamicform_order_desc')) {
            direction = "DESC";
        }
        var orderElement  = new Element('input', {type: 'hidden', name:'direction', value:direction});
        var orderByElement = new Element('input', {type: 'hidden', name:'fieldOrder', value:listby});
        orderElement.inject($('filter_form'));
        orderByElement.inject($('filter_form'));
        $('filter_form').submit();
    }
</script>

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

<p>
    <?php echo $this->translate("YNDYNAMICFORM_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />
<div class="yndform_manage_form_admin_search clearfix">
    <?php echo $this->form->render($this);?>
</div>

    <form id='yndform_manage_form_table' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <?php echo $this->htmlLink(array(
    'route' => 'admin_default',
    'module' => 'yndynamicform',
    'controller' => 'form',
    'action' => 'create'),
    '<button>'.$this->translate('Add New Form').'</button>', array('class' => 'smoothbox',)) ?>
<?php if (count($this->paginator) > 0): ?>
        <button type='submit' value='delete' id="delete">
            <?php echo $this->translate("Delete Selected") ?>
        </button>
        <div class="uiYnfbpp_scroll">
        <table class='admin_table'>
            <thead>
            <tr>
                <th class='admin_table_short'>
                    <input onclick='selectAll();' type='checkbox' class='checkbox' />
                </th>
                <th field="title">
                    <a href="javascript:void(0);" onclick="changeOrder('title', this)">
                        <?php echo $this->translate("Title") ?>
                    </a>
                </th>
               <!-- <th field="category">
                    <a href="javascript:void(0);" onclick="changeOrder('category', this)">
                        <?php echo $this->translate("Category") ?>
                    </a>
                </th> -->
                <th field="creation_date">
                    <a href="javascript:void(0);" onclick="changeOrder('creation_date', this)">
                        <?php echo $this->translate("Creation Date") ?>
                    </a>
                </th>
                <th field="enable">
                    <a href="javascript:void(0);" onclick="changeOrder('enable', this)">
                        <?php echo $this->translate("Status") ?>
                    </a>
                </th>
                <th field="view_count">
                    <a href="javascript:void(0);" onclick="changeOrder('view_count', this)">
                        <?php echo $this->translate("Views") ?>
                    </a>
                </th>
                <th field="total_entries">
                    <a href="javascript:void(0);" onclick="changeOrder('total_entries', this)">
                        <?php echo $this->translate("Entries") ?>
                    </a>
                </th>
                <th field="conversation_rate">
                    <a href="javascript:void(0);" onclick="changeOrder('conversation_rate', this)">
                        <?php echo $this->translate("Conversion Rate") ?>
                    </a>
                </th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->paginator as $item): ?>
                <tr>
                    <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->form_id; ?>' value='<?php echo $item->form_id ?>' /></td>
                    <td><?php echo $item->title ?></td>
                 <!--  <td><?php ?></td>-->
                    <td><?php
                        $create_date = $this->locale()->toDateTime($item->creation_date);
                        echo date("d F Y", strtotime($create_date))?></td>
                    <td><?php echo $item->enable ? "Enabled" : "Disabled"?></td>
                    <td><?php echo $item->view_count ?></td>
                    <td><?php echo $item->total_entries ?></td>
                    <td><?php echo round($item->total_entries / $item -> view_count * 100) . '%' ?></td>
                    <td>
                    <div>
                        <div>
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'entries' , 'action' => 'list', 'form_id' => $item->form_id), $this->translate("Entries"))
                            ?>
                        </div>
                        <div>
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form' , 'action' => 'main-info', 'form_id' => $item->form_id), $this->translate("Settings"))
                            ?>
                        </div>
                        <div>
                            <a href='<?php echo $this->baseUrl();?>/admin/yndynamicform/form-fields?option_id=<?php echo $item->option_id ?>&id=<?php echo $item->form_id ?>'><?php echo $this->translate("Manage Fields") ?></a>
                        </div>
                        <div class="yndform_option_btn">
                            <span class="ynicon yn-caret-down"></span>
                            <ul class="yndform_option_items">
                                <li class="yndform_option_item">
                                    <?php
                                    echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'moderators', 'form_id' => $item->form_id), $this->translate("Moderators"))
                                    ?>
                                </li>
                                <li class="yndform_option_item">
                                    <?php
                                    echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'clone', 'form_id' => $item->form_id), $this->translate("Clone"), array('class' => 'smoothbox'))
                                    ?>
                                </li>
                                <li>
                                    <?php
                                    echo $this->htmlLink(
                                        array('route' => 'admin_default', 'module' => 'yndynamicform', 'controller' => 'form', 'action' => 'delete', 'id' => $item->form_id), $this->translate("Delete"), array('class' => 'smoothbox'))
                                    ?>
                                </li>
                            </ul>
                        </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </form>

    <br />

    <div>
        <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->params,
        ));
        ?>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no forms available.") ?>
        </span>
    </div>
<?php endif; ?>

<script language="javascript" type="text/javascript">
    var fieldOrder = '<?php echo (!empty($this->params['fieldOrder']))?$this->params['fieldOrder']:'' ?>';
    var direction = '<?php echo (!empty($this->params['fieldOrder']))?$this->params['direction']:'' ?>';
    if (fieldOrder) {
        var headerCells = $$('.admin_table > thead > tr > th');
        for (var i = 0; i < headerCells.length; i++) {
            if (headerCells[i].get('field') == fieldOrder) {
                if (direction == 'ASC') {
                    headerCells[i].addClass('yndynamicform_order_asc');
                } else if (direction == 'DESC') {
                    headerCells[i].addClass('yndynamicform_order_desc');
                }
                break;
            }
        }
    }
</script>

<script type="text/javascript">
    window.addEvent('domready', function() {
        dynamicOptions();
    });
</script>
