<div class="markProgress ">

	<?php $step_done = true;
$path_parts=pathinfo($_SERVER['SCRIPT_NAME']);     
    foreach(array('Choose industries', 'Mark Info', 'Order Summary', 'Register Account','Tax Invoice') as $i => $title): $i++ ?>
		<?php
			$current_step = false;
			if($path_parts['filename'] == "mark_step{$i}"){
				$step_done = false;
				$current_step = true;
				}
		?>
		<?php if($step_done): ?>
			<div class="step done">
				<a href="mark_step<?php echo $i ?>.php">
					<?php echo $i ?>. <?php echo $title ?>  <b>&#10003;</b>
				</a>
			</div>
		<?php elseif($current_step): ?>
			<div class="step current">
				<?php echo $i ?>. <?php echo $title ?>
			</div>
		<?php else: ?>
			<div class="step">
				<?php echo $i ?>. <?php echo $title ?>
			</div>
		<?php endif ?>
	<?php endforeach ?>
</div>
