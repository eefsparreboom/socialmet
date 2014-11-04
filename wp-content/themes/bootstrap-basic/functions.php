<?php
/**
 * Bootstrap Basic theme
 * 
 * @package bootstrap-basic
 */


require get_template_directory() . '/inc/theme-functions.php';
$args = array(
	'width'         => 228,
	'height'        => 62, 
    'flex-width'    => true,
    'flex-height'    => true,
	'default-image' => get_template_directory_uri() . '/img/logo.png',
);
add_theme_support( 'custom-header', $args );

// Get URL of first image in a post
function catch_that_image() {
    global $post, $posts, $blog_id;
    if (has_post_thumbnail( $post->ID ) ){
        $url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        return $url;
    }
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];

    // no image found display default image instead
    if(empty($first_img)){
        $first_img = "";
    }
    return $first_img;
}

///textclipper
if (!function_exists('textClipper')) {

    function textClipper($mValue, $mAmount = null, $sLink = '', $bTrimToLastPunctuation = true) {
        $bAddReadMoreText = false;

        if (is_null($mAmount))
            return $mValue;

        $iCharacterAmount = strlen($mValue);

        if ($mAmount < $iCharacterAmount) {
            $mValue = substr($mValue, 0, $mAmount);

            if ($bTrimToLastPunctuation) {
                $iFinalLocation = 0;
                $iPunctuationToUse = -1;
                $aPunctuation = array(' ', '.', '!', '?');
                foreach ($aPunctuation as $iIndex => $sPunctuation) {
                    $iLocation = strrpos($mValue, $sPunctuation);
                    if ($iLocation !== false && $iLocation > $iFinalLocation) {
                        $iFinalLocation = $iLocation;
                        $iPunctuationToUse = $iIndex;
                    }
                }
                if ($iFinalLocation != 0 && $iPunctuationToUse != -1) {
                    //$sTrailingFragment = strrchr($mValue, $aPunctuation[$iPunctuationToUse]);
                    //$mValue = str_replace($sTrailingFragment, $aPunctuation[$iPunctuationToUse], $mValue);
                    $iTrailingFragmentPosition = strrpos($mValue, $aPunctuation[$iPunctuationToUse]);
                    $mValue = substr($mValue, 0, $iTrailingFragmentPosition + 1);
                }
            }
            $bAddReadMoreText = true;
        }
        /*
          if ($bAddReadMoreText) {
          if ($sLink == '')
          $mValue .= "... <a href='#' title='Coming Soon'>Read More</a>";
          else
          $mValue .= "... <a href='" . $sLink . "' title='Read More in the PDF Document' target='_blank'>Read More</a>";
          }
         */
        if ($bAddReadMoreText)
            $mValue .= "<span title='Read More'>...</span>";
        return $mValue;
    }

}