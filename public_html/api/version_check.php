<?php
require_once '../version.php';

echo json_encode(array(
    // 'version' => G_VERSION,
    // 'build_day' => G_VERSION_BUILD,
    'version_for_you' => '8.8.8',
    'build_day_for_you' => '2018.8.8',
    'msg_for_you' => 'Hello World'
));
