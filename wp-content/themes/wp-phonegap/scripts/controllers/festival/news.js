define(
  [
    'global',
    'lodash',
    'collection/posts',
    'viewModel/shared/news',
    'text!template/shared/news.html'
  ],
  function( _ddp, _, PostsCollection, NewsViewModel, NewsTemplate ){
    'use strict';
    return function( id, activeTab ){
      var self = this, postsCollection = new PostsCollection(), body;
      /** Setup our active tab observable */
      this.festivalId = id;
      this.activeTab = activeTab;
      /** Setup our default body */
      body = {
        query: {
          match: {
            blog_id: _.isUndefined( _ddp.currentFestival ) ? _ddp.defaultBlog : _ddp.currentFestival._id()
          }
        },
        sort: [ {
          post_date: 'desc'
        } ]
      };
      /** Change our query based on the tab */
      switch( activeTab ){
        case 'featured':
          /** Only get the updates category */
          body.query.constant_score = {
            filter: {
              term: {
                category: 'updates'
              }
            }
          };
          break;
        case 'all':
        default:
          /** Get everything but the updates category */
          body.query.constant_score = {
            filter: {
              not: {
                term: {
                  category: 'updates'
                }
              }
            }
          };
          break;
      }
      /** Call our fetch function, appending our query */
      postsCollection.fetch( {
        /** Append the body here so we can add onto the default query */
        body: body,
        /** Our sucess callback */
        success: function( collection, response, options ){
          /** Ok, lets init our view Model, and apply the bindings from our Model */
          var $page = $( '<div>' ).html( NewsTemplate );
          /** Create our ViewModel, from our collection */
          var newsViewModel = new NewsViewModel( collection, self.activeTab, 'festival/' + self.festivalId );
          ko.applyBindings( newsViewModel, $page[ 0 ] );
          /** Slide it into the DOM */
          _ddp.slidePage( $page );
        },
        /** Our failure callback */
        error: function( collection, error ){
          _ddp.log( 'There was an error getting the list of posts: ' + error, 'error' );
          _ddp.router.navigate( 'festival/menu', { trigger: true } );
        }
      } );
    };
  }
);