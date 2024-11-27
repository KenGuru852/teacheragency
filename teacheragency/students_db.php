<?php
require 'db.php';

function get_students() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM students");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_student($id) {
    global $mysqli;
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function add_student($fio, $phone, $email, $address, $description) {
    global $mysqli;
    $fio = $mysqli->real_escape_string($fio);
    $phone = $mysqli->real_escape_string($phone);
    $email = $mysqli->real_escape_string($email);
    $address = $mysqli->real_escape_string($address);
    $description = $mysqli->real_escape_string($description);

    if (empty($fio) || empty($phone) || empty($email) || empty($address) || empty($description)) {
        show_error("Все поля должны быть заполнены.");
        return;
    }

    $fio_pattern = "/^[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u";
    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

    if (!preg_match($fio_pattern, $fio)) {
        show_error("Неверный формат ФИО.");
    } elseif (!check_unique_phone($mysqli, $phone)) {
        show_error("Такой телефон уже существует.");
    } elseif (!check_unique_email($mysqli, $email)) {
        show_error("Такой email уже существует.");
    } else {
        $sql = "INSERT INTO students (fio, phone, email, address, description) 
                VALUES ('$fio', '$phone', '$email', '$address', '$description')";
        if ($mysqli->query($sql) === TRUE) {
            echo "Новый студент успешно добавлен.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

function edit_student($id, $fio, $phone, $email, $address, $description) {
    global $mysqli;
    $id = (int)$id;
    $fio = $mysqli->real_escape_string($fio);
    $phone = $mysqli->real_escape_string($phone);
    $email = $mysqli->real_escape_string($email);
    $address = $mysqli->real_escape_string($address);
    $description = $mysqli->real_escape_string($description);

    if (empty($fio) || empty($phone) || empty($email) || empty($address) || empty($description)) {
        show_error("Все поля должны быть заполнены.");
        return;
    }

    $fio_pattern = "/^[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+\s[А-ЯЁ][а-яё]+$/u";
    $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

    if (!preg_match($fio_pattern, $fio)) {
        show_error("Неверный формат ФИО.");
    } elseif (!check_unique_phone($mysqli, $phone, $id)) {
        show_error("Такой телефон уже существует.");
    } elseif (!check_unique_email($mysqli, $email, $id)) {
        show_error("Такой email уже существует.");
    } else {
        $sql = "UPDATE students SET fio = ?, phone = ?, email = ?, address = ?, description = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssssi", $fio, $phone, $email, $address, $description, $id);

        if ($stmt->execute()) {
            echo "Запись успешно обновлена.";
        } else {
            echo "Ошибка при обновлении записи: " . $stmt->error;
        }
        $stmt->close();
    }
}

function delete_student($id) {
    global $mysqli;
    $id = (int)$id;
    $mysqli->query("DELETE FROM students WHERE id = $id");
}

function show_error($message) {
    echo "<p>Ошибка: $message</p>";
}

function check_unique_phone($mysqli, $phone, $id = null) {
    $check_phone_sql = "SELECT * FROM students WHERE phone = '$phone'";
    if ($id) {
        $check_phone_sql .= " AND id != $id";
    }
    $check_phone_result = $mysqli->query($check_phone_sql);
    return $check_phone_result->num_rows === 0;
}

function check_unique_email($mysqli, $email, $id = null) {
    $check_email_sql = "SELECT * FROM students WHERE email = '$email'";
    if ($id) {
        $check_email_sql .= " AND id != $id";
    }
    $check_email_result = $mysqli->query($check_email_sql);
    return $check_email_result->num_rows === 0;
}
?>