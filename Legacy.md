
### Setting Up Local Environment
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

### Development Notes
* If you put 'define( 'SCRIPT_DEBUG', true );' in your local config (system.php), it will use the JS assets which are not concatinated.
  - This will help with debugging.
  - You can still run 'grunt requirejs' to build an updated, minified file for exclusion, or 'grunt' to compile CSS + JS
  - Generally, if you know that it's being used, bring it in as an AMD module, and remove any enqueue that you can, as upon build, you'll have all the script in one file.
  - For now, continue to use components with composer for JS assets.
* In wp-festival, if you remove the 'styles/app.css' file, each request to the CSS file will be dynamically generated on the fly.
  - No need to run grunt watch
  - ** Only for *nix right now, will work on Windows with some tweaks **
  - Be sure to compile the grunt asset before a deployment
* If you're working on 'wp-festival-2', you'll need to 'composer install', then remove 'vendor/libraries/autoload.php', as it conflicts
  - You'll need to install the front end assets here while in development mode
* Here are the latest hosts that are used with this network:
  - https://gist.github.com/jbrw1984/bc706cdb05dec4d46794
* You can look at the current HTML mockups at: vendor/themes/wp-festival-2/static/mocks
