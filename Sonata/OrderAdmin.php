<?php

namespace Draw\PaymentBundle\Sonata;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class OrderAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('clientEmail', null, ['format' => '%s%%', 'show_filter' => true])
            ->add('clientId')
            ->add('clientName');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('_action', 'actions', ['actions' => ['delete' => [], 'edit' => []]]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('uniqueId')
            ->add('currencyCode')
            ->add('clientId')
            ->add('clientName')
            ->add('clientEmail')
            ->add('billingAddress.locality');
    }
}