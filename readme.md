## Changelog

### 0.3.0
 - Added check for UPLOADBLOGSDIR to be defined or wp_die().
 - Added support for UPLOADBLOGSDIR when configuring media path and URL.

## Overview

## Recognized Constants

 - WP_VENEER_DOMAIN_MEDIA - Enable's domain-specific media storage directories instead of blog IDs.

## Domain Mapping

 - Each site has a primary domain, which may be a second level domain or a subdomain.
 - Subdomains could be supported.
 - If no site can be found, visitor is directed to network homepage.
 - Virtually all link functions reference "siteurl" and "home" options, we modify them once a request is validated.

 - The network's (WordPress calls it "site") domain setting in wp_sites is used in links but not served.

## Structure

### Veneer\Loader

### Veneer\Core

### Veneer\Developer

### Veneer\ResizeImage

## Deployment