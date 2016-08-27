<?php

namespace Draw\PaymentBundle\Controller\Api;

use Draw\PaymentBundle\Entity\Payment as Entity;
use Draw\DrawBundle\Controller\DoctrineControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Draw\Swagger\Schema as Swagger;
use FOS\RestBundle\Controller\Annotations as Rest;

class PaymentsController extends Controller
{
    use DoctrineControllerTrait;

    /**
     * Create a Payment
     *
     * @Swagger\Tag(name="DrawPayments")
     *
     * @Rest\Post("/draw-payments", name="draw_payment_create")
     *
     * @Rest\View(statusCode=201, serializerGroups={"draw-payment:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"draw-payment:create"}},
     *         "deserializationContext"={"groups"={"draw-payment:create"}}
     *     }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     * @return \Draw\PaymentBundle\Entity\Payment
     */
    public function createAction(Entity $entity)
    {
        return $this->persistAndFlush($entity);
    }

    /**
     * Get a Payment
     *
     * @Swagger\Tag(name="DrawPayments")
     *
     * @Rest\Get("/draw-payments/{id}", name="draw_payment_get")
     *
     * @Rest\View(serializerGroups={"draw-payment:read"})
     *
     * @ParamConverter(name="entity")
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Payment
     */
    public function getAction(Entity $entity)
    {
        return $entity;
    }

    /**
     * Update a Payment
     *
     * @Swagger\Tag(name="DrawPayments")
     *
     * @Rest\Get("/draw-payments/{id}", name="draw_payment_update")
     *
     * @Rest\View(serializerGroups={"draw-payment:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "propertiesMap"={"id":"id"},
     *         "validator"={"groups"={"draw-payment:update"}},
     *         "deserializationContext"={"groups"={"draw-payment:update"}}
     *     }
     * )
     *
     * @Security("is_granted('OWN', entity)")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Payment
     */
    public function updateAction(Entity $entity)
    {
        return $this->flush($entity);
    }

    /**
     * List Payments
     *
     * @Swagger\Tag(name="DrawPayments")
     *
     * @Rest\Get("/draw-payments", name="draw_payment_list")
     *
     * @Rest\View(serializerGroups={"draw-payment:read"})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Draw\PaymentBundle\Entity\Payment[]
     */
    public function listAction()
    {
        return $this->createOrmQueryBuilder("DrawPaymentBundle:Payment", "entity")
            //->where()
            //->setParameter()
            ->getQuery()
            //->setMaxResults(50)
            ->getResult();
    }

    /**
     * Delete a Payment
     *
     * @Swagger\Tag(name="DrawPayments")
     *
     * @Rest\Get("/draw-payments/{id}", name="draw_payment_delete")
     *
     * @Rest\View(statusCode=204)
     *
     * @ParamConverter(name="entity")
     *
     * @Security("is_granted('OWN', entity)")
     *
     * @param Entity $entity
     */
    public function deleteAction(Entity $entity)
    {
        return $this->removeAndFlush($entity);
    }
}
