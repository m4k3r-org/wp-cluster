WP-Site Master Repository

## About

This is the master repository from which all other WordPress sites Usability Dynamics creates is forked from. In
addition to this, this repository will have 3 branches, depending on which type of site you're trying to use:

* master - this is the default branch for a standalone site (i.e. usabilitydynaimcs.com)
* multisite - this is the default branch for a multisite implementation (i.e. TDB)
* cluster - this is the default branch for a cluster (read: vertical) site (i.e. edm-cluster) with multi-db support

## Why GitHub?

* Better synchronization across repositories
* Ability to do cross repository pull requests when updates are made
* Keep branches in sync better
* No plethora of individual repositories where "one-off" changes are made and are not in sync with the master branches

## Running Build

We use grunt to run the build, here is the command that should be used:

```shell
You can use this grunt file to do the following:
   * grunt install - installs and builds environment
   * Arguments:
      --environment={environment} - builds specific environment: (production**, development, staging, local)
      --system={system} - build for a specific system: (linux**, windows
      --type={type} - build for a specific site type: (standalone**, cluster, multisite)
```

## Notes

* Each directory has a corresponding 'readme.md' which gives a brief spiel on what the directory should be used for

## Problems

The following packages need to be installed:
  Problem 1
    - The requested package wpackagist-plugin/gravityformsmailchimp could not be found in any version, there may be a typo in the package name.
  Problem 2
    - The requested package wpackagist-plugin/gravityforms-multilingual could not be found in any version, there may be a typo in the package name.
  Problem 3
    - The requested package wpackagist-plugin/jf3-maintenance-mode could not be found in any version, there may be a typo in the package name.
  Problem 4
    - The requested package wpackagist-plugin/wp-admin-column-view-master could not be found in any version, there may be a typo in the package name.
  Problem 5
    - The requested package wpackagist-plugin/wpml-cms-nav could not be found in any version, there may be a typo in the package name.
  Problem 6
    - The requested package wpackagist-plugin/wpml-media could not be found in any version, there may be a typo in the package name.
  Problem 7
    - The requested package wpackagist-plugin/wpml-sticky-links could not be found in any version, there may be a typo in the package name.
  Problem 8
    - The requested package wpackagist-plugin/wpml-string-translation could not be found in any version, there may be a typo in the package name.
  Problem 9
    - The requested package wpackagist-plugin/wpml-translation-management could not be found in any version, there may be a typo in the package name.
  Problem 10
    - The requested package wpackagist-plugin/wp-rpc could not be found in any version, there may be a typo in the package name.
  Problem 11
    - The requested package wpackagist-plugin/wp-seo-addon could not be found in any version, there may be a typo in the package name.
