<?php

namespace App\Form;

use App\Entity\Employe;
use App\Enum\EmployeStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('nom', TextType::class)

        ->add('prenom', TextType::class)

        ->add('email', EmailType::class)

        ->add('date_entree', DateType::class, [
            'input' => 'datetime_immutable',
            'widget' => 'single_text',
        ])

        ->add('status', EnumType::class, [
            'class' => EmployeStatus::class, 
        ])

        ->add('plainPassword', RepeatedType::class, [
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passes doivent être identiques.',
            'required' => true,
            'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe devrait avoir {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            'first_options'  => ['label' => 'Mot de passe'],
            'second_options' => ['label' => 'Répéter le mot de passe'],
        ])
        ->add('roles', ChoiceType::class, [
            'choices' => [
                'Chef de Projet'=> 'ROLE_CHEF_DE_PROJET',
                'Collaborateur'=>'ROLE_COLLABORATEUR'
            ],
            'expanded' =>false,
            'multiple' =>true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
