<?php

namespace App\Form;

use App\Entity\RoadTrip;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoadTripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du Road Trip',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('depart_date', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('arriver_date', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('depart_address', TextType::class, [
                'label' => 'Adresse de départ',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('arrive_address', TextType::class, [
                'label' => 'Adresse d\'arrivée',
                'attr' => ['class' => 'form-control'],
            ])
            
            ->add('description_supplementaire', TextareaType::class, [
                'label' => 'Description supplémentaire',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('medias', CollectionType::class, [
                'entry_type' => MediaType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true, // Permet d'ajouter plusieurs fichiers
                'allow_delete' => true, // Permet de supprimer des fichiers
                'by_reference' => false, // IMPORTANT : pour que Doctrine puisse gérer la relation
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'type',
                'placeholder' => 'Choisissez un véhicule',
                'label' => 'Véhicule',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoadTrip::class,
        ]);
    }
}
