<?php

namespace Draw\PaymentBundle\Controller\Api;

use Draw\PaymentBundle\Entity\Item as Entity;
use Draw\DrawBundle\Controller\DoctrineControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Draw\Swagger\Schema as Swagger;
use FOS\RestBundle\Controller\Annotations as Rest;

class ItemsController extends Controller
{
    use DoctrineControllerTrait;

    /**
     * Create a Item
     *
     * @Swagger\Tag(name="DrawItems")
     *
     * @Rest\Post("/draw-items", name="draw_item_create")
     *
     * @Rest\View(statusCode=201, serializerGroups={"draw-item:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"draw-item:create"}},
     *         "deserializationContext"={"groups"={"draw-item:create"}}
     *     }
     * )
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     * @return \Draw\PaymentBundle\Entity\Item
     */
    public function createAction(Entity $entity)
    {
        return $this->persistAndFlush($entity);
    }

    /**
     * Get a Item
     *
     * @Swagger\Tag(name="DrawItems")
     *
     * @Rest\Get("/draw-items/{id}", name="draw_item_get")
     *
     * @Rest\View(serializerGroups={"draw-item:read"})
     *
     * @ParamConverter(name="entity")
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Item
     */
    public function getAction(Entity $entity)
    {
        return $entity;
    }

    /**
     * Update a Item
     *
     * @Swagger\Tag(name="DrawItems")
     *
     * @Rest\Get("/draw-items/{id}", name="draw_item_update")
     *
     * @Rest\View(serializerGroups={"draw-item:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "propertiesMap"={"id":"id"},
     *         "validator"={"groups"={"draw-item:update"}},
     *         "deserializationContext"={"groups"={"draw-item:update"}}
     *     }
     * )
     *
     * @Security("is_granted('OWN', entity)")
     *
     * @param Entity $entity
     *
     * @return \Draw\PaymentBundle\Entity\Item
     */
    public function updateAction(Entity $entity)
    {
        return $this->flush($entity);
    }

    /**
     * List Items
     *
     * @Swagger\Tag(name="DrawItems")
     *
     * @Rest\Get("/draw-items", name="draw_item_list")
     *
     * @Rest\View(serializerGroups={"draw-item:read"})
     *
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Draw\PaymentBundle\Entity\Item[]
     */
    public function listAction()
    {
        return $this->createOrmQueryBuilder("DrawPaymentBundle:Item", "entity")
            //->where()
            //->setParameter()
            ->getQuery()
            //->setMaxResults(50)
            ->getResult();
    }

    /**
     * Delete a Item
     *
     * @Swagger\Tag(name="DrawItems")
     *
     * @Rest\Delete("/draw-items/{id}", name="draw_item_delete")
     *
     * @Rest\View(statusCode=204)
     *
     * @ParamConverter(name="entity")
     *
     * @Security("is_granted('OWN', entity.getOrder())")
     *
     * @param Entity $entity
     */
    public function deleteAction(Entity $entity)
    {
        return $this->removeAndFlush($entity);
    }
}
