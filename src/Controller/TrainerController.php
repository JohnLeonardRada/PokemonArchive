<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Trainer;
use App\Form\TrainerType;
use App\Service\TrainerService;
use App\Repository\TrainerRepository;

/**
 * @Route("/trainer")
 */
class TrainerController extends AbstractController
{
    private $trainerService;

    public function __construct(TrainerService $trainerService)
    {
        $this->trainerService = $trainerService;
    }

    /**
     * @Route("/home", name="trainer_home")
     */
    public function index(TrainerRepository $trainerRepository): Response
    {
        $trainers = $trainerRepository->findAll();

        return $this->render('trainer/index.html.twig', [
            'trainers' => $trainers,
        ]);
    }

    /**
     * @Route("/new", name="trainer_new", methods={"GET", "POST"})
     */
    public function new(Request $request): Response
    {
        $trainer = new Trainer();
        $form = $this->createForm(TrainerType::class, $trainer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->trainerService->add($trainer);

            $this->addFlash(
                'new',
                'Added Successfully!'
            );

            return $this->redirectToRoute('trainer_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainer/new.html.twig', [
            'trainer' => $trainer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/info/{id}", name="trainer_info", methods={"GET"})
     */
    public function info(Trainer $trainer): Response
    {
        return $this->render('trainer/info.html.twig', [
            'trainer' => $trainer,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="trainer_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Trainer $trainer): Response
    {
        $form = $this->createForm(TrainerType::class, $trainer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->trainerService->add($trainer);

            $this->addFlash(
                'edit',
                'Updated Successfully!'
            );

            return $this->redirectToRoute('trainer_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trainer/edit.html.twig', [
            'trainer' => $trainer,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="trainer_delete", methods={"POST"})
     */
    public function delete(Request $request, Trainer $trainer, TrainerRepository $trainerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trainer->getId(), $request->request->get('_token'))) {
            $this->trainerService->remove($trainer);

            $this->addFlash(
                'delete',
                'Deleted Successfully!'
            );
        }

        return $this->redirectToRoute('trainer_home', [], Response::HTTP_SEE_OTHER);
    }
}
