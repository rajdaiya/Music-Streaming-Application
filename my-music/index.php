<?php

if (isset($_SESSION['username'])) {
    header("Location: user-dashboard.php");
} else {
    header("Location: login.html");
}
?>

