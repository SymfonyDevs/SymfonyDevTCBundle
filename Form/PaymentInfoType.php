<?php

namespace SymfonyDev\TCBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type as WidgetType;

class PaymentInfoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('required' => true, 'label' => 'Name'))
            ->add('postCode', WidgetType\IntegerType::class, array('required' => true, 'label' => 'Post Code'))
            ->add('type', WidgetType\ChoiceType::class, array('required' => true, 'label' => 'Type', 'choices' => \SymfonyDev\TCBundle\Entity\PaymentInfo::getTypeOptions()))
            ->add('creditCardNumberPlain', null, array('required' => true, 'label' => 'Credit Card Number'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SymfonyDev\TCBundle\Entity\PaymentInfo'
        ));
    }
}
