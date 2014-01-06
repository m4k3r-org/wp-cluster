Removed temporarily
    "usabilitydynamics/wp-elastic": "master",


This used to be wp-cluster but was renamed to wp-cluster and made private.

## Network Concepts
  - upload_url_path honored when serving images although option is hidden in MS.
  - upload_path is honored but relative to /system and re-creates directory structure on load.
  - the main wp_ tables should store vIndustry settings. vOrganization settings stored in wp_sitemeta
  - $current_site has wrong blog_id while $current_blog is fine.
  - the /manage/network should probably only be accessible via the "main" site domain.
  - Veneer .htaccess rules must be inserted before WP rules.
  - "vendor" libraries should be excluded from most repositories
  ? "components" should almost always be included in repository since they are usually in built state
  - "modules" should probably be excluded from repositories to allow WordPress-based control.
  - sites (blogs) within a network that are not part of the same organization should be logically seperated by "site"

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

### Cluster\Loader

### Cluster\Core

### Cluster\Developer

### Cluster\ResizeImage

## Deployment