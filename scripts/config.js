/**
 * This file holds our global require configuration
 */
define( {
  /** Items here are DDP specific configuration options */
  ddp: {
    debug: true,
    mockRequests: true,
    loaded: false, /** Don't change this, the app uses it */
    useResizely: true,
    defaultBlog: 12,
    screen: {
      width: document.documentElement.clientWidth, /** Override to set a manual screen width */
      height: document.documentElement.clientHeight, /** Override to set a manual screen height */
      dpr: ( 'devicePixelRatio' in window ? window.devicePixelRatio : '1' ) /** Override this to '1' to disable retina support */
    },
    elasticsearch: {
      host: {
        protocol: 'http',
        host: 'api.discodonniepresents.com',
        port: 80,
        country: 'US',
        weight: 10,
        headers: {
          'x-access-key': 'qccj-nxwm-etsk-niuu-xctg-ezsd-uixa-jhty'
        }
      }
    }
  },
  /** Things below here are Require.js specific options */
  baseUrl: 'scripts/src',
  paths: {
    /** Basic shorthand syntax */
    'library': 'libraries',
    'model': 'models',
    'collection': 'collections',
    'viewModel': 'viewModels',
    'template': 'templates',
    'controller': 'controllers',
    'contract': 'contracts',
    'element': 'elements',
    'module': 'modules',
    'dataset': 'datasets',
    /** Specify library shorthand */
    'global': 'libraries/global',
    'baseModel': 'libraries/baseModel',
    'baseCollection': 'libraries/baseCollection',
    'baseViewModel': 'libraries/baseViewModel',
    'elasticsearch': 'libraries/elasticsearch',
    /** Map our components */
    'jquery': '../../components/jquery/dist/jquery.min',
    'knockout': '../../components/knockout/dist/knockout',
    'backbone': '../../components/backbone/backbone',
    'knockback': '../../components/knockback/knockback-core.min',
    'underscore': '../../components/underscore/underscore',
    'text': '../../components/requirejs-text/text',
    'json': '../../components/requirejs-plugins/src/json',
    'lodash': '../../components/lodash/dist/lodash.underscore.min',
    'moment': '../../components/moment/moment',
    'jquery-hammerjs': '../../components/jquery-hammerjs/jquery.hammer',
    'hammerjs': '../../components/hammerjs/hammer.min',
    'knockout-amd-helpers': '../../components/knockout-amd-helpers/build/knockout-amd-helpers.min'
  },
  shim: {
    'elasticsearch': {
      deps: [ 'jquery' ]
    }
  }
} );