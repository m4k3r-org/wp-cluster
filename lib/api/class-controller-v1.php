<?php
/**
 *
 * https://api.wpcloud.io/cluster/v1/controller/list.json
 * https://api.wpcloud.io/cluster/v1/controller/hosts.json
 * https://api.wpcloud.io/cluster/v1/controller/balancers.json
 * https://api.wpcloud.io/cluster/v1/controller/plans.json
 *
 * ### GitHub Event Types
 * - status
 * - push
 * - create
 *
 * @author potanin@UD
 */
namespace UsabilityDynamics\Cluster\API\Controller\V1 {

  class Register {

    public function __construct() {

      add_action( 'wp_ajax_/cluster/v1/controller/list',        array( 'UsabilityDynamics\Cluster\API\Controller\V1\Actions', 'controllersPrivate' ) );
      add_action( 'wp_ajax_/cluster/v1/controller/balancers',   array( 'UsabilityDynamics\Cluster\API\Controller\V1\Actions', 'balancersPrivate' ) );
      add_action( 'wp_ajax_/cluster/v1/controller/plans',       array( 'UsabilityDynamics\Cluster\API\Controller\V1\Actions', 'plansPrivate' ) );

    }

  }

  class Actions {

    /**
     * List Balancers
     *
     * https://wpcloud.io/wp-admin/admin-ajax.php?action=/cluster/v1/controller/balancers
     */
    public static function balancersPrivate() {

      wp_send_json(array(
        "ok" => true,
        "data" => apply_filters( 'wpCloud:controller:balancers', array() )
      ));

    }

    /**
     * List Controllers
     *
     * https://wpcloud.io/wp-admin/admin-ajax.php?action=/cluster/cluster/v1/controller/list
     */
    public static function controllersPrivate() {

      wp_send_json(array(
        "ok" => true,
        "data" => apply_filters( 'wpCloud:controller:controllers', array() )
      ));

    }

    /**
     * List Plans
     *
     * https://wpcloud.io/wp-admin/admin-ajax.php?action=/cluster/v1/controller/plans
     */
    public static function plansPrivate() {

      wp_send_json(array(
        "ok" => true,
        "data" => apply_filters( 'wpCloud:controller:plans', array() )
      ));

    }

  }

}
