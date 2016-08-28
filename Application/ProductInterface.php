<?php

namespace Draw\PaymentBundle\Application;

interface ProductInterface
{
    public function getApplicationSku();

    public function getUnitPrice();
}