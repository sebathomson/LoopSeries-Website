<?php
include(dirname(__FILE__) . "/../header.php");

if(!$user_obj->getIsLogged()) {
	include_once($website_config->getTemplatesPath() . "login_required.php");
	exit;
}
$anime_obj = new Animes(false, false);

$num_records 	= 10;
$page 			= 1;
$view 			= "list";

foreach($_GET as $key=>$value)
	$$key = $value;

if(isset($op)) {

	switch ($op)  {
		case "mark_all_seen":
			if($anime_obj->setAllEpisodesAsSeen($id_anime,$id_season,$user_obj->getUserInfo("id_user")))
				echo "OK";
			else
				echo "There was a problem marking all episodes as seen. Please contact webmaster@loop-anime.com.";
			exit;
			break;
		case "dismark_see":
		case "mark_see":
			if(isset($id_episode)) {
				$anime_obj->setEpisodeAsSeen($id_episode, $user_obj->getUserInfo("id_user"));
			} else echo "Missing one or more arguments. Please try again!";
			break;
		case "search_seasons":
			$result = $anime_obj->getAnimeAllSeasons("animes_seasons.id_anime = '$id_anime'");
			$return = array();
			foreach($result as $r) {
				$return[] = array("id_season"=>$r["id_season"],"season"=>$r["season"]);
			}
			echo json_encode($return);
			exit;
			break;
	}
}

// Remove params
$remove_params = array("page","op","id_episode");
$query_string = $_SERVER['QUERY_STRING'];
foreach($remove_params as $param)
	$query_string = preg_replace('/&?'.$param.'=[^&]*/', '', $query_string);

$start_record = ($page - 1) * $num_records;

$where_clause = "TRUE";

// Wheres Clauses
if(isset($id_anime) and $id_anime != "") 
	$where_clause .= " AND animes.id_anime = '$id_anime'";
if(isset($id_season) and $id_season != "")
	$where_clause .= " AND animes_seasons.id_season = '$id_season'";
if(isset($filter_seen) and $filter_seen != "") {
	if($filter_seen == "2_see")
		$where_clause .= " AND (views.id_view IS NULL OR views.completed = 0)";
	elseif($filter_seen == "seen")
		$where_clause .= " AND (views.id_view IS NOT NULL)";
} else
	$where_clause .= " AND (views.id_view IS NULL OR views.completed = 0)";

$anime_obj->gather_max_rows = true;
$user_track_system = $anime_obj->getUser2SeeEpisodes($user_obj->getUserInfo("id_user"),$where_clause,$start_record,$num_records,"animes_episodes.air_date DESC");
$max_rows 	= array_pop($anime_obj->max_rows["getUser2SeeEpisodes"]);
$anime_obj->gather_max_rows = false;
$max_pages = ceil($max_rows / $num_records);

$user_favs = $user_obj->getUserFavoritesList("TRUE");
?>
<?php include(dirname(__FILE__) . "/../top_subpages.php"); ?>
<!-- Content -->
<div class="col-lg-12">
	<div class="col-lg-12"><h3><div class="glyphicon glyphicon-list pull-left"></div>&nbsp;&nbsp;Track System</h3>
	<p>Control Your Track System</p></div>
	
	<div class="clearfix"></div>
	<br>
	
	<div class="col-lg-12">
	
		<!--  Search Form -->
		<form method="get" action="track_system.php" id="track_system_form" class="form-inline" ajax="false">
			<div class="col-lg-12 pull-left">
				<div class="form-group">
					<select name="id_anime" id="combo_sel_anime" class="form-control input-small input-sm col-lg-4" style="height:20px;">
						<option value=""></option>
						<?php foreach($user_favs as $favorite) { ?>
							<option value="<?php echo $favorite["id_anime"] ?>" <?= isset($id_anime) ? ($favorite["id_anime"] == $id_anime ? "selected='selected'" : "") : "" ?>><?php echo $favorite["title"] ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<select name="id_season" id="combo_sel_season" class="form-control input-small input-sm col-lg-4 disabled" disabled="disabled" style="height:20px;">
					</select>
				</div>
				<div class="form-group">
					<select name="filter_seen" id="combo_filter_seen" class="form-control input-small input-sm col-sm-4">
						<option value="2_see" <?= (isset($filter_seen) and $filter_seen == "2_see") ? "selected='selected'" : "" ?>>Episodes to See</option>
						<option value="seen" <?= (isset($filter_seen) and $filter_seen == "seen") ? "selected='selected'" : "" ?>>Episodes i've Seen</option>
						<option value="all" <?= (isset($filter_seen) and $filter_seen == "all") ? "selected='selected'" : "" ?>>All episodes</option>
					</select>
				</div>
			</div>
		</form>
		
		<div class="clearfix"></div><br>
		<div class="col-sm-12 pull-right">
			<button class="btn btn-success btn-small pull-right" onclick="submit_form($('#track_system_form'))"><div class="glyphicon glyphicon-search pull-left"></div>&nbsp;Filtrar</button>
		</div>
		
		<!-- End Search Form -->
		
		<div class="clearfix"></div><br>
		
		<!-- Mark all as Seen -->
		<div class="col-lg-4 pull-left">
			<form method="get" action="track_system.php" id="mark_all_as_seen_form" class="form-inline" ajax="true">
				<input type="hidden" name="op" value="mark_all_seen">
				<input type="hidden" name="id_anime" value="<?php echo isset($id_anime) ? $id_anime : 0 ?>">
				<input type="hidden" name="id_season" value="<?php echo isset($id_season) ? $id_season : 0 ?>">
				<button class="btn btn-small btn-primary track_system_button"><div class="glyphicon glyphicon-eye-open pull-left"></div>&nbsp;Mark All as Seen</button>
			</form>
		</div>
		
		<!-- Pagination -->
		<?php pagination_layout("track_system.php?$query_string&page=", $page,$max_pages); ?>	
	
		<!-- List of Favorites -->
		<table class="table table-striped table-responsive">
			<tr>
				<th class="text-center">#</th>
				<th class="text-center">Anime</th>
				<th class="text-center">Title</th>
				<th class="text-center">Episode</th>
				<th class="text-center">Air Date</th>
				<th class="text-center">Operations</th>
			</tr>
			<?php foreach($user_track_system as $episode) { ?>
			<tr>
				<td class="text-center"><?php echo $episode["id_episode"]?></td>
				<td class="text-center"><a href="<?= $episode["anime_url"] ?>" class="follow"><?php echo $episode["title"] ?></a></td>
				<td class="text-center"><a href="<?= $episode["episode_url"] ?>" class="follow"><?php echo $episode["episode_title"] ?></a></td>
				<td class="text-center"><?php echo $episode["episode"]?></td>
				<td class="text-center"><?php echo date("Y-m-d",strtotime($episode["air_date"])) ?></td>
				<td class="text-center">
					<?php if($episode["completed"] == "1") { ?>
					<a href="track_system.php?op=dismark_see&id_episode=<?php echo $episode["id_episode"] ?><?= $query_string?>"><div class="glyphicon glyphicon-eye-close" style="color:red" title="Mark to see"></div></a>
					<?php } elseif(strtotime($episode["air_date"]) <= time()) { ?>
					<a href="track_system.php?op=mark_see&id_episode=<?php echo $episode["id_episode"] ?><?= $query_string?>"><div class="glyphicon glyphicon-eye-open" title="Mark as Seen"></div></a>
					<?php } else echo "<div class='label label-danger'>Future Episode!</label>"?>
				</td>
			</tr>
			<?php } ?>
		</table>
		<!-- End of favorites -->
		
		
		<!-- Pagination -->
		<?php pagination_layout("track_system.php?$query_string&page=", $page,$max_pages); ?>	
	</div>
	
</div>
<!-- End of content -->

<script type="text/javascript">

	$(document).ready(function(e) {

		var sel_season = "<?= isset($id_season) ? $id_season : 0 ?>";

		$('.track_system_button').click(function(e) {
			e.preventDefault();
			if(confirm("Do you really want to mark all episodes as Seen?\r\n Note: Not having an anime selected / filtered will mark all animes / seasons as seen!")) {
				$.ajax({url: $('#mark_all_as_seen_form').attr('action'), type: 'GET', data: $('#mark_all_as_seen_form').serialize()}).success(function(data) {
					if(data == "OK")
						$('ul.user_cp_ul li.active').trigger("click");
					else
						alert(data);
				});
				
			}
		});
		
		$('#combo_sel_anime').change(function(e) {
			$('#combo_sel_season').empty();
			if($(this).val() == "") {
				$('#combo_sel_season').val("").prop("disabled",true);
			} else {
				$('#combo_sel_season').val("").prop("disabled",false);
				$.ajax({url:"track_system.php?op=search_seasons&id_anime="+$(this).val(), type: "GET", dataType:'json'}).success(function(data) {
					$('#combo_sel_season').append($('<option>').attr('value',"").text("All Seasons"));
					$.each(data,function(i,e) {
						$('#combo_sel_season').append($('<option>').attr('value',e.id_season).text("Season " + e.season));
						if(e.id_season == sel_season)
							$('#combo_sel_season option:last').prop('selected',true);
					});
					
				});
			}
		});

		<?= isset($id_season) ? "$('#combo_sel_anime').trigger('change');" : "" ?>
	});
</script>
<?php include(dirname(__FILE__) . "/../bottom_subpages.php"); ?>