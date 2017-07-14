<html>
<head>
	<?php echo $template->scripts(true); ?>
	<?php echo $template->parts->head; ?>
</head>
<body>
	<div id="header">
		<div class="container">
			<?php echo $template->parts->header; ?>
		</div>
	</div>
	
	
	<div id="content">
		<div class='container'>
			<?php echo $template->parts->view; ?>
		</div>
	</div>
	
	<div id="footer">
		<div class="container">
			<?php echo $template->parts->footer; ?>	
		</div>
	</div>
	<?php echo $template->scripts(); ?>
</body>
</html>

