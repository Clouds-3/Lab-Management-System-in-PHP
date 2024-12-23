<?php
  $page_title = 'Add Product';
  require_once('includes/load.php');
  date_default_timezone_set('Asia/Kolkata');
  // Check what level user has permission to view this page
  page_require_level(2);
  $all_categories = find_all('categories');
  $all_photo = find_all('media');
  $all_suppliers = find_all('suppliers'); // Fetch all suppliers
?>
<?php
 if(isset($_POST['add_damage_product'])){
   $req_fields = array('product-title','product-categorie','supplier-name','product-quantity','buying-price' );
   validate_fields($req_fields);
   if(empty($errors)){
     $p_name  = remove_junk($db->escape($_POST['product-title']));
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $s_name  = remove_junk($db->escape($_POST['supplier-name'])); // Supplier name
     $p_qty   = remove_junk($db->escape($_POST['product-quantity']));
     $p_buy   = remove_junk($db->escape($_POST['buying-price']));
     if (is_null($_POST['product-photo']) || $_POST['product-photo'] === "") {
       $media_id = '0';
     } else {
       $media_id = remove_junk($db->escape($_POST['product-photo']));
     }
     $date    = make_date();
     $query  = "INSERT INTO dam_product ("; // Changed 'products' to 'dam_product'
     $query .=" name, quantity, buy_price, categorie_id, supplier_id, media_id, date"; // Add supplier_id
     $query .=") VALUES (";
     $query .=" '{$p_name}', '{$p_qty}', '{$p_buy}', '{$p_cat}', '{$s_name}', '{$media_id}', '{$date}'"; // Add supplier value
     $query .=")";
     $query .=" ON DUPLICATE KEY UPDATE name='{$p_name}'";
     if($db->query($query)){
       $session->msg('s',"Damaged Product added ");
       redirect('add_damage_product.php', false);
     } else {
       $session->msg('d',' Sorry failed to add damaged product!');
       redirect('damaged_product.php', false);
     }

   } else{
     $session->msg("d", $errors);
     redirect('add_damage_product.php',false);
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
  <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Add New Damaged Product</span>
         </strong>
        </div>
        <div class="panel-body">
         <div class="col-md-12">
          <form method="post" action="add_damage_product.php" class="clearfix">
              <div class="form-group">
                <div class="input-group">
                  <span class="input-group-addon">
                   <i class="glyphicon glyphicon-th-large"></i>
                  </span>
                  <input type="text" class="form-control" name="product-title" placeholder="Product Title">
               </div>
              </div>
              
              <!-- Supplier Dropdown -->
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="supplier-name">
                      <option value="">Select Supplier Name</option>
                      <?php foreach ($all_suppliers as $supplier): ?>
                        <option value="<?php echo (int)$supplier['id'] ?>">
                          <?php echo $supplier['name'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <!-- Existing categories -->
                  <div class="col-md-6">
                    <select class="form-control" name="product-categorie">
                      <option value="">Select Product Category</option>
                      <?php foreach ($all_categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id'] ?>">
                          <?php echo $cat['name'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <select class="form-control" name="product-photo">
                      <option value="">Select Product Photo</option>
                      <?php foreach ($all_photo as $photo): ?>
                        <option value="<?php echo (int)$photo['id'] ?>">
                          <?php echo $photo['file_name'] ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="form-group">
               <div class="row">
                 <div class="col-md-4">
                   <div class="input-group">
                     <span class="input-group-addon">
                      <i class="glyphicon glyphicon-shopping-cart"></i>
                     </span>
                     <input type="number" class="form-control" name="product-quantity" placeholder="Product Quantity">
                  </div>
                 </div>
                 <div class="col-md-4">
                  <div class="input-group">
                    <span class="input-group-addon">
                      ₹ <!-- Add Rupee symbol directly -->
                    </span>
                    <input type="number" class="form-control" name="buying-price" placeholder="Buying Price">
                    <span class="input-group-addon">.00</span>
                  </div>
                </div>
               </div>
              </div>

              <button type="submit" name="add_damage_product" class="btn btn-danger">Add Product</button>
          </form>
         </div>
        </div>
      </div>
    </div>
  </div>

<?php include_once('layouts/footer.php'); ?>
