<?php
include 'website_parser.php';

//Instance of WebsiteParser
$parser = new WebsiteParser('http://morshed-alam.com/');

//Get all hyper links
$links = $parser->getHrefLinks();

//Get all image sources
$images = $parser->getImageSources();

echo "<pre>";
print_r($links);
echo "<br />";
print_r($images);
echo "</pre>";


/**
 * ==========================================
 * Sample Output
 * ==========================================
Array
(
    [0] => Array
        (
            [0] => https://github.com/joliss/jquery-ui-rails
            [1] => jquery-ui-rails
        )

    [1] => Array
        (
            [0] => http://gembundler.com/rails23.html
            [1] => Click here
        )

    [2] => Array
        (
            [0] => https://www.box.com/shared/9f75gl4v45xt8cxs0xhe
            [1] => Resume
        )

    [3] => Array
        (
            [0] => https://github.com/morshedalam
            [1] => Github Profile
        )

    [4] => Array
        (
            [0] => https://www.odesk.com/users/%7E%7E284d2f8a720839c5
            [1] => oDesk Profile
        )

    [5] => Array
        (
            [0] => http://www.linkedin.com/in/morshed
            [1] => Linkedin Profile
        )

    [6] => Array
        (
            [0] => http://stackoverflow.com/users/1193139/morshed-alam
            [1] => StackOverflow
        )

    [7] => Array
        (
            [0] => http://workingwithrails.com/person/157938-morshed
            [1] => Working With Rails
    )

    [8] => Array
        (
            [0] => http://www.facebook.com/morshed.alam.bd
            [1] => Facebook Page
        )

)
 *
Array
(
    [0] => http://img1.blogblog.com/img/icon18_wrench_allbkg.png
    [1] => http://img1.blogblog.com/img/icon18_email.gif
    [2] => http://img2.blogblog.com/img/icon18_edit_allbkg.gif
    [3] => http://morshed-alam.com/images/test.png
    [4] => //lh5.googleusercontent.com/-azur_ybZk6k/AAAAAAAAAAI/AAAAAAAACUM/C2U5WL3KX84/s512-c/photo.jpg
)
 */