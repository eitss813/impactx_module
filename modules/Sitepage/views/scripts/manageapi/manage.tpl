<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    manage.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Manage Api', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">

                <div style="color: #44AEC1;">
                <?php
                  echo $this->htmlLink(array('action' => 'create', 'reset' => false), $this->translate('Create New API Consumer'), array(
                 'class' => 'fa fa-plus',
                ))
                ?>
                </div>
                <br />

                <div class='admin_search'>
                    <div class="clear">
                        <div class="search">

                            <form method="get" class="global_form_box" action="<?php echo $this->url(array( 'controller' => 'manageapi', 'action' => 'manage','page_id'=>$this->page_id),  false); ?>">

                                <div style=" margin-bottom: 15px;">
                                    <label id="search_label">
                                        <?php echo $this->translate("Title") ?>
                                    </label>
                                    <?php if (empty($this->title)): ?>
                                    <input type="text" name="title" />
                                    <?php else: ?>
                                    <input type="text" name="title" value="<?php echo $this->title ?>"/>
                                    <?php endif; ?>
                                </div>

                                <div style=" margin-bottom: 15px;">
                                    <label id="search_label">
                                        <?php echo $this->translate("Consumer Key") ?>
                                    </label>
                                    <?php if (empty($this->title)): ?>
                                    <input type="text" name="key" />
                                    <?php else: ?>
                                    <input type="text" name="key" value="<?php echo $this->key ?>"/>
                                    <?php endif; ?>
                                </div>

                                <div style=" margin-bottom: 15px;">
                                    <label id="search_label">
                                        <?php echo $this->translate("Consumer Secret") ?>
                                    </label>
                                    <?php if (empty($this->title)): ?>
                                    <input type="text" name="secret" />
                                    <?php else: ?>
                                    <input type="text" name="secret" value="<?php echo $this->secret ?>"/>
                                    <?php endif; ?>
                                </div>

                                <div style=" margin-bottom: 15px;">
                                    <label id="search_label">
                                        <?php echo "Status"; ?>
                                    </label>
                                    <select id="status" name="status" style="width: 164px !important;height: 32px;">
                                        <option value="2" ></option>
                                        <?php
                                        if ($this->status == 1):
                                        echo '<option value="1" selected="selected">Enabled</option>';
                                        else:
                                        echo '<option value="1">Enabled</option>';
                                        endif;


                                        if ($this->status == 0):
                                        echo '<option value="0" selected="selected">Disabled</option>';
                                        else:
                                        echo '<option value="0">Disabled</option>';
                                        endif;
                                        ?>
                                    </select>
                                </div>

                                <div >
                                    <div class="buttons" >
                                        <button type="submit" name="search" ><?php echo $this->translate("Search") ?></button>
                                        <button type="submit" onclick="reset()" name="search" ><?php echo $this->translate("Reset") ?></button>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>

                <br />

                <?php if (!empty($this->paginator)): ?>
                <div class='admin_results'>
                    <div>
                        <?php $count = $this->paginator->getTotalItemCount() ?>
                        <?php echo $this->translate(array("%s consumer found.", "%s consumers found.", $count), $this->locale()->toNumber($count))
                        ?>
                    </div>
                    <div>
                        <?php echo $this->paginationControl($this->paginator); ?>
                    </div>
                </div>
                <?php endif; ?>

                <br />


                <?php if (!empty($this->paginator) && count($this->paginator)): ?>
                <!--<form id='multidelete_form' method="post" action="<?php // echo $this->url();       ?>" onSubmit="return multiDelete()">-->
               <div >
                   <table class='admin_table'>
                       <thead>
                       <tr>
                           <!--<th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>-->
                           <th class='admin_table_short'>ID</th>
                           <th><?php echo $this->translate("Title") ?></th>
                           <th><?php echo $this->translate("Consumer Key") ?></th>
                           <th><?php echo $this->translate("Consumer Secret") ?></th>
                           <th><?php echo $this->translate("Status") ?></th>
                           <th><?php echo $this->translate("Options") ?></th>
                       </tr>
                       </thead>
                       <tbody>
                       <?php foreach ($this->paginator as $item): ?>
                       <tr>
                           <!--<td><input type='checkbox' class='checkbox' name='delete_<?php // echo $item->getIdentity();        ?>' value="<?php // echo $item->getIdentity();        ?>" /></td>-->
                           <td><?php echo $item->consumer_id ?></td>
                           <td title="<?php echo $item->title ?>"><?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($item->title, 20) ?></td>
                           <td title="<?php echo $item->key ?>"><i><?php echo $item->key; ?></i></td>
                           <td><i><?php echo $item->secret; ?></i></td>
                           <td>
                               <?php echo ( $item->status ? $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/approved.gif', '', array('title' => $this->translate('Disable it'))), array('class' => 'smoothbox')) : $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'status', 'id' => $item->getIdentity()), $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/images/disapproved.gif', '', array('title' => $this->translate('Enable it'))), array('class' => 'smoothbox')) ) ?>
                           </td>
                           <td>
                               <!--  <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'siteapi', 'controller' => 'consumers', 'action' => 'tokens', 'consumer_id' => $item->getIdentity()), $this->translate('OAuth Tokens')); ?>
                                 -->
                               <?php
                            echo $this->htmlLink(
                               array('route' => 'sitepage_api',  'controller' => 'manageapi', 'action' => 'edit', 'id' => $item->getIdentity(),'page_id'=>$this->page_id), $this->translate("edit"))
                               ?>
                           </td>
                       </tr>
                       <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>

                <br />

                <?php else: ?>
                <div class="tip">
        <span>
            <?php echo $this->translate("There are no OAuth Consumer available yet.") ?>
        </span>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
<script>
    function reset(){
        let url= 'https://stage.impactx.co/organizations/manageapi/manage/page_id/7';
        console.log('url',url);
       // window.location.href = url;
    }
</script>
<style>
    #search_label{
        width: auto;
        min-width: 150px;
        text-align: left;
        padding: 4px 10px 0 0;
        margin-bottom: 6px;
        overflow: hidden;
        float: left;
        clear: left;
        font-size: 13px;
        color: #444;
        -webkit-box-sizing: border-box;
        -mox-box-sizing: border-box;
        box-sizing: border-box;
        font-weight: 500;
    }
    th.header_title_big {
        width: 15%;
    }
    th.header_title {
        width: 10%;
    }

    table.transaction_table.admin_table.seaocore_admin_table {
        width: 100%;
    }

    table.admin_table tbody tr:nth-child(even) {
        background-color: #f8f8f8
    }

    table.admin_table td{
        padding: 10px;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        border-bottom: 1px solid #aaa;
        font-weight: bold;
        height: 45px;
        padding-top: 7px;
        padding-bottom: 7px;
        white-space: nowrap;
        color: #5ba1cd !important;
    }
    .admin_table_centered {
        text-align: center;
    }
    .fa fa-plus{
        color: #44AEC1 !important;
        margin-right: 7px;
        font-size: 17px !important;
    }
    .fa-plus:before {
        font-size: 17px !important;
        content: #44AEC1 !important;
        margin-right: 6px;
    }

</style>























