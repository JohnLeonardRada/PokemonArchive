<?php

namespace App\Service;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;

class PokemonService
{
    private $pokemonRepository;

    public function __construct(PokemonRepository $pokemonRepository)
    {
        $this->pokemonRepository = $pokemonRepository;
    }

    public function add(Pokemon $pokemon)
    {
        $this->pokemonRepository->add($pokemon);
    }

    public function edit(Pokemon $pokemon)
    {
        $this->pokemonRepository->add($pokemon);
    }

    public function remove(Pokemon $pokemon)
    {
        $this->pokemonRepository->remove($pokemon);
    }
}
