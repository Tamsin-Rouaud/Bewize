<?php 

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Tache;
use App\Enum\TacheStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $isCollaborateur = $options['is_collaborateur']; // récupéré depuis le contrôleur

    $builder
        ->add('titre', TextType::class, [
            'disabled' => $isCollaborateur,
        ])
        ->add('description', TextType::class, [
            'required' => false,
            'disabled' => $isCollaborateur,
        ])
        ->add('deadline', DateType::class, [
            'input' => 'datetime_immutable',
            'widget' => 'single_text',
            'required' => false,
            'disabled' => $isCollaborateur,
        ])
        ->add('employe', EntityType::class, [
            'class' => Employe::class,
            'choice_label' => fn($employe) => $employe->getPrenom() . ' ' . $employe->getNom(),
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'disabled' => $isCollaborateur,
        ])
        ->add('status', EnumType::class, [
            'class' => TacheStatus::class,
            'required' => true,
            'label' => 'Statut',
            'placeholder' => 'Choisir un statut',
        ]);
}


public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'data_class' => Tache::class,
        'is_collaborateur' => false, // valeur par défaut
    ]);
}

}
