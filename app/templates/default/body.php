<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $template->parts->head; ?>
</head>
<body>
	<div id="content">
		
		<?php echo $template->parts->header; ?>

		<?php echo $template->parts->view; ?>

		<?php echo $template->parts->footer; ?>	
	
		<?php echo $template->scripts(); ?>
	</div>
</body>
</html>

