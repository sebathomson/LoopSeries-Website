<?php
include(dirname(__FILE__) . "/../header.php");

if(!$user_obj->getIsLogged()) {
	include_once($website_config->getTemplatesPath() . "login_required.php");
	exit;
}

$num_records = 10;
$page = 1;

foreach($_GET as $key=>$value)
	$$key = $value;

if(isset($op)) {
	
	if(!isset($anime_obj))
		$anime_obj = new Animes(false,false);
	
	switch ($op)  {
		case "del":
			$anime_obj->removeFavorite($id_favorite);
			break;
		case "add":
			if($id_anime == "") 
				echo "Please search for a valid anime title or ID";
			else {
				$users_favs = $user_obj->getUserFavoritesList("id_anime='$id_anime'", 0, 1);
				if(count($users_favs) > 0)
					echo "You already have this anime in your favorite list!";
				else {
					$anime_obj->setAnimeAsFavorite($id_anime, $user_obj->getUserInfo("id_user"));
					echo "OK";
				}
			}
			exit;
			break;
		case "search_title":
			$result = $anime_obj->getAnimes("title LIKE '%$q%' OR id_anime = '$q'");
			echo json_encode($result);
			exit;
			break;
	}
}

// Remove params
$remove_params = array("page");
$query_string = $_SERVER['QUERY_STRING'];
foreach($remove_params as $param)
	$query_string = preg_replace('/&?'.$param.'=[^&]*/', '', $query_string);

$start_record = ($page - 1) * $num_records;

// Gets Users Fav
$users_favs = $user_obj->getUserFavoritesList("TRUE", $start_record, $num_records);
$max_rows 	= $user_obj->max_rows;
$max_pages = ceil($max_rows / $num_records);

?>
<?php include(dirname(__FILE__) . "/../top_subpages.php"); ?>
<!-- Content -->
<div class="col-lg-12">
	<div class="col-lg-12"><h3><div class="glyphicon glyphicon-star pull-left"></div>&nbsp;&nbsp;Favorite List</h3>
	<p>Control your favorite List</p></div>
	
	<div class="clearfix"></div>
	<br>
	
	<div class="col-lg-12">
	
		<!-- Form to Add new Anime -->
		<div class="col-lg-4 pull-left">
		<form class="form-control form-inline" style="width:600px" name="favorite_list" id="favorite_list_add" method="get" action="favorite_list.php">
			<input type="hidden" name="op" value="add">
			<input type="hidden" id="id_anime" name="id_anime">
			<input type="text" id="anime_name" name="anime_name" class="form-control input-small pull-left" placeholder="Title or ID of anime..." style="width: 200px"  value="">
			&nbsp;&nbsp;<button class="form-control btn btn-success btn-small" style="top:0px; position:absolute">Add to Favorite!</button>
		</form>
		</div>
		<!-- End Add Form -->
		
		<!-- Pagination -->
		<?php //pagination_layout("favorite_list.php?$query_string&page=", $page,$max_pages); ?>	
		<div class="clearfix"></div><br>
		
		<!-- List of Favorites -->
		<?php 
		if(count($users_favs) == 0)
			include($website_config->getTemplatesPath() . '/no_records_found.php');
		else {?>
		<table class="table table-striped table-responsive">
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Status</th>
				<th>Episodes</th>
				<th>Saw</th>
				<th>To See</th>
				<th>Operations</th>
			</tr>
			<?php foreach($users_favs as $serie) { ?>
			<tr>
				<td class="text-center"><?php echo $serie["id_anime"]?></td>
				<td class="text-center"><a href="<?= $serie["anime_url"]?>" class="follow"><?php echo $serie["title"] ?></a></td>
				<td class="text-center"><?php echo $serie["status"]?></td>
				<td class="text-center"><?php echo $serie["total_episodes"] ?></td>
				<td class="text-center"><?php echo $serie["total_saw"] ?></td>
				<td class="text-center"><?php echo $serie["total_episodes"] - $serie["total_saw"] ?></td>
				<td class="text-center">
					<a href="favorite_list.php?op=del&id_favorite=<?php echo $serie["id_favorite"] ?>"><div class="glyphicon glyphicon-trash" data-toggle="tooltip" title="Delete Record"></div></a>
				</td>
			</tr>
			<?php } ?>
		</table>
		<!-- End of favorites -->
		
		<!-- Pagination -->
		<?php pagination_layout("favorite_list.php?$query_string&page=", $page,$max_pages); ?>	
		<?php } ?>
	</div>
	
</div>
<!-- End of content -->

<script type="text/javascript">
	$(document).ready(function(e) {

		$('#anime_name').typeahead({
			name: "anime_search",
			remote: 'favorite_list.php?op=search_title&q=%QUERY',
			template: "<div style='white-space:nowrap; width:200px; height:100px'><img src='{{poster}}' style='height:100px; float:left; margin-right:3px;'><strong>Title:</strong><br><span>{{title}}</span><br><strong>Release:<br></strong><span>{{startTime}}</span></div>",
			engine: Hogan,
			valueKey: 'title'
		}).on('typeahead:selected', function(obj, datum) {
			$('#anime_name').val(datum.title);
			$('#id_anime').val(datum.id_anime);
		}).on('typeahead:closed', function() {
			console.log("alert");
			if($('#id_anime').val() != "")
				return false;
			else {
				$('#id_anime').val('');
				$('#anime_name').val('');
				return true;
			}
		});
	});
</script>

<?php include(dirname(__FILE__) . "/../bottom_subpages.php"); ?>