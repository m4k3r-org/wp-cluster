<?php
/**
 * Generate Site Screenshot(s)
 *
 * @see https://url2png.com/dashboard/#sample_code
 * @example
 *
 *
 *    $_image = siteScreenshot::worker( array(
 *      "url" => "google.com",
 *      "force" => false,
 *      "fullpage" => false,
 *      "thumbnail_max_width" => false,
 *      "viewport" => "1280x1024"
 *    ));
 *
 *    echo "<img src='{$_image->url}'> />
 *
 */
class siteScreenshot {

  /**
   * Worker.
   *
   * @param {Object} $job
   *
   * @return {Object}
   */
  static public function worker( $job ) {

    $options = $job->post_content;

    # Get your apikey from http://url2png.com/plans
    $URL2PNG_APIKEY = "PXXXX";
    $URL2PNG_SECRET = "SXXXX";

    # urlencode request target
    $options[ 'url' ] = urlencode( $url );

    $options += $args;

    # create the query string based on the options
    foreach( $options as $key => $value ) {
      $_parts[ ] = "$key=$value";
    }

    # create a token from the ENTIRE query string
    $query_string = implode( "&", $_parts );
    $TOKEN        = md5( $query_string . $URL2PNG_SECRET );

    //return "http://beta.url2png.com/v6/$URL2PNG_APIKEY/$TOKEN/png/?$query_string";

    return $job;
  }

}
