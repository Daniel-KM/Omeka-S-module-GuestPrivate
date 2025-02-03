<?php declare(strict_types=1);

namespace GuestPrivate;

use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Omeka\Module\AbstractModule;

/**
 * Guest Private.
 *
 * @copyright Daniel Berthereau, 2023-2025
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.txt
 */
class Module extends AbstractModule
{
    const ROLE_GUEST_PRIVATE = 'guest_private';

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function install(ServiceLocatorInterface $services)
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $services->get('Omeka\Connection');
        $connection->executeStatement('DELETE FROM `module` WHERE `id` = "GuestPrivateRole";');
    }

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);

        /** @var \Omeka\Permissions\Acl $acl */
        $services = $this->getServiceLocator();
        $acl = $services->get('Omeka\Acl');

        if (version_compare(\Omeka\Module::VERSION, '3.2', '<')) {
            $aclResources = [
                \Omeka\Entity\Resource::class,
                \Omeka\Entity\Site::class,
                \Omeka\Entity\SitePage::class,
                \Omeka\Entity\Value::class,
            ];
        } else {
            $aclResources = [
                \Omeka\Entity\Resource::class,
                \Omeka\Entity\Site::class,
                \Omeka\Entity\SitePage::class,
                \Omeka\Entity\Value::class,
                \Omeka\Entity\ValueAnnotation::class,
            ];
        }

        // Other modules can add the same role for easier management.
        if (!$acl->hasRole(self::ROLE_GUEST_PRIVATE)) {
            $acl->addRole(self::ROLE_GUEST_PRIVATE);
        }
        $acl
            ->addRoleLabel(self::ROLE_GUEST_PRIVATE, 'Guest private'); // @translate
        $acl
            ->deny(
                [self::ROLE_GUEST_PRIVATE],
                [
                    'Omeka\Controller\SiteAdmin\Index',
                    'Omeka\Controller\SiteAdmin\Page',
                ]
            )
            ->allow(
                [self::ROLE_GUEST_PRIVATE],
                $aclResources,
                [
                    'read',
                    'view-all',
                ]
            )
        ;
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager): void
    {
        $sharedEventManager->attach(
            '*',
            'user.login',
            [$this, 'handleUserLogin']
        );
    }

    /**
     * @see https://github.com/omeka/omeka-s/pull/1961
     * @uses \Guest\Mvc\Controller\Plugin\UserRedirectUrl
     *
     * Copy :
     * @see \Guest\Module::handleUserLogin()
     * @see \GuestPrivate\Module::handleUserLogin()
     */
    public function handleUserLogin(Event $event): void
    {
        $userRedirectUrl = $this->getServiceLocator()->get('ControllerPluginManager')->get('userRedirectUrl');
        $userRedirectUrl();
    }
}
