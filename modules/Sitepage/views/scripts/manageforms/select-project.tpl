<?php
$this->headScript()
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<!--Menus-->
<div class="initiative_menu headline sitecrowdfunding_inner_menu">
    <div class='tabs sitecrowdfunding_nav' style=" display: flex; justify-content: center; ">
        <select id="tab_select" onchange="show_ui()" style="width: 335px;border: 2px solid #DDD;border-radius: 29px; background: #F8F8F8; margin-top: 11px;
             position: relative;overflow: hidden;"   class="text"  >
            <option  value="assign_projects" >  Assign Projects</option>
            <option  value="assign_users" > Assign Users</option>
        </select>


    </div>
</div>


<!--Menus-->
<div class="initiative_menu headline sitecrowdfunding_inner_menu" id="sub_tab" >
    <div class='tabs sitecrowdfunding_nav' style="border-bottom: 1px solid #ccc;    margin-bottom: 13px;"  >
        <ul class='initiative_menu_nav navigation' style="display: flex !important;flex-direction: column;justify-content: center;align-items: center;">
            <div   style="margin-bottom: 15px;display: none;  margin-top: 5px;" id="users_tab" >
                <li>
                    <a id="all_project_users" style="font-size: 15px"   href="javascript:void(0);" onclick="selected_ui('all_project_users')" >
                        <?php echo $this->translate('All Project Users'); ?>
                    </a>
                </li>
                <li>
                    <a id="project_admins" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('project_admins')" >
                        <?php echo $this->translate('Project Admins'); ?>
                    </a>
                </li>
                <li>
                    <a id="project_members" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('project_members')" >
                        <?php echo $this->translate('Project Members'); ?>
                    </a>
                </li>
            </div>
            <div   style="display: none;"    id="users_tab1" >
                <li>
                    <a id="all_users" style="font-size: 15px"   href="javascript:void(0);" onclick="selected_ui('all_users')" >
                        <?php echo $this->translate('All Users'); ?>
                    </a>
                </li>
                <li>
                    <a id="org_admins" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('org_admins')" >
                        <?php echo $this->translate('Org Admins'); ?>
                    </a>
                </li>
                <li>
                    <a id="org_members" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('org_members')" >
                        <?php echo $this->translate('Org Members'); ?>
                    </a>
                </li>
            </div>
            <div style="margin-top: 8px;"  id="projects_tab">
                <li>
                    <a id="all_projects" style="font-size: 15px" class="active" href="javascript:void(0);" onclick="selected_ui('all_projects')" >
                        <?php echo $this->translate('All Projects'); ?>
                    </a>
                </li>
                <li>
                    <a id="projects_assigned" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('projects_assigned')" >
                        <?php echo $this->translate('Projects Assigned'); ?>
                    </a>
                </li>
                <li>
                    <a id="projects_byinitiative" style="font-size: 15px"  href="javascript:void(0);" onclick="selected_ui('projects_byinitiative')" >
                        <?php echo $this->translate('Projects By Initiative'); ?>
                    </a>
                </li>
            </div>
        </ul>
    </div>
</div>


<div id="landing_page_projects"  >
    <?php if($this->tab_link == 'all_projects' || $this->tab_link == 'projects_assigned' || $this->tab_link == 'projects_byinitiative'):  ?>


    <!-- search form -->
    <div id="error_msg_outer_container">
        <div id="error_msg_container" style="display: none"></div>
    </div>
    <?php echo $this->searchForm->render($this) ?>
    <div id="search_spinner"></div>

    <!-- initiative dropdown-->
    <?php if ( $this->tab_link == 'projects_byinitiative') : ?>
    <div id="initiative-wrapper" class="form-wrapper" style="display: flex;justify-content: center">
        <div id="initiative-label" class="form-label">
            <label for="initiative" class="optional"></label>&nbsp;
        </div>
        <div id="initiative-element" class="form-element">
            <select name="initiative" id="initiative" tabindex="-1" onchange="onInitiativeChange()">
                <option value=null>Select Initiative</option>
                <?php foreach( $this->initiatives as $initiative ): ?>
                <option value="<?php echo $initiative['initiative_id']?>"  <?=$initiative['initiative_id'] == $this->initiative_id ? ' selected="selected"' : '';?> ><?php echo $initiative['title'];?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <br>
    <div id="assign_success_toast_projects" style="display: none">
        <p id="assign_toast_projects"></p>
    </div>
    <div style="display: flex;margin-bottom: 10px;justify-content: flex-end;margin-right: 14px;">
        <p style="align-items: center;justify-content: center;display: flex;font-size: 18px;" >Assign to all projects</p>
        <div style="margin-left: 11px;">
            <label class="switch">

                <input class="custom_toggle" id="custom_toggle" type="checkbox" onclick="assignFormToAllProjects(<?php echo $this->form_id; ?>)"  <?php echo $this->assignStatus ? "checked" : ""; ?> >
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <?php endif; ?>
   <div id="assign_success_toast_projects" style="display: none">
       <p id="assign_toast_projects"></p>
   </div>
    <?php if($this->paginator): ?>
    <?php $totalItems = $this->paginator->getTotalItemCount(); ?>

    <?php if($totalItems > 0): ?>

    <!-- assign to all projects -->
    <?php if ( $this->tab_link == 'all_projects' ) : ?>
    <div style="display: flex;margin-bottom: 10px;justify-content: flex-end;margin-right: 14px;">
        <p style="align-items: center;justify-content: center;display: flex;font-size: 18px;" >Assign to all projects</p>
        <div style="margin-left: 11px;">
            <label class="switch">

                <input class="custom_toggle" id="custom_toggle" type="checkbox" onclick="assignFormToAllProjects(<?php echo $this->form_id; ?>)"  <?php echo $this->assignStatus ? "checked" : ""; ?> >
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <?php endif; ?>

    <!-- total count -->
    <div class="count_div">
        <h3><?php echo $this->translate('%s project(s) found.', $totalItems) ?>
    </div>
    <br><br>

    <!-- pagination -->
    <?php echo $this->paginationControl($this->paginator, null, array("pagination/pagination.tpl", "sitecrowdfunding")); ?>

    <br>

    <!--projects list -->
    <div class="manageproject_table_scroll">
        <table class="transaction_table admin_table seaocore_admin_table">

            <thead>
            <tr class="sitecrowdfunding_detail_table_head">

                <!-- Project Name -->
                <?php $class = ( $this->sort_field === 'project_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                <th class="header_title_big <?php echo $class; ?>" >
                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                        Project Name
                    </a>
                </th>

                <!-- Assign Form-->
                <th class="header_title" style="width: 25%;text-align: center;" >
                    <a href="javascript:void(0);" >
                        <?php echo $this->translate("Assign Form") ?>
                    </a>
                </th>

            </tr>
            </thead>

            <?php foreach ($this->paginator as $item) : ?>
            <tr>
                <td title="<?php echo $item->getTitle() ?>">
                    <a href="<?php echo $this->url(array('project_id' => $item->project_id, 'slug' => $item->getSlug()), "sitecrowdfunding_entry_view") ?>"  target='_blank'>
                    <?php echo $item->getTitle(); ?> <div id="<?php echo 'assign_spinner_'.$this->form_id.'_'.$item->project_id ;?>"></div>
                    </a>
                </td>
                <td class="header_title" style="width: 25%;text-align: center;">
                    <label class="switch">
                        <?php $assign_status =  Engine_Api::_()->getDbtable('projectforms', 'sitepage')->getProjectAssiginedCountByFormId($this->form_id,$item->project_id); ?>
                        <input class="custom_toggle" id="custom_toggle_<?php echo $this->form_id; ?>" type="checkbox" onclick="assignForm(<?php echo $item->project_id; ?>,<?php echo $this->form_id; ?>)" <?php echo $assign_status > 0 ? "checked" : ""; ?> >
                        <span class="slider round"></span>
                    </label>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>

    <?php else: ?>
    <div class="tip">
                <span>
                    <?php echo $this->translate('No projects'); ?>
                </span>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="tip">
            <span>
                <?php echo $this->translate('No projects'); ?>
            </span>
    </div>
    <?php endif; ?>


    <?php endif;  ?>


  <!--   oo  -->




        <?php if($this->tab_link == 'all_project_users' || $this->tab_link == 'project_admins' || $this->tab_link == 'project_members' ||
        $this->tab_link == 'all_users' || $this->tab_link == 'org_admins' || $this->tab_link == 'org_members'):  ?>

    <div id="assign_success_toast_users" style="display: none">
        <p id="assign_toast_users"></p>
    </div>
    <div style="display: flex;margin-bottom: 10px;justify-content: flex-end;margin-right: 14px;">

        <p style="align-items: center;justify-content: center;display: flex;font-size: 18px;">Assign to all users</p>
        <div style="margin-left: 11px;">
            <label class="switch">

                <input class="custom_toggle" id="custom_toggle" type="checkbox" onclick="assignFormToAllUsers(<?php echo $this->form_id; ?>)"  <?php echo $this->assignStatus ? "checked" : ""; ?> >
                <span class="slider round"></span>
            </label>
        </div>
    </div>

                    <!-- search form -->
                    <div id="error_msg_outer_container">
                        <div id="error_msg_container" style="display: none"></div>
                    </div>
                    <div id="search_spinner" ></div>

                    <!--projects list -->
                    <div class="manageproject_table_scroll">
                        <table class="transaction_table admin_table seaocore_admin_table">

                            <thead>
                            <tr class="sitecrowdfunding_detail_table_head">

                                <!-- Project Name -->
                                <?php $class = ( $this->sort_field === 'project_name' ? 'admin_table_ordering admin_table_direction_' . strtolower($this->sort_direction) : '' ) ?>
                                <th class="header_title_big <?php echo $class; ?>" >
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                                        User Name
                                    </a>
                                </th>
                                <th class="header_title_big <?php echo $class; ?>" >
                                    <a class="table_heading" href="javascript:void(0);" onclick="javascript:changeOrder('project_name', 'asc');">
                                        User Email
                                    </a>
                                </th>
                                <!-- Assign Form-->
                                <th class="header_title" style="width: 25%;text-align: center;" >
                                    <a href="javascript:void(0);" >
                                        <?php echo $this->translate("Assign Form") ?>
                                    </a>
                                </th>

                            </tr>
                            </thead>

                            <?php foreach ($this->paginatorss as $item) : ?>
                            <tr>

                                <?php if($this->flag == false) : ?>
                                    <?php if($item) {
                                          $user = Engine_Api::_()->user()->getUser($item);
                                          $displayname= $user->displayname;
                                          $email =  $user->email;
                                    }?>
                                <?php endif; ?>
                                <?php if($this->flag == true) : ?>
                                    <?php
                                    if( $item->user_id) {
                                        $user = Engine_Api::_()->user()->getUser( $item->user_id);
                                        $item =  $item->user_id;
                                        $displayname=  $user->displayname;
                                        $email =  $user->email;
                                    }?>
                                <?php endif; ?>
                                <?php if($this->flag == 'p_memebers') : ?>

                                <?php
                                 if($item['user_id']) {
                                     $user = Engine_Api::_()->user()->getUser($item['user_id']);
                                    $item = $item['user_id'];
                                    $displayname= $user->displayname;
                                    $email =  $user->email;
                                }?>
                                <?php endif; ?>
                                <td >
                                    <?php echo  $displayname; ?>

                                </td>
                                <td >
                                    <?php echo   $email; ?>

                                </td>
                                <td class="header_title" style="width: 25%;text-align: center;">

                                    <?php $assign_status =  Engine_Api::_()->getDbtable('projectforms', 'sitepage')->getUserAssiginedCountByFormId($this->form_id,$item); ?>

                                    <!-- assign to all projects -->
                                    <?php if ( $this->tab_link == 'all_users' || $this->tab_link == 'org_admins' || $this->tab_link == 'org_members' ||
                                    $this->tab_link == 'all_project_users' || $this->tab_link == 'project_admins' || $this->tab_link == 'project_members') : ?>

                                        <label class="switch">
                                            <input class="custom_toggle" id="custom_toggle" type="checkbox" onclick="assignFormToUser(<?php echo $item; ?>,<?php echo $this->form_id; ?>)"  <?php echo $assign_status > 0 ? "checked" : ""; ?> >
                                            <span class="slider round"></span>
                                        </label>


                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                        </table>
                    </div>




        <?php endif;  ?>


    </div>





</div>






<div id="hidden_ajax_data" style="display: none;"></div>

<script type="text/javascript">


    // on initiative change
    function onInitiativeChange() {
        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    // sort the fields
    function changeOrder (orderField, orderDirection) {
        var currentOrderField = $('sort_field').value;
        var currentOrderDirection = $('sort_direction').value;
        if (orderField === currentOrderField) {
            $('sort_direction').value = (currentOrderDirection === 'asc' ? 'desc' : 'asc');
        } else {
            $('sort_field').value = orderField;
            $('sort_direction').value = orderDirection;
        }
        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    };

    // paginate
    function pageAction(page) {
        $('page').value = page;
        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        ajaxRenderData();
    }

    // assign form
    function assignForm(project_id,form_id){
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = null;
        var assign_spinner_name = 'assign_spinner_'+form_id+'_'+project_id;
        $(assign_spinner_name).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-form',
            method: 'POST',
            data: {
                format: 'json',
                project_id: project_id,
                form_id:form_id,
                page_id:page_id
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {
                $(assign_spinner_name).innerHTML = '';
                ajaxRenderData();
            }
        })
        request.send();
    }

    // assign form user
    function assignFormToUser(user_id,form_id){
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = null;
        var assign_spinner_name = 'assign_spinner_'+form_id+'_'+user_id;
      //  $(assign_spinner_name).innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';

         console.log('user_id',user_id);
         console.log('form_id',form_id);
         console.log('page_id',page_id);

        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-form-to-user',
            method: 'POST',
            data: {
                format: 'json',
                user_id: user_id,
                form_id:form_id,
                page_id:page_id
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {
              //  $(assign_spinner_name).innerHTML = '';
                ajaxRenderData();
                console.log('after ajaxRenderData');
            }
        })
        request.send();
    }



    // assign form to all projects
    function  assignFormToAllProjects(form_id) {
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = document.getElementById('custom_toggle').checked ? 1 : 0;
        var projectsIds = '<?php echo json_encode($this->projectsIds); ?>';
        var userIds = '<?php echo json_encode($this->userIds); ?>';


        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-form-to-all-projects',
            method: 'POST',
            data: {
                format: 'json',
                form_id:form_id,
                page_id:page_id,
                status:assign_status,
                projectsIds:projectsIds.toString(),
                userIds : userIds.toString()
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {

                console.log('assign_status ----',assign_status);
                setTimeout(function(){
                       if(assign_status) {
                           let dataCount = '<?php echo $totalItems; ?>' ;
                           document.getElementById('assign_success_toast_projects').style.display = "flex";
                           document.getElementById('assign_toast_projects').innerHTML = "Successfully Assigned  Form To ("+dataCount +") Projects";
                           $('search_spinner').innerHTML = '';
                           setTimeout(function(){
                               ajaxRenderData();
                           }, 1500);
                       }else{
                           let dataCount = '<?php echo $totalItems; ?>' ;
                           document.getElementById('assign_success_toast_projects').style.display = "flex";
                           document.getElementById('assign_toast_projects').innerHTML = "Successfully Unassigned Form From ("+dataCount +") Projects";
                           $('search_spinner').innerHTML = '';
                           setTimeout(function(){
                              ajaxRenderData();
                           }, 1500);
                       }

                }, 3000);


            }
        })
        request.send();
    }
    function  assignFormToAllUsers(form_id) {
        var page_id='<?php echo $this->page_id; ?>';
        var assign_status = document.getElementById('custom_toggle').checked ? 1 : 0;
        var projectsIds = '<?php echo json_encode($this->projectsIds); ?>';
        var userIds = '<?php echo json_encode($this->userIds); ?>';



        $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
        var request = new Request.JSON({
            url: en4.core.baseUrl + 'organizations/manageforms/assign-form-to-all-users',
            method: 'POST',
            data: {
                format: 'json',
                form_id:form_id,
                page_id:page_id,
                status:assign_status,
                user_id:123,
                projectsIds:projectsIds.toString(),
                userIds : userIds.toString()
            },
            onRequest: function () {
            },
            onSuccess: function (responseJSON) {


                setTimeout(function(){
                    if(assign_status) {
                        let dataCount = '<?php echo count($this->paginatorss); ?>';
                        document.getElementById('assign_success_toast_users').style.display = "flex";
                        document.getElementById('assign_toast_users').innerHTML = "Successfully Assigned  Form To ("+dataCount +") Users";
                        $('search_spinner').innerHTML = '';
                        setTimeout(function(){
                            ajaxRenderData();
                        }, 1500);
                    }else{
                        let dataCount = '<?php echo count($this->paginatorss); ?>';
                        document.getElementById('assign_success_toast_users').style.display = "flex";
                        document.getElementById('assign_toast_users').innerHTML = "Successfully Unassigned Form From ("+dataCount +") Users";
                        $('search_spinner').innerHTML = '';
                        setTimeout(function(){
                            ajaxRenderData();
                        }, 1500);
                    }

                }, 3000);
            }
        })
        request.send();
    }
    //render data
    function ajaxRenderData(){
        var form_id = '<?php echo $this->form_id;?>';
        var tab_link = '<?php echo $this->tab_link;?>';
        var initiative_id  = document.getElementById('initiative') ? document.getElementById('initiative').value:null;

        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/manageforms/select-project/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
        data: {
            format: 'html',
                search: 1,
                subject: en4.core.subject.guid,
                page:$('page') ? $('page').value : null,
                project_name :  $('project_name') ? $('project_name').value : null,
                project_id: $('project_id') ? $('project_id').value : null,
                form_id:form_id,
                tab_link: tab_link,
                initiative_id:initiative_id,
                project_order:  $('project_order') ? $('project_order').value : null,
                user_name: $('user_name') ? $('user_name').value : null,
                user_id:  $('user_id') ? $('user_id').value : null,
                project_status: $('project_status') ? $('project_status').value : null,
                funding_status: $('funding_status') ? $('funding_status').value : null,
                is_published_yn: $('is_published_yn') ? $('is_published_yn').value : null,
                is_funding_enabled_yn: $('is_funding_enabled_yn') ? $('is_funding_enabled_yn').value : null,
                is_payment_edit:  $('is_payment_edit') ? $('is_payment_edit').value : null,
                goal_amount_min: $('goal_amount_min') ? $('goal_amount_min').value : null,
                goal_amount_max: $('goal_amount_max') ? $('goal_amount_max').value : null,
                sort_field: $('sort_field') ? $('sort_field').value : null,
                sort_direction:  $('sort_direction') ? $('sort_direction').value : null
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;


                $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').get('html');





        }
    }));
    }



    function ajaxRenderDataParam(tab_link){
        var form_id = '<?php echo $this->form_id;?>';
     
        var initiative_id  = document.getElementById('initiative') ? document.getElementById('initiative').value:null;

        en4.core.request.send(new Request.HTML({
            url: en4.core.baseUrl + 'organizations/manageforms/select-project/page_id/' + <?php echo sprintf('%d', $this->page_id) ?>,
        data: {
            format: 'html',
                search: 1,
                subject: en4.core.subject.guid,
                page:$('page') ? $('page').value : null,
                project_name :  $('project_name') ? $('project_name').value : null,
                project_id: $('project_id') ? $('project_id').value : null,
                form_id:form_id,
                tab_link: tab_link,
                initiative_id:initiative_id,
                project_order:  $('project_order') ? $('project_order').value : null,
                user_name: $('user_name') ? $('user_name').value : null,
                user_id:  $('user_id') ? $('user_id').value : null,
                project_status: $('project_status') ? $('project_status').value : null,
                funding_status: $('funding_status') ? $('funding_status').value : null,
                is_published_yn: $('is_published_yn') ? $('is_published_yn').value : null,
                is_funding_enabled_yn: $('is_funding_enabled_yn') ? $('is_funding_enabled_yn').value : null,
                is_payment_edit:  $('is_payment_edit') ? $('is_payment_edit').value : null,
                goal_amount_min: $('goal_amount_min') ? $('goal_amount_min').value : null,
                goal_amount_max: $('goal_amount_max') ? $('goal_amount_max').value : null,
                sort_field: $('sort_field') ? $('sort_field').value : null,
                sort_direction:  $('sort_direction') ? $('sort_direction').value : null
        },
        onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
            $('hidden_ajax_data').innerHTML = responseHTML;


            $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').get('html');





        }
    }));
    }

    //on ready event
    en4.core.runonce.add(function () {

        // project autocomplete
        var projectAutoComplete = new Autocompleter.Request.JSON('project_name', '<?php echo $this->url(array('action' => 'get-projects' , 'page_id' => $this->page_id ), 'sitepage_transaction', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'maxChoices': 40,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
        projectAutoComplete.addEvent('onSelection', function (element, selected, value, input) {
            document.getElementById('project_id').value = selected.retrieve('autocompleteChoice').id;
        });

        // clear click
        $('clear').addEvent('click', function (e) {
            e.stop();
            $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
            $('page').value = 1;
            $('project_name').value = null;
            $('project_id').value = null;
            $('project_order').value = null;
            $('user_name').value = null;
            $('user_id').value = null;
            $('project_status').value = null;
            $('funding_status').value = null;
            $('is_published_yn').value = null;
            $('is_funding_enabled_yn').value = null;
            $('is_payment_edit').value = null;
            $('goal_amount_min').value = null;
            $('goal_amount_max').value = null;
            $('sort_field').value = null;
            $('sort_direction').value = null;
            ajaxRenderData();
        });

        // search click link
        $('search').addEvent('click', function (e) {

            var isValidatedYn= true;

            var sort_field = $('sort_field').value;
            var sort_direction = $('sort_direction').value;

            // sort field validation
            if( (sort_field === null || sort_field === "") &&
                (sort_direction !== null && sort_direction !== "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both sort by and direction need to be filled';
            }else if( (sort_field !== null && sort_field !== "") &&
                (sort_direction === null || sort_direction === "" ) ) {
                isValidatedYn = false;
                $('error_msg_container').style.display="block";
                $('error_msg_container').innerHTML = 'Both sort by and direction need to be filled';
            }

            if(isValidatedYn == true) {
                e.stop();
                $('search_spinner').innerHTML = '<img src="' + en4.core.staticBaseUrl + 'application/modules/Sitecrowdfunding/externals/images/loading.gif" />';
                $('page').value = 1;
                ajaxRenderData();
            }

        });
    });

      function show_ui() {

        //  $('assign_projects').removeClass('active');
        //  $('assign_users').removeClass('active');


          // active class
  //        $(tabLink).addClass('active');
          console.log('tabLink -------',document.getElementById('tab_select').value);
            let tabLink = document.getElementById('tab_select').value
          if( tabLink == 'assign_users') {
              document.getElementById('sub_tab').style.display="block";
              document.getElementById('users_tab').style.display="block";
              document.getElementById('users_tab1').style.display="block";
              console.log('tabLink -------',tabLink);
              document.getElementById('projects_tab').style.display="none";

              ajaxRenderDataParam('all_project_users');
              $('all_project_users').addClass('active');


          }else if( tabLink == 'assign_projects') {
              document.getElementById('sub_tab').style.display="block";
              document.getElementById('projects_tab').style.display="block";

              console.log('tabLink else -------',tabLink);
              document.getElementById('users_tab').style.display="none";
              document.getElementById('users_tab1').style.display="none";

              ajaxRenderDataParam('all_projects');
                $('all_projects').addClass('active');
              //  $('assign_users').removeClass('active');
          }

      }
    // menu select function
    function selected_ui(tabLink){

        // remove active class
        $('all_projects').removeClass('active');
        $('projects_assigned').removeClass('active');
        $('projects_byinitiative').removeClass('active');

        $('all_users').removeClass('active');
        $('org_admins').removeClass('active');
        $('org_members').removeClass('active');
        $('all_project_users').removeClass('active');
        $('project_admins').removeClass('active');
        $('project_members').removeClass('active');

        // active class
        $(tabLink).addClass('active');

        console.log('tabLink ---',tabLink);

        // if(tabLink == 'all_users' || tabLink == 'org_admins' || tabLink =='org_members' ||
        //    tabLink == 'all_project_users' || tabLink == 'project_admins' || tabLink == 'project_members'){
        //     $('landing_page_users').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';
        //
        // }else {
            $('landing_page_projects').innerHTML = '<div class="clr"></div><div class="seaocore_content_loader"></div>';

        // }


        var params = {
            requestParams:<?php echo json_encode($this->params) ?>
        };
          console.log('tabLink -------------',tabLink);
        var request = new Request.HTML({
            url: en4.core.baseUrl + "organizations/manageforms/select-project?tab_link="+tabLink,
            data: {
                format: 'html',
                subject: en4.core.subject.guid,
                page_id: <?php echo $this->page_id?>,
                form_id: <?php echo $this->form_id?>,
                tab_link: tabLink
       },
        evalScripts: true,
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

            $('hidden_ajax_data').innerHTML = responseHTML;

            // if(tabLink == 'all_users' || tabLink == 'org_admins' || tabLink == 'org_members' ||
            //    tabLink == 'all_project_users' || tabLink == 'project_admins' || tabLink == 'project_members'){
            //     $('landing_page_users').innerHTML = $('hidden_ajax_data').getElement('#landing_page_users').get('html');
            //     $('hidden_ajax_data').innerHTML = '';
            //
            //
            //
            //     //fundingProgressiveBarAnimation();
            //     Smoothbox.bind($('landing_page_users'));
            //     en4.core.runonce.trigger();
            //
            // }else {
                $('landing_page_projects').innerHTML = $('hidden_ajax_data').getElement('#landing_page_projects').get('html');
                $('hidden_ajax_data').innerHTML = '';



                //fundingProgressiveBarAnimation();
                Smoothbox.bind($('landing_page_projects'));
                en4.core.runonce.trigger();

            // }



            // remove active class
            $('all_projects').removeClass('active');
            $('projects_assigned').removeClass('active');
            $('projects_byinitiative').removeClass('active');
            $('all_users').removeClass('active');
            $('org_admins').removeClass('active');
            $('org_members').removeClass('active');
            $('all_project_users').removeClass('active');
            $('project_admins').removeClass('active');
            $('project_members').removeClass('active');

            // active class
            $(tabLink).addClass('active');

        }
        });
        request.send();

    }
</script>


<style>
    div#assign_success_toast_projects , div#assign_success_toast_users{
        text-align: center;
        justify-content: center;
        display: flex;
        width: 100%;

    }
    p#assign_toast_projects, p#assign_toast_users {
        background-color: #215f11;
        border: 1px solid rgb(20 77 6);
        color: rgb(250 250 250);
        width: 64%;
        padding: 3px;
        margin-bottom: 11px;
        display: flex;
        justify-content: center;
    }

    div li {
        margin-right: 7px;
    }
    .headline {
        background-color: #fff;
        border: 0 solid transparent;
        border-radius: 0;
        overflow: hidden;
         margin-bottom: unset !important;
        margin-top: 10px;
        padding: unset !important;
    }
  .seaocore_content_loader {
      height: 32px;
      width: 32px;
      margin: 50px auto;
      display: none;
  }
  iframe#TB_iframeContent {
      width: 561px !important;
      height: 704px;
  }
  table.transaction_table.admin_table.seaocore_admin_table{
      width: 95% !important;
      margin: auto !important;
  }
  .seaocore_pagination {
      margin-left: 11px;

  }
  .initiative_menu_nav {
      display: flex !important;
      justify-content: center;
  }
  .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
  }

  .switch input {
      opacity: 0;
      width: 0;
      height: 0;
  }

  .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
  }

  .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
  }
  #custom_toggle:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
  }
  #custom_toggle:checked + .slider {
      background-color: #2196F3;
  }

  #custom_toggle:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
  }

  #custom_toggle:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
  }
  .custom_toggle:checked + .slider {
      background-color: #2196F3;
  }

  .custom_toggle:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
  }

  .custom_toggle:checked + .slider:before {
      -webkit-transform: translateX(26px);
      -ms-transform: translateX(26px);
      transform: translateX(26px);
  }
  /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
    span#global_content_simple {
        height: calc(100vh - 40px);
        overflow: auto;
    }
    .scramble_order_btn{
        margin-top: 18px;
    }
    #user_name-wrapper,
    #project_status-wrapper,
    #funding_status-wrapper,
    #is_published_yn-wrapper,
    #is_funding_enabled_yn-wrapper,
    #is_payment_edit-wrapper,
    #goal_amount_min-wrapper,
    #goal_amount_max-wrapper,
    #project_order-wrapper
    {
        display:none;
    }
    iframe#TB_iframeContent {
        width: 571px !important;
        height: 567px !important;
    }
    .headline .tabs > ul > div > li > a.active {
        border-color: #44AEC1;
        color: #44AEC1;
        border: 2px solid #44AEC1;
        border-radius: 4px;
        padding: 5px;
    }
    a#all_project_users, a#project_admins , a#project_members{
        font-size: 15px !important;
    }
    a#all_users, a#org_admins , a#org_members{
        font-size: 15px !important;
    }
    #all_projects,  #projects_assigned, #projects_byinitiative  {
        font-size: 15px !important;
    }
    .save{
        color: #2a88c3 !important;
        cursor: pointer;
        margin-left: 11px;
        text-decoration: underline;
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
    .send_msg{
        /* padding: 5px!important; */
        color: #5ba1cd !important;
        font-size: 12px !important;
        text-decoration: underline !important;
    }
    table.admin_table tbody tr:nth-child(even) {
        background-color: #f8f8f8
    }
    .manageproject_table_scroll {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 20px;
    }
    table.admin_table td{
        padding: 10px;
    }
    table.admin_table thead tr th {
        background-color: #f5f5f5;
        padding: 10px;
        border-bottom: 1px solid #aaa;
        font-weight: bold;
        padding-top: 7px;
        padding-bottom: 7px;
        white-space: nowrap;
    }
    .admin_table_centered {
        text-align: center;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .custom_toggle:checked + .slider {
        background-color: #2196F3;
    }

    .custom_toggle:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    .custom_toggle:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .transaction_table thead tr th.admin_table_direction_asc > a,
    .transaction_table thead tr th > a.admin_table_direction_asc {
        background-image: url(/application/modules/Core/externals/images/admin/move_up.png?c=350);
    }
    .transaction_table thead tr th.admin_table_direction_desc > a,
    .transaction_table thead tr th > a.admin_table_direction_desc {
        background-image: url(/application/modules/Core/externals/images/admin/move_down.png?c=350);
    }
    .transaction_table thead tr th.admin_table_ordering > a,
    .transaction_table thead tr th > a.admin_table_ordering {
        font-style: italic;
        padding-right: 20px;
        background-position: 100% 50%;
        background-repeat: no-repeat;
    }
    #buttons-wrapper{
        text-align: center;
    }

    #sort_field-wrapper, #sort_direction-wrapper {
        display: none;
    }
    .count_div {
        padding: 5px;
        background-color: #f0f0f0;
        margin: 0 15px !important;
    }
    .count_div > h3 {
        font-weight: bold;
        margin-bottom: 0px !important;
    }

    ul.tag-autosuggest {
        margin-top: 0px;
        max-height: 100px;
        overflow-y: auto !important;
    }

    .table_heading{
        color: #5ba1cd !important;
    }
    .table_heading:hover{
        text-decoration: unset !important;
    }

    #error_msg_container {
        display: flex;
        padding: 7px;
        color: #D8000C;
    }
    #error_msg_outer_container {
        margin-top: 11px;
    }

    #search_spinner{
        text-align: center;
        margin-bottom: 20px;
    }

    .global_form div.form-label{
        min-width: 180px !important;
    }



</style>

