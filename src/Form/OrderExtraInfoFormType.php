<?php

namespace App\Form;
use App\Entity\Order;
use App\Entity\Customer;
use App\Entity\OrderExtraInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class OrderExtraInfoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipping_company', TextType::class, ['label' => 'Shipping Company','required' => true])
            ->add('tracking_number', TextType::class, ['label' => 'Shipping Tracking Number', 'required' => true])
            ->add('imageFile', VichImageType::class, ['label' => 'Shipping Label', 'required' => true , 'allow_delete' => true,'download_link' => true ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrderExtraInfo::class
        ]);
    }

}