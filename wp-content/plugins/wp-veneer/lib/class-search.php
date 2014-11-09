<?php
/**
 * ElasticSearch API Controller
 *
 * @module Veneer
 * @author potanin@UD
 */
namespace UsabilityDynamics\Veneer {

  if( !class_exists( 'UsabilityDynamics\Veneer\Search' ) ) {

    /**
     * Class Search
     *
     * @module Veneer
     */
    class Search extends \Elastica\Client {}

  }

}