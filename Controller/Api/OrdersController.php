<?php

namespace Draw\PaymentBundle\Controller\Api;

use Draw\PaymentBundle\Entity\Order as Entity;
use Draw\DrawBundle\Controller\DoctrineControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Draw\Swagger\Schema as Swagger;
use FOS\RestBundle\Controller\Annotations as Rest;

class OrdersController extends Controller
{
    use DoctrineControllerTrait;

    /**
     * Create a Order
     *
     * @Swagger\Tag(name="DrawOrders")
     *
     * @Rest\Post("/draw-orders", name="draw_order_create")
     *
     * @Rest\View(statusCode=201, serializerGroups={"draw-order:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"draw-order:create"}},
     *         "deserializationContext"={"groups"={"draw-order:create"}}
     *     }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     * @return \Draw\PaymentBundle\Entity\Order
     */
    public function createAction(Entity $entity)
    {
        $entity->setClientId($this->getUser()->getId());
        $entity->setCurrencyCode('CAD');
        return $this->persistAndFlush($entity);
    }

    /**
     * Get a Order
     *
     * @Swagger\Tag(name="DrawOrders")
     *
     * @Rest\Get("/draw-orders/{id}", name="draw_order_get")
     *
     * @Rest\View(serializerGroups={"draw-order:read"})
     *
     * @ParamConverter(name="entity")
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Order
     */
    public function getAction(Entity $entity)
    {
        return $entity;
    }

    /**
     * Update a Order
     *
     * @Swagger\Tag(name="DrawOrders")
     *
     * @Rest\Put("/draw-orders/{id}", name="draw_order_update")
     *
     * @Rest\View(serializerGroups={"draw-order:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "propertiesMap"={"id":"id"},
     *         "validator"={"groups"={"draw-order:update"}},
     *         "deserializationContext"={"groups"={"draw-order:update"}}
     *     }
     * )
     *
     * @Security("is_granted('OWN', entity)")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Order
     */
    public function updateAction(Entity $entity)
    {
        return $this->flush($entity);
    }

    /**
     * List Orders
     *
     * @Swagger\Tag(name="DrawOrders")
     *
     * @Rest\Get("/draw-orders", name="draw_order_list")
     *
     * @Rest\View(serializerGroups={"draw-order:read"})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Draw\PaymentBundle\Entity\Order[]
     */
    public function listAction()
    {
        return $this->createOrmQueryBuilder("DrawPaymentBundle:Order", "entity")
            ->andWhere('entity.clientId = :clientId')->setParameter('clientId', $this->getUser()->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Delete a Order
     *
     * @Swagger\Tag(name="DrawOrders")
     *
     * @Rest\Get("/draw-orders/{id}", name="draw_order_delete")
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
