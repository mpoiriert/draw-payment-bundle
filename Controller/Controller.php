<?php

namespace Draw\PaymentBundle\Controller;

use Draw\PaymentBundle\Entity\Item as Entity;
use Draw\DrawBundle\Controller\DoctrineControllerTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Draw\Swagger\Schema as Swagger;
use FOS\RestBundle\Controller\Annotations as Rest;

class Controller extends Controller
{
    use DoctrineControllerTrait;

    /**
     * Create a Item
     *
     * @Swagger\Tag(name="")
     *
     * @Rest\Post("/items", name="item_create")
     *
     * @Rest\View(statusCode=201, serializerGroups={"item:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "validator"={"groups"={"item:create"}},
     *         "deserializationContext"={"groups"={"item:create"}}
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
     * @Swagger\Tag(name="")
     *
     * @Rest\Get("/items/{id}", name="item_get")
     *
     * @Rest\View(serializerGroups={"item:read"})
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
     * @Swagger\Tag(name="")
     *
     * @Rest\Get("/items/{id}", name="item_update")
     *
     * @Rest\View(serializerGroups={"item:read"})
     *
     * @ParamConverter(
     *     name="entity",
     *     converter="fos_rest.request_body",
     *     options={
     *         "propertiesMap"={"id":"id"},
     *         "validator"={"groups"={"item:update"}},
     *         "deserializationContext"={"groups"={"item:update"}}
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
     * @Swagger\Tag(name="")
     *
     * @Rest\Get("/items", name="item_list")
     *
     * @Rest\View(serializerGroups={"item:read"})
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
     * @Swagger\Tag(name="")
     *
     * @Rest\Get("/items/{id}", name="item_delete")
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
