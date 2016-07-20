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
            ->with('Order', ['class' => 'col-md-6'])
                ->add('uniqueId')
                ->add('currencyCode')
            ->end()
            ->with('Client',['class' => 'col-md-6'])
                ->add('clientId')
                ->add('clientName')
                ->add('clientEmail')
            ->end()
            ->with('BillingAddress')
                ->add('billingAddress.locality')
            ->end()
            ->with('Content')
                ->add('items',
                    'sonata_type_collection',
                    [
                        'by_reference' => false
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table'
                    ]
                )
            ->end();
    }
}