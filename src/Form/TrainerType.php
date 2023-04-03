<?php

namespace App\Form;

use App\Entity\Trainer;
use App\Entity\Pokemon;
use App\Repository\TrainerRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class TrainerType extends AbstractType
{

    private $logger;
    private $entityManager;
    private $trainerRepository;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, TrainerRepository $trainerRepository)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->trainerRepository = $trainerRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name:'
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name:'
            ])
            ->add('age', NumberType::class, [
                'label' => 'Age:',
                'html5' => true,
                'attr' => [
                    'pattern' => '\d*',
                    'inputmode' => 'numeric'
                ]
            ])
            ->add('pokemon', EntityType::class, [
                'class' => 'App\Entity\Pokemon',
                'label' => 'Pokemon:',
                'expanded' => true,
                'multiple' => true,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::POST_SET_DATA, [$this, 'onPostSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
            ->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trainer::class,
        ]);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $trainer = $event->getData();
        $form = $event->getForm();

        if ($trainer instanceof Trainer && $trainer->getId() !== null) {
            $form->add('firstName', TextType::class, [
                'label' => 'First Name:',
                'attr' => ['readonly' => true],
            ]);
            $form->add('lastName', TextType::class, [
                'label' => 'Last Name:',
                'attr' => ['readonly' => true],
            ]);
            $form->add('age', NumberType::class, [
                'label' => 'Age:',
                'html5' => true,
                'attr' => [
                    'pattern' => '\d*',
                    'inputmode' => 'numeric',
                    'readonly' => true
                ]
            ]);
        }
    }

    public function onPostSetData(FormEvent $event)
    {
        $trainer = $event->getData();
        $form = $event->getForm();

        // Only pre-select default Pokemon for new trainers
        if (!$trainer || !$trainer->getId()) {
            $defaultPokemon = $this->entityManager->getRepository(Pokemon::class)->findBy([
                'name' => ['Charmander', 'Squirtle', 'Bulbasaur']
            ]);

            $form->get('pokemon')->setData($defaultPokemon);
        }
        
    }

    public function onPreSubmit(Formevent $event): void
    {
        $trainer = $event->getData();
        $form = $event->getForm();

        $firstName = $trainer['firstName'];
        $lastName = $trainer['lastName'];

        $existingTrainer = $this->trainerRepository->findOneBy([
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        if($existingTrainer)
        {
            $form->addError(new FormError('This trainer already exists'));
            $event->stopPropagation();
            return;
        }

        if($form->isSubmitted() && !$form->isValid())
        {
            $event->stopPropagation();
            return;
        }
    }

    public function onSubmit(Formevent $event): void
    {
        $trainer = $event->getData();
        $form = $event->getForm();
        $age = $trainer->getAge();

        if($age < 5 || $age > 100)
        {
            $form->addError(new FormError('Age must be between 8 and 100'));
        }
    }

    public function onPostSubmit(FormEvent $event)
    {
        $trainer = $event->getData();
        $form = $event->getForm();

        if($form->isSubmitted() && $form->isValid()) {

            if (!$trainer->getCreatedAt()) {
                $trainer->setCreatedAt(new \DateTime());
            }
            $trainer->setUpdatedAt(new \DateTime());
    
            $this->entityManager->persist($trainer);
            $this->entityManager->flush();
        }
    }
}
