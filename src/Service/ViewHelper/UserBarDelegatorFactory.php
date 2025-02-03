<?php declare(strict_types=1);

namespace GuestPrivate\Service\ViewHelper;

use GuestPrivate\View\Helper\UserBarDelegator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;

class UserBarDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $services, $name, callable $callback, array $options = null)
    {
        return new UserBarDelegator($callback());
    }
}
