<?php declare(strict_types=1);

namespace GuestPrivate;

/**
 * @var Module $this
 * @var \Laminas\ServiceManager\ServiceLocatorInterface $services
 * @var string $newVersion
 * @var string $oldVersion
 */

$settings = $services->get('Omeka\Settings');

if (version_compare((string) $oldVersion, '3.4.9', '<')) {
    // The checkbox to disable public and local api became a radio to restrict
    // each api separately.
    $settings->set('guestprivate_restrict_api', $settings->get('guestprivate_disable_public_api') ? 'all' : '');
    $settings->delete('guestprivate_disable_public_api');
}
