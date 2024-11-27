<?php
require_once('includes/load.php');

/*--------------------------------------------------------------*/
/* Function for find all database table rows by table name
/*--------------------------------------------------------------*/
function find_all($table) {
    global $db;
    if (tableExists($table)) {
        return find_by_sql("SELECT * FROM " . $db->escape($table));
    }
}

/*--------------------------------------------------------------*/
/* Function for Perform queries
/*--------------------------------------------------------------*/
function find_by_sql($sql) {
    global $db;
    $result = $db->query($sql);
    $result_set = $db->while_loop($result);
    return $result_set;
}

/*--------------------------------------------------------------*/
/* Function for Find data from table by id
/*--------------------------------------------------------------*/
function find_by_id($table, $id) {
    global $db;
    $id = (int)$id;
    if (tableExists($table)) {
        $sql = $db->query("SELECT * FROM {$db->escape($table)} WHERE id='{$db->escape($id)}' LIMIT 1");
        if ($result = $db->fetch_assoc($sql))
            return $result;
        else
            return null;
    }
}

/*--------------------------------------------------------------*/
/* Function for Delete data from table by id
/*--------------------------------------------------------------*/
function delete_by_id($table, $id) {
    global $db;
    if (tableExists($table)) {
        $sql = "DELETE FROM " . $db->escape($table);
        $sql .= " WHERE id=" . $db->escape($id);
        $sql .= " LIMIT 1";
        $db->query($sql);
        return ($db->affected_rows() === 1) ? true : false;
    }
}

/*--------------------------------------------------------------*/
/* Function for Count id By table name
/*--------------------------------------------------------------*/
function count_by_id($table) {
    global $db;
    if (tableExists($table)) {
        $sql = "SELECT COUNT(id) AS total FROM " . $db->escape($table);
        $result = $db->query($sql);
        return ($db->fetch_assoc($result));
    }
}

/*--------------------------------------------------------------*/
/* Determine if database table exists
/*--------------------------------------------------------------*/
function tableExists($table) {
    global $db;
    $table_exit = $db->query('SHOW TABLES FROM ' . DB_NAME . ' LIKE "' . $db->escape($table) . '"');
    if ($table_exit) {
        if ($db->num_rows($table_exit) > 0)
            return true;
        else
            return false;
    }
}

/*--------------------------------------------------------------*/
/* Login with the data provided in $_POST, coming from the login form.
/*--------------------------------------------------------------*/
function authenticate($username = '', $password = '') {
    global $db;

    $username = $db->escape($username);
    $password = $db->escape($password);

    $sql = "SELECT id, username, password, user_level FROM users WHERE username = '{$username}' LIMIT 1";

    // Debug output for SQL query
    echo "SQL Query: $sql\n"; // Print the query being executed

    // Execute the query
    $result = $db->query($sql);

    // Check for SQL errors
    if (!$result) {
        die("Database query failed: " . $db->error); // Show error if the query fails
    }

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $password_request = sha1($password); // Hash the password for comparison
        
        if ($password_request === $user['password']) {
            return $user['id']; // Return user ID if password matches
        }
    }

    return false; // Return false if authentication fails
}







/*--------------------------------------------------------------*/
/* Find current log in user by session id
/*--------------------------------------------------------------*/
function current_user() {
    static $current_user;
    global $db;
    if (!$current_user) {
        if (isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);
            $current_user = find_by_id('users', $user_id);
        }
    }
    return $current_user;
}

/*--------------------------------------------------------------*/
/* Find all user by Joining users table and user groups table
/*--------------------------------------------------------------*/
function find_all_user() {
    global $db;
    $results = array();
    $sql = "SELECT u.id, u.name, u.username, u.user_level, u.status, u.last_login, g.group_name ";
    $sql .= "FROM users u ";
    $sql .= "LEFT JOIN user_groups g ON g.group_level=u.user_level ORDER BY u.name ASC";
    $result = find_by_sql($sql);
    return $result;
}

/*--------------------------------------------------------------*/
/* Function to update the last log in of a user
/*--------------------------------------------------------------*/
function updateLastLogIn($user_id) {
    global $db;
    $date = make_date();
    $sql = "UPDATE users SET last_login='{$date}' WHERE id ='{$user_id}' LIMIT 1";
    $result = $db->query($sql);
    return ($result && $db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Find all Group name
/*--------------------------------------------------------------*/
function find_by_groupName($val) {
    global $db;
    $sql = "SELECT group_name FROM user_groups WHERE group_name = '{$db->escape($val)}' LIMIT 1 ";
    $result = $db->query($sql);
    return ($db->num_rows($result) === 0 ? true : false);
}

/*--------------------------------------------------------------*/
/* Find group level
/*--------------------------------------------------------------*/
function find_by_groupLevel($level) {
    global $db;
    if (empty($level)) {
        return null; // Return null if level is empty
    }
    $sql = "SELECT group_level FROM user_groups WHERE group_level = '{$db->escape($level)}' LIMIT 1 ";
    $result = $db->query($sql);
    return ($db->num_rows($result) === 0 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for checking which user level has access to page
/*--------------------------------------------------------------*/
function page_require_level($require_level) {
    global $session;
    $current_user = current_user();
    $login_level = find_by_groupLevel($current_user['user_level']);

    // Check if the user is logged in
    if (!$session->isUserLoggedIn(true)) {
        $session->msg('d', 'Please login...');
        redirect('index.php', false);
    }

    // If group status is inactive
    if ($login_level !== null && $login_level['group_status'] === '0') {
        $session->msg('d', 'This level user has been banned!');
        redirect('home.php', false);
    }

    // Checking if the logged-in User level is less than or equal to required level
    if ($current_user['user_level'] <= (int)$require_level) {
        return true;
    } else {
        $session->msg("d", "Sorry! you don't have permission to view the page.");
        redirect('home.php', false);
    }
}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* JOIN with categorie, media, and supplier database tables
/*--------------------------------------------------------------*/
function join_product_table() {
    global $db;
    $sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.media_id, p.date, c.name AS categorie, m.file_name AS image, s.name AS supplier_name ";
    $sql .= "FROM products p ";
    $sql .= "LEFT JOIN categories c ON c.id = p.categorie_id ";
    $sql .= "LEFT JOIN media m ON m.id = p.media_id ";
    $sql .= "LEFT JOIN suppliers s ON s.id = p.supplier_id "; // Join suppliers table
    $sql .= "ORDER BY p.id ASC";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Finding all product name
/* Request coming from ajax.php for auto suggest
/*--------------------------------------------------------------*/
function find_product_by_title($product_name) {
    global $db;
    $p_name = remove_junk($db->escape($product_name));
    $sql = "SELECT name FROM products WHERE name like '%$p_name%' LIMIT 5";
    $result = find_by_sql($sql);
    return $result;
}

/*--------------------------------------------------------------*/
/* Function for Finding all product info by product title
/* Request coming from ajax.php
/*--------------------------------------------------------------*/
function find_all_product_info_by_title($title) {
    global $db;
    $sql = "SELECT * FROM products ";
    $sql .= "WHERE name ='{$title}' ";
    $sql .= "LIMIT 1";
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Update product quantity
/*--------------------------------------------------------------*/
function update_product_qty($qty, $p_id) {
    global $db;
    $qty = (int)$qty;
    $id = (int)$p_id;
    $sql = "UPDATE products SET quantity=quantity - '{$qty}' WHERE id = '{$id}'";
    $result = $db->query($sql);
    return ($db->affected_rows() === 1 ? true : false);
}

/*--------------------------------------------------------------*/
/* Function for Display Recent product Added
/*--------------------------------------------------------------*/
function find_recent_product_added($limit) {
    global $db;
    $sql = "SELECT p.id, p.name, p.media_id, c.name AS categorie, m.file_name AS image FROM products p ";
    $sql .= "LEFT JOIN categories c ON c.id = p.categorie_id ";
    $sql .= "LEFT JOIN media m ON m.id = p.media_id ";
    $sql .= "ORDER BY p.id DESC LIMIT " . $db->escape((int)$limit);
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Find Highest selling Products
/*--------------------------------------------------------------*/
function find_highest_selling_products($limit) {
    global $db;
    $sql = "SELECT p.id, p.name, p.sale_price, SUM(s.quantity) AS total_sold ";
    $sql .= "FROM products p ";
    $sql .= "LEFT JOIN sales s ON s.product_id = p.id ";
    $sql .= "GROUP BY p.id ";
    $sql .= "ORDER BY total_sold DESC LIMIT " . $db->escape((int)$limit);
    return find_by_sql($sql);
}

/*--------------------------------------------------------------*/
/* Function for Find All Categories
/*--------------------------------------------------------------*/
function find_all_categories() {
    global $db;
    return find_by_sql("SELECT * FROM categories ORDER BY name ASC");
}

/*--------------------------------------------------------------*/
/* Function for Find All Media
/*--------------------------------------------------------------*/
function find_all_media() {
    global $db;
    return find_by_sql("SELECT * FROM media ORDER BY id ASC");
}

/*--------------------------------------------------------------*/
/* Function for Find All Suppliers
/*--------------------------------------------------------------*/
function find_all_suppliers() {
    global $db;
    return find_by_sql("SELECT * FROM suppliers ORDER BY id ASC");
}

/*--------------------------------------------------------------*/
/* Function for Finding all damage product name
/* JOIN with categorie, media, and supplier database tables
/*--------------------------------------------------------------*/
function join_dam_product_table() {
    global $db;
    $sql = "SELECT dp.id, dp.name, dp.quantity, dp.buy_price, dp.media_id, dp.date, c.name AS categorie, m.file_name AS image, s.name AS supplier_name ";
    $sql .= "FROM dam_product dp ";
    $sql .= "LEFT JOIN categories c ON c.id = dp.categorie_id ";
    $sql .= "LEFT JOIN media m ON m.id = dp.media_id ";
    $sql .= "LEFT JOIN suppliers s ON s.id = dp.supplier_id "; // Join suppliers table
    $sql .= "ORDER BY dp.id ASC";
    return find_by_sql($sql);
}



?>
