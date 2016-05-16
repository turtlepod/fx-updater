<?php
$data = fx_updater_plugin_information();
nocache_headers();
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Expires: 0' );
echo json_encode( $data );
exit;