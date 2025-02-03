<?php declare(strict_types=1);

namespace GuestPrivate;

use Common\TraitModule;
use GuestPrivate\Permissions\Acl as GuestPrivateAcl;
use GuestPrivate\Permissions\Assertion as GuestPrivateAssertion;
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
    use TraitModule;

    const NAMESPACE = __NAMESPACE__;

    public function install(ServiceLocatorInterface $services)
    {
        // Required during install because the role is set in config.
        require_once __DIR__ . '/src/Permissions/Acl.php';

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

        // The private site role can see private site, but not the private
        // pages, neither the private resources.

        // Other modules can add the same role for easier management.
        if (!$acl->hasRole(GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE)) {
            $acl->addRole(GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE);
        }

        $acl
            ->addRoleLabel(GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE, 'Guest private site'); // @translate
        $acl
            ->deny(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE],
                [
                    'Omeka\Controller\SiteAdmin\Index',
                    'Omeka\Controller\SiteAdmin\Page',
                ]
            )
            ->allow(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE],
                [
                    \Omeka\Entity\Site::class,
                ],
                [
                    'read',
                    'view-all',
                ]
            )
            /*
            ->allow(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE],
                [\Omeka\Entity\SitePage::class],
                [
                    'read',
                    'view-all',
                ],
                new OmekaAssertion\SitePageIsPublicAssertion
            )
            */
            // FIXME Allow access only to public pages of private site.
            ->allow(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE_SITE],
                [\Omeka\Entity\SitePage::class],
                [
                    'read',
                    'view-all',
                ]
                // new GuestPrivateAssertion\SitePageIsPublicAllSitesAssertion()
            )
        ;

        if (!$acl->hasRole(GuestPrivateAcl::ROLE_GUEST_PRIVATE)) {
            $acl->addRole(GuestPrivateAcl::ROLE_GUEST_PRIVATE);
        }
        $acl
            ->addRoleLabel(GuestPrivateAcl::ROLE_GUEST_PRIVATE, 'Guest private'); // @translate
        $acl
            ->deny(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE],
                [
                    'Omeka\Controller\SiteAdmin\Index',
                    'Omeka\Controller\SiteAdmin\Page',
                ]
            )
            ->allow(
                [GuestPrivateAcl::ROLE_GUEST_PRIVATE],
                [
                    \Omeka\Entity\Resource::class,
                    \Omeka\Entity\Site::class,
                    \Omeka\Entity\SitePage::class,
                    \Omeka\Entity\Value::class,
                    \Omeka\Entity\ValueAnnotation::class,
                ],
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

        $sharedEventManager->attach(
            \Omeka\Form\SettingForm::class,
            'form.add_elements',
            [$this, 'handleMainSettings']
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
