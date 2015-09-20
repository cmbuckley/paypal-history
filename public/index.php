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

        <label for="exporter">Output:</label>
        <select name="converter[exporter]" id="exporter">
            <option value="csv">CSV</option>
            <option value="ofx">OFX</option>
        </select>

        <input type="submit" />
    </form>
</body>
</html>
