<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="shortcut icon" href="<?php echo BASE_URL ?>assets/img/logo.png" type="image/x-icon">
<link rel="stylesheet" href="<?php echo BASE_URL ?>assets/css/styles.css?v=<?php echo time(); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">