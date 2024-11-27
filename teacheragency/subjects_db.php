<?php
require 'db.php';

function get_subjects() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM subjects");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_subject($id) {
    global $mysqli;
    $sql = "SELECT * FROM subjects WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function add_subject($name) {
    global $mysqli;
    $name = $mysqli->real_escape_string($name);
    $sql = "INSERT INTO subjects (name) VALUES ('$name')";
    if ($mysqli->query($sql) === TRUE) {
        echo "Новый предмет успешно добавлен.";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
    }
}

function edit_subject($id, $name) {
    global $mysqli;
    $id = (int)$id;
    $name = $mysqli->real_escape_string($name);
    $sql = "UPDATE subjects SET name = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("si", $name, $id);

    if ($stmt->execute()) {
        echo "Record updated successfully";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

function delete_subject($id) {
    global $mysqli;
    $id = (int)$id;
    $mysqli->query("DELETE FROM subjects WHERE id = $id");
}
?>