<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType as TypeIntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 200
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Entrer votre nom',
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 200
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Entrer votre e-mail',
                    'class' => 'form-control'
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone *',
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 100
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Entrer votre numéro téléphone',
                    'class' => 'form-control'
                ]
            ])
            ->add('nbPersons', TypeIntegerType::class, [
                'label' => 'Nombre de personnes *',
                'constraints' => [
                    new Length([
                        'min' => 0,
                        'max' => 100
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Entrer le nombre de personne',
                    'class' => 'form-control'
                ]
            ])
            ->add('message', TextareaType::class, ['label' => 'Message', 'required' => false])
            ->add('offer', EntityType::class, [
                'label' => "Offre associé",
                'required' => true,
                'class' => Offer::class,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
