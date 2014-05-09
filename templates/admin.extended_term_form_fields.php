<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Andy
 * Date: 1/25/13
 * Time: 1:02 PM
 *
 */
?>

  <style type="text/css">
  .flawless_extended_taxonomy_meta td label > input.regular-text {
    position: absolute;
    left: 120px;
  }

  .flawless_extended_taxonomy_meta td label {
    position: relative;
  }
</style>

<?php if( $tag->taxonomy == 'hdp_venue' ) { ?>
  <tr class="flawless_extended_taxonomy_meta">
    <th scope="row" valign="top">Address</th>
    <td>
      <input type="text" name="post_meta[formatted_address]" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'formatted_address', true ); ?>"/>
    </td>
  </tr>
<?php } ?>

<?php switch( $tag->taxonomy ) {
  case 'hdp_promoter':
  case 'hdp_venue':
  case 'hdp_credit':
  case 'credit':
  case 'hdp_artist':
    ?>
    <tr class="flawless_extended_taxonomy_meta">
  <th scope="row" valign="top">URLs and Accounts</th>
  <td>
    <ul>
      <li>
        <label>Facebook:
          <input type="text" name="post_meta[hdp_facebook_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_facebook_url', true ); ?>"/>
        </label>
      </li>
      <li>
        <label>Official Website:
          <input type="text" name="post_meta[hdp_website_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_website_url', true ); ?>"/>
        </label>
      </li>
      <li>
        <label>YouTube:
          <input type="text" name="post_meta[hdp_youtube_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_youtube_url', true ); ?>"/>
        </label>
      </li>
      <li>
        <label>Google Plus:
          <input type="text" name="post_meta[hdp_google_plus_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_google_plus_url', true ); ?>"/>
        </label>
      </li>
      <li>
        <label>Twitter:
          <input type="text" name="post_meta[hdp_twitter_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_twitter_url', true ); ?>"/>
        </label>
      </li>
      <?php if( $tag->taxonomy == 'hdp_venue' && false ) {
        /** Depreciating - JBRW */
        ?>
        <li>
          <label>Ticket Purchase:
            <input type="text" name="post_meta[hdp_purchase_url]" placeholder="" class="regular-text" value="<?php echo get_term_meta( $tag->term_id, 'hdp_purchase_url', true ); ?>"/>
          </label>
        </li>
      <?php } ?>
    </ul></td>
</tr>

    <?php
    break;

}
