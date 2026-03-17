<?php
$tmp = tmpfile();
if ($tmp === false) {
    echo "tmpfile() FAILED" . PHP_EOL;
} else {
    echo "tmpfile() SUCCESS: " . stream_get_meta_data($tmp)['uri'] . PHP_EOL;
    fclose($tmp);
}
