/**
 * The only thing this file is going to do is basically require everything that should be
 * loaded at a low level. This way this functionality will be already ready to go
 * and we won't have to include it in each script
 */
define(
  [
    'global',
    'knockout',
    'element/loader',
    'text',
    'json',
    'knockout-amd-helpers',
    'jquery-hammerjs',
    'lodash'
  ],
  function( _ddp, ko, Toolbar ){
    /** Setup our KO defaults */
    ko.amdTemplateEngine.defaultPath = '';
    ko.amdTemplateEngine.defaultSuffix = '.html';
    ko.bindingHandlers.module.templateProperty = 'template';
    ko.bindingHandlers.module.initializer = 'init';
    ko.bindingHandlers.module.baseDir = '';
    /** We're done, log it */
    _ddp.log( 'Finished low level init' );
  }
);