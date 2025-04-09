<?php

namespace App\Form;

use App\Entity\Employe;
use App\Entity\Projet;
use App\Entity\Tache;
use App\Enum\TacheStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('status', EnumType::class,[
                'class' => TacheStatus::class,
                'required' => false,
            ])
            ->add('description', TextType::class, [
                'required' => false,
                
            ])
            ->add('deadline', DateType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('employe', EntityType::class,[
                'class' =>Employe::class,
                 'choice_label' => function ($employe) {
                    return $employe->getPrenom() . ' ' . $employe->getNom();
                },
                'multiple' => false,
                'expanded' => false, // 👉 true = cases à cocher ; false = liste déroulante multiselect
                
                'required' => false,
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tache::class,
        ]);
    }
}
