<?php

namespace LoopAnime\AdminBundle\Controller;


use LoopAnime\AdminBundle\Form\Type\AddNewAnimeType;
use LoopAnime\AdminBundle\Form\Type\CrawlEpisodesType;
use LoopAnime\CrawlersBundle\Services\CrawlerService;
use LoopAnime\CrawlersBundle\Services\hosters\Anitube;
use LoopAnime\ShowsBundle\Entity\Animes;
use LoopAnime\ShowsBundle\Entity\AnimesEpisodesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller
{

    public function addAnimeAction()
    {
        $form = $this->createForm(new AddNewAnimeType($this->getDoctrine()->getManager()))->createView();
        return $this->render('LoopAnimeAdminBundle:admin:addAnime.html.twig',['form' => $form]);
    }

    public function populateLinksAction(Request $request)
    {
        $form = $this->createForm(new CrawlEpisodesType($this->getDoctrine()->getManager()));
        $form->bind($request);
        if($form->isValid()) {
            $data = $form->getData();
            $hoster = strtolower($data['hoster']);
            $anime = $data['anime'];
            $all = $data['all'];
            /** @var Animes $animeObj */
            $animeObj = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\Animes')->find($anime);
            /** @var AnimesEpisodesRepository $aEpisodesRepo */
            $aEpisodesRepo = $this->getDoctrine()->getRepository('LoopAnime\ShowsBundle\AnimesEpisodes');
            $episodes = $aEpisodesRepo->getEpisodes2Update($anime, $hoster, $all);

            foreach($episodes as $episode) {
                switch ($hoster) {
                    case "anitube":
                        $crawler = new CrawlerService($animeObj, new Anitube());
                    break;
                    case "anime44":
                        $crawler = new CrawlerService($animeObj, new Anime44());
                        break;
                    default:
                        throw new \Exception("I dont have the hoster $hoster");
                        break;
                }
            }

//            foreach($episodes as $episode) {
//
//                echo ("Looking for the episode " . $row2["title"] . " " . $row2["absolute_number"]);
//
//                $crawler->setIdEpisode($row2["id_episode"])->crawl_search()->crawl_episode();
//                $mirrors = $crawler->getMirrors();
//
//                logCLass($crawler->getMatchText());
//
//                $percentage 		= $crawler->getPercentage();
//
//                if(($percentage == "100" or $force == "true") and count($mirrors) > 0) {
//                    foreach ($mirrors as $link) {
//                        $link_struct = array();
//                        $link_struct["id_episode"] 	= $row2["id_episode"];
//                        $link_struct["hoster"] 		= $q;
//                        $link_struct["link"] 		= $link;
//                        $link_struct["status"] 		= "1";
//                        $link_struct["id_user"] 	= "0";
//                        $link_struct["subtitles"] 	= "1";
//                        $link_struct["lang"] 		= "JAP";
//                        $link_struct["sub_lang"] 	= $crawler->getSubtitlesLang();
//                        $link_struct["file_type"] 	= "";
//                        $animes_obj->insLink($link_struct);
//                        logCLass("  <b><font style='color:green'>Inserted!! Link: ".$link."</font></b>  ");
//                    }
//                } else
//                    logCLass(" <b><font style='color:orange'>Not sure if should insert this one?</font> Episode Link:".$crawler->getEpisodeLink()." / Mirrors " .count($mirrors) . ". <a href='force_populate.php?hoster=$q&id_episode=".$row2["id_episode"]."' target='_blank'>Force add!</a></b> ");
//
//            }
//            echo "<br><a href='populate_links.php'><< Go back to panel</a>";
//            exit;

        }
        $form = $form->createView();
        return $this->render('LoopAnimeAdminBundle:admin:crawl4Episodes.html.twig',['form' => $form]);
    }



}