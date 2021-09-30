<h3>
    Initiative Details
</h3>
<?php
foreach($this->projectInitiativeAnswers as $questionanswer) {
  ?>
<div>
    <p class="question"> <?php echo $questionanswer['initiative_question']; ?> </p>
    <p class="answer"> <?php echo $questionanswer['initiative_answer']; ?> </p><br>
</div>
<?php }
?>
<style>
    .question{
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 3px;
        font-family: Roboto, sans-serif;
    }
</style>