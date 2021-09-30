<?php if($this->item):?>
<div class="yndform_form_center yndform_form_grid_mode <?php echo ($this->item->photo_id) ? '' : 'yndform_form_no_photo'?>">
    <div class="yndform_opacity"></div>
    <?php $photo_url = ($this->item->getPhotoUrl('thumb.normal')) ? $this->item->getPhotoUrl('thumb.normal') : 'application/modules/Yndynamicform/externals/images/nophoto_form_thumb_normal.png'; ?>
    <div class="yndform_image_parent">
        <div class="yndform_parent_opacity"></div>
        <div class="yndform_form_image_parent">
            <a href="dynamic-form/entry/create/1/form_id/<?php echo $this -> item -> form_id; ?>/project_id/<?php echo $this->project_id; ?>" class="yndform_form_image" style="background-image: url('<?php echo $photo_url ?>')">
            </a>
        </div>
    </div>

    <div class="yndform_info_parent">

        <div class="yndform_parent_opacity"></div>

        <div class="yndform_form_title" style="display: flex">

            <?php if(!$this->project_id): ?>
                <?php if($this->type == 'assign'): ?>
                    <a href="dynamic-form/entry/create/1/form_id/<?php echo $this -> item -> form_id; ?>/project_id/<?php echo $this->project_id; ?>">
                        <?php echo $this -> item -> getTitle(); ?>
                    </a>
                <?php endif; ?>
                <?php if($this->type == 'submit'): ?>
                    <?php $entryRes =  Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getEntryIdByProjectFormId($this -> item -> form_id,$this->project_id); ?>
                    <?php $entry_id = $entryRes[0]['entry_id']; ?>
                    <a href="dynamic-form/entry/view/<?php echo $entry_id; ?>/type/project/id/<?php echo $this->project_id; ?>">
                        <?php echo $this -> item -> getTitle(); ?>
                    </a>
                    <?php
                             $entriesTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntriesByEntryId($entry_id);
                     print_r($entriesTable[0]);
                    echo "----".$entriesTable[0]->allow_edit;
                    if($entriesTable[0]->allow_edit == 1):
                    ?>
                    <a target="_blank" id="edit_button" href="dynamic-form/entry/edit/<?php echo $entry_id; ?>/type/project/id/<?php echo $this->project_id; ?>">
                        <?php echo "Edit"; ?>
                    </a>
                    <?php endif; ?>

            <?php endif; ?>

               <!-- <?php echo $this->htmlLink($this -> item -> getHref(), $this -> item -> getTitle()) ?> -->
            <?php endif;?>

            <?php if($this->project_id):?>
              <!--  <?php echo $this->htmlLink(
                array(
                    'route' => 'yndynamicform_project_form_detail',
                    'reset' => true,
                    'form_id' => $this -> item -> form_id,
                    'project_id' => $this -> project_id,
                    'slug' => $this->item->getSlug()
                ),
                $this -> item -> getTitle()
                ); ?>
                -->
                <?php if($this->type == 'assign'): ?>
                    <a href="dynamic-form/entry/create/1/form_id/<?php echo $this -> item -> form_id; ?>/project_id/<?php echo $this->project_id; ?>">
                        <?php echo $this -> item -> getTitle(); ?>
                    </a>
                <?php endif; ?>
                <?php if($this->type == 'submit'): ?>
                    <?php $entryRes =  Engine_Api::_()->getDbTable('projectforms', 'sitepage')->getEntryIdByProjectFormId($this -> item -> form_id,$this->project_id); ?>
                    <?php $entry_id = $entryRes[0]['entry_id']; ?>
                    <a href="dynamic-form/entry/view/<?php echo $entry_id; ?>/type/project/id/<?php echo $this->project_id; ?>">
                        <?php echo $this -> item -> getTitle(); ?>
                    </a>
                    <?php
                     $entriesTable = Engine_Api::_()->getDbTable('entries', 'yndynamicform')->getEntriesByEntryId($entry_id);

                        if($entriesTable[0]['allow_edit'] == 1):
                     ?>
                        <a target="_blank" id="edit_button" href="dynamic-form/entry/edit/<?php echo $entry_id; ?>/type/project/id/<?php echo $this->project_id; ?>">
                            <?php echo "Edit"; ?>
                        </a>
                   <?php endif; ?>

                <?php endif; ?>
            <?php endif;?>

        </div>

        <?php if(strlen($this->item->description) > 0): ?>
            <div class="yndform_form_description">
                <?php echo $this->item->description; ?>
            </div>
        <?php endif; ?>

        <div class="yndform_form_category_entries">

            <!-- assigned by -->
            <?php if($this->item->page_id):?>
                <?php $sitepage = Engine_Api::_()->getItem('sitepage_page', $this->item->page_id); ?>
                <div class="yndform_form_category_parent">
                    Assigned by <a href="<?php echo $sitepage->getHref() ?>"><span><?php echo $sitepage->getTitle(); ?></span></a>
                </div>
            <br/>
            <?php endif;?>

            <!-- Assigned time -->
            <div class="yndform_post_time">
                <?php $project_forms =  Engine_Api::_()->getDbtable('projectforms', 'sitepage')->getProjectFormDetails($this->item->form_id); ?>
                <?php if($project_forms[0]['creation_date']):?>
                    <?php echo $this->translate('Assigned on'). ' ' .$this->timestamp($project_forms[0]['creation_date']); ?>
                <?php endif;?>
            </div>
            <br/>

            <!-- Valid Upto time -->
            <?php if($this->item->valid_to_date):?>
                <div class="yndform_post_time">
                    <?php echo $this->translate('Valid upto'). ' ' .$this->timestamp($this->item->valid_to_date); ?>
                </div>
                <br/>
            <?php endif;?>

        </div>

    </div>
</div>
<?php endif; ?>

<style>

    .yndform_form_title {
        justify-content: space-between;
    }
    #edit_button {
        float:right;
        padding: 7px 16px;
        font-size: 14px;
        background-color: #44AEC1;
        color: #fff;
        border: 2px solid #44AEC1;
        cursor: pointer;
        outline: none;
        position: relative;
        overflow: hidden;
        -webkit-transition: all 500ms ease 0s;
        -moz-transition: all 500ms ease 0s;
        -o-transition: all 500ms ease 0s;
        transition: all 500ms ease 0s;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-sizing: border-box;
        -mox-box-sizing: border-box;
        box-sizing: border-box;

    }
    .submit{
        font-size: 16px;
        line-height: 24px;
        font-weight: bold;
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        word-break: break-word;
        word-wrap: break-word;
        text-overflow: ellipsis;
        display: block;
    }
</style>