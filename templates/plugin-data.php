<?php
/**
 * Query Plugin Data as JSON
**/
$data = fx_updater_plugin_data();
header( 'Content-Type: application/json' );
echo json_encode( $data );