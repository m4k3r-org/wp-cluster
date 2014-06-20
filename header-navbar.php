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

    <div id="autocompletion" class="mobile">
        <input data-suggest="mobile" data-bind="elasticSuggest:{
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
          return_fields:['summary','url'],
          //** Makes return only upcoming events */
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
        <img class="cancel" data-bind="visible:!mobile.loading(),click:mobile.clear" src="<?php echo get_stylesheet_directory_uri() ?>/img/cancel.png"/>
        <div class="clearfix"></div>
        <ul data-bind="enable:label=true,visible:mobile.visible,foreach:mobile.documents">
          <!-- ko if: _type != label -->
          <li data-bind="attr:{class:'ac_label '+_type+'_icon'}" class="ac_label"><i class="icon"></i><h5 data-bind="visible:label=_type,html: $root.mobile.types()[_type]"></h5></li>
          <!-- /ko -->
          <li class="ac_item">
            <a data-bind="attr:{href:fields.url},html: fields.summary"></a>
          </li>
        </ul>
      </div>

  </div>
</div>
<?php } ?>
