<?php

namespace App\Form;

use App\Entity\Restaurant;
use App\Entity\Table;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('restaurant', EntityType::class, [
                'class' => 'App\Entity\Restaurant',
                'choice_label' => function (Restaurant $restaurant) {
                    return $restaurant->getId();
                },
                'choice_value' => function($choiceValue) {
                    if ($choiceValue === 4) {
                        return $choiceValue;
                    }
                }
            ])
            ->add('capacity')
            ->add('number')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Table::class,
            'restaurant_id' => null,
        ]);
    }
}
