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
			 * @var array Holds the share counts
			 */
			private $_shares = [
				'facebook' => null,
				'twitter' => null,
				'google_plus' => null,
				'pinterest' => null,
				'total' => null,
				'url' => null
			];


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

				$this->_shares['url'] = $atts['url'];

				if ( (bool) $atts['facebook'] === true )
				{
					$this->_shares['facebook'] = $this->_get_facebook_shares();
				}
				if ( (bool) $atts['twitter'] === true )
				{
					$this->_shares['twitter'] = $this->_get_twitter_shares();
				}
				if ( (bool) $atts['google_plus'] === true )
				{
					$this->_shares['google_plus'] = $this->_get_google_plus_shares();
				}
				if ( (bool) $atts['pinterest'] === true )
				{
					$this->_shares['pinterest'] = $this->_get_pinterest_shares();
				}
				if ( (bool) $atts['total'] === true )
				{
					$this->_shares['total'] = $this->_get_total_shares();
				}

				return json_encode($this->_shares);
			}


			private function _get_facebook_shares()
			{
				$fb_url = 'http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' .$this->_shares['url'];

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

			private function _get_twitter_shares()
			{
				$twitter_url = 'http://urls.api.twitter.com/1/urls/count.json?url=' .$this->_shares['url'];

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

			private function _get_google_plus_shares()
			{
				$curl = curl_init();
				curl_setopt( $curl, CURLOPT_URL, "https://clients6.google.com/rpc" );
				curl_setopt( $curl, CURLOPT_POST, true );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' .rawurldecode($this->_shares['url']) .'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]' );
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

			private function _get_pinterest_shares()
			{
				$pinterest_url = 'http://api.pinterest.com/v1/urls/count.json?url=' .$this->_shares['url'];

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

			private function _get_total_shares()
			{
				if ( $this->_shares['facebook'] === null )
				{
					$this->_shares['facebook'] = $this->_get_facebook_shares();
				}
				if ( $this->_shares['twitter'] === null )
				{
					$this->_shares['twitter'] = $this->_get_twitter_shares();
				}
				if ( $this->_shares['google_plus'] === null )
				{
					$this->_shares['google_plus'] = $this->_get_google_plus_shares();
				}
				if ( $this->_shares['pinterest'] === null )
				{
					$this->_shares['pinterest'] = $this->_get_pinterest_shares();
				}

				return
						(int) $this->_shares['facebook'] +
						(int) $this->_shares['twitter'] +
						(int) $this->_shares['google_plus'] +
						(int) $this->_shares['pinterest'];
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



