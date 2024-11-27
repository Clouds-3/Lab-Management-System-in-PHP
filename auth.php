<?php include_once('includes/load.php'); ?>

<?php
$req_fields = array('username', 'password');
validate_fields($req_fields);

$username = remove_junk($_POST['username']);
$password = remove_junk($_POST['password']);

if (empty($errors)) {
    // Authenticate user
    $user_id = authenticate($username, $password);
    if ($user_id) {
        // Create session with id
        $session->login($user_id);
        // Update Sign in time
        updateLastLogIn($user_id);
        $session->msg("s", "Welcome to Inventory Management System");
        redirect('admin.php', false);
    } else {
        $session->msg("d", "Sorry, Username/Password incorrect.");
        redirect('index.php', false);
    }
} else {
    $session->msg("d", $errors);
    redirect('index.php', false);
}
?>