<?php
function validate_password($password) {
    // Return true if the password is valid, i.e., it meets all conditions.
    return !(strlen($password) < 8 || 
             !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password) || 
             !preg_match('/[A-Z]/', $password) || 
             !preg_match('/[a-z]/', $password));
}
?>
