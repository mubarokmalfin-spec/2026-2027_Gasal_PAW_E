<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Data</title>
</head>
<body>
    <h1>Register</h1>

    <?php
    require_once 'validate.inc';

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        validateName($errors, $_POST, 'surname');
        validateEmail($errors, $_POST, 'email');
        validatePassword($errors, $_POST, 'password');
        validateAddress($errors, $_POST, 'address');
        validateState($errors, $_POST, 'state');
        validateGender($errors, $_POST, 'gender');

        if (empty($errors)) {
            echo "<h1>Form submitted successfully with no errors</h1>";
        } else {
            echo "<h2>Invalid, correct the following errors:</h2>";
            foreach ($errors as $field => $error) {
                echo htmlspecialchars($field) . ": " . htmlspecialchars($error) . "<br>";
            }
            include 'form.inc';
        }
    } else {
        include 'form.inc';
    }
    ?>
</body>
</html>