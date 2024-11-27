<?php
require 'teachers_subjects_db.php';

if (isset($_GET['delete'])) {
    delete_teacher_subject($_GET['delete'], $_GET['subject']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_teacher_id']) && isset($_POST['add_subject_id'])) {
    add_teacher_subject($_POST['add_teacher_id'], $_POST['add_subject_id']);
}

$teacher_subjects = get_teacher_subjects();

if (!empty($teacher_subjects)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>Преподаватель</th>
                <th>Предмет</th>
                <th>Действия</th>
            </tr>";
    foreach ($teacher_subjects as $row) {
        echo "<tr>
                <td>".$row["teacher_name"]."</td>
                <td>".$row["subject_name"]."</td>
                <td>
                    <a href='?delete=".$row["teacher_id"]."&subject=".$row["subject_id"]."'>Удалить</a>
                </td>
              </tr>";
    }
    echo "</table>";
    echo "</form>";
} else {
    echo "Нет данных.";
}

$teachers = get_teachers();

$subjects = get_subjects();

echo "<h2>Добавить новую связь</h2>";
echo "<form method='post' action=''>
        <label for='add_teacher_id'>Преподаватель:</label>
        <select id='add_teacher_id' name='add_teacher_id' required>
            <option value=''>Выберите преподавателя</option>";
foreach ($teachers as $teacher_id => $teacher_fio) {
    echo "<option value='$teacher_id'>$teacher_fio</option>";
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

echo "<br><a href='index.php'>На главную</a>";
?>