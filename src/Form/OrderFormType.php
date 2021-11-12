<?php

namespace App\Form;
use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\Item;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_id', IntegerType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('order_quantity', IntegerType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('order_total', IntegerType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('order_discount', IntegerType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('shipping_address', TextType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('city', TextType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('province', TextType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('postal_code', TextType::class,[
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('customer', EntityType::class,[
                'class' => Customer::class,
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('item', EntityType::class,[
                'class' => Item::class,
                'multiple' => true,
                'constraints' => [
                    new NotNull(),
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class
        ]);
    }

}