<?php if ($this->pageBreakConfigs['progress_indicator'] == 'none'): ?>
<div id ="progress_indicator_none-wrapper" class="yndform_progress_indicator_none"></div>
<?php else: ?>
<?php $type = $this->pageBreakConfigs['progress_indicator'];?>
    <div id ="progress_indicator-wrapper" class="yndform_progress_indicator">
        <div class="yndform_indicator_progress_<?php echo $type ?>_parent">
            <ul class="yndform_indicator_progress_<?php echo $type ?>_items">
                <?php for ($i = 1; $i < $this -> totalPageBreak; $i++): ?>
                <li title="<?php echo $this->pageBreakConfigs['page_names'][$i-1]?>" style="width: <?php echo (int)(100/(int)($this->totalPageBreak-1)) ?>%" class="yndform_progress_<?php echo $type ?>_item" id="progress_<?php echo $i ?>">
                    <?php if($type == 'step'): ?>
                        <span class="yndform_progree_<?php echo $type ?>_text_inverse"><?php echo $this->translate($i) ?></span>
                        <span class="yndform_progree_<?php echo $type ?>_text"><?php echo $this->pageBreakConfigs['page_names'][$i-1] ?></span>
                        <span class="yndform_progree_<?php echo $type ?>_break"></span>
                    <?php elseif($type == 'bar'): ?>
                        <span class="ynform_progress_<?php echo $type ?>_percen"><?php echo (int)(100*$i/(int)($this->totalPageBreak-1)) ?>%</span>
                        <span class="ynform_progress_<?php echo $type ?>_circle">
                            <span class="ynform_progress_<?php echo $type ?>_circle_child"></span>
                            <span class="ynform_progress_<?php echo $type ?>_circle_child_decs"><?php echo $this->pageBreakConfigs['page_names'][$i-1]?></span>
                        </span>
                        <span class="ynform_progress_<?php echo $type ?>_bg"></span>
                    <?php endif; ?>
                </li>
                <?php endfor; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<style>
    <?php if($type == 'step'): ?>
    .yndform_progress_step_item.active {
        background-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    .yndform_progress_step_item.active .yndform_progree_step_break {
        border-top-color: #eee;
        border-bottom-color: #eee;
    }
    .yndform_progress_step_item.actived .yndform_progree_step_break {
        border-top-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
        border-bottom-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    .yndform_progress_step_item.active .yndform_progree_step_break:before{
         border-left-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
     }
    .yndform_progress_step_item.active .yndform_progree_step_text_inverse {
        color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
        background-color: <?php echo $this->pageBreakConfigs['text_color'] ?> !important;
    }
    .yndform_progress_step_item.active .yndform_progree_step_text{
        background-color: #fff;
        color: <?php echo $this->pageBreakConfigs['text_color'] ?> !important;
    }
    .yndform_progress_step_item.active .yndform_progree_step_text{
        background-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    <?php elseif($type == 'bar'): ?>
    .yndform_progress_bar_item.active .ynform_progress_bar_bg,
    .yndform_progress_bar_item.active .ynform_progress_bar_circle_child {
        background-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    .yndform_progress_bar_item .ynform_progress_bar_percen {
        color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    .yndform_progress_bar_item.active .ynform_progress_bar_circle {
        border-color: <?php echo $this->pageBreakConfigs['background_color'] ?> !important;
    }
    .yndform_progress_bar_item.active .ynform_progress_bar_circle_child_decs {
        color: <?php echo $this->pageBreakConfigs['text_color'] ?> !important;
    }
    <?php endif; ?>
</style>