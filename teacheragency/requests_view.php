<?php
require 'requests_db.php';

if (isset($_GET['delete'])) {
    delete_request($_GET['delete']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student_id']) && isset($_POST['add_subject_id'])) {
    add_request($_POST['add_student_id'], $_POST['add_subject_id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id_for_requests']) && isset($_POST['status_for_requests'])) {
    $subject_id = (int)$_POST['subject_id_for_requests'];
    $status = $_POST['status_for_requests'];
    $requests = get_requests_by_subject_and_status($subject_id, $status);
    display_requests($requests);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $requests = get_requests_by_date_range($start_date, $end_date);
    display_requests_by_date_range($requests, $start_date, $end_date);
}

$requests = get_requests();

if (!empty($requests)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Студент</th>
                <th>Предмет</th>
                <th>Дата заявки</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>";
    foreach ($requests as $row) {
        $student_name = get_student_name($row['student_id']);
        $subject_name = get_subject_name($row['subject_id']);

        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$student_name."</td>
                <td>".$subject_name."</td>
                <td>".$row["request_date"]."</td>
                <td>".$row["Status"]."</td>
                <td>
                    <a href='?delete=".$row["id"]."'>Удалить</a>
                </td>
              </tr>";
    }
    echo "</table>";
    echo "</form>";
} else {
    echo "Нет данных.";
}

$students = get_students();

$subjects = get_subjects();

echo "<h2>Добавить новую заявку</h2>";
echo "<form method='post' action=''>
        <label for='add_student_id'>Студент:</label>
        <select id='add_student_id' name='add_student_id' required>
            <option value=''>Выберите студента</option>";
foreach ($students as $student_id => $student_fio) {
    echo "<option value='$student_id'>$student_fio</option>";
}
echo "</select>
        <br>
        <label for='add_subject_id'>Предмет:</label>
        <select id='add_subject_id' name='add_subject_id' required>
            <option value=''>Выберите предмет</option>";
foreach ($subjects as $subject_id => $subject_name) {
    echo "<option value='$subject_id'>$subject_name</option>";
}
echo "</select>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<h2>Вывести заявки по выбранному предмету и статусу</h2>";
echo "<form method='post' action=''>
        <label for='subject_id_for_requests'>Предмет:</label>
        <select id='subject_id_for_requests' name='subject_id_for_requests' required>";
foreach ($subjects as $subject_id => $subject_name) {
    echo "<option value='$subject_id'>$subject_name</option>";
}
echo "</select>
        <br>
        <label for='status_for_requests'>Статус:</label>
        <select id='status_for_requests' name='status_for_requests' required>";
$statuses = get_request_statuses();
foreach ($statuses as $status) {
    echo "<option value='$status'>$status</option>";
}
echo "</select>
        <br>
        <input type='submit' value='Вывести заявки'>
      </form>";

echo "<h2>Вывести заявки за выбранный период</h2>";
echo "<form method='post' action=''>
        <label for='start_date'>Начальная дата:</label>
        <input type='date' id='start_date' name='start_date' required>
        <br>
        <label for='end_date'>Конечная дата:</label>
        <input type='date' id='end_date' name='end_date' required>
        <br>
        <input type='submit' value='Вывести заявки'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";

function display_requests($requests) {
    echo "<h2>Заявки по выбранному предмету и статусу</h2>";
    if (!empty($requests)) {
        echo "<table border='1'>
                <tr>
                    <th>Студент</th>
                    <th>Предмет</th>
                    <th>Дата заявки</th>
                    <th>Статус</th>
                </tr>";
        foreach ($requests as $request) {
            echo "<tr>
                    <td>".$request['student_fio']."</td>
                    <td>".$request['subject_name']."</td>
                    <td>".$request['request_date']."</td>
                    <td>".$request['Status']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Нет заявок по выбранному предмету и статусу.";
    }
}

function display_requests_by_date_range($requests, $start_date, $end_date) {
    echo "<h2>Заявки, оставленные в промежутке $start_date - $end_date</h2>";
    if (!empty($requests)) {
        echo "<table border='1'>
                <tr>
                    <th>Студент</th>
                    <th>Предмет</th>
                    <th>Дата заявки</th>
                    <th>Статус</th>
                </tr>";
        foreach ($requests as $request) {
            echo "<tr>
                    <td>".$request['student_fio']."</td>
                    <td>".$request['subject_name']."</td>
                    <td>".$request['request_date']."</td>
                    <td>".$request['Status']."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Нет заявок в выбранном промежутке.";
    }
}
?>