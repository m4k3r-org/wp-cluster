<?php
/**
 * Name: Social Share Count
 * ID: social_share_count
 * Type: shortcode
 * Group: Festival
 * Class: UsabilityDynamics\Festival2\Social_Share_Count
 * Version: 1.0
 * Description: Get the share count for Facebook, Twitter, G+, Pinterest
 */
namespace UsabilityDynamics\Festival2 {

	/**
	 * Prevent class redeclaration
	 */
	if( !class_exists( 'UsabilityDynamics\Festival2\Social_Share_Count' ) ) {

		/**
		 * main shortcode class
		 * @extends \UsabilityDynamics\Shortcode\Shortcode
		 */
		class Social_Share_Count extends \UsabilityDynamics\Shortcode\Shortcode {

			/**
			 * ID
			 * @var type
			 */
			public $id = 'social_share_count';

			/**
			 * Group
			 * @var type
			 */
			public $group = 'Festival';

			/**
			 * Construct
			 *
			 * @param array|\UsabilityDynamics\Festival2\type $options
			 */
			public function __construct( $options = array() ) {

				$this->name = __( 'Social Share Count', wp_festival2( 'domain' ) );

				$this->description = __( 'Get the share count for Facebook, Twitter, G+, Pinterest.', wp_festival2( 'domain' ) );

				$this->params = array(
						'facebook' => '',
						'twitter' => '',
						'google_plus' => '',
						'pinterest' => '',
						'total' => '',
						'url' => ''
				);

				parent::__construct( $options );
			}

			/**
			 * Caller
			 *
			 * @param string|\UsabilityDynamics\Festival2\type $atts
			 *
			 * @return type
			 */
			public function call( $atts = "" ) {

				$atts = shortcode_atts([

					'facebook' => false,
					'twitter' => false,
					'google_plus' => false,
					'pinterest' => false,
					'total' => false,
					'url' => ''

				], $atts);

				// If no URL was specified
				if ( empty( $atts[ 'url' ] ) )
				{
					return json_encode(false);
				}

				$shares = [
					'twitter' => 0,
					'facebook' => 0,
					'google_plus' => 0,
					'pinterest' => 0,
					'total' => 0,
					'url' => null
				];


				if ( (bool) $atts['total'] === true )
				{
					$shares = $this->_get_total_shares( $atts['url'] );
				}
				else
				{
					if ( (bool) $atts['facebook'] === true )
					{
						$shares['facebook'] = $this->_get_facebook_shares( $atts['url'] );
					}
					if ( (bool) $atts['twitter'] === true )
					{
						$shares['twitter'] = $this->_get_twitter_shares( $atts['url'] );
					}
					if ( (bool) $atts['google_plus'] === true )
					{
						$shares['google_plus'] = $this->_get_google_plus_shares( $atts['url'] );
					}
					if ( (bool) $atts['pinterest'] === true )
					{
						$shares['pinterest'] = $this->_get_pinterest_shares( $atts['url'] );
					}
				}

				$shares['url'] = $atts['url'];

				return json_encode($shares);
			}


			private function _get_facebook_shares( $url )
			{
				$fb_url = 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' .$url;

				$data = $this->_curl_get_data( $fb_url );
				$data = json_decode( $data, true );

				if ( isset( $data[0][ 'total_count' ] ) )
				{
					$data = $data[0][ 'total_count' ];
				}
				else
				{
					$data = 0;
				}

				return $data;
			}

			private function _get_twitter_shares( $url )
			{
				$twitter_url = 'http://urls.api.twitter.com/1/urls/count.json?url=' .$url;

				$data = $this->_curl_get_data( $twitter_url );
				$data = json_decode( $data, true );

				if ( isset( $data[ 'count' ] ) )
				{
					$data = $data[ 'count' ];
				}
				else
				{
					$data = 0;
				}

				return $data;
			}

			private function _get_google_plus_shares( $url )
			{
				$curl = curl_init();
				curl_setopt( $curl, CURLOPT_URL, "https://clients6.google.com/rpc" );
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' .rawurldecode($url) .'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, array('Content-type: application/json' ));

				$data = curl_exec( $curl );
				curl_close( $curl );

				$data = json_decode( $data, true );

				if ( isset( $data[0]['result']['metadata']['globalCounts']['count'] ) )
				{
					$data = $data[0]['result']['metadata']['globalCounts']['count'];
				}
				else
				{
					$data = 0;
				}

				return $data;
			}

			private function _get_pinterest_shares( $url )
			{
				$pinterest_url = 'http://api.pinterest.com/v1/urls/count.json?url=' .$url;

				$data = $this->_curl_get_data( $pinterest_url );
				$data = preg_replace('/^receiveCount((.*))$/', "\1", $data);
				$data = json_decode( $data, true );

				if ( isset( $data[ 'count' ] ) )
				{
					$data = $data[ 'count' ];
				}
				else
				{
					$data = 0;
				}

				return $data;
			}

			private function _get_total_shares( $url )
			{
				$twitter = $this->_get_twitter_shares( $url );
				$facebook = $this->_get_facebook_shares( $url );
				$google_plus = $this->_get_google_plus_shares( $url );
				$pinterest = $this->_get_pinterest_shares( $url );

				$total = (int) $twitter + (int) $facebook + (int) $google_plus + (int) $pinterest;

				return [
					'twitter' => $twitter,
					'facebook' => $facebook,
					'google_plus' => $google_plus,
					'pinterest' => $pinterest,
					'total' => $total
				];
			}


			private function _curl_get_data( $data_url )
			{
				$curl = curl_init();

				curl_setopt( $curl, CURLOPT_URL, $data_url );
				curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
				curl_setopt( $curl, CURLOPT_FAILONERROR, 1 );
				curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER,1 );
				curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

				$data = curl_exec( $curl );

				if ( curl_error( $curl ) )
				{
					$data = false;
				}

				curl_close( $curl );

				return $data;
			}
		}
	}
}



