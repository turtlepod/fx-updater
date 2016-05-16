<?php
/**
 * Query Theme Data as JSON
**/
$data = fx_updater_theme_data();
nocache_headers();
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Expires: 0' );
echo json_encode( $data );
exit;