DiscoDonniePresents.com & the EDM Cluster

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

* Each directory has a corresponding 'readme.md' which gives a brief spiel on what the directory should be used for.
* The username should be 'reidwilliams' for the local environment, and the password should be 'password'
* Use http://umesouthpadre.com/ as an example site that version 1 of the festival theme has been implemented

## Setting Up Local Environment

1. Make sure that both node and Composer are installed in your environment.
   * You should be able to run both 'npm', and 'composer'.
2. Run `npm install --development` to install all the node modules required.
3. Run `composer install --prefer-source` to install all of the PHP repositories and libraries required.
4. Run `grunt install --type=cluster` in order to properly configure your environment.
5. Create a file called 'application/static/etc/wp-config/system.php', in here define your config details specific to your environment
   * Do this as your normally would in a wp-config file (i.e. define( 'DB_HOST', 'localhost' ) ).
6. Modify your hosts files to add the appropriate domains to your implementation.
7. Import the 'application/static/fixtures/' base SQL file.
8. Navigate to the site. :)
