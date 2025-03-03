<?php declare(strict_types=1);

namespace GuestPrivate\Mvc;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\EventManagerInterface;
use Laminas\I18n\Translator\TranslatorInterface as TranslatorInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Router\Http\RouteMatch;
use Omeka\Service\Delegator\SitePaginatorDelegatorFactory;

class MvcListeners extends AbstractListenerAggregate
{
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'setSiteForMainLogin']
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_ROUTE,
            [$this, 'disablePublicApi']
        );
    }

    public function setSiteForMainLogin(MvcEvent $event)
    {
        $services = $event->getApplication()->getServiceManager();
        $auth = $services->get('Omeka\AuthenticationService');

        if ($auth->hasIdentity()) {
            return;
        }

        $routeMatch = $event->getRouteMatch();
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        if (!in_array($matchedRouteName, ['top', 'login', 'maintenance', 'create-password', 'forgot-password'])) {
            return;
        }

        /**
         * @var \Omeka\Settings\Settings $settings
         * @var \Doctrine\ORM\EntityManager $entityManager
         */
        $settings = $services->get('Omeka\Settings');
        $entityManager = $services->get('Omeka\EntityManager');

        // Redirect early to login when there is no public site.
        if ($matchedRouteName === 'top'
            && $settings->get('guestprivate_redirect_top_to_login')
        ) {
            $siteEntityRepository = $entityManager->getRepository(\Omeka\Entity\Site::class);
            $count = $siteEntityRepository->count(['isPublic' => 1]);
            if (!$count) {
                $params =  [
                    'controller' => 'Omeka\Controller\Login',
                    'action' => 'login',
                ];
                $routeMatch = new RouteMatch($params);
                $routeMatch->setMatchedRouteName('login');
                $event->setRouteMatch($routeMatch);
                // Don't return here in order to theme login page if wanted.
            }
        }

        if (!$settings->get('guestprivate_theme_login')) {
            return;
        }

        $defaultSite = (int) $settings->get('default_site');
        if (!$defaultSite) {
            return;
        }

        /**
         * @var \Omeka\Entity\Site $siteEntity
         * @var \Omeka\Api\Representation\SiteRepresentation $site
         *
         * @see \Omeka\Mvc\MvcListeners::prepareSite()
         * @see \Omeka\Mvc\MvcListeners::preparePublicSite()
         */

        // The site may be private, so don't use api.
        $siteEntity = $entityManager->find(\Omeka\Entity\Site::class, $defaultSite);
        if (!$siteEntity) {
            $services->get('Omeka\Logger')->err('The setting "default site" refers to an inexisting site.'); // @translate
            return;
        }

        $siteTheme = $siteEntity->getTheme();

        // Set the current theme for this site.
        $themeManager = $services->get('Omeka\Site\ThemeManager');
        $currentTheme = $themeManager->getTheme($siteTheme);
        if (!$currentTheme) {
            return;
        }

        $themeManager->setCurrentTheme($currentTheme);

        $adapter = $services->get('Omeka\ApiAdapterManager')->get('sites');
        $site = $adapter->getRepresentation($siteEntity);

        // Inject the site into things that need it.
        $services->get('Omeka\Settings\Site')->setTargetId($site->id());
        $services->get('ControllerPluginManager')->get('currentSite')->setSite($site);
        $services->get('ViewHelperManager')->get('currentSite')->setSite($site);

        // Set the site to the top level view model
        $event->getViewModel()->site = $site;

        $hasTranslations = $currentTheme->getIni('has_translations');
        if ($hasTranslations) {
            $translator = $services->get(TranslatorInterface::class);
            $translator->getDelegatedTranslator()->addTranslationFilePattern(
                'gettext',
                $currentTheme->getPath('language'),
                '%s.mo'
            );
        }

        // Update more data.

        $services->addDelegator('Omeka\Paginator', SitePaginatorDelegatorFactory::class);

        // Add the theme view templates to the path stack.
        $services->get('ViewTemplatePathStack')->addPath($currentTheme->getPath('view'));

        // Load theme view helpers on-demand.
        $helpers = $currentTheme->getIni('helpers');
        if (is_array($helpers)) {
            foreach ($helpers as $helper) {
                $factory = function ($pluginManager) use ($site, $helper, $currentTheme) {
                    require_once $currentTheme->getPath('helper', "$helper.php");
                    $helperClass = sprintf('\OmekaTheme\Helper\%s', $helper);
                    return new $helperClass;
                };
                $services->get('ViewHelperManager')->setFactory($helper, $factory);
            }
        }

        // Set the runtime locale and translator language to the configured site
        // locale.
        $locale = $services->get('Omeka\Settings\Site')->get('locale');
        if ($locale) {
            if (extension_loaded('intl')) {
                \Locale::setDefault($locale);
            }
            $services->get('MvcTranslator')->getDelegatedTranslator()->setLocale($locale);
        }

        // Append the site slug to the routes to simplify creation of urls.
        // Append a flag too to indicate this is not a site to simplify process.
        $routeMatch
            ->setParam('site-slug', $siteEntity->getSlug())
            ->setParam('outside', true);
    }

    public function disablePublicApi(MvcEvent $event)
    {
        $services = $event->getApplication()->getServiceManager();
        $auth = $services->get('Omeka\AuthenticationService');

        if ($auth->hasIdentity()) {
            return;
        }

        $routeMatch = $event->getRouteMatch();
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        if ($matchedRouteName !== 'api/default') {
            return;
        }

        $settings = $services->get('Omeka\Settings');
        if (!$settings->get('guestprivate_disable_public_api')) {
            return;
        }

        $params =  [
            '__API__' => true,
            '__KEYAUTH__' => true,
            'controller' => 'Omeka\Controller\Api',
        ];
        $routeMatch = new RouteMatch($params);
        $routeMatch->setMatchedRouteName('api');
        $event->setRouteMatch($routeMatch);
    }
}
