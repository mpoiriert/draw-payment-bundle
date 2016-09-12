<?php

namespace Draw\PaymentBundle\Application;

interface UserInterface
{
    public function getApplicationClientReferenceId();
    public function getApplicationClientEmail();
    public function getApplicationClientName();
}