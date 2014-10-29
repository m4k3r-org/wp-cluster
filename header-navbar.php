<?php
/**
 * Top Navigation bar
 *
 *
 *
 *
 *
 * @package Flawless
 * @since Flawless 0.3.3
 *
 */

 ?>

<?php if( $flawless[ 'navbar' ][ 'html' ] ) { ?>

<div class="navbar navbar-fixed-top <?php echo ( $flawless[ 'mobile' ][ 'use_mobile_navbar']  == 'true' ? 'not-for-mobile' : '' ); ?> visible-desktop">
  <div class="navbar-inner">

    <div class="container">

      <?php if( $flawless[ 'navbar' ][ 'show_brand' ] == 'true' ) {
        echo '<a href="' . get_bloginfo( 'url') . '" class="brand">' . flawless_word_trim( get_bloginfo(), 40, true ) . '</a>';
      } ?>

      <?php if( $flawless[ 'navbar' ][ 'collapse' ] ) { ?>
      <a data-target=".nav-collapse" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <?php } ?>

      <?php echo $flawless[ 'navbar' ][ 'html' ]; ?>

    </div>

  </div>
</div>

<?php } ?>

<?php if( $flawless[ 'mobile_navbar' ][ 'html' ] ) { ?>
<div class="navbar navbar-fixed-top hidden-desktop">
  <div class="navbar-inner">

    <div class="container">

      <?php if( $flawless[ 'mobile_navbar' ][ 'show_brand' ] == 'true' ) {
        echo '<a href="' . get_bloginfo( 'url') . '" class="brand">' . get_bloginfo() . '</a>';
      } ?>

      <a data-target=".nav-collapse-mobile" data-toggle="collapse" class="btn btn-navbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <?php echo $flawless[ 'mobile_navbar' ][ 'html' ]; ?>

    </div>

    <div id="autocompletion_mobile" class="mobile autocompletion">
      <div style="position:relative;">
        <input data-suggest="mobile" data-bind="elasticSuggest:{
          selector: '#autocompletion_mobile',
          timeout: 1,
          size:50,
          document_type:{
            event:'Events',
            imagegallery:'Galleries',
            videoobject:'Videos',
            artist:'Artists',
            promoter:'Promoters',
            tour:'Tours',
            venue:'Venues',
            city:'City',
            'event-type':'Type',
            state:'State'
          },
          return_fields:['summary','url', 'venue.address.city', 'address.city', 'venue.address.state', 'address.state', 'start_date', 'event_date'],
          custom_query: {
            filter: {
              or: [
                {
                  range: {
                    start_date: {
                      gte: 'now-1d'
                    }
                  }
                },
                {
                  not: {
                    filter: {
                      exists: {
                        field: 'start_date'
                      }
                    }
                  }
                }
              ]
            }
          }
        }" placeholder="Search" />
        <img class="loader" data-bind="visible:mobile.loading" src="<?php echo get_template_directory_uri() ?>/img/ajax-loader.gif"/>
        <img class="cancel" data-bind="visible:!mobile.loading()&&mobile.has_text(),click:mobile.clear" src="<?php echo get_stylesheet_directory_uri() ?>/img/cancel.png"/>
      </div>
        <div class="clearfix"></div>
        <ul data-bind="enable:label=true,visible:mobile.visible"">
          <!-- ko if:mobile.documents().length -->
            <!-- ko foreach:mobile.documents -->
              <!-- ko if: _type != label -->
              <li data-bind="attr:{class:'ac_label '+_type+'_icon'}" class="ac_label"><i class="icon"></i><h5 data-bind="visible:label=_type,html: $root.mobile.types()[_type]"></h5></li>
              <!-- /ko -->
              <li class="ac_item">
                <a data-bind="attr:{href:fields.url}">
                  <!-- ko html:fields.summary --><!-- /ko -->

                  <!-- ko if:fields.event_date -->
                  <br /><i data-bind="if:(fields['venue.address.city']&&fields['venue.address.state'])">
                    <!-- ko html:fields['venue.address.city'] --><!-- /ko -->, <!-- ko html:fields['venue.address.state'] --><!-- /ko --> on <!-- ko html:moment(fields.event_date[0]).format('MMM Do, YYYY') --><!-- /ko -->
                  </i>
                  <!-- /ko -->

                  <!-- ko if:fields.start_date -->
                  <br /><i data-bind="if:(fields['venue.address.city']&&fields['venue.address.state'])">
                    <!-- ko html:fields['venue.address.city'] --><!-- /ko -->, <!-- ko html:fields['venue.address.state'] --><!-- /ko --> on <!-- ko html:moment(fields.start_date[0]).format('MMM Do, YYYY') --><!-- /ko -->
                  </i>
                  <!-- /ko -->

                  <!-- ko if:fields['address.city'] -->
                  <br /><i data-bind="if:(fields['address.city']&&fields['address.state'])">
                    <!-- ko html:fields['address.city'] --><!-- /ko -->, <!-- ko html:fields['address.state'] --><!-- /ko -->
                  </i>
                  <!-- /ko -->
                </a>
              </li>
            <!-- /ko -->
          <!-- /ko -->
          <!-- ko if:mobile.documents().length == 0 -->
            <li class="ac_item"><a href="#">Nothing found</a></li>
          <!-- /ko -->
        </ul>
      </div>

  </div>
</div>
<?php } ?>
