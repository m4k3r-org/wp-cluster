#### 0.4.5
* Added SUBDOMAIN_COOKIE constant check for adding a "." prefix to SUBDOMAIN_COOKIE if set to true.
* Added Utility methods: get_git_branch, get_git_tag, get_git_version and get_git_commit_message.
* Added admin toolbar with Git branch, version and commit message.
* Added global $wp_cluster variable for instance to load into.
* Added support for blog-not-found.php dropin.
* Added Grunt module for locale generation, removed unused Grunt modules.

#### 0.3.0
* Added check for UPLOADBLOGSDIR to be defined or wp_die().
* Added support for UPLOADBLOGSDIR when configuring media path and URL.
