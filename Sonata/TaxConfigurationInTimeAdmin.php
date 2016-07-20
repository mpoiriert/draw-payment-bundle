<?php

namespace Draw\PaymentBundle\Sonata;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class TaxConfigurationInTimeAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('activeFrom', 'sonata_type_datetime_picker', ['required' => false])
            ->add('activeTo', 'sonata_type_datetime_picker', ['required' => false])
            ->add('rate');
    }
}