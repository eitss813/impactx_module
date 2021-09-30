<?php /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    create.tpl 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/sitepage_dashboard_main_header.tpl'; ?>
<div class="generic_layout_container layout_middle">
    <div class="generic_layout_container layout_core_content">
        <?php // include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/payment_navigation_views.tpl'; ?>
        <div class="layout_middle">
            <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/edit_tabs.tpl'; ?>
            <?php echo $this->
            partial('application/modules/Sitepage/views/scripts/sitepage_dashboard_section_header.tpl', array(
            'sitepage_id'=>$this->sitepage->page_id,'sectionTitle'=> 'Create Token', 'sectionDescription' => '')); ?>
            <div class="sitepage_edit_content">


                <div class="seaocore_settings_form">
                    <div class='settings'>
                        <?php echo $this->form->render($this); ?>
                    </div>
                </div>

                <script type="text/javascript">
                    window.addEvent('domready', function () {
                        expireAccessToken();
                    });

                    function expireAccessToken() {
                        if ($("expire-0") && $("expire-0").checked) {
                            $('expire_limit-wrapper').style.display = 'none';
                        } else {
                            $('expire_limit-wrapper').style.display = 'block';
                        }
                    }
                </script>
            </div>

        </div>
    </div>
</div>
<style>

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


    .global_form div.form-label{
        min-width: 173px !important;
    }

</style>
