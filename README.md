### About

A Website parser class to extract links and images from Web pages.
[View Demo](http://www.scripts.morshed-alam.com/url-scraper-php/) or Try it out and Rate on [phpclasses.org](http://www.phpclasses.org/package/8113-PHP-Parse-and-extract-links-and-images-from-Web-pages.html)
 

##### Uses

<pre><code>include 'website_parser.php';

//Instance of WebsiteParser
$parser = new WebsiteParser('http://morshed-alam.com/');

//Get all hyper links
$links = $parser->getHrefLinks();

//Get all image sources
$images = $parser->getImageSources();

//Get all metatags and Facebook open graph properties
$images = $parser->getMetaTags();</code></pre>

##### Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Added some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
