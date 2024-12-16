<?php
function validate_name($name) {
    return preg_match('/^[A-Z][a-zA-Z-]*$/', $name);
}
?>