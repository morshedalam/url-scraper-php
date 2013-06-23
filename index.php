<?php
include 'class/website_parser.php';

$links = $images = array();
$target_url = $_GET['target_url'];
$default_check = 'checked';

$parser = new WebsiteParser($target_url);

if (isset($_GET['target_url'])) {
    $default_check = '';

    if ($_GET['href'])
        $links = $parser->getHrefLinks();

    if ($_GET['image'])
        $images = $parser->getImageSources(($_GET['href'] ? false : true));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Extract Urls</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Morshed Alam">

    <link href="../assets/stylesheets/bootstrap.css" rel="stylesheet">

    <style>
        ul li a {
            font-size: 10px;
        }

        .images {
            margin-left: 0px;
        }

        .images img {
            margin: 5px;
            max-width: 50px;
            max-height: 50px;
        }
        small.error{
            color: red;
            font-size: 10px;
        }
    </style>
</head>
<body>

<div class="container" style="margin-top: 60px;">
    <div>
        <h4>
            Extract website links
            <small class="error"><?=$parser->message ? ('( ' . $parser->message . ' )') : ''?></small>
        </h4>

        <form method="get" action="">

            <div class="input-prepend input-append">
                <input class="span2" type="text" style="width: 650px;height: 20px;"
                       value="<?=$target_url?>" name="target_url"
                       placeholder="Enter a public website URL with trailing slash"/>
                <span class="add-on">
                        <input type="checkbox" name="href" value="1" <?=$_GET['href'] ? 'checked' : $default_check?> /> Href
                        <input type="checkbox" name="image"
                               value="1" <?=$_GET['image'] ? 'checked' : $default_check?> /> Image
                </span>
                <input class="btn btn-primary" type="submit" name="extract" value="Extract Links"/>
            </div>
            <br/>
        </form>
        <?php include 'views/href.html.php'; ?>
        <?php include 'views/image.html.php'; ?>
    </div>
</div>
</body>
</html>