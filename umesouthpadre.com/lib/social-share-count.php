<?php

class Social_Share_Count
{
	public function get_facebook( $url )
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

	public function get_twitter( $url )
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

	public function get_google_plus( $url )
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

	public function get_pinterest( $url )
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

	public function get_total( $url )
	{
		$twitter = $this->get_twitter( $url );
		$facebook = $this->get_facebook( $url );
		$google_plus = $this->get_google_plus( $url );
		$pinterest = $this->get_pinterest( $url );

		$total = (int) $twitter + (int) $facebook + (int) $google_plus + (int) $pinterest;

		return array(
				'twitter' => $twitter,
				'facebook' => $facebook,
				'google_plus' => $google_plus,
				'pinterest' => $pinterest,
				'total' => $total
		);
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