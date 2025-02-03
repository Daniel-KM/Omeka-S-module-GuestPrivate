<?php declare(strict_types=1);

namespace GuestPrivate\Permissions\Assertion;

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Omeka\Entity\SitePage;

/**
 * Unlike SitePageIsPublicAssertion, the site is not checked so it is possible
 * to see a public page of a private site.
 *
 * @see \Omeka\Permissions\Assertion\SitePageIsPublicAssertion
 */
class SitePageIsPublicAllSitesAssertion implements AssertionInterface
{
    public function assert(Acl $acl, RoleInterface $role = null,
        ResourceInterface $resource = null, $privilege = null
    ) {
        // This method is defined nowhere, but may be needed for automatic call.
        if (method_exists($resource, 'getSitePage')) {
            $resource = $resource->getSitePage();
        }
        return $resource instanceof SitePage
            && $resource->isPublic();
    }
}
