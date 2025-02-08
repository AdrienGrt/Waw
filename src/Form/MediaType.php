<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Media;

class MediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Ajouter une photo',
                'required' => false,
                'mapped' => false,  // ✅ Empêche Doctrine de gérer ce champ directement
                'multiple' => true,  // ✅ Permet l'upload de plusieurs fichiers
                'attr' => ['class' => 'form-control', 'accept' => 'image/*'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
