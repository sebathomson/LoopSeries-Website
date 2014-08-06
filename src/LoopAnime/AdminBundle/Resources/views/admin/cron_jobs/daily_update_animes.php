<?php

set_time_limit(0);

include(dirname(__FILE__) . '/../../connections/conn_anime.php');
include(dirname(__FILE__) . '/../../includes/animes_class.php');

$db 			= new Anime_db();
$animes_obj 	= new Animes(false, false);

$query = "SELECT api_anime_key FROM animes_api GROUP BY api_anime_key";
$rs_animes_api = $db->Query($query);

if($rs_animes_api->num_rows == 0) exit;

while($row = $rs_animes_api->fetch_assoc()) {
	$anime_key = $row ['api_anime_key'];
	$animes_obj->CreateNewAnime("3", $anime_key); // First the Trakt.tv update
	$animes_obj->CreateNewAnime("2", $anime_key); // Second the TvDB
}

?>