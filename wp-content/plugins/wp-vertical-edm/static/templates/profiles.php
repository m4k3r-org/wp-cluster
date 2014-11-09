<div class="wrap">
  <h2>Profile</h2>

  <div class="intelligence-panel">

    <div class="intelligence-debug">
      <div class="query-debug" data-bind="text: $data.debug.request, visible: $data.debug.request && view.showDebug"></div>
      <div class="query-debug" data-bind="text: $data.debug.response, visible: $data.debug.response && view.showDebug"></div>
    </div>

    <ul class="application-state hidden">
      <li data-state="read" data-bind="visible: is.ready">Ready</li>
      <li data-state="loading" data-bind="visible: is.loading">Loading</li>
    </ul>

    <ul class="common-queries">
      <li class="title">Common Searches</li>
      <li class="common-query hidden" data-bind="click: testApp"   >Run Tests</li>
      <li class="common-query" data-bind="click: testOne"   data-query='{"query":{"interests.artists":"tiesto","location.state":"NC"}}'>What sort of people like Tiesto in North Carolina?</li>
      <li class="common-query" data-bind="click: testTwo"   data-query='{"query":{"location.state":"US-NC"}}'>What is Tiësto's popularity popular in the United States amongst women?</li>
      <li class="common-query" data-bind="click: testThree" data-query='{"query":{"location.country":"USA", "gender": "female"}}'>Which artists are trending in the United States amongst women?</li>
    </ul>

    <div class="query-result">

      <div class="query-metrics">
        <div class="hidden" data-bind="template: { name: 'person-template', data: $data.buyer }"></div>
        <div class="hidden" data-bind="template: { name: 'person-template', data: $data.seller }"></div>
      </div>

    </div>

  </div>

  <div class="intelligence-filters">

    <div class="section">
      <h3>Location</h3>
      <ul>
        <li><label>City<input data-input="location.city" /></label></li>
        <li><label>State<input data-input="location.state" /></label></li>
      </ul>
    </div>

    <div class="section">
      <h3>Demographics</h3>
      <ul>
        <li><label><input value="female" type="checkbox" />Female</label></li>
        <li><label><input value="male" type="checkbox" />Male</label></li>
      </ul>
    </div>

    <div class="section">
      <h3>Interests</h3>
      <ul>
        <li><label><input value="female" type="checkbox"  />Tiësto</label></li>
        <li><label><input value="female" type="checkbox"  />Steve Aoki</label></li>
        <li><label><input value="male" type="checkbox"    />Deorro</label></li>
        <li><label><input value="male" type="checkbox"    />GTA</label></li>
        <li><label><input value="male" type="checkbox"    />GTA</label></li>
        <li><label><input value="male" type="checkbox"    />Zedd</label></li>
        <li><label><input value="male" type="checkbox"    />Bassnectar</label></li>
      </ul>
    </div>

    <div class="section">
      <h3>Audience</h3>
      <ul>
        <li><label><input value="female" type="checkbox"  />Customers</label></li>
        <li><label><input value="female" type="checkbox"  />Subscribers</label></li>
        <li><label><input value="male" type="checkbox"    />Visitors</label></li>
        <li><label><input value="male" type="checkbox"    />Other</label></li>
      </ul>
    </div>

    <div class="section">
      <h3>Social Metrics</h3>
      <ul>
        <li><label><input value="female" type="checkbox"  />High Followers</label></li>
        <li><label><input value="male" type="checkbox"    />Active Publishing</label></li>
      </ul>
    </div>

  </div>

</div>

<script type="text/javascript">

  function IntelligenceBootstrap() {

    var contaienr   = jQuery( '.intelligence-panel' );
    var client      = require( 'analysis.client' );
    var visualizer  = require( 'analysis.visualizer' );
    var ko          = require( 'knockout' );

    ko.applyBindings( new function IntelligenceViewModel() {

      var app = this;

      function clearResults() {
        jQuery( '.result-table' ).fadeOut().remove();
        jQuery( '.result-map' ).fadeOut().remove();
      }

      function genericResponse( error, res, type, req ) {
        console.debug( 'genericResponse', type, res );
        app.debug.request( JSON.stringify( req, null, 4 ) );
        app.debug.response( JSON.stringify( res, null, 4 ) );
      }

      /**
       * Debug Methods
       *
       */
      function testApp() {

        client.getResults({
          filter: {
            and: [
              {
                term: {
                  age: 23
                }
              },
              {
                term: {
                  gender: "female"
                }
              }
            ]
          },
          facets: {
            tags: {
              terms: {
                field: "location.state"
              }
            }
          }
        }, genericResponse );

        client.getFacets({
            and: [
              { term: { age: 23 } }
            ]
        }, {
          gender: { terms: { field: "gender" } },
          locale: { terms: { field: "locale" } }
        }, genericResponse );

        return;

        visualizer.Map( 'Artist Popularity', {
          State: [],
          Popularity: [],
          raw: [
            ['State', 'Popularity'],
            ['US-NC', 200],
            ['US-FL', 300],
            ['US-TX', 400],
            ['US-NY', 500],
            ['US-AL', 600],
            ['US-MN', 700]
          ]
        });

        visualizer.Pie( 'Gender Preference', null );

        visualizer.Table( 'Some List', null );

      }

      /**
       * What sort of people like Tiesto in North Carolina?
       *
       */
      function testOne() {
        clearResults();

        client.getFacets({
          bool: {
            must: [
              { "term": { "interests.artists":  "tiesto" } },
              { "term": { "location.state":     "US-NC" } },
              { "term": { "location.country":   "USA" } }
            ]
          }
        }, {
          age:        { statistical:  { all_terms: true, field: "age" } },
          friends:    { statistical:  { all_terms: true, field: "metrics.friends" } },
          state:      { terms:        { all_terms: true, field: "location.state" } },
          city:       { terms:        { all_terms: true, field: "location.city",   size: 10 } },
          country:    { terms:        { all_terms: true, field: "location.country" } },
          ticket:     { terms:        { all_terms: true, field: "ticket_history" } },
          gender:     { terms:        { all_terms: true, field: "gender" } },
          artists:    { terms:        { all_terms: true, field: "interests.artists" } },
          locale:     { terms:        { all_terms: true, field: "locale" } },
          networks:   { terms:        { all_terms: true, field: "networks", exclude: [ "crappy-chat" ] } }
        }, function handleResponse( error, response, type, request ) {
          console.log( 'response', response );

          visualizer.Table( 'Age',      response.age );
          visualizer.Table( 'Friends',  response.friends );
          visualizer.Table( 'State',    response.state );
          visualizer.Table( 'City',     response.city );
          visualizer.Table( 'Gender',   response.gender );
          visualizer.Table( 'Networks', response.networks );
          visualizer.Table( 'Locale',   response.locale );
          visualizer.Table( 'Timezone', response.timezone );

          app.debug.response( JSON.stringify( response, null, 4 ) );

        });

      }

      /**
       *  What is Tiësto's popularity in the United States amongst women?
       *
       */
      function testTwo() {
        clearResults();

        client.getFacets({
          bool: {
            must: [
              { "term": { "gender":             "male" } },
              { "term": { "interests.artists":  "tiesto" } },
              { "term": { "location.state":     "US-NC" } },
              { "term": { "location.country":   "USA" } }
            ]
          }
        }, {
          age:        { statistical:  { all_terms: true, field: "age" } },
          friends:    { statistical:  { all_terms: true, field: "metrics.friends" } },
          activity:   { statistical:  { all_terms: true, field: "metrics.monthly_activity" } },
          accounts:   { statistical:  { all_terms: true, field: "metrics.social_accounts" } },
          attended:   { statistical:  { all_terms: true, field: "metrics.events_attended" } },
          state:      { terms:        { all_terms: true, field: "location.state" } },
          city:       { terms:        { all_terms: true, field: "location.city", size: 10 } },
          country:    { terms:        { all_terms: true, field: "location.country" } },
          ticket:     { terms:        { all_terms: true, field: "ticket_history" } },
          gender:     { terms:        { all_terms: true, field: "gender" } },
          artists:    { terms:        { all_terms: true, field: "interests.artists" } },
          locale:     { terms:        { all_terms: true, field: "locale" } },
          networks:   { terms:        { all_terms: true, field: "networks", exclude: [ "crappy-chat" ] } }
        }, function handleResponse( error, response, type, request ) {
          console.log( 'testTwo::handleResponse', response, type );

          visualizer.Map( 'Artist Popularity', {
            State: [],
            Popularity: [],
            raw: [
              ['State', 'Tiesto Popularity'],
              ['US-NC', 1200],
              ['US-FL', 300],
              ['US-TX', 400],
              ['US-NY', 500],
              ['US-AL', 600],
              ['US-MN', 700]
            ]
          });

          visualizer.Table( 'City',   response.city );
          visualizer.Table( 'Gender', response.gender );

          visualizer.Pie( 'Audience', [
            ['Audience', 'Count'],
            ['Customers',     25],
            ['Subscribers',      20],
            ['Visitors',  40],
            ['Other', 10]
          ] );

          app.debug.request(  JSON.stringify( request, null, 4 ) );
          app.debug.response( JSON.stringify( response, null, 4 ) );

        });

      }

      /**
       * Which artists are trending in the United States amongst women?
       *
       */
      function testThree() {

      }

      // Initialize ElastiSearch.
      client.createClient( 'elastic.uds.io:12200', 'jezf-truq-qgox-hfxp', 'profile' );

      client.getMeta( null, function() {
        app.is.ready( true );
        app.is.loading( false );
      });

      this.is = {
        ready:    ko.observable( false ),
        loading:  ko.observable( true )
      };

      this.view = {
        showDebug:  ko.observable( true, { persist: 'showDebug' } ),
        showFilter: ko.observable( null, { persist: 'showFilter' } ),
        showFacets: ko.observable( null, { persist: 'showFacets' } )
      };

      this.buyer = { name: 'Franklin', credits: 250 };
      this.seller = { name: 'Mario', credits: 5800 };

      this.testApp = testApp;
      this.testOne = testOne;
      this.testTwo = testTwo;
      this.testThree = testThree;

      app.debug = {
        request: ko.observable(),
        response: ko.observable()
      };

      jQuery( '.hide-postbox-tog' ).change( function optionChange( event ) {
        console.debug( 'screen option toggle', event.target.value, event.target.checked );

        if( !app.view[ event.target.value ] ) {
          app.view[ event.target.value ] = ko.observable( null, { persist: event.target.value });
        }

        app.view[ event.target.value ]( event.target.checked );

      });

    }, contaienr.get(0) );

  }

  require( [ 'knockout', 'knockout.mapping', 'knockout.localStorage', 'analysis.client', 'intelligence-app' ], IntelligenceBootstrap );

</script>

<script type="text/html" id="facet-template">
  <h3 data-bind="text: $data.name"></h3>
  <p>Credits: <span data-bind="text: $data.credits"></span></p>
</script>

<script type="text/html" id="person-template">
  <h3 data-bind="text: $data.name"></h3>
  <p>Credits: <span data-bind="text: $data.credits"></span></p>
</script>
