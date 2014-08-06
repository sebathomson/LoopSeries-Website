<?php

set_time_limit(0);

include("../includes/website_config.php");

$website_config = new website_config();

include($website_config->getIncludesPath() . "animes_class.php");

$animes_obj = new Animes(false);

include_once($website_config->getConnectionsPath() ."conn_anime.php");

$db = new Anime_db();

foreach($_GET as $key=>$value)
	$$key = $value;

if(isset($run) and $run == "true") {

	$animes_obj = new Animes(true,true);
	
	if($api == "BOTH") {
		$animes_obj->CreateNewAnime("3", $api_anime_key);
		$animes_obj->CreateNewAnime("2", $api_anime_key);
	} else 
		$animes_obj->CreateNewAnime($api, $api_anime_key);
		
	echo "<br><a href='index.php'><< Go back to panel</a>";
	exit;
}

$query = "SELECT animes.title, animes_api.api_anime_key FROM animes_api JOIN animes USING(id_anime)";
$rs_api_keys = $db->Query($query);


?>

<html>
<head>
  <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
</head>
<body>
	<form method="get" action="index.php">
		<input type="hidden" name="run" value="true">
		Api: <select name="api"><option value="BOTH">Both</option><option value="3">Trakt.tv</option><option value="2">TheTVDB</option></select><br>
		Update Anime: <select name="id_anime" id="anime_sel"><option value=""></option><?php while($row = $rs_api_keys->fetch_assoc()) echo '<option value="'.$row["api_anime_key"].'">'.$row["title"].'</option>'; ?></select> <br>
		TvDB ID: <input type="text" name="api_anime_key" id="api_anime_key">
		<input type="submit" value="Go For it!">
	</form>
	
	<script type="text/javascript">
	$(document).ready(function(e) {

		$('#anime_sel').change(function(e){
			$('#api_anime_key').val($(this).val());
		});
		
	});
	</script>
</body>
</html>