<?php

namespace App\Form;

use App\Entity\RoadTrip;
use App\Entity\Vehicle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('image', FileType::class, [
                'label' => 'Image principale',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description_supplementaire', TextareaType::class, [
                'label' => 'Description supplémentaire',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('image_supplementaire', FileType::class, [
                'label' => 'Images supplémentaires',
                'required' => false,
                'mapped' => false,
                'multiple' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('vehicle', EntityType::class, [ // Utilisation d'EntityType pour lier avec l'entité Vehicle
                'class' => Vehicle::class,
                'choice_label' => 'type', // Utilise la colonne "type" comme label pour le menu déroulant
                'placeholder' => 'Choisissez un véhicule',
                'label' => 'Véhicule',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoadTrip::class, // L'entité associée à ce formulaire
        ]);
    }
}
