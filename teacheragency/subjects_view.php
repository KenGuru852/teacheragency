<?php
require 'subjects_db.php';

if (isset($_GET['delete'])) {
    delete_subject($_GET['delete']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_name'])) {
    add_subject($_POST['add_name']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    edit_subject($_POST['edit_id'], $_POST['edit_name']);
}

$subjects = get_subjects();

if (!empty($subjects)) {
    echo "<form method='post' action=''>";
    echo "<table border='1'>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Действия</th>
            </tr>";
    foreach ($subjects as $row) {
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
    $subject = get_subject($_GET['edit']);
    if ($subject) {
        echo "<h2>Редактировать предмет</h2>";
        echo "<form method='post' action=''>
                <input type='hidden' name='edit_id' value='".$subject['id']."'>
                <label for='edit_name'>Название:</label>
                <input type='text' id='edit_name' name='edit_name' value='".$subject['name']."' required>
                <br>
                <input type='submit' value='Сохранить'>
              </form>";
    }
}

echo "<h2>Добавить новый предмет</h2>";
echo "<form method='post' action=''>
        <label for='add_name'>Название:</label>
        <input type='text' id='add_name' name='add_name' required>
        <br>
        <input type='submit' value='Добавить'>
      </form>";

echo "<br><a href='index.php'>На главную</a>";
?>