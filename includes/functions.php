<?php
// Prevent form resubmission by redirecting after success (PRG pattern)
function preventResubmission() {
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>