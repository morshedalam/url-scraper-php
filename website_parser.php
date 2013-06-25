<?php
/**
 * A Website parser class
 *
 * Grab website contents and extracts all hyper links and image sources
 *
 * @package WebsiteParser
 * @author Morshed Alam <morshed201@gmail.com>
 * @link http://www.scripts.morshed-alam.com/scrapping/
 * @website http://morshed-alam.com
 */
class WebsiteParser
{
    /**
     * Links type
     */
    const LINK_TYPE_UNKNOWN   = 0;
    const LINK_TYPE_ALL      = 1;
    const LINK_TYPE_INTERNAL = 2;
    const LINK_TYPE_EXTERNAL = 3;

    /**
     * Link type
     * @var integer
     */
    protected $link_type = self::LINK_TYPE_ALL;

    /**
     * The target website url to parse
     * @var string
     */
    public $target_url = '';

    /**
     * Base Url from target website
     * @var string
     */
    public $base_url = '';

    /**
     * Full website Url
     * @var  string
     */
    public $absolute_url = '';

    /**
     * Only domain name
     * @var string
     */
    protected $domain = '';

    /**
     * Grabbed html content from target website
     * @var text
     */
    public $content = null;

    /**
     * Hyper links
     * @var array
     */
    public $href_links = array();

    /**
     * Image sources
     * @var array
     */
    public $image_sources = array();

    /**
     * Regular expression
     * @full_link_pattern To match urls containing protocol
     * @href_filter_pattern Filter out invalid hyper links
     * @href_expression Extract hyper links
     * @img_expression Extract image sources
     */
    private $full_link_pattern = '/\/\/|www\.|mailto:/';
    private $href_filter_pattern = '/\<|#|javascript:void/';
    private $href_expression = '/\<a\s[^>]*href\s*=\s*\"([^\"]*)\"[^>]*>(.*?)<\/a>/';
    private $img_expression = '/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/';
    private $external_link_pattern = "/^(https?:){0,1}\/\/(www\.){0,1}(.*)/i";
    private $internal_link_pattern = "/^(https?:){0,1}\/\/(www\.){0,1}#domain#/i";

    /**
     * cUrl option
     * @var Array
     */
    private $curl_options = array(
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_FOLLOWLOCATION => true, // follow redirects
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_USERAGENT => "spider", // who am i
        CURLOPT_AUTOREFERER => true, // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 60, // timeout on connect
        CURLOPT_TIMEOUT => 120, // timeout on response
        CURLOPT_MAXREDIRS => 5, // stop after 10 redirects
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false
    );

    /**
     * Message of WebsiteParser
     * @var String
     */
    public $message = '';

    /**
     * Class constructor
     * @param string  $url  Target Url to parse
     */
    function __construct($url)
    {
        $this->target_url = $url;
        $this->setUrls();
    }

    /**
     * A public function to grab and return content
     * @params boolean $grab, flag to perform real time grab or use class content
     * @returned text $content, truncated text
     */
    public function getContent($grab = false)
    {
        if ($grab)
            $this->grabContent();

        return $this->content;
    }

    /**
     * Extract all href links from grab contents
     * @params boolean $grab, flag to perform real time grab or use class content
     * @returned array $href_links, an array with extracted hyper links
     */
    public function getHrefLinks($grab = true)
    {
        if ($grab)
            $this->grabContent();

        if (!is_null($this->content)) {
            preg_match_all($this->href_expression, $this->content, $match_links);

            $unique_urls = array_unique($match_links[1]);

            if (count($unique_urls)) {

                foreach ($unique_urls as $index => $url) {
                    $title = trim($match_links[2][$index] ? $match_links[2][$index] : $url);

                    if (!(preg_match($this->href_filter_pattern, $url, $filter_out_url)
                        || preg_match($this->href_filter_pattern, $title, $filter_out_link))
                    ) {
                        if (!preg_match($this->full_link_pattern, $url, $match))
                            $url = $this->sanitizeUrl($url);

                        if ($this->link_type !== self::LINK_TYPE_ALL) {
                            if ($this->getLinkType($url) !== $this->link_type) continue;
                        }

                        $this->href_links[] = array($url, $title);
                    }
                }
            }
        }

        return $this->href_links;

    }

    /**
     * Extract all images sources from grabbed contents
     * @param boolean $grab, flag to perform real time grab or use class content
     * @return array, an array of extracted images sources
     */
    public function getImageSources($grab = false)
    {
        if ($grab)
            $this->grabContent();

        if (!is_null($this->content)) {

            preg_match_all($this->img_expression, $this->content, $match_images);

            foreach ($match_images[2] as $match_image) {

                $match_image = trim($match_image);

                if ($match_image) {

                    if (!preg_match($this->full_link_pattern, $match_image, $match))
                        $match_image = $this->sanitizeUrl($match_image);

                    $this->image_sources[] = $match_image;
                }
            }
        }

        $this->image_sources = array_values(array_unique(array_filter($this->image_sources)));

        return $this->image_sources;

    }

    /**
     * Truncate text in to preferred length
     * @params text $text, input text to truncate
     * @params int $length int, how many character to keep
     * @params string $replace_by string, text to explain continuity
     * @returned text $text, truncated text
     */
    public function truncateText($text, $length = 50, $replace_by = '...')
    {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - 3) . $replace_by;
        }

        return $text;
    }

    /**
     * Prepare base and full url from given website link to grab
     */
    private function setUrls()
    {
        $host = parse_url($this->target_url, PHP_URL_HOST);
        $host = $host ? $host : parse_url($this->target_url, PHP_URL_PATH);
        $this->base_url = 'http://' . rtrim($host, '/');
        $this->domain = $host;
        $this->internal_link_pattern = str_replace("#domain#", $this->domain, $this->internal_link_pattern);

        $this->absolute_url = substr($this->target_url, 0, strrpos($this->target_url, '/'));
        $this->absolute_url = $this->absolute_url ? $this->absolute_url . '/' : $this->base_url;
    }

    /**
     * A private method grabs website content using cUrl
     * And put content it into a class variable
     * Can be replace by file_get_contents() but it's very slow, cpu intensive
     * and does not handle redirects, caching, cookies, etc.
     */
    private function grabContent()
    {

        try {
            $ch = curl_init($this->target_url);

            curl_setopt_array($ch, $this->curl_options);

            $this->content = curl_exec($ch);

            if ($this->content === FALSE) {
                throw new Exception();
            }

        } catch (Exception $e) {
            $this->message = 'Unable to grab site contents';
        }

        curl_close($ch);
    }

    private function sanitizeUrl($url)
    {
        if (strpos($url, '/') == 0) {
            $url = $this->base_url . $url;
        } else {
            $url = $this->absolute_url . $url;
        }
        return $url;
    }

    private function getLinkType($url) {
        if (preg_match($this->internal_link_pattern, $url))
            return self::LINK_TYPE_INTERNAL;
        else if (preg_match($this->external_link_pattern, $url))
            return self::LINK_TYPE_EXTERNAL;
        return self::LINK_TYPE_UNKNOWN;
    }

    public function setLinksType($type)
    {
        $this->link_type = (int)$type;
    }
}