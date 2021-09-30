<div id="<?php echo $this -> id ?>-wrapper" class="form-wrapper">
    <div id="<?php echo $this -> id ?>-label" class="form-label">
        <label for="<?php echo $this -> id ?>" class="<?php echo $this -> params['required'] ? 'required':'optional' ?>">
            <?php echo $this->translate($this -> params['label'])?>
        </label>
    </div>
    <div id="<?php echo $this -> id ?>-element" class="form-element">
        <input type="hidden" name="<?php echo $this -> id ?>" id="<?php echo $this -> id ?>" value="<?php echo $this -> params['value'] ? $this -> params['value'] : 0 ?>">
        <?php if ($this -> params['disabled'] == 'disabled'): ?>
            <div style="<?php echo $this -> params['style'] ?>" id="<?php echo $this -> id ?>" class="yndform_rating">
                <span id="rate_1" class="ynicon yn-star"></span>
                <span id="rate_2" class="ynicon yn-star"></span>
                <span id="rate_3" class="ynicon yn-star"></span>
                <span id="rate_4" class="ynicon yn-star"></span>
                <span id="rate_5" class="ynicon yn-star"></span>
            </div>
        <?php else: ?>
            <div style="<?php echo $this -> params['style'] ?>" id="<?php echo $this -> id ?>" class="yndform_rating" onmouseout="rating_out();">
                <span id="rate_1" class="ynicon yn-star" onclick="rate(1);" onmouseover="rating_over(1);"></span>
                <span id="rate_2" class="ynicon yn-star" onclick="rate(2);" onmouseover="rating_over(2);"></span>
                <span id="rate_3" class="ynicon yn-star" onclick="rate(3);" onmouseover="rating_over(3);"></span>
                <span id="rate_4" class="ynicon yn-star" onclick="rate(4);" onmouseover="rating_over(4);"></span>
                <span id="rate_5" class="ynicon yn-star" onclick="rate(5);" onmouseover="rating_over(5);"></span>
                <span id="rating_text" class="yndform_rating_decs"></span>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    span.ynicon.yn-star {
        color : <?php echo $this -> params['unselected_star_color'] ?>;
    }
    span.ynicon.yn-star.rating {
        color: <?php echo $this -> params['selected_star_color'] ?>;
    }
</style>

<script type="text/javascript">
    en4.core.runonce.add(function() {
        var pre_rate = $('<?php echo $this -> id ?>').value;

        var rating_over = window.rating_over = function(rating) {
            $('rating_text').innerHTML = "<?php echo $this->translate('Click to rate'); ?>";
            for(var x=1; x<=5; x++) {
                if(x <= rating) {
                    $('rate_'+x).set('class', 'ynicon yn-star rating');
                } else {
                    $('rate_'+x).set('class', 'ynicon yn-star');
                }
            }
        }
        var rating_out = window.rating_out = function() {
            $('rating_text').innerHTML = "";
            if (pre_rate != 0){
                set_rating();
            }
            else {
                for(var x=1; x<=5; x++) {
                    $('rate_'+x).set('class', 'ynicon yn-star');
                }
            }
        }
        var set_rating = window.set_rating = function() {
            var rating = pre_rate;
            for(var x=1; x<=parseInt(rating); x++) {
                $('rate_'+x).set('class', 'ynicon yn-star rating');
            }

            for(var x=parseInt(rating)+1; x<=5; x++) {
                $('rate_'+x).set('class', 'ynicon yn-star');
            }
        }
        var rate = window.rate = function(rating) {
            pre_rate = rating;
            set_rating();
            $('<?php echo $this -> id ?>').value = rating;
            $('<?php echo $this -> id ?>').fireEvent('change');
        }
        set_rating();
    });
</script>