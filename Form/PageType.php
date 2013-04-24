<?php

namespace Silverkix\CMSBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

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
            ->add('parent', 'entity', array(
                'class' => 'SilverkixCMSBundle:Page',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->where('p.parent is Null AND p.slug != ?1')
                        ->setParameter(1, '');
                },
                "empty_value" => "== Root Page ==",
                "empty_data" => null,
                "required" => false
                )
            )
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
