<?php
  $page_title = 'Edit Supplier';
  require_once('includes/load.php');
  date_default_timezone_set('Asia/Kolkata');
  // Check what level user has permission to view this page
  page_require_level(1);
?>
<?php
  // Fetch supplier by id
  $supplier = find_by_id('suppliers', (int)$_GET['id']);
  if(!$supplier){
    $session->msg("d", "Missing supplier id.");
    redirect('supplier.php');
  }
?>

<?php
if(isset($_POST['edit_supplier'])){
  $req_fields = array('supplier-name', 'place', 'shop-name');
  validate_fields($req_fields);
  
  $supplier_name = remove_junk($db->escape($_POST['supplier-name']));
  $place = remove_junk($db->escape($_POST['place']));
  $shop_name = remove_junk($db->escape($_POST['shop-name']));
  
  if(empty($errors)){
    $sql = "UPDATE suppliers SET name='{$supplier_name}', place='{$place}', shop_name='{$shop_name}'";
    $sql .= " WHERE id='{$supplier['id']}'";
    $result = $db->query($sql);
    if($result && $db->affected_rows() === 1) {
      $session->msg("s", "Successfully updated Supplier");
      redirect('supplier.php', false);
    } else {
      $session->msg("d", "Sorry! Failed to Update");
      redirect('supplier.php', false);
    }
  } else {
    $session->msg("d", $errors);
    redirect('edit_supplier.php?id=' . $supplier['id'], false);
  }
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="row">
   <div class="col-md-12">
     <?php echo display_msg($msg); ?>
   </div>
   <div class="col-md-5">
     <div class="panel panel-default">
       <div class="panel-heading">
         <strong>
           <span class="glyphicon glyphicon-th"></span>
           <span>Editing <?php echo remove_junk(ucfirst($supplier['name']));?></span>
        </strong>
       </div>
       <div class="panel-body">
         <form method="post" action="edit_supplier.php?id=<?php echo (int)$supplier['id'];?>">
           <div class="form-group">
               <input type="text" class="form-control" name="supplier-name" value="<?php echo remove_junk(ucfirst($supplier['name']));?>" placeholder="Supplier Name">
           </div>
           <div class="form-group">
               <input type="text" class="form-control" name="place" value="<?php echo remove_junk(ucfirst($supplier['place']));?>" placeholder="Place">
           </div>
           <div class="form-group">
               <input type="text" class="form-control" name="shop-name" value="<?php echo remove_junk(ucfirst($supplier['shop_name']));?>" placeholder="Shop Name">
           </div>
           <button type="submit" name="edit_supplier" class="btn btn-primary">Update Supplier</button>
       </form>
       </div>
     </div>
   </div>
</div>

<?php include_once('layouts/footer.php'); ?>
