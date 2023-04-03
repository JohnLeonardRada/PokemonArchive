<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Element;
use App\Form\ElementType;
use App\Service\ElementService;
use App\Repository\ElementRepository;

/**
 * @Route("/element")
 */
class ElementController extends AbstractController
{

    private $elementService;

    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * @Route("/home", name="element_home")
     */
    public function index(ElementRepository $elementRepository): Response
    {
        $elements = $elementRepository->findAll();

        return $this->render('element/index.html.twig', [
            'elements' => $elements,
        ]);
    }

    /**
     * @Route("/new", name="element_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $element = new Element();
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->elementService->add($element);

            $this->addFlash(
                'new',
                'Added Successfully!'
            );

            return $this->redirectToRoute('element_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('element/new.html.twig', [
            'element' => $element,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/info/{id}", name="element_info", methods={"GET"})
     */
    public function info(Element $element): Response
    {
        return $this->render('element/info.html.twig', [
            'element' => $element,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="element_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Element $element, ElementRepository $elementRepository): Response 
    {
        $form = $this->createForm(ElementType::class, $element);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->elementService->add($element);

            $this->addFlash(
                'edit',
                'Updated Successfully!'
            );

            return $this->redirectToRoute('element_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('element/edit.html.twig', [
            'element' => $element,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="element_delete", methods={"POST"})
     */
    public function delete(Request $request, Element $element, ElementRepository $elementRepository): Response 
    {
        if ($this->isCsrfTokenValid('delete'.$element->getId(), $request->request->get('_token'))) {
            $this->elementService->remove($element);

            $this->addFlash(
                'delete',
                'Deleted Successfully!'
            );
        }

        return $this->redirectToRoute('element_home', [], Response::HTTP_SEE_OTHER);
    }
}
