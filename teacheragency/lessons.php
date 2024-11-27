<?php
require 'db.php';

function show_error($message) {
    echo "<p>Ошибка: $message</p>";
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $lesson = $mysqli->query("SELECT request_id FROM lessons WHERE id = $id")->fetch_assoc();
    $request_id = $lesson['request_id'];
    $mysqli->query("UPDATE requests SET Status = 'закрыта' WHERE id = $request_id");
    $mysqli->query("DELETE FROM lessons WHERE id = $id");
}

if (isset($_GET['complete'])) {
    $id = (int)$_GET['complete'];
    $lesson = $mysqli->query("SELECT request_id FROM lessons WHERE id = $id")->fetch_assoc();
    $request_id = $lesson['request_id'];
    $mysqli->query("UPDATE requests SET Status = 'закрыта' WHERE id = $request_id");
    $mysqli->query("DELETE FROM lessons WHERE id = $id");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher_id']) && isset($_POST['add_request_id']) && isset($_POST['add_date']) && isset($_POST['add_start_time']) && isset($_POST['add_end_time']) && isset($_POST['add_cost'])) {
    $teacher_id = (int)$_POST['add_teacher_id'];
    $request_id = (int)$_POST['add_request_id'];
    $date = $mysqli->real_escape_string($_POST['add_date']);
    $start_time = $mysqli->real_escape_string($_POST['add_start_time']);
    $end_time = $mysqli->real_escape_string($_POST['add_end_time']);
    $cost = (int)$_POST['add_cost'];

    $has_error = false;

    $request_status = $mysqli->query("SELECT Status, request_date, subject_id FROM requests WHERE id = $request_id")->fetch_assoc();
    if ($request_status['Status'] != 'новая') {
        show_error("Нельзя создать урок по заявке со статусом 'в работе' или 'закрыта'.");
        $has_error = true;
    }

    if ($date < $request_status['request_date']) {
        show_error("Дата урока не может быть раньше даты заявки.");
        $has_error = true;
    }

    if ($start_time >= $end_time) {
        show_error("Время начала урока должно быть раньше времени окончания.");
        $has_error = true;
    }

    $subject_id = $request_status['subject_id'];
    $check_teacher_subject_sql = "SELECT * FROM teachers_subjects WHERE teacher_id = $teacher_id AND subject_id = $subject_id";
    $check_teacher_subject_result = $mysqli->query($check_teacher_subject_sql);
    if ($check_teacher_subject_result->num_rows == 0) {
        show_error("Преподаватель не преподает данный предмет.");
        $has_error = true;
    }

    if (!$has_error) {
        $mysqli->query("UPDATE requests SET Status = 'в работе' WHERE id = $request_id");

        $sql = "INSERT INTO lessons (teacher_id, request_id, date, start_time, end_time, cost) 
                VALUES ($teacher_id, $request_id, '$date', '$start_time', '$end_time', $cost)";
        if ($mysqli->query($sql) === TRUE) {
            echo "Новый урок успешно добавлен.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $id = (int)$_POST['edit_id'];
    $teacher_id = (int)$_POST['edit_teacher_id'];
    $request_id = (int)$_POST['edit_request_id'];
    $date = $mysqli->real_escape_string($_POST['edit_date']);
    $start_time = $mysqli->real_escape_string($_POST['edit_start_time']);
    $end_time = $mysqli->real_escape_string($_POST['edit_end_time']);
    $cost = (int)$_POST['edit_cost'];

    $has_error = false;

    $request_date = $mysqli->query("SELECT request_date FROM requests WHERE id = $request_id")->fetch_assoc()['request_date'];
    if ($date < $request_date) {
        show_error("Дата урока не может быть раньше даты заявки.");
        $has_error = true;
    }

    if ($start_time >= $end_time) {
        show_error("Время начала урока должно быть раньше времени окончания.");
        $has_error = true;
    }

    if (!$has_error) {
        $sql = "UPDATE lessons SET teacher_id = $teacher_id, request_id = $request_id, date = '$date', start_time = '$start_time', end_time = '$end_time', cost = $cost WHERE id = $id";
        if ($mysqli->query($sql) === TRUE) {
            echo "Урок успешно обновлен.";
        } else {
            echo "Ошибка: " . $sql . "<br>" . $mysqli->error;
        }
    }
}

$result = $mysqli->query("SELECT * FROM lessons");

if ($result->num_rows > 0) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Преподаватель</th>
                <th>Заявка</th>
                <th>Дата</th>
                <th>Время начала</th>
                <th>Время окончания</th>
                <th>Стоимость</th>
                <th>Действия</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        $teacher_name = $mysqli->query("SELECT fio FROM teachers WHERE id = " . $row['teacher_id'])->fetch_assoc()['fio'];
        $request = $mysqli->query("SELECT student_id, subject_id FROM requests WHERE id = " . $row['request_id'])->fetch_assoc();
        $student_fio = $mysqli->query("SELECT fio FROM students WHERE id = " . $request['student_id'])->fetch_assoc()['fio'];
        $subject_name = $mysqli->query("SELECT name FROM subjects WHERE id = " . $request['subject_id'])->fetch_assoc()['name'];
        $request_description = $student_fio . " - " . $subject_name;

        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$teacher_name."</td>
                <td>".$request_description."</td>
                <td>".$row["date"]."</td>
                <td>".$row["start_time"]."</td>
                <td>".$row["end_time"]."</td>
                <td>".$row["cost"]."</td>
                <td>
                    <a href='?delete=".$row["id"]."'>Удалить</a> |
                    <a href='?complete=".$row["id"]."'>Завершить</a>
                </td>
              </tr>";
    }
    echo "</table>";
    echo "</form>";
} else {
    echo "Нет данных.";
}

$teachers_result = $mysqli->query("SELECT id, fio FROM teachers");
$teachers = [];
while ($teacher = $teachers_result->fetch_assoc()) {
    $teachers[$teacher['id']] = $teacher['fio'];
}

$requests_result = $mysqli->query("SELECT id, student_id, subject_id FROM requests WHERE Status = 'новая'");
$requests = [];
while ($request = $requests_result->fetch_assoc()) {
    $student_fio = $mysqli->query("SELECT fio FROM students WHERE id = " . $request['student_id'])->fetch_assoc()['fio'];
    $subject_name = $mysqli->query("SELECT name FROM subjects WHERE id = " . $request['subject_id'])->fetch_assoc()['name'];
    $requests[$request['id']] = $student_fio . " - " . $subject_name;
}

echo "<h2>Добавить новый урок</h2>";
echo "<form method='post' action=''>
        <label for='add_teacher_id'>Преподаватель:</label>
        <select id='add_teacher_id' name='add_teacher_id' required>
            <option value=''>Выберите преподавателя</option>";
foreach ($teachers as $teacher_id => $teacher_fio) {
    echo "<option value='$teacher_id'>$teacher_fio</option>";
}
echo "</select>
        <br>
        <label for='add_request_id'>Заявка:</label>
        <select id='add_request_id' name='add_request_id' required>
            <option value=''>Выберите заявку</option>";
foreach ($requests as $request_id => $request_description) {
    echo "<option value='$request_id'>$request_description</option>";
}
echo "</select>
        <br>
        <label for='add_date'>Дата:</label>
        <input type='date' id='add_date' name='add_date' required>
        <br>
        <label for='add_start_time'>Время начала:</label>
        <input type='time' id='add_start_time' name='add_start_time' required>
        <br>
        <label for='add_end_time'>Время окончания:</label>
        <input type='time' id='add_end_time' name='add_end_time' required>
        <br>
        <label for='add_cost'>Стоимость:</label>
        <input type='number' id='add_cost' name='add_cost' min='0' required>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";
?>