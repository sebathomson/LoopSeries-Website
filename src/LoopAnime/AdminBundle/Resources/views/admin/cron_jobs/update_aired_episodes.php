<?php

set_time_limit(0);

include(dirname(__FILE__) . '/../../connections/conn_anime.php');
include(dirname(__FILE__) . '/../../includes/animes_class.php');

$db 			= new Anime_db();
$animes_obj 	= new Animes(false, false);

$query = "SELECT hoster FROM animes_crawlers GROUP BY hoster";
$rs_crawlers = $db->Query($query);

while($row = $rs_crawlers->fetch_assoc()) {
	
	$query = "SELECT animes_episodes.id_episode FROM animes_episodes LEFT JOIN animes_links ON animes_links.id_episode = animes_episodes.id_episode AND animes_links.hoster = '".$row["hoster"]."' WHERE air_date = DATE(NOW()) AND animes_links.id_link IS NULL";
	$rs_episodes = $db->Query($query);
	
	if($rs_episodes->num_rows == 0) continue;
	
	if(isset($crawler))
		unset($crawler);
	
	switch($row["hoster"]) {
		case "anitube":
			include(dirname(__FILE__) . '/../../includes/crawlers/anitube.php');
			$crawler = new crawler_anitube();
			break;
		case "anime44":
			include(dirname(__FILE__) . '/../../includes/crawlers/anime44.php');
			$crawler = new crawler_anime44();
			break;
		default:
			die();
			break;
	}
	
	while($row2 = $rs_episodes->fetch_assoc()) {

		$crawler->setIdEpisode($row2["id_episode"])->crawl_search()->crawl_episode();
		$mirrors = $crawler->getMirrors();
							
		$percentage 		= $crawler->getPercentage();

		if($percentage == "100" and count($mirrors) > 0)
			foreach ($mirrors as $link) {
				$link_struct = array();
				$link_struct["id_episode"] 	= $row2["id_episode"];
				$link_struct["hoster"] 		= $row["hoster"];
				$link_struct["link"] 		= $link;
				$link_struct["status"] 		= "1";
				$link_struct["id_user"] 	= "0";
				$link_struct["subtitles"] 	= "1";
				$link_struct["lang"] 		= "JAP";
				$link_struct["sub_lang"] 	= $crawler->getSubtitlesLang();
				$link_struct["file_type"] 	= "";
				$animes_obj->insLink($link_struct);
			}
		
	}
	
}

?>