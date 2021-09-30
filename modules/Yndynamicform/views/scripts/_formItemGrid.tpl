<div class="yndform_form_center yndform_form_grid_mode <?php echo ($this->item->photo_id) ? '' : 'yndform_form_no_photo'?>">
    <div class="yndform_opacity"></div>
    <?php $photo_url = ($this->item->getPhotoUrl('thumb.normal')) ? $this->item->getPhotoUrl('thumb.normal') : 'application/modules/Yndynamicform/externals/images/nophoto_form_thumb_normal.png'; ?>
    <div class="yndform_image_parent">
        <div class="yndform_parent_opacity"></div>
        <a href="<?php echo $this->item->getHref() ?>" class="yndform_form_image" style="background-image: url('<?php echo $photo_url ?>')">
        </a>
    </div>

    <div class="yndform_info_parent">
        <div>
            <div class="yndform_parent_opacity"></div>
            <div class="yndform_parent_opacity_border"></div>
            <div class="yndform_max_height">
                <div class="yndform_form_title">
                    <?php echo $this->htmlLink($this -> item -> getHref(), $this -> item -> getTitle()) ?>
                </div>
                <div class="yndform_form_category_entries">
                    <div class="yndform_post_time">
                        <?php  echo $this->translate('Created on'). ' ' .$this->timestamp($this->item->creation_date) ?>
                    </div>
                    <i class="yn_dots">.</i>
                    <div class="yndform_form_category_parent">
                        <?php 
                            if (count($this -> category -> getBreadCrumNode()) > 0):
                            echo $this->htmlLink(
                            $this -> category->getHref(),
                            $this->translate($this -> category->getTitle()),
                            array('title' => $this->translate($this -> category->getTitle())));
                        else:
                            echo $this->translate("Uncategoried");
                        endif; ?>
                    </div>
                </div>
                <?php if(strlen($this->item->description) > 0): ?>
                <div class="yndform_form_description">
                    <?php echo $this -> item -> description ?>
                </div>
                <?php endif; ?>
            </div>
            <span class="yndform_form_entries">
                <?php echo (isset($this->item->total_entries) ? '<span class="ynicon yn-bars"></span>'.$this->item->total_entries : 0) . ' ' .$this->translate('entries') ?>
            </span>
        </div>
    </div>
</div>
