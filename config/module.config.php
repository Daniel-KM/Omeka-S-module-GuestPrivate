<?php declare(strict_types=1);

namespace GuestPrivate;

use GuestPrivate\Permissions\Acl as GuestPrivateAcl;

return [
    'roles' => [
        GuestPrivateAcl::ROLE_GUEST_PRIVATE => [
            'role' => GuestPrivateAcl::ROLE_GUEST_PRIVATE,
            'label' => 'Guest private', // @translate
            'admin' => false,
            'parents' => [],
        ],
    ],
    'service_manager' => [
        'invokables' => [
            Mvc\MvcListeners::class => Mvc\MvcListeners::class,
        ],
    ],
    'listeners' => [
        Mvc\MvcListeners::class,
    ],
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'view_helpers' => [
        'delegators' => [
            \Omeka\View\Helper\UserBar::class => [
                Service\ViewHelper\UserBarDelegatorFactory::class,
            ],
        ],
    ],
    'form_elements' => [
        'invokables' => [
            Form\SettingsFieldset::class => Form\SettingsFieldset::class,
        ],
    ],
    'controller_plugins' => [
        'factories' => [
            'userRedirectUrl' => Service\ControllerPlugin\UserRedirectUrlFactory::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'guestprivate' => [
        'settings' => [
            'guestprivate_redirect_top_to_login' => false,
            'guestprivate_theme_login' => false,
            'guestprivate_disable_public_api' => false,
        ],
    ],
];
