<div class="progress">
	<?php $step_done = true; foreach(array('Choose countries', 'Order summary', 'Register account', 'Tax invoice') as $i => $title): $i++ ?>
		<?php
			$current_step = false;
			if(substr($_SERVER['SCRIPT_NAME'], 1) == "step{$i}.php"){
				$step_done = false;
				$current_step = true;
				}
		?>
		<?php if($step_done): ?>
			<div class="step done">
				<a href="step<?php echo $i ?>.php">
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
