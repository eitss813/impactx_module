<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    CB Page Analytics
 * @copyright  Copyright Consecutive Bytes
 * @license    https://consecutivebytes.com/agreement
 * @author     Consecutive Bytes
 */
?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/scripts/jquery-1.12.4.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/scripts/jquery-ui.js'); ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Cbpageanalytics/externals/styles/jquery-ui.css'); ?>

<style>
    div.search select{
        height: 30px;
        max-width: 250px;
    }
    .admin_table {
        width: 100%;
    }
</style>

<h2>
    <?php echo $this->translate('CB - Page Analytics Plugin') ?>
</h2>

<p>
    <?php echo $this->translate("Here you can view and manage all the page tracking done by your plugin. You can sort, search & group these records as your requirement. If you need more help please contact at <a href='mailto:support@consecutivebytes.com'>support@consecutivebytes.com</a> or create a ticket from your user panel on our <a href='http://www.consecutivebytes.com' target='_blank'>website</a>.") ?>
</p>
<br><br>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div>
    <p><?php echo $this->translate("Here, you can keep track of visits on all pages");?></p>
    <br>

</div>  
<?php if (empty($this->pageAvailable)): ?>
    <div class="tip">
        <span>
            There are no page visits registered yet.
        </span>
    </div>
    <?php
    return;
endif;
?>
<div class="search">
    <form name="credit_manage_code_search_form" id="credit_manage_code_search_form" method="get" class="global_form_box" action="">
        <input type="hidden" name="post_search" />
        <div>
            <label>Specific Page</label>
            <?php if (empty($this->title)): ?>
                <input type="text" id="title" name="title" /> 
            <?php else: ?>
                <input type="text" id="title" name="title" value="<?php echo $this->title ?>"/>
            <?php endif; ?>
        </div>
        
        <div>
            <label>Specific Member</label>
            <input type="text" id="user_id" name="user" value="<?php echo (!empty($this->user))? $this->user: ''; ?>" placeholder="Enter member name">   
        </div>
        
        <div>
            <label>Group By</label>
            <select id="group_by" name="group_by">
                <option value="" <?php echo (empty($this->group_by))? 'selected' : ''; ?> ></option>
                <option value="page_name" <?php echo (!empty($this->group_by) && $this->group_by == 'page_name')? 'selected' : ''; ?> >By pagename</option>
                <option value="user_id" <?php echo (!empty($this->group_by) && $this->group_by == 'user_id')? 'selected' : ''; ?> >By username</option>
                <option value="creation_date" <?php echo (!empty($this->group_by) && $this->group_by == 'creation_date')? 'selected' : ''; ?> >By visit date</option>
            </select>
        </div>

        <div style="margin-top:16px;">
            <button type="submit" name="search" >Search</button>
        </div>
    </form>
</div>

<div class='admin_search'>
    <?php echo $this->formFilter->render($this) ?>
</div>

<br>
<div class="mbot10">
    <?php $count = $this->paginator->getTotalItemCount(); ?>
    <?php echo $this->translate(array("%s record found", "%s records found", $count), $count) ?>
</div>
<br>

<?php if (count($this->paginator)): ?>
    <form id='code_form' method="post" action="<?php echo $this->url(); ?>">
        <table class='admin_table'>
            <thead>
                <tr>
                    <?php $class = ( $this->order == 'page_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('page_id', 'ASC');">ID</a></th>

                    <?php $class = ( $this->order == 'title' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');">Title</a></th>
                    
                    <th align="center">Page URL</th>

                    <?php $class = ( $this->order == 'user_id' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>"  align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'ASC');">Viewer</a></th>
            
                    <?php if (!empty($this->group_by)): ?>
                        <th align="center">Total Views</th>
                    <?php endif; ?>
                    
                    <?php $class = ( $this->order == 'creation_date' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->order_direction) : '' ) ?>
                    <th class="<?php echo $class ?>" align="center"><a href="javascript:void(0);" onclick="javascript:changeOrder('creation_date', 'ASC');">Visited on</a></th>
                    
                    <th align="center">Options</th>
                </tr> 
            </thead>
            
            <tbody>
                <?php
                $k = 0;
                foreach ($this->paginator as $item):
                    if (isset($item->user_id) && !empty($item->user_id) && $item->user_id != 0) {
                        $user = Engine_Api::_()->getItem('user', $item->user_id);

                        $viewer = $this->htmlLink($user->getHref(), $user->getTitle(), array('target' => '_blank'));
                    } else {
                        $viewer = 'Not Logged in';
                    }

                    if (isset($item->page_original_id) && !empty($item->page_original_id)) {
                        $pageData = Engine_Api::_()->getItem('core_page', $item->page_original_id);

                        $page = $this->htmlLink($item->page_url, $pageData->getTitle(), array('target' => '_blank'));
                    } else {
                        $title =  (!empty($item->getTitle()))? $item->getTitle() : 'No title';
                        $page = $this->htmlLink($item->page_url, $title, array('target' => '_blank'));
                    }
                    ?>
                    <tr>
                        <td><?php echo $item->page_id ?></td>
                        <td><?php echo $page; ?></td>
                        <td><?php echo $this->htmlLink($item->page_url, $item->page_url, array('target' => '_blank')); ?></td>
                        <td><?php echo $viewer ?></td>
                        <?php if (!empty($this->group_by)): ?>
                            <td><?php echo $item->total_views ?></td>
                        <?php endif; ?>
                        <td><?php echo date('dS F Y', strtotime($item->creation_date)); ?></td>
                        <td>   
                            <?php
                            echo $this->htmlLink(
                                    array('route' => 'admin_default', 'module' => 'cbpageanalytics', 'controller' => 'settings', 'action' => 'view-detail', 'id' => $item->page_id), "Details", array('class' => 'smoothbox'))
                            ?>  | 
                            <?php
                            echo $this->htmlLink(
                                    array('route' => 'admin_default', 'module' => 'cbpageanalytics', 'controller' => 'settings', 'action' => 'delete', 'id' => $item->page_id), "Delete", array('class' => 'smoothbox'))
                            ?>        
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>

    <br/>

<?php endif; ?>
<div style="clear:left;">

    <?php
    echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    ));
    ?>
</div>

<script type="text/javascript">
    var currentOrder = '<?php echo $this->order ?>';
    var currentOrderDirection = '<?php echo $this->order_direction ?>';
    var changeOrder = function (order, default_direction) {
        // Just change direction
        if (order == currentOrder) {
            $('order_direction').value = (currentOrderDirection == 'ASC' ? 'DESC' : 'ASC');
        } else {
            $('order').value = order;
            $('order_direction').value = default_direction;
        }

        $('filter_form').submit();
    };
    function isNumberKey(evt) {
        var charCode = (evt.charCode) ? evt.which : event.keyCode

        if (charCode > 31 && (charCode < 48 || charCode > 57) || charCode == 46)
            return false;

        return true;
    }
</script>

<script>jQuery.noConflict();</script>
<script type="text/javascript">
    var names = <?php echo json_encode($this->users); ?>
    
    var namesArray = [];
    jQuery.each(names, function (key, value) {
        namesArray.push({id: key, name: value});
    });
    
    jQuery('#user_id').keyup(function () {
        jQuery('#user_id').autocomplete({
            source: function (request, response) {
                response(jQuery.map(namesArray, function (value, key) {

                    var name = value.name.toUpperCase();
                    if (name.indexOf(request.term.toUpperCase()) != -1) {
                        return {
                            label: value.name,
                            value: value.id
                        }
                    } else {
                        return null;
                    }
                }));
            },
            select: function (event, ui) {
                event.preventDefault();
                jQuery('#user_id').val(ui.item.label);
            }
        });
    });
</script> 