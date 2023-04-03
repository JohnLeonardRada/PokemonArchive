<?php

namespace App\Service;

use App\Entity\Element;
use App\Repository\ElementRepository;

class ElementService
{
    private $elementRepository;

    public function __construct(ElementRepository $elementRepository)
    {
        $this->elementRepository = $elementRepository;
    }

    public function add(Element $element)
    {
        $this->elementRepository->add($element);
    }

    public function edit(Element $element)
    {
        $this->elementRepository->add($element);
    }

    public function remove(Element $element)
    {
        $this->elementRepository->remove($element);
    }
}
