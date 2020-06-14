<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Season;
use App\Form\CategoryType;
use App\Form\ProgramSearchType;
//use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Program;
use Symfony\Component\HttpFoundation\Request;

Class WildController extends AbstractController
{
    /**
     * Show all rows from Program's entity
     *
     * @Route("/wild", name="wild_index")
     * @return Response A response instance
     */
    public function index() : Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if(!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $form = $this->createForm(ProgramSearchType::class,null,["method" => Request::METHOD_GET]);

        return $this->render('wild/index.html.twig', ['programs' => $programs, 'form' => $form->createView()]);
    }

    /**
     * Getting a program with a formatted slug for title
     *
     * @param string $slug The slugger
     * @Route("/wild/show/{slug<[a-z0-9-]+$>}", defaults={"slug" = null}, name="wild_show")
     * @return Response
     */
    public function show(?string $slug) : Response
    {
        if(!$slug) {
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = preg_replace('/-/',' ', ucwords(trim(strip_tags($slug)), "-"));
        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => mb_strtolower($slug)]);
        if(!$program) {
            throw $this->createNotFoundException('No program with '.$slug.' title, found in program\'s table');
        }

        return $this->render('wild/show.html.twig', ['program' => $program, 'slug' => $slug]);
    }

    /**
     * @Route("/wild/show", name="wild_noshow")
     */
    public function noshow() : Response
    {
        return $this->render('wild/show.html.twig', ['slug' => 'Aucune série sélectionnée, veuillez choisir une série']);
    }

    /**
     * Getting 3 programs with a tri by id
     *
     * @param string $categoryName
     * @Route("/wild/category/{categoryName}", name="wild_show_category")
     * @return Response
     */
    public function showByCategory(?string $categoryName) : Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $categoryName]);

        $repository = $this->getDoctrine()->getRepository(Program::class);

        $programs = $repository->findBy(['category' => $category], ['id' => 'asc'], 10, 0);

        return $this->render('wild/category.html.twig', ['programs' => $programs, 'category' => $category]);
    }

    /**
     * Getting 1 program with season details
     *
     * @param string $programName
     * @Route("/wild/program/{programName}", name="wild_show_program")
     * @return Response
     */
    public function showByProgram(?string $programName) : Response
    {
        if(!$programName) {
            throw $this->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $programName = preg_replace('/-/',' ', ucwords(trim(strip_tags($programName)), "-"));

        $program = $this->getDoctrine()->getRepository(Program::class)->findOneBy(['title' => $programName]);

        $repository = $this->getDoctrine()->getRepository(Season::class);

        $seasons = $repository->findBy(['program' => $program], ['number' => 'asc'], 10, 0);

        return $this->render('wild/program.html.twig', ['seasons' => $seasons, 'program' => $program]);
    }

    /**
 * Getting 1 season of a program
 *
 * @param integer $id
 * @Route("/wild/season/{id}", name="wild_show_season")
 * @return Response
 */
    public function showBySeason(?int $id) : Response
    {
        $season = $this->getDoctrine()->getRepository(Season::class)->findOneBy(['id' => $id]);

        $program = $season->getProgram();

        $episodes = $season->getEpisodes();

        return $this->render('wild/season.html.twig', ['season' => $season, 'program' => $program, 'episodes' => $episodes]);
    }

    /**
     * @Route("/wild/episode/{id}", name="wild_show_episode")
     */
    public function showEpisode(Episode $episode) : Response
    {
        $season = $episode->getSeason();

        $program = $season->getProgram();

        return $this->render('wild/episode.html.twig', ['episode' => $episode, 'season' => $season, 'program' => $program]);
    }
}