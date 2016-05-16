<?php
/**
 * Query Plugin Data as JSON
**/
$data = fx_updater_plugin_data();
nocache_headers();
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Expires: 0' );
echo json_encode( $data );
exit;