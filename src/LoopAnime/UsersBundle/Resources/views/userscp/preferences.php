<?php
include(dirname(__FILE__) . '/../top_subpages.php');

if(!$user_obj->getIsLogged())
	die("You Don't Have Access to this page!");

$msg = ""; $errors = array();

if(isset($_GET["op"]) and $_GET["op"] != "") {
	unset($_GET["op"]);
	if($user_obj->setUserPreferences($_GET))
		$msg = "Your preferences has been updated successfully!";
	else
		$errors = $user_obj->getErrors();
}

$stats = $user_obj->getStatistics();

$preferences_arr = $user_obj->getUserInfo("preferences");

?>

<?php if(count($errors) > 0 or $msg != "") { ?>
<div class="col-lg-12 alert alert-<?= count($errors) > 0 ? "danger" : "success" ?>">
	<p><b><?= count($errors) > 0 ? "The follow errors just happened:" : "Message:" ?></b></p>
	<?php foreach($errors as $error) echo " - " . $error . "<br>"; ?>
	<?php echo $msg;?>
</div>
<?php } ?>

<div class="col-lg-12">
	<div class="col-lg-12"><h3><div class="glyphicon glyphicon-cog pull-left"></div>&nbsp;&nbsp;Preferences</h3></div>
	
	<div class="clearfix"></div>
	
	<!-- Change Password Form -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Website Preferences</h4></div>
		<form class="form-control form-horizontal" id="change_password" name="change_password" method="post" action="preferences.php" ajax="false">
			<input type="hidden" name="op" value="run">
			<div class="form-group">
				<label class="col-lg-6 text-right">Full Screen (Hide SideBar):</label>
				<div class="col-lg-6">
					<input type="checkbox" name="full_screen" class="form-control input-small" <?= $preferences_arr["full_screen"] == "1" ? "checked='checked'" : "" ?> 
					onclick="if($(this).prop('checked')) $(this).next('input').val('1'); else $(this).next('input').val('0');">
					<input type="hidden" name="full_screen" value="<?= $preferences_arr["full_screen"] ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">My profile is public:</label>
				<div class="col-lg-6">
					<input type="checkbox" name="public_profile" class="form-control input-small" <?= $preferences_arr["public_profile"] == "1" ? "checked='checked'" : "" ?> 
					onclick="if($(this).prop('checked')) $(this).next('input').val('1'); else $(this).next('input').val('0');">
					<input type="hidden" name="public_profile" value="<?= $preferences_arr["public_profile"] ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Share my Lists:</label>
				<div class="col-lg-6">
					<input type="checkbox" name="share_lists" class="form-control input-small" <?= $preferences_arr["share_lists"] == "1" ? "checked='checked'" : "" ?> value="1"
					onclick="if($(this).prop('checked')) $(this).next('input').val('1'); else $(this).next('input').val('0');">
					<input type="hidden" name="share_lists" value="<?= $preferences_arr["share_lists"] ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="col-lg-12 text-right">
				<button class="btn btn-small btn-success">Update Preferences</button>
			</div>
		</form>
	</div>
	<!-- End Change Password Form -->
	
	<div class="clearfix"></div>
	
	<!-- Change Avatar Form -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;VideoPlayer Preferences</h4></div>
		<form class="form-control form-horizontal" id="change_avatar" name="change_avatar" method="post" action="preferences.php" ajax="false">
			<input type="hidden" name="op" value="run">
			<div class="form-group">
				<label class="col-lg-6 text-right">Quality of the Video using Mobile:</label>
				<div class="col-lg-6">
					<select name="mobile_videoq" class="form-control input-small">
						<option value="hq" <?= $preferences_arr["mobile_videoq"] == "hq" ? "selected='selected'" : "" ?>>Best Quality (increased file size)</option>
						<option value="dq" <?= $preferences_arr["mobile_videoq"] == "dq" ? "selected='selected'" : "" ?>>Default Quality (suitable for most of cases)</option>
						<option value="lq" <?= $preferences_arr["mobile_videoq"] == "lq" ? "selected='selected'" : "" ?>>Low Quality (small file size)</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Quality of the Video using Website:</label>
				<div class="col-lg-6">
					<select name="website_videoq" class="form-control input-small">
						<option value="hq" <?= $preferences_arr["website_videoq"] == "hq" ? "selected='selected'" : "" ?>>Best Quality (increased file size)</option>
						<option value="dq" <?= $preferences_arr["website_videoq"] == "dq" ? "selected='selected'" : "" ?>>Default Quality (suitable for most of cases)</option>
						<option value="lq" <?= $preferences_arr["website_videoq"] == "lq" ? "selected='selected'" : "" ?>>Low Quality (small file size)</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Mirrors Priority:</label>
				<div class="col-lg-6">
					<select name="mirrors_choice" class="form-control input-small">
						<option value="mirror_1" <?= $preferences_arr["mirrors_choice"] == "mirror_1" ? "selected='selected'" : "" ?>>Pick the first mirror available</option>
						<option value="mirror_hoster_most_use" <?= $preferences_arr["mirrors_choice"] == "mirror_hoster_most_use" ? "selected='selected'" : "" ?>>Pick the mirror from a hoster i use the most</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Mirrors Subtitles:</label>
				<div class="col-lg-6">
					<select name="mirrors_subtitles" class="form-control input-small">
						<option value="sub_without" <?= $preferences_arr["mirrors_subtitles"] == "sub_without" ? "selected='selected'" : "" ?>>Without Subtitles</option>
						<option value="sub_eng" <?= $preferences_arr["mirrors_subtitles"] == "sub_eng" ? "selected='selected'" : "" ?>>English</option>
						<option value="sub_user" <?= $preferences_arr["mirrors_subtitles"] == "sub_user" ? "selected='selected'" : "" ?>>My User Language</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></div><br>
			<div class="col-lg-12 text-right">
				<button class="btn btn-small btn-success pull-right">Update Preferences</button>
			</div>
		</form>
	</div>
	<!-- End Change Avatar Form -->
	
	<div class="clearfix"></div>
	
	<!--  Change Email Form  -->
	<div class="col-lg-12">
		<div class="col-lg-12 header-title"><h4><div class="glyphicon glyphicon-chevron-right pull-left"></div>&nbsp;Track System</h4></div>
		<form class="form-control form-horizontal" id="change_email" name="change_email" method="post" action="preferences.php" ajax="false">
			<input type="hidden" name="op" value="run">
			<div class="form-group">
				<label class="col-lg-6 text-right">Automatic Mark Episode as seen:</label>
				<div class="col-lg-6">
					<select name="automatic_track" class="form-control input-small">
						<option value="disabled" <?= $preferences_arr["automatic_track"] == "disabled" ? "selected='selected'" : "" ?>>Disabled</option>
						<option value="on_player_start" <?= $preferences_arr["automatic_track"] == "on_player_start" ? "selected='selected'" : "" ?>>On Player Start</option>
						<option value="after_10min" <?= $preferences_arr["automatic_track"] == "after_10min" ? "selected='selected'" : "" ?>>After 10 minutes on episode page</option>
						<option value="after_20min" <?= $preferences_arr["automatic_track"] == "after_20min" ? "selected='selected'" : "" ?>>After 20 minutes on episode page</option>
						<option value="askme_before_leave" <?= $preferences_arr["automatic_track"] == "askme_before_leave" ? "selected='selected'" : "" ?>>Ask me Before Leave Page</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Sort Episodes:</label>
				<div class="col-lg-6">
					<select name="track_episodes_sort" class="form-control input-small">
						<option value="desc" <?= $preferences_arr["track_episodes_sort"] == "desc" ? "selected='selected'" : "" ?>>Newer to older</option>
						<option value="asc" <?= $preferences_arr["track_episodes_sort"] == "asc" ? "selected='selected'" : "" ?>>Older to Newer</option>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Include Special Episodes on 2 See List:</label>
				<div class="col-lg-6">
					<input type="checkbox" name="2_see_list_specials" class="form-control input-small" <?= $preferences_arr["2_see_list_specials"] == "1" ? "checked='checked'" : "" ?> value="1"
					onclick="if($(this).prop('checked')) $(this).next('input').val('1'); else $(this).next('input').val('0');">
					<input type="hidden" name="2_see_list_specials" value="<?= $preferences_arr["2_see_list_specials"] ?>">
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group">
				<label class="col-lg-6 text-right">Include Special Episodes on Future List:</label>
				<div class="col-lg-6">
					<input type="checkbox" name="future_list_specials" class="form-control input-small" <?= $preferences_arr["future_list_specials"] == "1" ? "checked='checked'" : "" ?> value="1"
					onclick="if($(this).prop('checked')) $(this).next('input').val('1'); else $(this).next('input').val('0');">
					<input type="hidden" name="future_list_specials" value="<?= $preferences_arr["future_list_specials"] ?>">
				</div>
			</div>
			<div class="clearfix"></div><br>
			<div class="col-lg-12 text-right">
				<button class="btn btn-small btn-success pull-right">Update Preferences</button>
			</div>
		</form>
	</div>
	<!-- End Change Email Form -->
	
	<div class="clearfix"></div><br>
	
</div>