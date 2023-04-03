<?php

namespace App\Form;

use App\Entity\Element;
use App\Repository\ElementRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class ElementType extends AbstractType
{

    private $logger;
    private $entityManager;
    private $elementRepository;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, ElementRepository $elementRepository)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->elementRepository = $elementRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name:',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:'
            ])
            ->add('strongAgainst', EntityType::class, [
                'class' => 'App\Entity\Element',
                'label' => 'Strong Against:',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('weakAgainst', EntityType::class, [
                'class' => 'App\Entity\Element',
                'label' => 'Weak Against:',
                'expanded' => true,
                'multiple' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            // ->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
            // ->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Element::class,
        ]);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $element = $event->getData();
        $form = $event->getForm();

        if ($element instanceof Element && $element->getId() !== null) {
            $form->add('name', TextType::class, [
                'label' => 'Name:',
                'required' => true,
                'attr' => ['readonly' => true],
            ]);
            $form->add('description', TextType::class, [
                'label' => 'Description:',
                'attr' => ['readonly' => true],
            ]);
        }
    }

    // public function onPostSetData(FormEvent $event): void
    // {
    //     $element = $event->getData();
    //     $form = $event->getForm();

    //     if ($element instanceof Element) {
    //         // Set the value of "Normal" as the default selected value for strongAgainst field
    //         $strongAgainst = $element->getStrongAgainst();
    //         if (!empty($strongAgainst)) {
    //             $normalElement = null;
    //             foreach ($strongAgainst as $element) {
    //                 if ($element->getName() === "Normal") {
    //                     $normalElement = $element;
    //                     break;
    //                 }
    //             }
    //             if (!is_null($normalElement)) {
    //                 $defaultStrongAgainst = [$normalElement->getId()];
    //                 $form->get('strongAgainst')->setData($defaultStrongAgainst);
    //             }
    //         }
    //     }
    // }

    public function onPreSubmit(FormEvent $event): void
    {
        $element = $event->getData();
        $form = $event->getForm();

        $name = $element['name'];
        $existingElement = $this->elementRepository->findOneBy([
            'name' => $name,
        ]);

        if($existingElement) {
            $form->addError(new FormError('This element already exists'));
            $event->stopPropagation();
            return;
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $event->stopPropagation();
            return;
        }
    }    

    public function onPostSubmit(FormEvent $event)
    {
        $element = $event->getData();
        $form = $event->getForm();
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            if (!$element->getCreatedAt()) {
                $element->setCreatedAt(new \DateTime());
            }
            $element->setUpdatedAt(new \DateTime());

            $this->entityManager->persist($element);
            $this->entityManager->flush();
        }
    }
}
