<?php
set_time_limit(0);

function logCLass($message, $level = 0, $color = "black") {

	$spaces = "";
	for($i = 0; $i <= $level; $i++)
		$spaces .= "&nbsp;&nbsp;";

		$message = "<font style='color:$color'>"  .$spaces . $message . "</font>";
		echo $message . "<br>";  ob_flush(); flush(); usleep(30000);
}

if (ob_get_level() == 0) ob_start();
	logCLass("Log actions activated..<br>");

include("../includes/website_config.php");

$website_config = new website_config();

include($website_config->getIncludesPath() . "animes_class.php");

$animes_obj = new Animes(false);

include_once($website_config->getConnectionsPath() ."conn_anime.php");
$db = new Anime_db();

$force = "false";
foreach($_GET as $key=>$value)
	$$key = $value;

if(isset($run) and $run == "true") {
	
	$where_clause = "";
	if(isset($id_episode) and $id_episode != "")
		$where_clause .= " AND animes_episodes.id_episode = '$id_episode'";
	else
		die("Need a valid episode");
	
	if(!isset($not_null) or (isset($not_null) and $not_null == "true"))
		$where_clause .= " AND animes_links.id_link IS NULL";
	
	$query = "
			SELECT
				animes.title,
				animes_episodes.absolute_number,
				animes_episodes.id_episode
			FROM animes_episodes
				JOIN animes_seasons
					USING (id_season)
				JOIN animes
					USING (id_anime)
				LEFT JOIN animes_links
					 ON animes_links.hoster = '$hoster' AND animes_links.id_episode = animes_episodes.id_episode
				LEFT JOIN animes_crawlers
					ON animes_crawlers.hoster = '$hoster' AND animes_crawlers.id_anime = animes_seasons.id_anime
			WHERE TRUE $where_clause GROUP BY animes_episodes.id_episode";
	$rs_temp = $db->Query( $query );
	
	switch($hoster) {
		case "anitube":
			include($website_config->getCrawlersPath() . "anitube.php");
			$crawler = new crawler_anitube();
			break;
		case "anime44":
			include($website_config->getCrawlersPath() . "anime44.php");
			$crawler = new crawler_anime44();
			break;
		default:
			die("Not recognized $hoster");
			break;
	}
	
	while($row2 = $rs_temp->fetch_assoc()) {
		
		logCLass("Force Insert episode " . $row2["title"] . " " . $row2["absolute_number"]);
		
		$crawler->setEpisodeLink($episode_link)->crawl_episode();
		
		$mirrors = $crawler->getMirrors();
		
		if(count($mirrors) > 0) {
			foreach ($mirrors as $link) {
				$link_struct = array();
				$link_struct["id_episode"] 	= $row2["id_episode"];
				$link_struct["hoster"] 		= $hoster;
				$link_struct["link"] 		= $link;
				$link_struct["status"] 		= "1";
				$link_struct["id_user"] 	= "0";
				$link_struct["subtitles"] 	= "1";
				$link_struct["lang"] 		= "JAP";
				$link_struct["sub_lang"] 	= $crawler->getSubtitlesLang();
				$link_struct["file_type"] 	= "";
				$animes_obj->insLink($link_struct);
				logCLass("  <b><font style='color:green'>Inserted!! Link: ".$link."</font></b>  ");
			}
		} else 
			logCLass(" <b><font style='color:orange'>Not fount any Mirrors. Mirrors: " .count($mirrors));
				
	}
	echo "<br><a href='populate_links.php'><< Go back to panel</a>";
	exit;
}

?>
<html>
<head>

</head>
<body>
	<form method="get" action="force_populate.php">
		<input type="hidden" name="run" value="true">
		ID Episode: <input type="text" name="id_episode" value="<?php echo $id_episode ?>">
		Look on Hoster: <select name="hoster"><option value="anitube" <?php echo ($hoster == "anitube" ? "selected" : "") ?>>Anitube</option><option value="anime44" <?php echo ($hoster == "anime44" ? "selected" : "") ?>>Anime44</option></select>	<br>
		Link for the episode: <input type="text" name="episode_link" value=""><br>
		Where there isnt any link?: <select name="not_null"><option value="true">True</option><option value="false">False</option></select> <br>
		<input type="submit" value="Go For it!">
	</form>
</body>
</html>