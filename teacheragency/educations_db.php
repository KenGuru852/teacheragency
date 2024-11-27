<?php
require 'db.php';

function get_educations() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM educations");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_education($id) {
    global $mysqli;
    $sql = "SELECT * FROM educations WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function add_education($name) {
    global $mysqli;
    $name = $mysqli->real_escape_string($name);
    $check_sql = "SELECT * FROM educations WHERE name = '$name'";
    $check_result = $mysqli->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "Ошибка: Такое название уже существует.";
    } else {
        $sql = "INSERT INTO educations (name) VALUES ('$name')";
        if ($mysqli->query($sql) === TRUE) {
            echo "Новое образование успешно добавлено.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

function edit_education($id, $name) {
    global $mysqli;
    $id = (int)$id;
    $name = $mysqli->real_escape_string($name);
    $check_sql = "SELECT * FROM educations WHERE name = '$name' AND id != $id";
    $check_result = $mysqli->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "Ошибка: Такое название уже существует.";
    } else {
        $sql = "UPDATE educations SET name = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("si", $name, $id);

        if ($stmt->execute()) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    }
}

function delete_education($id) {
    global $mysqli;
    $id = (int)$id;
    $mysqli->query("DELETE FROM teachers WHERE education_id = $id");
    $mysqli->query("DELETE FROM educations WHERE id = $id");
}
?>