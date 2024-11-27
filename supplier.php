<?php
  $page_title = 'All Suppliers';
  require_once('includes/load.php');
  date_default_timezone_set('Asia/Kolkata');
  // Check what level user has permission to view this page
  page_require_level(2);

  // Function to fetch all suppliers
  $all_suppliers = find_all('suppliers'); // Ensure this function returns an array
?>
<?php
 if(isset($_POST['add_supplier'])){
   $req_fields = array('supplier-name', 'place', 'shop-name');
   validate_fields($req_fields);
   $supplier_name = remove_junk($db->escape($_POST['supplier-name']));
   $place = remove_junk($db->escape($_POST['place']));
   $shop_name = remove_junk($db->escape($_POST['shop-name']));

   if(empty($errors)){
      $sql  = "INSERT INTO suppliers (name, place, shop_name)";
      $sql .= " VALUES ('{$supplier_name}', '{$place}', '{$shop_name}')";
      if($db->query($sql)){
        $session->msg("s", "Successfully Added New Supplier");
        redirect('supplier.php',false);
      } else {
        $session->msg("d", "Sorry Failed to insert.");
        redirect('supplier.php',false);
      }
   } else {
     $session->msg("d", $errors);
     redirect('supplier.php',false);
   }
 }
?>
<?php include_once('layouts/header.php'); ?>

  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
  </div>
   <div class="row">
    <div class="col-md-5">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Supplier</span>
         </strong>
        </div>
        <div class="panel-body">
          <form method="post" action="supplier.php">
            <div class="form-group">
                <input type="text" class="form-control" name="supplier-name" placeholder="Supplier Name" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="place" placeholder="Place" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="shop-name" placeholder="Shop Name" required>
            </div>
            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-7">
    <div class="panel panel-default">
      <div class="panel-heading">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Suppliers</span>
       </strong>
      </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">#</th>
                    <th>Supplier Name</th>
                    <th>Place</th>
                    <th>Shop Name</th>
                    <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
              <?php if($all_suppliers && count($all_suppliers) > 0): // Check if $all_suppliers is valid and has data ?>
                <?php foreach ($all_suppliers as $supplier): ?>
                  <tr>
                      <td class="text-center"><?php echo count_id();?></td>
                      <td><?php echo remove_junk(ucfirst($supplier['name'])); ?></td>
                      <td><?php echo remove_junk(ucfirst($supplier['place'])); ?></td>
                      <td><?php echo remove_junk(ucfirst($supplier['shop_name'])); ?></td>
                      <td class="text-center">
                        <div class="btn-group">
                          <a href="edit_supplier.php?id=<?php echo (int)$supplier['id'];?>"  class="btn btn-xs btn-warning" data-toggle="tooltip" title="Edit">
                            <span class="glyphicon glyphicon-edit"></span>
                          </a>
                          <a href="delete_supplier.php?id=<?php echo (int)$supplier['id'];?>"  class="btn btn-xs btn-danger" data-toggle="tooltip" title="Remove">
                            <span class="glyphicon glyphicon-trash"></span>
                          </a>
                        </div>
                      </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                  <tr>
                      <td colspan="5" class="text-center">No suppliers found</td>
                  </tr>
              <?php endif; ?>
            </tbody>
          </table>
       </div>
    </div>
    </div>
   </div>
  </div>
<?php include_once('layouts/footer.php'); ?>
