<?php

namespace Draw\PaymentBundle\Controller\Api;

use Draw\PaymentBundle\Entity\Payment as Entity;
use Draw\DrawBundle\Controller\DoctrineControllerTrait;
use Draw\PaymentBundle\Entity\Transaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Draw\Swagger\Schema as Swagger;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\EventDispatcher\GenericEvent;

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
        $entity->setState(Entity::STATE_PENDING);
        $entity->getOrder()->computeTotals();
        $entity->setAmount($entity->getOrder()->getTotal());
        $this->persistAndFlush($entity);

        $order = $entity->getOrder();

        \Stripe\Stripe::setApiKey($this->container->getParameter('draw_payment.stripe_api_key'));

        $transaction = new Transaction();
        $transaction->setState(Transaction::STATE_PENDING);
        $transaction->setType('pay');
        $transaction->setRequestData(
            [
                "amount" => $order->getTotal(),
                "currency" => strtolower($order->getCurrencyCode()),
                "source" => $entity->getData()['token'],
                "description" => "Example charge"
            ]
        );

        $entity->addTransaction($transaction);
        $this->persistAndFlush($transaction);

        $result = \Stripe\Charge::create($transaction->getRequestData());

        if ($result['status'] == 'succeeded') {
            $transaction->setResponseData(\Stripe\Util\Util::convertStripeObjectToArray($result));
            $transaction->setState(Transaction::STATE_SUCCESS);
            $entity->setState(Entity::STATE_SUCCESS);
            $this->flushAll($transaction);
            $this->get('event_dispatcher')->dispatch(
                'draw.payment.success',
                new GenericEvent($entity)
            );
        }

        return $entity;
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
     * @Rest\Delete("/draw-payments/{id}", name="draw_payment_delete")
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
