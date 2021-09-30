<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
            <div class="main_metrics_info" id="web_view">
                <div class="main_metrics_common_container">
                    <div class="main_metrics_common_sub_container">
                        <div class="main_metrics_info_container" id="main_metrics_container_info">
                            <div class="main_metrics_info_title">
                                <h3>
                                    <a style="cursor: default" href="javascript:void(0);">
                                        Metric Name: <?php echo $this->metric_details['metric_name'];?>
                                    </a>
                                </h3>
                            </div>
                            <div class="main_metrics_sub_info_desc">
                                <ul style="list-style-type:none;padding-left: 10px;">
                                    <li>
                                        <?php echo $this->metric_details['metric_description'];?>
                                    </li>
                                </ul>
                            </div>

                            <?php if($this->metric_details['page_id']):?>
                            <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->metric_details['page_id']);?>
                            <div class="main_metrics_info_icon" title="Community Independence Initiative">
                                <?php echo $this->itemPhoto($sitepage, 'thumb.profile', '', array()); ?>
                                <div class="organization_container">
                                    <a style="text-align: left" class="main_metrics_parent_title" href="<?php echo $sitepage->getHref();?>">
                                        <b>Organisation :</b> <?php echo $sitepage->getTitle();?>
                                    </a>
                                </div>
                            </div>
                            <?php endif;?>
                        </div>
                    </div>
                    <div class="main_metrics_info_options">
                        <div class="main_metrics_info_status">
                            <div>
                                <div class="generic_layout_container layout_metric_options">
                                    <div>
                                        <div class="metric_options_successful metric_options">
                                            <a href="<?php echo $this->url(array('action' => 'edit','metric_id' => $this->metric_details['metric_id'] ), 'sitepage_metrics', true) ?>" class="common_btn_custom">
                                                Edit
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main_metrics_sub_info_funding">
                            <div class="main_metrics_info_amount">
                                <?php if($this->totalAggregateValue):?>
                                <span><?php echo $this->metric_details['metric_unit'];?>  <?php echo $this->totalAggregateValue;?></span>
                                <?php else:?>
                                <span><?php echo $this->metric_details['metric_unit'];?>  0</span>
                                <?php endif;?>
                            </div>
                        </div>
                        <?php /*
                        <div class="main_metrics_info_options_inside">
                        <div>
                        <div class="main_metrics_info_follow_join_count_container">
                        <div class="main_metrics_info_follow_count">
                        <h2 class="follow follow_scroll" id="follow_scroll"><a>Projects</a></h2>
                        <h2 class="follow_count follow_scroll">
                        <?php if($this->project_entries):?>
                        <?php echo $this->metric_details['metric_unit'];?> <?php echo $this->project_aggregate_value;?>
                        <?php else:?>
                        <?php echo $this->metric_details['metric_unit'];?> 0
                        <?php endif;?>
                        </h2>
                    </div>
                    <div class="main_metrics_info_members_count">
                        <h2 class="backer backer_scroll" id="backer_scroll"><a>Members</a></h2>
                        <h2 class="backer_count backer_scroll">
                            <?php if($this->user_entries):?>
                            <?php echo $this->metric_details['metric_unit'];?> <?php echo $this->user_aggregate_value;?>
                            <?php else:?>
                            <?php echo $this->metric_details['metric_unit'];?> 0
                            <?php endif;?>
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        */?>
    </div>
</div>
</div>

<?php if($this->project_entries && $this->user_entries):?>
<h3>Submissions (<?php echo ((int)$this->project_entries->count() + (int)$this->user_entries->count());?> )</h3>
<?php elseif(!$this->project_entries && $this->user_entries):?>
<h3>Submissions (<?php echo ((int)$this->user_entries->count());?> )</h3>
<?php elseif($this->project_entries && !$this->user_entries):?>
<h3>Submissions (<?php echo ((int)$this->project_entries->count());?> )</h3>
<?php elseif(!$this->project_entries && !$this->user_entries):?>
<h3>Submissions (0)</h3>
<?php else:?>
<h3>Submissions (0)
    <?php endif;?>


    <div class="metrics_projects_users cardview">
        <?php if($this->user_entries):?>
        <?php foreach ($this->user_entries as $user_details) : ?>
        <?php $user = Engine_Api::_()->getItem('user', $user_details['user_id']); ?>
        <div class="project_user_result" onclick="location.href = '<?php echo $user->getHref();?>';" style="cursor: pointer;">
            <div class="project_user_photo">
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.cover')) ?>
            </div>
            <div class="project_user_info">
                <!-- Title-->
                <?php echo $this->htmlLink($user->getHref(), $user->getTitle(), array('class' => 'project_user_title')) ?>
                <!-- Type-->
                <div class="project_user_type">
                    <div><p id="user_type" style="margin-bottom: 8px;">Member </p></div>

                    <!-- value -->
                    <div>
                        <?php
                        echo $this->htmlLink(array(
                        'route' => 'yndynamicform_entry_specific',
                        'module' => 'yndynamicform',
                        'controller' => 'entries',
                        'action' =>'view',
                        'entry_id' => $user_details['entry_id'],
                        'type' => 'user',
                        'id' => $user->getIdentity()
                        ),
                        $this->metric_details['metric_unit'].' '.$user_details['value'],
                        array('class' => 'project_user_metrics_value')
                        )
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif;?>

        <?php if($this->project_entries):?>
        <?php foreach ($this->project_entries as $project_entry) : ?>
        <?php $project = Engine_Api::_()->getItem('sitecrowdfunding_project', $project_entry['project_id']); ?>
        <div class="project_user_result" onclick="location.href = '<?php echo $project->getHref();?>';" style="cursor: pointer;">
            <div class="project_user_photo">
                <?php echo $this->htmlLink($project->getHref(), $this->itemPhoto($project, 'thumb.cover')) ?>
            </div>
            <div class="project_user_info">
                <!-- Title-->
                <?php echo $this->htmlLink($project->getHref(), $project->getTitle(), array('class' => 'project_user_title')) ?>
                <!-- Type-->
                <div class="project_user_type">
                    <!-- Tag -->
                    <div><p id="project_type" style="margin-bottom: 8px;">Project </p></div>
                    <!-- value -->
                    <div>
                        <?php
                        echo $this->htmlLink(array(
                        'route' => 'yndynamicform_entry_specific',
                        'module' => 'yndynamicform',
                        'controller' => 'entries',
                        'action' =>'view',
                        'entry_id' => $project_entry['entry_id'],
                        'type' => 'project',
                        'id' => $project->getIdentity()
                        ),
                        $this->metric_details['metric_unit'].' '.$project_entry['value'],
                        array('class'=> 'project_user_metrics_value')
                        )
                        ?>
                    </div>
                </div>
                <!-- Description-->
                <p class="project_user_description">
                    <?php echo Engine_Api::_()->seaocore()->seaocoreTruncateText($project->getDescription(), 130);?>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif;?>

    </div>
</div>
</div>
<!-- </div> -->

<style>
    .common_btn_custom {
        color: #ffffff !important;
        background: #44AEC1;
        margin: 5px;
        padding: 8px 8px;
        border-radius: 3px;
    }
    .generic_layout_container > h3 {
        position: relative;
        text-align: center;
        font-size: 18px;
        border-bottom: unset !important;
    }
    .generic_layout_container > h3::before {
        left: 0 !important;
        margin: 0 auto !important;
        right: 0 !important;
        text-align: center !important;
        width: 85px !important;
        background: #44AEC1 !important;
        top: 100% !important;
        content: "" !important;
        display: block !important;
        min-height: 2px !important;
        position: absolute !important;
        border-bottom: unset !important;
    }
    .generic_layout_container > h3:after {
        content: '';
        display: inline-block;
        background-repeat: no-repeat;
        width: 0;
        border-width: 0;
        height: 0;
        margin: 0;
        position: absolute;
    }
    .project-list-in-box>.item,.user-list-in-box>.item {
        -webkit-box-shadow: none;
        box-shadow: none;
        border-radius: 0;
        border-bottom: 1px solid #f4f4f4;
    }
    .projects-list>.item,.users-list>.item {
        border-radius: 3px;
        -webkit-box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
        padding: 10px 0;
        background: #fff;
    }
    .projects-list>.item:before,
    .projects-list>.item:after ,
    .users-list>.item:before,
    .users-list>.item:after {
        content: " ";
        display: table;
    }
    .projects-list .project-img,
    .users-list .user-img {
        float: left;
    }
    .projects-list .project-info,
    .users-list .user-info{
        margin-left: 60px;
    }
    .projects-list>.item:after,
    .users-list>.item:after{
        clear: both;
    }
    .projects-list .project-img img,
    .users-list .user-img img{
        width: 50px;
        height: 50px;
    }
    .projects-list .project-title,
    .users-list .user-title{
        font-weight: 600;
    }
    .label {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
        background-color: #44AEC1 !important;
    }
    .label a{
        color: #fff !important;
    }
    .projects-list .project-description,
    .users-list .user-description{
        display: block;
        color: #999;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    #feed_viewmore{
        background-color: #c1c1c1;
        padding: 3px;
    }
    .tip{
        background-color: #fff !important;
        margin: 0px !important;
    }
    .tip>span{
        margin-bottom: 0px !important;
    }
    .main_metrics_info {
        border-bottom: 1px dashed #d9d8d8;
    }
    .main_metrics_info, .main_metrics_info_box {
        width: 100%;
        display: block;
    }
    .main_metrics_info {
        margin-bottom: 20px;
    }

    .main_metrics_info {
        display: flex !important;
        justify-content: space-between !important;
    }

    .main_metrics_info > div {
        display: table-cell;
        vertical-align: top;
    }
    .main_metrics_common_container {
        display: flex !important;
        width: 100%;
    }
    .main_metrics_common_sub_container {
        width: 65%;
        border-right: 1px dashed #d9d8d8;
    }
    .main_metrics_info_container {
        flex-direction: column;
    }
    .main_metrics_info_container {
        display: flex !important;
        padding: 10px;
    }
    .main_metrics_info_title {
        margin-top: 5px;
        margin-bottom: 10px;
    }
    .main_metrics_info_title > h3 > a {
        font-size: 24px;
    }
    .main_metrics_sub_info_desc {
        font-size: 16px;
        line-height: 32px;
        color: #565555 !important;
    }
    .main_metrics_sub_info_desc {
        padding: 10px;
    }
    .main_metrics_info_icon {
        display: flex !important;
        padding-right: 20px !important;
    }

    .main_metrics_info_icon {
        padding-right: 15px;
        text-align: center;
        width: auto;
    }
    .main_metrics_info_icon img {
        background: none !important;
        box-shadow: 2px 2px 2px 2px #e0e0e0;
    }

    .main_metrics_info_icon img {
        width: 120px;
        height: 120px;
        background: none !important;
        padding: 0px !important;
    }
    .main_metrics_info_icon i, .main_metrics_info_icon img {
        font-size: 30px;
        margin-bottom: 5px;
        padding: 15px;
    }
    .main_metrics_info_icon i, .main_metrics_info_icon img {
        background: #44AEC1;
        color: #fff;
    }
    .organization_container {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        width: 100%;
        margin-left: 8px;
    }
    .main_metrics_parent_title {
        padding-left: unset !important;
        font-size: 17px;
    }
    .metric_options {
        box-sizing: border-box;
        color: #fff;
        padding: 3px !important;
        border-radius: 3px;
        text-align: center;
    }
    .metric_options {
        box-sizing: border-box;
        color: #fff;
        padding: 8px;
        border-radius: 3px;
        text-align: center;
    }
    .main_metrics_info_status {
        display: flex;
        justify-content: flex-end;
    }
    .main_metrics_info_options {
        display: flex !important;
        width: 35%;
        padding: 10px;
        flex-direction: column;
    }
    .main_metrics_sub_info_funding {
        padding: 10px;
    }
    .main_metrics_info_amount {
        font-size: 14px !important;
        margin-top: 0 !important;
    }
    .main_metrics_info_amount span {
        font-size: 20px;
    }
    .has_joined_label> li > h3, .has_favourite_label > li > h3 {
        color: #44AEC1;
        font-weight: bold;
        margin-top: 10px;
    }
    .main_metrics_info_options_inside {
        display: flex;
        justify-content: center;
        padding: 10px;
    }
    .main_metrics_info_options_inside {
        width: 100%;
    }
    @media (min-width: 991px) and (max-width: 12606px){
        .main_metrics_info_options_inside {
            position: relative;
            right: 15px;
            width: 391px;
        }
    }
    .main_metrics_info_options_inside {
        width: 100%;
    }
    .main_metrics_info_follow_join_count_container {
        display: flex;
        justify-content: space-around;
    }
    .main_metrics_info_follow_count, .main_metrics_info_members_count {
        text-align: center;
        margin: 20px;
    }
    #follow_scroll,
    #backer_scroll {
        text-decoration: underline;
    }

    .project_user_result {
        box-shadow: 0 2px 2px 0 rgb(0 0 0 / 14%), 0 3px 1px -2px rgb(0 0 0 / 20%), 0 1px 5px 0 rgb(0 0 0 / 12%);
        display: flex !important;
        flex-direction: column;
        width: 267px;
        height: 273px;
        margin: 10px;
        padding: 10px;
    }
    .project_user_result {
        overflow: hidden;
        margin-top: 10px;
        border-top-width: 1px;
        padding-top: 10px;
    }
    .project_user_photo {
        display: block;
        float: unset !important;
        overflow: hidden;
        width: 100%;
        height: 158px;
        margin-right: unset !important;
    }
    .project_user_result img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .project_user_result img {
        margin: auto;
        display: block;
    }
    .project_user_result .project_user_info {
        display: block;
        overflow: hidden;
    }
    .project_user_info {
        margin-top: 8px;
    }
    .project_user_result .project_user_title {
        font-size: 13px;
    }
    .project_user_title {
        font-weight: 500;
        font-size: 16px !important;
        color: #201f1f;
    }
    .project_user_title {
        font-size: 13px;
        margin-left: 3px;
        color: #21a8c1;
    }
    .project_user_type {
        display: flex;
        justify-content: space-between;
    }
    #user_type, #project_type{
        border-radius: 3px;
        padding: 1px 5px;
        color: white;
        text-align: center;
    }
    #project_type {
        background-color: #d32727;
    }
    #user_type {
        background-color: #206a8d;
    }
    p.project_user_description {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 6px;
        margin-left: -5px;
    }
    .cardview {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .project_user_metrics_value {
        text-align: center;
        border-radius: 3px;
        padding: 4px 5px;
        background-color: #f36e29;
        color: white !important;
        text-align: center;
    }
    .main_metrics_info_amount{
        background: #fff;
        margin: 2%;
        padding: 35px 20px;
        box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%);
        border-radius: 3px;
        width: auto;
        height: 25px;
        text-align: center;
        align-items: center;
        justify-content: center;
    }
</style>