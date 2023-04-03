<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Pokemon;
use App\Form\PokemonType;
use App\Service\PokemonService;
use App\Repository\PokemonRepository;

class PokemonController extends AbstractController
{

    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    /**
     * @Route("/index", name="base_index")
     */
    public function index(PokemonRepository $pokemonRepository): Response
    {
        return $this->render('pokemon/index.html.twig');
    }

    /**
     * @Route("/pokemon/home", name="pokemon_home")
     */
    public function home(PokemonRepository $pokemonRepository): Response
    {
        $pokemons = $pokemonRepository->findAll();

        return $this->render('pokemon/home.html.twig', [
            'pokemons' => $pokemons,
        ]);
    }

    /**
     * @Route("/pokemon/new", name="pokemon_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->pokemonService->add($pokemon);

            $this->addFlash(
                'new',
                'Added Successfully!'
            );

            return $this->redirectToRoute('pokemon_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pokemon/new.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pokemon/info/{id}", name="pokemon_info", methods={"GET"})
     */
    public function info(Pokemon $pokemon): Response
    {
        return $this->render('pokemon/info.html.twig', [
            'pokemon' => $pokemon,
        ]);
    }

    /**
     * @Route("/pokemon/edit/{id}", name="pokemon_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Pokemon $pokemon): Response 
    {
        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->pokemonService->add($pokemon);

            $this->addFlash(
                'edit',
                'Updated Successfully!'
            );

            return $this->redirectToRoute('pokemon_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pokemon/edit.html.twig', [
            'pokemon' => $pokemon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/pokemon/delete/{id}", name="pokemon_delete", methods={"POST"})
     */
    public function delete(Request $request, Pokemon $pokemon): Response 
    {
        if ($this->isCsrfTokenValid('delete'.$pokemon->getId(), $request->request->get('_token'))) {
            $this->pokemonService->remove($pokemon);

            $this->addFlash(
                'delete',
                'Deleted Successfully!'
            );
        }

        return $this->redirectToRoute('pokemon_home', [], Response::HTTP_SEE_OTHER);
    }
}
