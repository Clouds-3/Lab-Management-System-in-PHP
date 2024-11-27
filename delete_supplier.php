<?php
  require_once('includes/load.php');
  // Check what level user has permission to view this page
  page_require_level(1);
?>
<?php
  // Fetch the supplier by ID
  $supplier = find_by_id('suppliers', (int)$_GET['id']);
  if(!$supplier){
    $session->msg("d", "Missing Supplier id.");
    redirect('supplier.php');
  }
?>
<?php
  // Attempt to delete the supplier
  $delete_id = delete_by_id('suppliers', (int)$supplier['id']);
  if($delete_id){
      $session->msg("s", "Supplier deleted.");
      redirect('supplier.php');
  } else {
      $session->msg("d", "Supplier deletion failed.");
      redirect('supplier.php');
  }
?>
