<?php
/**
 * Query Group Data as JSON
**/
$data = fx_updater_group_data();
//$data = json_encode( $data );
//$data = json_decode( $data, true );
//ccdd( $data );
header( 'Content-Type: application/json' );
echo json_encode( $data );