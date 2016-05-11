<?php
/**
 * Query Theme Data as JSON
**/
$data = fx_updater_theme_data( $_REQUEST );
header( 'Content-Type: application/json' );
echo json_encode( $data );