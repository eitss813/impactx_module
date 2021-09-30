<?php $coreMenus = Engine_Api::_()->getApi('menus', 'core');
$this->navigationProfile = $coreMenus->getNavigation("sitecrowdfunding_project_profile");
?>
<?php if (count($this->navigationProfile) > 0): ?>
    <h3 >
        Admin Actions
    </h3>
    <ul class="custom-nav-list1">
        <?php if($_SERVER['SERVER_NAME'] == 'stage.impactx.co'):?>
            <li>
                <a href="/network/projects/dashboard/overview/<?php echo $this->project_id; ?>" class="buttonlink seaocore_icon_edit menu_sitecrowdfunding_project_profile sitecrowdfunding_profile_edit" style="background-image: url();" target="">Edit Project</a>
            </li>
        <?php else: ?>
            <li>
                <a href="/net/projects/dashboard/overview/<?php echo $this->project_id; ?>" class="buttonlink seaocore_icon_edit menu_sitecrowdfunding_project_profile sitecrowdfunding_profile_edit" style="background-image: url();" target="">Edit Project</a>
            </li>
        <?php endif; ?>
        <!--<?php if($this->project->steps_completed): ?>
            <li>
                <a href="/projects/edit/<?php echo $this->project_id; ?>" class="buttonlink seaocore_icon_edit menu_sitecrowdfunding_project_profile sitecrowdfunding_profile_edit" style="background-image: url();" target="">Edit Project</a>
            </li>
        <?php else: ?>
            <li>
                <a href="/projects/create-new/step-one/<?php echo $this->project_id; ?>" class="buttonlink seaocore_icon_edit menu_sitecrowdfunding_project_profile sitecrowdfunding_profile_edit" style="background-image: url();" target="">Edit Project</a>
            </li>
        <?php endif; ?> -->
        <!-- <li>
            <a href="/projects/delete/<?php echo $this->project_id ?>/format/smoothbox" class="buttonlink smoothbox">Delete Project</a>
        </li> -->
    </ul>
    <?php //echo $this->navigation()->menu()->setContainer($this->navigationProfile)->setPartial(array('_customNavIcons.tpl', 'sitecrowdfunding'))->render(); ?>
<?php endif; ?>
<style type="text/css">
    .custom-nav-list1 > li{
        border-bottom: 1px solid #eee;
        font-size: 16px;
        padding: 10px;
        border-radius: 3px;
        font-family: 'fontawesome', Roboto, sans-serif;
    }
    .custom-nav-list1 > .active{
        background: #44AEC1;
        color: #fff;
    }
    .custom-nav-list1 li:hover{
        cursor: pointer;
    }
    .custom-nav-list1 li a{
        font-size: 16px !important;
    }
    .custom-nav-list1 li a::before{
        content: '';
    }
</style>