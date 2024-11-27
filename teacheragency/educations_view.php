<?php
require 'educations_db.php';

// Обработка удаления записи
if (isset($_GET['delete'])) {
    delete_education($_GET['delete']);
}

// Обработка добавления новой записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_name'])) {
    add_education($_POST['add_name']);
}

// Обработка редактирования записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    edit_education($_POST['edit_id'], $_POST['edit_name']);
}

$educations = get_educations();

if (!empty($educations)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Действия</th>
            </tr>";
    foreach ($educations as $row) {
        echo "<tr>
                <td>".$row["id"]."</td>
                <td>".$row["name"]."</td>
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
    $education = get_education($_GET['edit']);
    if ($education) {
        echo "<h2>Редактировать образование</h2>";
        echo "<form method='post' action=''>
                <input type='hidden' name='edit_id' value='".$education['id']."'>
                <label for='edit_name'>Название:</label>
                <input type='text' id='edit_name' name='edit_name' value='".$education['name']."' required>
                <br>
                <input type='submit' value='Сохранить'>
              </form>";
    }
}

echo "<h2>Добавить новое образование</h2>";
echo "<form method='post' action=''>
        <label for='add_name'>Название:</label>
        <input type='text' id='add_name' name='add_name' required>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";
?>