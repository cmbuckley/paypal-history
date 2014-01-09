<?php

require_once '../vendor/autoload.php';

use Starsquare\PayPal\Converter;

if (isset($_FILES['file'])) {
    echo new Converter($_FILES['file']['tmp_name'], $_POST);
    return;
}

?>
<!doctype html>
<html>
<body>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="converter[sendHeaders]" value="yes" />

        <label for="file">File:</label>
        <input type="file" name="file" id="file" />

        <input type="submit" />
    </form>
</body>
</html>
