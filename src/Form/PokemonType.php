<?php

namespace App\Form;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class PokemonType extends AbstractType
{
    private $logger;
    private $entityManager;
    private $pokemonRepository;

    public function __construct(LoggerInterface $logger, EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->pokemonRepository = $pokemonRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name:'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description:'
            ])
            ->add('region', ChoiceType::class, [
                'label' => 'Region:',
                'choices' => [
                    'Kanto' => 'Kanto',
                    'Johto' => 'Johto',
                    'Hoenn' => 'Hoenn',
                    'Sinnoh' => 'Sinnoh',
                    'Unova' => 'Unova',
                    'Kalos' => 'Kalos',
                    'Alola' => 'Alola',
                    'Galar' => 'Galar',
                ]
            ])
            ->add('elements', EntityType::class, [
                'class' => 'App\Entity\Element',
                'label' => 'Elements:',
                'expanded' => true,
                'multiple' => true,
                // MAKES THE LIST IN ASCENDING ORDER
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->orderBy('e.name', 'ASC');
                },
            ])
            // ->add('elements', CollectionType::class, [
            //     'entry_type' => ElementType::class,
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            // ]);
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'onPostSubmit'])
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pokemon::class,
        ]);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $pokemon = $event->getData();
        $form = $event->getForm();

        if($pokemon instanceof Pokemon && $pokemon->getId() !== null) {
            $form->add('name', TextType::class, [
                'label' => 'Name:',
                'attr' => ['readonly' => true],
            ]);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $pokemon = $event->getData();
        $form = $event->getForm();

        $name = $pokemon['name'];
        $existingPokemon = $this->pokemonRepository->findOneBy([
            'name' => $name,
        ]);

        if($existingPokemon) {
            $form->addError(new FormError('This pokemon already exists'));
            $event->stopPropagation();
            return;
        }

        if($form->isSubmitted() && !$form->isValid()) {
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
