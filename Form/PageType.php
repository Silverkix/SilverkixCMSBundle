<?php

namespace Silverkix\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('keywords')
            ->add('description')
            ->add('content')
            ->add('online')
            ->add('parent')
            ->add('home')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Silverkix\CMSBundle\Entity\Page'
        ));
    }

    public function getName()
    {
        return 'silverkix_cmsbundle_pagetype';
    }
}
