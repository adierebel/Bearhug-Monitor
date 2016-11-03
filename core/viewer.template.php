<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Bearhug Dashboard</title>
		<link href="css/bootstrap.css" rel="stylesheet">
		<link href="css/chartist.css" rel="stylesheet">
		<link rel="shortcut icon" href="img/icon.png">
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<link href="css/custom.css" rel="stylesheet">
	</head>
	<body>
		<div class="container-fluid">
			<center><h2>Dashboard</h3></center><br />
			<div class="row">
				<?php
					echo $main_content;
				?>
			</div>
		</div>
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="js/jquery.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.js"></script>
		<script src="js/chartist.js"></script>
		<script src="js/chartist-plugin-threshold.js"></script>
		<script src="js/all.js"></script>
		<script>
		<?php
			echo $main_script;
		?>
		</script>
	</body>
</html>