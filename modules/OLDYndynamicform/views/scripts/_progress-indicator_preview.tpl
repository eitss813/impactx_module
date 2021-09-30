<div id ="progress_indicator_none-wrapper" class="yndform_progress_indicator_none"></div>

<div id ="progress_indicator_step-wrapper" class="yndform_progress_indicator_bar">
	<div class="yndform_indicator_progress_bar_parent">
		<ul class="yndform_indicator_progress_bar_items">
			<li class="yndform_progress_bar_item yndform_preview_bg">
				<span class="yndform_progree_bar_text_inverse yndform_preview_text_inv"><?php echo $this->translate('1') ?></span>
				<span class="yndform_progree_bar_text yndform_preview_text"><?php echo $this->translate('Page 1') ?></span>
				<span class="yndform_progree_bar_break yndform_preview_bg"></span>
			</li>
			<li class="yndform_progress_bar_item">
				<span class="yndform_progree_bar_text_inverse"><?php echo $this->translate('2') ?></span>
				<span class="yndform_progree_bar_text"><?php echo $this->translate('Page 2') ?></span>
				<span class="yndform_progree_bar_break"></span>
			</li>
			<li class="yndform_progress_bar_item">
				<span class="yndform_progree_bar_text_inverse">
					<?php echo $this->translate('3') ?>
				</span>
				<span class="yndform_progree_bar_text">
					<?php echo $this->translate('Page 3') ?>
				</span>
			</li>
		</ul>
	</div>
</div>

<div id ="progress_indicator_bar-wrapper" class="yndform_progress_indicator_step">
	<div class="yndform_indicator_progress_step_parent">
		<ul class="yndform_indicator_progress_step_items">
			<li class="yndform_progress_step_item step_active">
				<span class="ynform_progress_step_circle yndform_preview_bg">
					<span class="ynform_progress_step_circle_child yndform_preview_text"><?php echo $this->translate('Page 1')?></span>
				</span>
				<span class="ynform_progress_step_bg yndform_preview_bg"></span>
			</li>
			<li class="yndform_progress_step_item step_active">
				<span class="ynform_progress_step_circle">
		        	<span class="ynform_progress_step_circle_child"><?php echo $this->translate('Page 2')?></span>
		        </span>
				<span class="ynform_progress_step_bg"></span>
			</li>
			<li class="yndform_progress_step_item">
				<span class="ynform_progress_step_circle">
					<span class="ynform_progress_step_circle_child"><?php echo $this->translate('Page 3')?></span>
				</span>
				<span class="ynform_progress_step_bg"></span>
			</li>
		</ul>
	</div>
</div>