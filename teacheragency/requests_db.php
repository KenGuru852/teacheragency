<?php
require 'db.php';

function get_requests() {
    global $mysqli;
    $result = $mysqli->query("SELECT * FROM requests");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_student_name($student_id) {
    global $mysqli;
    $result = $mysqli->query("SELECT fio FROM students WHERE id = $student_id");
    return $result->fetch_assoc()['fio'];
}

function get_subject_name($subject_id) {
    global $mysqli;
    $result = $mysqli->query("SELECT name FROM subjects WHERE id = $subject_id");
    return $result->fetch_assoc()['name'];
}

function get_students() {
    global $mysqli;
    $result = $mysqli->query("SELECT id, fio FROM students");
    $students = [];
    while ($student = $result->fetch_assoc()) {
        $students[$student['id']] = $student['fio'];
    }
    return $students;
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

function add_request($student_id, $subject_id) {
    global $mysqli;
    $student_id = (int)$student_id;
    $subject_id = (int)$subject_id;
    $request_date = date('Y-m-d');
    $status = 'новая';

    $sql = "INSERT INTO requests (student_id, subject_id, request_date, Status) 
            VALUES ($student_id, $subject_id, '$request_date', '$status')";
    if ($mysqli->query($sql) === TRUE) {
        echo "Новая заявка успешно добавлена.";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
    }
}

function delete_request($id) {
    global $mysqli;
    $id = (int)$id;
    $mysqli->query("DELETE FROM requests WHERE id = $id");
}

function get_requests_by_subject_and_status($subject_id, $status) {
    global $mysqli;
    $sql = "SELECT st.fio AS student_fio, s.name AS subject_name, r.request_date, r.Status
            FROM requests r
            JOIN students st ON r.student_id = st.id
            JOIN subjects s ON r.subject_id = s.id
            WHERE r.subject_id = ? AND r.Status = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $subject_id, $status);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_requests_by_date_range($start_date, $end_date) {
    global $mysqli;
    $sql = "SELECT st.fio AS student_fio, s.name AS subject_name, r.request_date, r.Status
            FROM requests r
            JOIN students st ON r.student_id = st.id
            JOIN subjects s ON r.subject_id = s.id
            WHERE r.request_date BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_request_statuses() {
    return ['новая', 'в работе', 'закрыта'];
}
?>