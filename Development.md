### WP-CLI Local Development
This site supports settings being set via Environment Variables. This can be done in MAMP, WAMP or in terminal:
```
export DB_USER=root
export DB_PASSWORD=root
export DB_HOST=localhost
wp core version
wp plugin delete fantastic-elasticsearch
wp plugin activate wp-fantastic-elasticsearch
```

Or, a more practical example, let's import MySQL dump:
```
wp db import edm_cluster_new_2014-09-09.sql
```
