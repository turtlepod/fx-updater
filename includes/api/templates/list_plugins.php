<?php
$data = fx_updater_list_plugins();
nocache_headers();
header( 'Content-Type: application/json; charset=utf-8' );
header( 'Expires: 0' );
echo json_encode( $data );
exit;