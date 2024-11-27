<?php
require 'students_db.php';

if (isset($_GET['delete'])) {
    delete_student($_GET['delete']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_fio']) && isset($_POST['add_phone']) && isset($_POST['add_email']) && isset($_POST['add_address']) && isset($_POST['add_description'])) {
    add_student($_POST['add_fio'], $_POST['add_phone'], $_POST['add_email'], $_POST['add_address'], $_POST['add_description']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    edit_student($_POST['edit_id'], $_POST['edit_fio'], $_POST['edit_phone'], $_POST['edit_email'], $_POST['edit_address'], $_POST['edit_description']);
}

$students = get_students();

if (!empty($students)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Адрес</th>
                <th>Описание</th>
                <th>Действия</th>
            </tr>";
    foreach ($students as $row) {
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["fio"]."</td>
                <td>".$row["phone"]."</td>
                <td>".$row["email"]."</td>
                <td>".$row["address"]."</td>
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
    $student = get_student($_GET['edit']);
    if ($student) {
        echo "<h2>Редактировать студента</h2>";
        echo "<form method='post' action=''>
                <input type='hidden' name='edit_id' value='".$student['id']."'>
                <label for='edit_fio'>ФИО:</label>
                <input type='text' id='edit_fio' name='edit_fio' value='".$student['fio']."' required>
                <br>
                <label for='edit_phone'>Телефон:</label>
                <input type='text' id='edit_phone' name='edit_phone' value='".$student['phone']."' required>
                <br>
                <label for='edit_email'>Email:</label>
                <input type='email' id='edit_email' name='edit_email' value='".$student['email']."'>
                <br>
                <label for='edit_address'>Адрес:</label>
                <input type='text' id='edit_address' name='edit_address' value='".$student['address']."'>
                <br>
                <label for='edit_description'>Описание:</label>
                <textarea id='edit_description' name='edit_description'>".$student['description']."</textarea>
                <br>
                <input type='submit' value='Сохранить'>
              </form>";
    }
}

echo "<h2>Добавить нового студента</h2>";
echo "<form method='post' action=''>
        <label for='add_fio'>ФИО:</label>
        <input type='text' id='add_fio' name='add_fio' required>
        <br>
        <label for='add_phone'>Телефон:</label>
        <input type='text' id='add_phone' name='add_phone' required>
        <br>
        <label for='add_email'>Email:</label>
        <input type='email' id='add_email' name='add_email'>
        <br>
        <label for='add_address'>Адрес:</label>
        <input type='text' id='add_address' name='add_address'>
        <br>
        <label for='add_description'>Описание:</label>
        <textarea id='add_description' name='add_description'></textarea>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";
?>