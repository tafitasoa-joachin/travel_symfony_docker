<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TypeTextType::class, [
                'label' => "Votre nom", // Étiquette du champ
                'attr' => [
                    'placeholder' => 'Entrez nom' // Placeholder du champ
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => "Votre adresse e-mail", // Étiquette du champ
                'attr' => [
                    'placeholder' => 'Entrez votre adresse e-mail' // Placeholder du champ
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, // Type de champ utilisé pour les deux entrées
                'constraints' => [
                    new Length([ // Contraintes de longueur pour le mot de passe
                        'min' => 4, // Longueur minimale
                        'max' => 200 // Longueur maximale
                    ])
                ],
                'first_options' => [ // Options pour le premier champ (nouveau mot de passe)
                    'label' => 'Entrer votre mot de pass', // Étiquette du champ
                    'hash_property_path' => 'password', // Chemin pour le stockage du mot de passe
                    'attr' => [
                        'placeholder' => 'Entrer votre mot de pass' // Placeholder du champ
                    ]
                ],
                'second_options' => [ // Options pour le second champ (confirmation du mot de passe)
                    'label' => 'Confirmer votre mot de pass', // Étiquette du champ
                    'attr' => [
                        'placeholder' => 'Confirmer votre mot de pass' // Placeholder du champ
                    ]
                ],
                'mapped' => false, // Ce champ n'est pas mappé à l'entité User
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider", // Étiquette du bouton
                'attr' => [
                    'class' => "btn btn-success w-100" // Classes CSS pour le style du bouton
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([ // Contrainte pour s'assurer que l'email est unique
                    'entityClass' => User::class, // Classe de l'entité à vérifier
                    'fields' => 'email' // Champ à vérifier pour l'unicité
                ])
            ],
            'data_class' => User::class, // Classe de données associée au formulaire
        ]);
    }
}
