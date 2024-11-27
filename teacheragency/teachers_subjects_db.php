<?php
require 'db.php';

function get_teacher_subjects() {
    global $mysqli;
    $result = $mysqli->query("SELECT ts.teacher_id, ts.subject_id, t.fio AS teacher_name, s.name AS subject_name 
                              FROM teachers_subjects ts
                              JOIN teachers t ON ts.teacher_id = t.id
                              JOIN subjects s ON ts.subject_id = s.id");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_teachers() {
    global $mysqli;
    $result = $mysqli->query("SELECT id, fio FROM teachers");
    $teachers = [];
    while ($teacher = $result->fetch_assoc()) {
        $teachers[$teacher['id']] = $teacher['fio'];
    }
    return $teachers;
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

function add_teacher_subject($teacher_id, $subject_id) {
    global $mysqli;
    $teacher_id = (int)$teacher_id;
    $subject_id = (int)$subject_id;

    $check_sql = "SELECT * FROM teachers_subjects WHERE teacher_id = $teacher_id AND subject_id = $subject_id";
    $check_result = $mysqli->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "Ошибка: Такая связь уже существует.";
    } else {
        $sql = "INSERT INTO teachers_subjects (teacher_id, subject_id) VALUES ($teacher_id, $subject_id)";
        if ($mysqli->query($sql) === TRUE) {
            echo "Связь успешно добавлена.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

function delete_teacher_subject($teacher_id, $subject_id) {
    global $mysqli;
    $teacher_id = (int)$teacher_id;
    $subject_id = (int)$subject_id;
    $mysqli->query("DELETE FROM teachers_subjects WHERE teacher_id = $teacher_id AND subject_id = $subject_id");
}
?>