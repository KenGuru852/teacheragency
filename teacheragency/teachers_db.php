<?php
require 'db.php';

function get_teachers_with_education() {
    global $mysqli;
    $result = $mysqli->query("SELECT t.*, e.name AS education_name FROM teachers t LEFT JOIN educations e ON t.education_id = e.id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_teacher($id) {
    global $mysqli;
    $sql = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function get_educations() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM educations");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function add_teacher($fio, $phone, $email, $education_id, $experience, $hour_price, $description) {
    global $mysqli;
    $fio = $mysqli->real_escape_string($fio);
    $phone = $mysqli->real_escape_string($phone);
    $email = $mysqli->real_escape_string($email);
    $education_id = (int)$education_id;
    $experience = (int)$experience;
    $hour_price = (int)$hour_price;
    $description = $mysqli->real_escape_string($description);

    if (empty($fio) || empty($phone) || empty($email) || empty($education_id) || empty($experience) || empty($hour_price) || empty($description)) {
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
        $sql = "INSERT INTO teachers (fio, phone, email, education_id, experience, hour_price, description) 
                VALUES ('$fio', '$phone', '$email', $education_id, $experience, $hour_price, '$description')";
        if ($mysqli->query($sql) === TRUE) {
            echo "Новый преподаватель успешно добавлен.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

function edit_teacher($id, $fio, $phone, $email, $education_id, $experience, $hour_price, $description) {
    global $mysqli;
    $id = (int)$id;
    $fio = $mysqli->real_escape_string($fio);
    $phone = $mysqli->real_escape_string($phone);
    $email = $mysqli->real_escape_string($email);
    $education_id = (int)$education_id;
    $experience = (int)$experience;
    $hour_price = (int)$hour_price;
    $description = $mysqli->real_escape_string($description);

    if (empty($fio) || empty($phone) || empty($email) || empty($education_id) || empty($experience) || empty($hour_price) || empty($description)) {
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
        $sql = "UPDATE teachers SET fio = ?, phone = ?, email = ?, education_id = ?, experience = ?, hour_price = ?, description = ? WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssiiisi", $fio, $phone, $email, $education_id, $experience, $hour_price, $description, $id);

        if ($stmt->execute()) {
            echo "Запись успешно обновлена.";
        } else {
            echo "Ошибка при обновлении записи: " . $stmt->error;
        }
        $stmt->close();
    }
}

function delete_teacher($id) {
    global $mysqli;
    $id = (int)$id;
    $mysqli->query("DELETE FROM teachers WHERE id = $id");
}

function show_error($message) {
    echo "<p>Ошибка: $message</p>";
}

function check_unique_phone($mysqli, $phone, $id = null) {
    $check_phone_sql = "SELECT * FROM teachers WHERE phone = '$phone'";
    if ($id) {
        $check_phone_sql .= " AND id != $id";
    }
    $check_phone_result = $mysqli->query($check_phone_sql);
    return $check_phone_result->num_rows === 0;
}

function check_unique_email($mysqli, $email, $id = null) {
    $check_email_sql = "SELECT * FROM teachers WHERE email = '$email'";
    if ($id) {
        $check_email_sql .= " AND id != $id";
    }
    $check_email_result = $mysqli->query($check_email_sql);
    return $check_email_result->num_rows === 0;
}

function get_teachers_by_subject($subject_id) {
    global $mysqli;
    $sql = "SELECT t.*, e.name AS education_name
            FROM teachers t
            JOIN teachers_subjects ts ON t.id = ts.teacher_id
            LEFT JOIN educations e ON t.education_id = e.id
            WHERE ts.subject_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_subjects() {
    global $mysqli;
    $result = $mysqli->query("SELECT id, name FROM subjects");
    $subjects = [];
    while ($subject = $result->fetch_assoc()) {
        $subjects[$subject['id']] = $subject['name'];
    }
    return $subjects;
}
?>