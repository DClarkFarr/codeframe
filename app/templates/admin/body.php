<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
	<?php echo $template->parts->head; ?>
</head>
<body>
	<div id="container">
		
		<?php echo $template->parts->header; ?>

		<?php echo $template->parts->view; ?>

		<?php echo $template->parts->footer; ?>	
	
		<?php echo $template->scripts(); ?>
	</div>
</body>
</html>

