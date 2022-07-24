<?php

namespace App\Form;

use App\Entity\Course;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('characterCode', TextType::class, [
                'label' => 'Код курса',
                'constraints' => [
                    new NotBlank(message: 'Поле не может быть пустым'),
                    new Length(
                        max: 255,
                        maxMessage: 'Максимальное количество допустимых символов 255',
                    ),
                ]
            ])
            ->add('name', TextType::class, [
                'label' => 'Название курса',
                'constraints' => [
                    new NotBlank(message: 'Поле не может быть пустым'),
                    new Length(
                        max: 255,
                        maxMessage: 'Максимальное количество допустимых символов 255',
                    ),
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Описание курса',
                'required' => 'false',
                'constraints' => [
                    new NotBlank(message: 'Поле не может быть пустым'),
                    new Length(
                        max: 1000,
                        maxMessage: 'Максимальное количество допустимых символов 1000',
                    ),
                ]
            ])
            ->add(
                'type',
                ChoiceType::class,
                [
                    'mapped' => false,
                    'choices' => [
                        'Бесплатный' => "0",
                        'Аренда' => "2",
                        'Покупка' => "1"
                    ],
                    'label' => 'Тип курса',
                    'constraints' => [
                        new NotBlank(message: 'Поле не может быть пустым.'),
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ],
                ])
            ->add(
                'price',
                NumberType::class,
                [
                    'label' => 'Стоимость курса',
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'mapped' => false,
                    'empty_data' => '',
                    'required' => false,
                    'constraints' => [
                        new NotBlank(message: 'Поле не может быть пустым.'),
                    ],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Course::class,
        ]);
    }
}
