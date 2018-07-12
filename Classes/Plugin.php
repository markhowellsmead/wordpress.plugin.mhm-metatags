<?php

namespace MHM\Metatags;

class Plugin
{

    /**
     * Initialize the plugin functions
     */
    public function __construct()
    {
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
        add_action('wp_head', array($this, 'render'), 5, 0);
    }

    /**
     * Load translation files from the indicated directory.
     */
    public function loadTextDomain()
    {
        load_plugin_textdomain('mhm-metatags', false, dirname(plugin_basename(__FILE__)) . '/../languages');
    }

    /**
     * Add the individual code snippets to wp_head
     */
    public function render()
    {
        $this->meta_tags();
        $this->og_tags();
        $this->twitter_cards();
    }

    /**
     * Get the URL of the post thumbnail of the current post or page
     * @return string Image URL, or null if none set
     */
    private function get_thumbnail_src()
    {
        $thumbnail_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'post-thumbnail-full');

        if (is_array($thumbnail_image)) {
            return $thumbnail_image[0];
        } else {
            if (($video_ref = get_post_meta(get_the_ID(), 'video_ref', true)) !== '') {
                $thumbnail_image = $this->getVideoThumbnailSRC($video_ref);
                if (!empty($thumbnail_image)) {
                    return $thumbnail_image;
                }
            }
        }
        return null;
    }

    /**
     * Add regular meta tags to the head of the page
     */
    public function meta_tags()
    {
        $tags = array();
        if (is_tax('collection')) {
            $term = get_queried_object();
            $tags[] = '<meta name="description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';
        } elseif (is_tag()) {
            $term = get_queried_object();
            $tags[] = '<meta name="description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';
        } elseif (is_singular()) {
            $tags[] = '<meta name="description" content="' . get_the_excerpt() . '" />';
        }
        echo implode(chr(10), $tags);
    }

    /**
     * Add Twitter-specific meta tags to the head of the page
     */
    public function twitter_cards()
    {
        $tags = array();

        if (is_tax('collection')) {
            $term = get_queried_object();

            if (empty($term->description)) {
                $term->description = __('A photo collection by Mark Howells-Mead, at his website “Permanent Tourist”.', 'mhm-metatags');
            }

            $tags[] = '<meta name="twitter:card" content="summary_large_image" />';
            $tags[] = '<meta name="twitter:site" content="@howellsmead" />';
            $tags[] = '<meta name="twitter:creator" content="@howellsmead" />';
            $tags[] = '<meta name="twitter:title" content="' . $term->name . '" />';
            $tags[] = '<meta name="twitter:description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';

            $tax_image = get_field('taxonomy_image', $term);

            if ($tax_image) {
                $tags[] = '<meta name="twitter:image" content="' . $tax_image['images']['medium'] . '" />';
            }
        } elseif (is_tag()) {
            $tags[] = '<meta name="twitter:card" content="summary_large_image" />';
            $tags[] = '<meta name="twitter:site" content="@howellsmead" />';
            $tags[] = '<meta name="twitter:creator" content="@howellsmead" />';

            $term = get_queried_object();

            $tags[] = '<meta name="twitter:title" content="' . $term->name . '" />';
            $tags[] = '<meta name="twitter:description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';

            $tax_image = get_field('taxonomy_image', $term);
            if ($tax_image) {
                $tags[] = '<meta name="twitter:image" content="' . $tax_image['sizes']['medium'] . '" />';
            }
        } elseif (is_singular()) {
            $tags[] = '<meta name="twitter:card" content="summary_large_image" />';
            $tags[] = '<meta name="twitter:site" content="@howellsmead" />';
            $tags[] = '<meta name="twitter:creator" content="@howellsmead" />';
            $tags[] = '<meta name="twitter:title" content="' . get_the_title() . '" />';
            $tags[] = '<meta name="twitter:description" content="' . get_the_excerpt() . '" />';

            if ($thumbnail = $this->get_thumbnail_src()) {
                $tags[] = '<meta name="twitter:image" content="' . $thumbnail . '" />';
            }
        }

        echo implode(chr(10), $tags);
    }

    /**
     * Add Open Graph-specific meta tags to the head of the page
     */
    public function og_tags()
    {
        $tags = array();

        if (is_tax('collection')) {
            $term = get_queried_object();

            $term = get_queried_object();

            if (empty($term->description)) {
                $term->description = __('A photo collection by Mark Howells-Mead, at his website “Permanent Tourist”.', 'mhm-metatags');
            }

            $tags[] = '<meta property="fb:app_id" content="168749983148605" />';
            $tags[] = '<meta property="og:type" content="article" />';
            $tags[] = '<meta property="og:title" content="' . $term->name . '" />';
            $tags[] = '<meta property="og:description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';
            $tags[] = '<meta property="og:url" content="' . get_term_link($term->term_id) . '" />';
            $tags[] = '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
            $tags[] = '<meta property="article:publisher" content="https://www.facebook.com/mhowellsmead" />';

            $tax_image = get_field('taxonomy_image', $term);
            if ($tax_image) {
                $tags[] = '<meta property="og:image" content="' . $tax_image['sizes']['medium'] . '" />';
            }
        } elseif (is_tag()) {
            $term = get_queried_object();

            $tags[] = '<meta property="fb:app_id" content="168749983148605" />';
            $tags[] = '<meta property="og:type" content="article" />';
            $tags[] = '<meta property="og:title" content="' . $term->name . '" />';
            $tags[] = '<meta property="og:description" content="' . htmlentities(preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $term->description)) . '" />';
            $tags[] = '<meta property="og:url" content="' . get_term_link($term->term_id) . '" />';
            $tags[] = '<meta property="og:site_name" content="' . get_bloginfo('name') . '" />';
            $tags[] = '<meta property="article:publisher" content="https://www.facebook.com/mhowellsmead" />';

            $tax_image = get_field('taxonomy_image', $term);
            if ($tax_image) {
                $tags[] = '<meta property="og:image" content="' . $tax_image['sizes']['medium'] . '" />';
            }
        } elseif (is_singular()) {
            $tags[] = '<meta name="og:title" content="' . get_the_title() . '" />';
            $tags[] = '<meta name="og:description" content="' . get_the_excerpt() . '" />';
            if ($thumbnail = $this->get_thumbnail_src()) {
                $tags[] = '<meta name="og:image" content="' . $thumbnail . '" />';
            }
        }
        echo implode(chr(10), $tags);
    }

    /**
     * Get the SRC of a remote service video, based on the video's URL
     * @ref    https://code.google.com/apis/youtube/2.0/developers_guide_php.html#Understanding_Feeds_and_Entries
     * @param  string $source_url The URL of the video, e.g. https://www.youtube.com/watch?v=Cr2_Dn0e5nU
     * @return string             The URL of the applicable video thumbnail image
     */
    public function getVideoThumbnailSRC($source_url)
    {
        if ($source_url == '') {
            return '';
        }

        $video_path = parse_url($source_url);
        $video_host = str_replace('www.', '', $video_host);

        switch ($video_host) {
            case 'youtu.be':
                $video_id = preg_replace('~^/~', '', $video_path['path']);

                return 'https://i.ytimg.com/vi/' . $video_id . '/0.jpg';
                break;

            case 'youtube.com':
                $aParams = explode('&', $video_path['query']);
                foreach ($aParams as $param) {
                    $url_parameter = explode('=', $param);
                    if (strtolower($url_parameter[0]) == 'v') {
                        $video_id = $url_parameter[1];
                        break;
                    }
                }
                if (!$video_id) {
                    return '';
                } else {
                    return 'https://i.ytimg.com/vi/' . $video_id . '/0.jpg';
                }
                break;

            case 'vimeo.com':

                $urlParts = explode('/', $source_url);
                $hash = @unserialize(@file_get_contents('https://vimeo.com/api/v2/video/' . $urlParts[3] . '.php'));
                if ($hash && $hash[0] && (isset($hash[0]['thumbnail_large']) && $hash[0]['thumbnail_large'] !== '')) {
                    return $hash[0]['thumbnail_large'];
                } else {
                    return '';
                }
                break;

            default:
                return '';
                break;
        }
    }
}

new Plugin();
