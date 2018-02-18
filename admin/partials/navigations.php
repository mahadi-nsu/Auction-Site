
<?php 
session_start();
if(!isset($_SESSION['User_id'])){
	header("Location:../../admin/index.php");
}
require_once '../../functions/functions.php';?>

<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- FONTS -->
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet">

	<!-- OFFLINE -->	
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/admin-style.css">
	<link rel="stylesheet" type="text/css" href="../../font-awesome/css/font-awesome.css">

	<script src="../js/jquery.js"></script>
	<script src="../js/bootstrap.js"></script>

</head>

<body>

	<div id="page-wrapper">
		
		<div class="top-bar">
			
			<h3 align="left">Content Management</h3>
			<h3 align="center">Welcome to admin panel</h3>
			<h3 align="right">hello,Admin</h3>

		</div> <!-- /top-bar -->

		


		<nav>
			
			<h4>Navigations</h4>
			<ul>
				<li><a href="../views/edit-contents.php"><i class="fa fa-pencil" aria-hidden="true"></i> Edit Content</a></li>
				<li><a href="../views/category-list.php"><i class="fa fa-list" aria-hidden="true"></i> Category</a></li>
				<li><a href="../views/member-list.php"><i class="fa fa-users" aria-hidden="true"></i> Manage Members</a></li>
				<li><a href="../views/admin-approval.php"><i class="fa fa-book" aria-hidden="true"></i>Posts</a></li>
			</ul>	
			<hr>
			

			



			<h4>Accounts</h4>
			<ul>
				<li><a href="../views/admin-setting.php"><i class="fa fa-cog" aria-hidden="true"></i> Settings</a></li>
				<li><a href="../views/logout.php"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a></li>
			</ul>	


		</nav>



		<div class="right-container">
			
		