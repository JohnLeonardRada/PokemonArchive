<?php

namespace App\Service;

use App\Entity\Trainer;
use App\Repository\TrainerRepository;

class TrainerService
{
    private $trainerRepository;

    public function __construct(TrainerRepository $trainerRepository)
    {
        $this->trainerRepository = $trainerRepository;
    }

    public function add(Trainer $trainer)
    {
        $this->trainerRepository->add($trainer);
    }

    public function edit(Trainer $trainer)
    {
        $this->trainerRepository->add($trainer);
    }

    public function remove(Trainer $trainer)
    {
        $this->trainerRepository->remove($trainer);
    }
}
