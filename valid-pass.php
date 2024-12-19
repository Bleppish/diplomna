<?php
function validate_password($password) {
    return !(strlen($password) < 8 || 
             !preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password) || 
             !preg_match('/[A-Z]/', $password) || 
             !preg_match('/[a-z]/', $password));
}
?>
