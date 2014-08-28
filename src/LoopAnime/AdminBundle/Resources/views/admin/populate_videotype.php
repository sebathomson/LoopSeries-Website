<?php

include_once(dirname(__FILE__) ."/../includes/file_class.php");
include_once(dirname(__FILE__) ."/../connections/conn_anime.php");
$db = new Anime_db();

$query = "SELECT id_link, hoster, link FROM animes_links WHERE file_type = '' OR file_type IS NULL";
$rs_temp = $db->Query($query);

while($row = $rs_temp->fetch_assoc()) {
	
	echo "Updating episode {$row['id_link']}<br>";
	
	$hoster 	= explode("-",$row["hoster"])[0];
	$video_link = $row["link"]; 
	
	$VFile = new VFile($video_link, $hoster);
	if($VFile->doParse()) {
		
		$query = "UPDATE animes_links SET file_type = '".$VFile->getVideoType()."', quality_type = '".$VFile->getVideoQuality()."', file_size = '".$VFile->getVideoSize()."' WHERE animes_links.id_link = '".$row["id_link"]."'";
		$db->Query($query);
	}
	
	flush();
    ob_flush();
    sleep(1);
}


?>