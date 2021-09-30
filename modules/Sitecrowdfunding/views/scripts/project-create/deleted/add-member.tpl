<?php include APPLICATION_PATH . '/application/modules/Sitecrowdfunding/views/scripts/project-create/common.tpl'; ?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<div class="sitecrowdfunding_project_new_steps">
    <div class="project_ready_intro">

       <form class="global_form">
           <div>
               <div>
                   <h3>
                       Project Members
                   </h3>
               </div>
           </div>
       </form>

        <br/>
        <div class="custom_description">
            Project Members mean the individuals who work together on a project to achieve its objectives.
            <br/>
            <br/>
            Do you want to identify individuals who are working on this project?  Click this box to add each project member
        </div>

        <br/>
        <br/>
        <div class="options_set_2_btn">
            <a class="button smoothbox icon seaocore_icon_add"
               href="../../../../../../../index.php">
                <span><?php echo $this->translate("Add Project Member"); ?></span>
            </a>
        </div>
        <br/>
        <br/>



        <div class="global_form">
            <div class="sitecrowdfunding_leaders">

                <?php foreach ($this->paginator as $item): ?>
                <?php $user_id = $item['user_id']; ?>
                <?php $user = Engine_Api::_()->getItem('user', $user_id); ?>
                <div id='<?php echo $user_id ?>_page_main' class='sitecrowdfunding_leaders_list'>
                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                        <a href="../../../../../../../index.php">
                            <?php echo $this->itemBackgroundPhoto($user, 'thumb.profile', null, array('tag' => 'i')); ?>
                        </a>
                    </div>
                    <div id='<?php echo $user_id ?>_page'
                         class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">
                        <h2>
                            <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                        </h2>
                        <?php if(!empty($item['title'])):?>
                        <h2>
                            <?php echo implode(', ', json_decode($item['title'])) ?>
                        </h2>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(count($this->pendingInvites) > 0): ?>
                <?php foreach ($this->pendingInvites as $item): ?>
                <div id="<?php echo $item['id'] ?>_page_main" class='sitecrowdfunding_leaders_list'>

                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                        <a href="javascript:void(0);">
                            <i class="bg_item_photo bg_thumb_profile bg_item_photo_user "
                               style=" background-image:url('<?php echo $defaultURL?>');"></i>
                        </a>
                    </div>

                    <div id="<?php echo $item['id'] ?>_page"
                         class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">
                        <h2>
                            <?php echo $item['recipient_name'] ?>
                            <div>
                                <?php echo $item['recipient'] ?>
                            </div>
                        </h2>
                        <?php if(!empty($item['project_role'])): ?>
                        <h2>
                            <?php
                                            $roles_id = json_decode($item['project_role']);
                                            $roleName = array();
                                            foreach($roles_id as $role_id) {
                                                $roleName[] = Engine_Api::_()->
                            getDbtable('roles','sitecrowdfunding')->getRoleName($role_id);
                            }
                            echo implode(', ', $roleName);
                            ?>
                        </h2>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div style="min-height: 100px;margin-right: 10px;margin-left: 10px">
            <button name="previous" id="previous" type="button" onclick="window.location.href='<?php echo $this->backURL; ?>'">Previous</button>
            <button name="execute" id="execute"  type="button" onclick="window.location.href='<?php echo $this->nextURL; ?>'">Next</button>
        </div>

        <br/>
    </div>
    <div class="common_star_info"> <span>* </span> Means required information</div>
</div>

<style>
    .sitecrowdfunding_leaders_list > div {
        display: inline-block;
        vertical-align: middle;
    }
    .custom_description{
        font-size: 15px;
        padding-left: 10px;
    }
    .sitecrowdfunding_members_details {
        background: none !important;
        border: none !important;
        padding: 0px !important;
        font-size: 14px !important;
    }

    .sitecrowdfunding_leaders_detail a:hover {
        color: #444;
    }

    .options_set_1_btn {
        float: left;
    }

    .options_set_2_btn {
        float: right;
    }

    .options_set_1_btn > a, .options_set_2_btn > a {
        font-weight: unset !important
    }
</style>

