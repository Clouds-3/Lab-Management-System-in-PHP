<?php
require_once('includes/load.php');
// Check what level user has permission to view this page
page_require_level(2);

// Retrieve the damaged product by ID
$product = find_by_id('dam_product', (int)$_GET['id']);
if (!$product) {
    $session->msg("d", "Missing Damaged Product id.");
    redirect('damage.php');
}

// Attempt to delete the damaged product by ID
$delete_id = delete_by_id('dam_product', (int)$product['id']);
if ($delete_id) {
    $session->msg("s", "Damaged Product deleted.");
    redirect('damage.php');
} else {
    $session->msg("d", "Damaged Product deletion failed.");
    redirect('damage.php');
}
?>
