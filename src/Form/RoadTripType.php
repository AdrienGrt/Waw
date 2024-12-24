<?php

namespace App\Form;

use App\Entity\RoadTrip;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // Importer ChoiceType
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
                'label' => 'Image supplémentaire',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('vehicle', ChoiceType::class, [
                'label' => 'Véhicule',
                'choices' => [
                    'Moto' => 'moto',
                    'Avion' => 'avion',
                    'Van' => 'van',
                    'Voiture' => 'voiture',
                    'Bus' => 'bus',
                ],
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
