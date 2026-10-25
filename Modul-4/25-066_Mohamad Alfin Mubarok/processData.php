<?php
require_once 'validate.inc';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateName($errors, $_POST, 'surname');
    validateEmail($errors, $_POST, 'email');
    validatePassword($errors, $_POST, 'password');
    validateAddress($errors, $_POST, 'address');
    validateState($errors, $_POST, 'state');
    validateGender($errors, $_POST, 'gender');

    if (empty($errors)) {
        echo "<h2>Data OK!</h2>";
        echo "<h3>Posted data:</h3>";
        foreach ($_POST as $key => $value) {
            echo "(" . htmlspecialchars($key) . ") => (" . htmlspecialchars((string)$value) . ")<br>";
        }
    } else {
        echo "<h2>Errors:</h2>";
        foreach ($errors as $field => $error) {
            echo htmlspecialchars($field) . ": " . htmlspecialchars($error) . "<br>";
        }
        echo '<p><a href="processData_form.html">Back to Form</a></p>';
    }
} else {
    echo "<p>No data submitted.</p>";
    echo '<p><a href="processData_form.html">Back to Form</a></p>';
}
?>