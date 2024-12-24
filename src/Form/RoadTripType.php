<?php

namespace App\Form;

use App\Entity\RoadTrip;
use App\Entity\User;
use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RoadTripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du Road Trip',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['class' => 'form-control']
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username', // Affiche le nom d'utilisateur dans la liste déroulante
                'label' => 'Utilisateur',
                'attr' => ['class' => 'form-control']
            ])
            ->add('depart_date', TextType::class, [
                'label' => 'Date de départ',
                'attr' => ['class' => 'form-control']
            ])
            ->add('arriver_date', TextType::class, [
                'label' => 'Date d\'arriver',
                'attr' => ['class' => 'form-control']
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du Road Trip',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('description_supplementaire', TextareaType::class, [
                'label' => 'Racontez-nous en plus sur votre Road Trip',
                'attr' => ['class' => 'form-control']
            ])
            ->add('image_supplementaire', FileType::class, [
                'label' => 'Image bonus du Road Trip',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'choice_label' => 'type', 
                'label' => 'Véhicule',
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Créer le Road Trip',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RoadTrip::class,
        ]);
    }
}
