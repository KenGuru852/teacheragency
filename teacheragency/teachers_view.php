<?php
require 'teachers_db.php';

if (isset($_GET['delete'])) {
    delete_teacher($_GET['delete']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fio']) && isset($_POST['add_phone']) && isset($_POST['add_email']) && isset($_POST['add_education_id']) && isset($_POST['add_experience']) && isset($_POST['add_hour_price']) && isset($_POST['add_description'])) {
    add_teacher($_POST['add_fio'], $_POST['add_phone'], $_POST['add_email'], $_POST['add_education_id'], $_POST['add_experience'], $_POST['add_hour_price'], $_POST['add_description']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    edit_teacher($_POST['edit_id'], $_POST['edit_fio'], $_POST['edit_phone'], $_POST['edit_email'], $_POST['edit_education_id'], $_POST['edit_experience'], $_POST['edit_hour_price'], $_POST['edit_description']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id_for_teachers'])) {
    $subject_id = (int)$_POST['subject_id_for_teachers'];
    $subject_name = get_subject_name($subject_id);
    $teachers = get_teachers_by_subject($subject_id);
    display_teachers($teachers, $subject_name);
}

$teachers = get_teachers_with_education();

if (!empty($teachers)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Образование</th>
                <th>Опыт</th>
                <th>Стоимость часа</th>
                <th>Описание</th>
                <th>Действия</th>
            </tr>";
    foreach ($teachers as $row) {
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["fio"]."</td>
                <td>".$row["phone"]."</td>
                <td>".$row["email"]."</td>
                <td>".$row["education_name"]."</td>
                <td>".$row["experience"]."</td>
                <td>".$row["hour_price"]."</td>
                <td>".$row["description"]."</td>
                <td>
                    <a href='?delete=".$row["id"]."'>Удалить</a> |
                    <a href='?edit=".$row["id"]."'>Редактировать</a>
                </td>
              </tr>";
    }
    echo "</table>";
    echo "</form>";
} else {
    echo "Нет данных.";
}

if (isset($_GET['edit'])) {
    $teacher = get_teacher($_GET['edit']);
    if ($teacher) {
        $educations = get_educations();

        echo "<h2>Редактировать преподавателя</h2>";
        echo "<form method='post' action=''>
                <input type='hidden' name='edit_id' value='".$teacher['id']."'>
                <label for='edit_fio'>ФИО:</label>
                <input type='text' id='edit_fio' name='edit_fio' value='".$teacher['fio']."' required>
                <br>
                <label for='edit_phone'>Телефон:</label>
                <input type='text' id='edit_phone' name='edit_phone' value='".$teacher['phone']."' required>
                <br>
                <label for='edit_email'>Email:</label>
                <input type='email' id='edit_email' name='edit_email' value='".$teacher['email']."' required>
                <br>
                <label for='edit_education_id'>Образование:</label>
                <select id='edit_education_id' name='edit_education_id' required>";
        foreach ($educations as $edu) {
            $selected = ($edu['id'] == $teacher['education_id']) ? 'selected' : '';
            echo "<option value='".$edu['id']."' $selected>".$edu['name']."</option>";
        }
        echo "</select>
                <br>
                <label for='edit_experience'>Опыт (лет):</label>
                <input type='number' id='edit_experience' name='edit_experience' value='".$teacher['experience']."' min='0' required>
                <br>
                <label for='edit_hour_price'>Стоимость часа:</label>
                <input type='number' id='edit_hour_price' name='edit_hour_price' value='".$teacher['hour_price']."' min='1' required>
                <br>
                <label for='edit_description'>Описание:</label>
                <textarea id='edit_description' name='edit_description' required>".$teacher['description']."</textarea>
                <br>
                <input type='submit' value='Сохранить'>
              </form>";
    }
}

echo "<h2>Добавить нового преподавателя</h2>";
echo "<form method='post' action=''>
        <label for='add_fio'>ФИО:</label>
        <input type='text' id='add_fio' name='add_fio' required>
        <br>
        <label for='add_phone'>Телефон:</label>
        <input type='text' id='add_phone' name='add_phone' required>
        <br>
        <label for='add_email'>Email:</label>
        <input type='email' id='add_email' name='add_email' required>
        <br>
        <label for='add_education_id'>Образование:</label>
        <select id='add_education_id' name='add_education_id' required>";
$educations = get_educations();
foreach ($educations as $edu) {
    echo "<option value='".$edu['id']."'>".$edu['name']."</option>";
}
echo "</select>
        <br>
        <label for='add_experience'>Опыт (лет):</label>
        <input type='number' id='add_experience' name='add_experience' min='0' required>
        <br>
        <label for='add_hour_price'>Стоимость часа:</label>
        <input type='number' id='add_hour_price' name='add_hour_price' min='1' required>
        <br>
        <label for='add_description'>Описание:</label>
        <textarea id='add_description' name='add_description' required></textarea>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<h2>Вывести преподавателей по выбранному предмету</h2>";
echo "<form method='post' action=''>
        <label for='subject_id_for_teachers'>Предмет:</label>
        <select id='subject_id_for_teachers' name='subject_id_for_teachers' required>";
$subjects = get_subjects();
foreach ($subjects as $subject_id => $subject_name) {
    echo "<option value='$subject_id'>$subject_name</option>";
}
echo "</select>
        <br>
        <input type='submit' value='Вывести преподавателей'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";

function get_subject_name($subject_id) {
    global $mysqli;
    $result = $mysqli->query("SELECT name FROM subjects WHERE id = $subject_id");
    return $result->fetch_assoc()['name'];
}
function display_teachers($teachers, $subject_name) {
    echo "<h2>Преподаватели по предмету \"$subject_name\"</h2>";
    if (!empty($teachers)) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>Образование</th>
                    <th>Опыт</th>
                    <th>Стоимость часа</th>
                    <th>Описание</th>
                </tr>";
        foreach ($teachers as $row) {
            echo "<tr>
                    <td>".$row["id"]."</td>
                    <td>".$row["fio"]."</td>
                    <td>".$row["phone"]."</td>
                    <td>".$row["email"]."</td>
                    <td>".$row["education_name"]."</td>
                    <td>".$row["experience"]."</td>
                    <td>".$row["hour_price"]."</td>
                    <td>".$row["description"]."</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "Нет преподавателей по выбранному предмету.";
    }
}
?>