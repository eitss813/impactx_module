
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Yndynamicform/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-1.10.2.min.js')
    ->appendFile($baseUrl .'application/modules/Yndynamicform/externals/scripts/jquery-ui-1.11.4.min.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Yndynamicform/externals/scripts/dynamic.js');
?>

<style type="text/css">
    .yndform_my_entries_table td:nth-of-type(1):before {content: "<?php echo $this->translate('ID')?>";}
    .yndform_my_entries_table td:nth-of-type(2):before {content: "<?php echo $this->translate('Form Title')?>";}
    .yndform_my_entries_table td:nth-of-type(3):before {content: "<?php echo $this->translate('Submission Time')?>";}
    .yndform_my_entries_table td:nth-of-type(4):before {content: "<?php echo $this->translate('Attached Files')?>";}
</style>

<script type="text/javascript">

    jQuery(document).ready(function(){
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
</script>

<?php echo $this->search_form->render($this); ?>
<?php if ($this->paginator->getTotalItemCount()): ?>
    <table class="yndform_my_entries_table">
        <thead>
            <tr>
                <th field="entry_id">
                    <a href="javascript:void(0);" onclick="changeOrder('entry_id', 'ASC')">
                        <?php echo $this->translate("ID") ?>
                    </a>
                </th>
                <th>
                    <?php echo $this->translate("Form Title") ?>
                </th>
                <th field="creation_date">
                    <a href="javascript:void(0);" onclick="changeOrder('creation_date', 'ASC')">
                        <?php echo $this->translate("Submission Time") ?>
                    </a>
                </th>
                <th>
                    <?php echo $this->translate("Attached Files") ?>
                </th>
                <th>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $entry): ?>
            <tr>
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
                    <?php echo $entry->getForm() ?>
                </td>
                <td>
                    <?php $options = array();
                    $options['format'] = 'H:m a, F';
                    echo $this->locale()->toDateTime($entry->creation_date, $options)?>
                </td>
                <td class="yndform_attached">
                    <?php if ($filesCount = $entry->getFilesCount())
                        echo '<span class="ynicon yn-paperclip-o"> </span>' . $filesCount;
                    ?>
                </td>
                <td>
                  <ul>
                          <li> <?php
                        echo $this->htmlLink(array(
                            'route' => 'yndynamicform_entry_specific',
                            'module' => 'yndynamicform',
                            'controller' => 'entries', 'action' =>'view',
                            'entry_id' => $entry->getIdentity()), $this->translate('View'))
                        ?></li>
                      <?php if ($entry->isEditable()) {
                          echo '<li>';
                          echo $this->htmlLink(array(
                              'route' => 'yndynamicform_entry_specific',
                              'module' => 'yndynamicform',
                              'controller' => 'entries', 'action' => 'edit',
                              'entry_id' => $entry->getIdentity()), $this->translate('Edit'));
                          echo '</li>';
                      }
                      ?>
                      </ul>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    
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
                <?php echo $this->translate("No entries found.") ?>
            </span>
    </div>
<?php endif; ?>
<script type="text/javascript">
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
        $('yndform_my_entries').submit();
    }
</script>
