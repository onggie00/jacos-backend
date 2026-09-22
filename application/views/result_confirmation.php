<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Confirmation Form</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body style="font-family: arial, sans-serif, Times;background-color: #5491F8; height: 100vh;">
	<div class="container">
		<div class="row ">
			<div class="col-md-12 mt-5 p-3 text-center bg-white rounded ">
				<h1>Thank You</h1>
				<?php if (!empty($this->session->flashdata('success'))): ?>
					<span class="badge badge-success p-3" >
						<h3><?php echo $this->session->flashdata('success'); ?></h3>
					</span>
				<?php endif ?>
				<?php if (!empty($this->session->flashdata('failed'))): ?>
					<span class="badge badge-danger p-3" >
						<h3><?php echo $this->session->flashdata('failed'); ?></h3>
					</span>
				<?php endif ?>
			</div>
		</div>
	</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>