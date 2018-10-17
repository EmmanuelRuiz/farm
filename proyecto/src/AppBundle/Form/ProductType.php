<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('product_name', TextType::class, array(
                    'label' => 'Nombre de producto',
                    'required' => 'required',
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ))
                ->add('description', TextType::class, array(
                    'label' => 'Descripción',
                    'required' => 'required',
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ))
                ->add('image', FileType::class, array(
                    'label' => 'Imagen',
                    'required' => false,
                    'attr' => array(
                        'class' => 'form-control'
                    )
                ))
                ->add('Guardar', SubmitType::class, array(
                    "attr" => array(
                        "class" => "form-submit btn btn-azul  btn-block"
                    )
                ))
        ;
    }

/**
     * {@inheritdoc}
     */

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BackendBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'backendbundle_product';
    }

}
