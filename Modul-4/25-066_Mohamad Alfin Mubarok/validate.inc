<?php
function validateName(&$errors, $data, $field)
{
    if (empty(trim($data[$field] ?? ""))) {
        $errors[$field] = "required";
    } elseif (!preg_match("/^[a-zA-Z'-]+$/", $data[$field])) {
        $errors[$field] = "invalid";
    }
}

function validateEmail(&$errors, $data, $field)
{
    if (empty(trim($data[$field] ?? ""))) {
        $errors[$field] = "required";
    } elseif (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
        $errors[$field] = "invalid email";
    }
}

function validatePassword(&$errors, $data, $field)
{
    if (empty($data[$field])) {
        $errors[$field] = "required";
    } elseif (strlen($data[$field]) < 8) {
        $errors[$field] = "minimal 8 karakter";
    }
}

function validateAddress(&$errors, $data, $field)
{
    if (empty(trim($data[$field] ?? ""))) {
        $errors[$field] = "required";
    }
}

function validateState(&$errors, $data, $field)
{
    $states = [
        "Australian Capital Territory",
        "Queensland",
        "Victoria",
        "New South Wales",
        "Tasmania",
        "South Australia",
        "Western Australia",
        "Northern Territory"
    ];

    if (empty($data[$field]) || !in_array($data[$field], $states, true)) {
        $errors[$field] = "pilihan state tidak valid";
    }
}

function validateGender(&$errors, $data, $field)
{
    $genders = ["Male", "Female"];

    if (empty($data[$field]) || !in_array($data[$field], $genders, true)) {
        $errors[$field] = "pilih jenis kelamin";
    }
}
?>