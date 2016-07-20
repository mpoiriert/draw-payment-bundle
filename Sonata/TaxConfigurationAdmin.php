<?php

namespace Draw\PaymentBundle\Sonata;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class TaxConfigurationAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('configurationName')
            ->add('taxName')
            ->add('taxNumber');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('configurationName')
            ->add('taxName')
            ->add('taxNumber')
            ->add('_action', 'actions', ['actions' => ['delete' => [], 'edit' => []]]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Tax')
                ->add('configurationName')
                ->add('taxName')
                ->add('taxNumber')
            ->end()
            ->with('Configuration')
                ->add('taxConfigurationInTimes',
                    'sonata_type_collection',
                    [
                        'by_reference' => false
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position'
                    ]
                )
            ->end();
    }
}