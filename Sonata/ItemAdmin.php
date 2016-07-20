<?php

namespace Draw\PaymentBundle\Sonata;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class ItemAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('applicationProduct')
            ->add('sku', null, ['disabled' => true])
            ->add('quantity')
            ->add('unitPrice');
    }
}