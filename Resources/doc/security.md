Security
=========================

## The roles

The CMS is restricted to a specific role : ``ROLE_BACKEND_ACCESS``.
This means that you can use the roles you want, but these roles must inherit from ``ROLE_BACKEND_ACCESS``.
This role gives you the key to enter the CMS, but you won't have access to anything.
Other roles will be used to give access to modules.

Administrators should be granted the ``ROLE_BACKEND_ADMIN`` which gives you access to all the CMS modules, like managing the roles for different kind of users in your ``Application``.
You'll see later that you can decide which ``Sections`` entities of the different ``Applications`` will be managed by each of these Roles.

There is a special role when logging into the CMS through the [``FlexyLdapBundle``](https://github.com/flexy/FlexyLdapBundle) named ``ROLE_DEVELOPER``.
Some features are restricted to this role like the ``Applications`` management.
Only developers can grant ``ROLE_DEVELOPER`` to other users. This role can't be viewed or edited by any non-developer users.

## The voter

The CMS uses a voter to manage restrictions to the Sections of the applications.
When creating or editing a role, you can decide which ``Sections`` entities will be managed by users having this role.
The voter will then hide and block access to the ``Sections`` that are not managable by this role.

Only roles beginning with ``ROLE_BACKEND_`` will be voted. Other roles will be ignored.

*IMPORTANT*: This works only for ``Sections`` entities, other bundles have to implement their own restriction logic.

## Impersonate a user

This feature is restricted to the ``ROLE_DEVELOPER`` users and lets you impersonate other non-developer users.
In the user list, click the silhouette icon next to the user name to impersonate this user.
It can be useful to test what can be managed by the different users of your ``Application``.
