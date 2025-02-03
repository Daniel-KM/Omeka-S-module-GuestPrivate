Guest Private (module for Omeka S)
==================================

> __New versions of this module and support for Omeka S version 3.0 and above
> are available on [GitLab], which seems to respect users and privacy better
> than the previous repository.__

[Guest Private] is a module for [Omeka S] that creates two roles to manage
users: `guest_private_site`, who can see all sites, public or private, but not
private pages or private resources; `guest_private`, who can see all sites, site
pages and resources that are public or private. They don't have admin permission
and cannot go to the admin board.

This module may be useful when all sites are private, so guest user can see
protected resources. Another use case is a site in development with all sites,
pages and resources set private, but an external person needs to see and check
them.


Installation
------------

See general end user documentation for [installing a module].

This module requires the module [Common], that should be installed first.

* From the zip

Download the last release [GuestPrivate.zip] from the list of releases, and
uncompress it in the `modules` directory.

* From the source and for development

If the module was installed from the source, rename the name of the folder of
the module to `GuestPrivate`.

Then install it like any other Omeka module and follow the config instructions.


Usage
-----

Simply set the role "Guest private site" or "Guest private" in the user settings.


Warning
-------

Use it at your own risk.

It’s always recommended to backup your files and your databases and to check
your archives regularly so you can roll back if needed.


Troubleshooting
---------------

See online issues on the [module issues] page.


License
-------

This plugin is published under the [CeCILL v2.1] license, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

In consideration of access to the source code and the rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software’s author, the holder of the economic rights, and the
successive licensors only have limited liability.

In this respect, the risks associated with loading, using, modifying and/or
developing or reproducing the software by the user are brought to the user’s
attention, given its Free Software status, which may make it complicated to use,
with the result that its use is reserved for developers and experienced
professionals having in-depth computer knowledge. Users are therefore encouraged
to load and test the suitability of the software as regards their requirements
in conditions enabling the security of their systems and/or data to be ensured
and, more generally, to use and operate it in the same conditions of security.
This Agreement may be freely reproduced and published, provided it is not
altered, and that no provisions are either added or removed herefrom.


Copyright
---------

* Copyright Daniel Berthereau, 2023-2025 (see [Daniel-KM] on GitLab)

This module was build for the [Fondation Maison de Salins].


[Guest Private Role]: https://gitlab.com/Daniel-KM/Omeka-S-module-GuestPrivate
[Omeka S]: https://www.omeka.org/s
[GitLab]: https://gitlab.com/Daniel-KM/Omeka-S-module-GuestPrivate
[installing a module]: https://omeka.org/s/docs/user-manual/modules/#installing-modules
[module issues]: https://gitlab.com/Daniel-KM/Omeka-S-module-GuestPrivate/-/issues
[CeCILL v2.1]: https://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[GitLab]: https://gitlab.com/Daniel-KM
[Fondation Maison de Salins]: https://collections.maison-salins.fr
[Daniel-KM]: https://gitlab.com/Daniel-KM "Daniel Berthereau"
