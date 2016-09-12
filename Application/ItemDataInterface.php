<?php

namespace Draw\PaymentBundle\Application;


use Draw\PaymentBundle\Entity\Item;

interface ItemDataInterface
{
    public function getItemDataReferenceId();

    public function setOrderItem(Item $item);
}