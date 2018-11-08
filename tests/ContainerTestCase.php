<?php

namespace Tests;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ContainerTestCase extends KernelTestCase
{
    protected static function getContainer() : ContainerInterface
    {
        return static::bootKernel()->getContainer();
    }
}
