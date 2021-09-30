<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitecrowdfunding/externals/styles/style_sitecrowdfunding_dashboard.css'); ?>

<div class="sitecrowdfunding_project_new_steps">
    <div class="project_ready_intro">
        <h1 class="form_title">
            Confirmation!
        </h1>
        <br/>

        <h2 style="text-align: center;padding: 15px;font-weight: bold">
            Your project is ready. Here is link: <a style="color: #44AEC1;"
                                                    href="<?php echo $this->project->getHref() ?>"><?php echo (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].$this->
                project->getHref() ?> </a>
        </h2>

        <h3 class="form_sub_title">Team member(s)</h3>

        <br/>
        <div class="options_set_2_btn">
            <a class="button smoothbox icon seaocore_icon_add"
               href="<?php echo $this->escape($this->url(array( 'action' => 'invite-members', 'project_id' => $this->project_id), 'sitecrowdfunding_project_member', true)); ?>">
                <span><?php echo $this->translate("Add Team Members"); ?></span>
            </a>
        </div>
        <br/><br/>


        <div class="global_form">
            <div class="sitecrowdfunding_leaders">

                <?php foreach ($this->paginator as $item): ?>
                <?php $user_id = $item['user_id']; ?>
                <?php $user = Engine_Api::_()->getItem('user', $user_id); ?>
                <div id='<?php echo $user_id ?>_page_main' class='sitecrowdfunding_leaders_list'>
                    <div class='sitecrowdfunding_leaders_thumb' id='<?php echo $user_id ?>_pagethumb'>
                        <a href="<?php echo $user->getHref();?>">
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

                    <br/>

                    <div id="<?php echo $item['id'] ?>_page"
                         class="sitecrowdfunding_leaders_detail sitecrowdfunding_members_details ">
                        <h2>
                            <?php echo $item['recipient_name'] ?>
                            <div>
                                <?php echo $item['recipient'] ?>
                            </div>
                        </h2>
                        <h2>
                            <?php echo $this->translate('External Member'); ?>
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

                <div class="options_btn">
                    <div class="options_set_1_btn">
                        <?php echo $this->htmlLink(array('route' => 'sitecrowdfunding_specific','action' =>
                        'edit','project_id' => $this->project_id), $this->translate('Edit your project'), array('class'
                        => 'button view_project_btn')); ?>
                    </div>
                    <div class="options_set_2_btn">
                        <?php echo $this->htmlLink($this->project->getHref(), $this->translate('Go to your project'),
                        array("class" => 'button view_project_btn')) ?>
                    </div>
                </div>

            </div>
        </div>
        <br/>
    </div>
</div>

<style>
    .sitecrowdfunding_project_new_steps {
        min-width: 430px;
        max-width: 850px;
        padding: 20px;
        margin-left: auto;
        margin-right: auto;
        position: relative;
        border-radius: 3px;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.19), 0 6px 6px rgba(0, 0, 0, 0.23);
        background: rgba(255, 255, 255, .9)
    }

    .form_title {
        text-align: center;
        margin: 10px;
    }

    .form_sub_title {
        font-size: 17px;
        border-bottom: 1px solid #f2f0f0;
        padding: 10px 10px;
        margin: -10px -10px 10px -10px;
    }

    .sitecrowdfunding_leaders_list > div {
        display: inline-block;
        vertical-align: middle;
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

