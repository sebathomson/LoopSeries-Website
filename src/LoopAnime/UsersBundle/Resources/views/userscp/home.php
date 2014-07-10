<?php
include(dirname(__FILE__) . '/../top_subpages.php');

if(!$user_obj->getIsLogged())
	die("You Don't Have Access to this page!");

$errors = array();
$msg = "";

if(isset($_GET["op"])) {
	
	$op = $_GET['op'];
	
	switch($op) {
		case "change_country":
			if($user_obj->setUserAccountInformation(array("country" => $_GET["country"], "lang" => $_GET["language"])))
				$msg = "Your account information has been updated successfully";
			else
				$errors = $user_obj->getErrors();
			break;
		case "change_account_info":
			if($user_obj->setUserAccountInformation(array("newsletter" => $_GET["newsletter"], "birthdate" => $_GET["birthdate"])))
				$msg = "Your account information has been updated successfully";
			else
				$errors = $user_obj->getErrors();
			break;
		case "change_pw":
			if($user_obj->setChangePw($_GET))
				$msg = "Your Password has been changed successfully!";
			else 
				$errors = $user_obj->getErrors();
			break;
		case "change_avatar":
			if($user_obj->setChangeAvatar($_GET,$_FILES))
				$msg = "Your Avatar has been changed successfully!";
			else
				$errors = $user_obj->getErrors();
			break;
		case "change_email":
			if($user_obj->setChangeEmail($_GET))
				$msg = "Your Email has been changed successfully!";
			else 
				$errors = $user_obj->getErrors();
			break;
		default:
			$errors[] = "Sent one unrecognized operation: $op";
			break;
	}
}

$countries 	= $user_obj->getCountries();
$stats 		= $user_obj->getStatistics();
$user_info 	= $user_obj->getUserInfo();
?>

<?php if(count($errors) > 0 or $msg != "") { ?>
<div class="col-lg-12 alert alert-<?= count($errors) > 0 ? "danger" : "success" ?>">
	<p><b><?= count($errors) > 0 ? "The follow errors just happened:" : "Message:" ?></b></p>
	<?php foreach($errors as $error) echo " - " . $error . "<br>"; ?>
	<?php echo $msg;?>
</div>
<?php } ?>

<div class="col-lg-12">
	<div class="col-lg-12"><h3><div class="glyphicon glyphicon-user pull-left"></div>&nbsp;&nbsp;Account</h3></div>
	
	<div class="clearfix"></div>
	
	<div class="col-lg-12 row">
		<div class="pull-left" style="margin-right: 20px">
			<img class="thumbnail" src="<?php echo $user_obj->getUserInfo("avatar")?>" width="100px">
		</div>
		<div class="pull-left">
			<p>
				<strong>User's Favorites:</strong>&nbsp;<?php echo $stats["tot_fav"]?><br>
				<strong>New episodes:</strong>&nbsp;<?php echo $stats["tot_newEpisodes"]?><br>
				<strong>To see:</strong>&nbsp;<?php echo $stats["tot_2see"]?><br>
				<strong>Have seen:</strong>&nbsp;<?php echo $stats["tot_seen"]?><br>
				<strong>Watching:</strong>&nbsp;<?php echo $stats["tot_onProgress"]?>
			</p>
		</div>
		<div class="pull-right">
		<small><b>Operations:</b></small>
		<ul style="list-style: none">
			<li><a href="#change_account_info" class="anchor">Change Account Information</a></li>
			<li><a href="#change_password" class="anchor">Change Password</a></li>
			<li><a href="#change_avatar" class="anchor">Change Avatar</a></li>
			<li><a href="#change_email" class="anchor">Change E-mail</a></li>
		</ul>
		</div>
	</div>
	
	<div class="clearfix"></div><br>
	
	<!-- Change Account Information -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Change Account Information</h4></div>
		<form class="form-control form-horizontal" id="change_account_info" name="change_password" method="post" action="home.php" ajax="false">
			<input type="hidden" name="op" value="change_account_info">
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Username:</label>
				<div class="col-lg-6">
					<!--  <input type="text" name="username" class="form-control input-small" value="<?= $user_obj->getUserInfo("username") ?>"> -->
					<?= $user_obj->getUserInfo("username") ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">BirthDate:</label>
				<div class="col-lg-6">
					<input type="text" name="birthdate" class="form-control input-small" value="<?= $user_obj->getUserInfo("birthdate") ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Receive emails:</label>
				<div class="col-lg-6">
					<input type="checkbox" name="newsletter2" class="form-control input-small" <?= $user_obj->getUserInfo("newsletter") == "1" ? "checked='checked'" : "" ?>> <small>*Includes news and other important announcements</small>
					<input type="hidden" name="newsletter" value="<?= $user_obj->getUserInfo("newsletter") ?>">
				</div>
			</div>
			<div class="clearfix"></div><br>
			<div class="col-lg-10 col-offset-1 text-right">
				<button class="btn btn-small btn-success">Change Account Informations</button>
			</div>
		</form>
	</div>
	<!-- End Change Account Form -->
	
	<!-- Change Password Form -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Change Password</h4></div>
		<form class="form-control form-horizontal" id="change_password" name="change_password" method="post" action="home.php" ajax="false">
			<input type="hidden" name="op" value="change_pw">
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Old Password:</label>
				<div class="col-lg-6">
					<input type="password" name="old_pw" class="form-control input-small">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">New Password:</label>
				<div class="col-lg-6">
					<input type="password" name="new_pw" class="form-control input-small">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Re-Type Password:</label>
				<div class="col-lg-6">
					<input type="password" name="new_pw2" class="form-control input-small"><br>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-10 col-offset-1 text-right">
				<button class="btn btn-small btn-success">Change Password</button>
			</div>
		</form>
	</div>
	<!-- End Change Password Form -->
	
	<div class="clearfix"></div>
	
	<!-- Change Avatar Form -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Change Avatar</h4></div>
		<form class="form-control form-horizontal" id="change_avatar" name="change_avatar" method="post" action="home.php" enctype="multipart/form-data" ajax="false">
			<input type="hidden" name="op" value="change_avatar">
			<!--  <div class="form-group">
				<label class="col-lg-4 text-right">Upload from your PC:</label>
				<div class="col-lg-6">
					<input type="file" name="local_avatar" class="form-control input-small">
				</div>
			</div>
			<div class="clearfix"></div>-->
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Upload from URL:</label>
				<div class="col-lg-6">
					<input type="text" name="remote_avatar" class="form-control input-small"><br>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-10 col-offset-1 text-right">
				<button class="btn btn-small btn-success pull-right">Upload Avatar</button>
			</div>
		</form>
	</div>
	<!-- End Change Avatar Form -->
	
	<div class="clearfix"></div>
	
	<!-- Change Avatar Form -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Change Country</h4></div>
		<form class="form-control form-horizontal" id="change_country" name="change_country" method="post" action="home.php" enctype="multipart/form-data" ajax="false">
			<input type="hidden" name="op" value="change_country">
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Country:</label>
				<div class="col-lg-6">
					<select name="country" class="form-control input-small">
						<option value=""></option>
						<?php foreach($countries as $country) { ?>
							<option value="<?php echo $country["iso3"] ?>" <?= ($user_info["country"] == $country["iso3"] ? "selected='selected'" : "" )?>><?php echo $country["description"]?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Language:</label>
				<div class="col-lg-6">
					<select name="language" class="form-control input-small">
						<option value="ENG" <?= $user_info["lang"] == "ENG" ? "selected='selected'" : "" ?>>English</option>
						<option value="BR" <?= $user_info["lang"] == "BR" ? "selected='selected'" : "" ?>>Portuguese-Brazilian</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></div><br>
			<div class="col-lg-10 col-offset-1 text-right">
				<button class="btn btn-small btn-success pull-right">Update Preferences</button>
			</div>
		</form>
	</div>
	<!-- End Change Avatar Form -->
	
	<div class="clearfix"></div>
	
	<!--  Change Email Form  -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Change E-mail</h4></div>
		<form class="form-control form-horizontal" id="change_email" name="change_email" method="post" action="home.php">
			<input type="hidden" name="op" value="change_email">
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">New Email:</label>
				<div class="col-lg-6">
					<input type="password" name="new_email" class="form-control input-small">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-4 col-offset-1 text-right">Password:</label>
				<div class="col-lg-6">
					<input type="password" name="pw" class="form-control input-small"><br>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-10 col-offset-1 text-right">
				<button class="btn btn-small btn-success pull-right">Change E-mail</button>
			</div>
		</form>
	</div>
	<!-- End Change Email Form -->
	
	<div class="clearfix"></div><br>
	
</div>