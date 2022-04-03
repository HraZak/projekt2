<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Seznam zaměstnanců";

    protected function body(): string
    {
        $stmt = $this->pdo->prepare('SELECT `key`.key_id, employee.name, employee.surname, room.name AS room_name FROM employee RIGHT JOIN `key` ON employee.employee_id=`key`.employee LEFT JOIN room ON room.room_id=`key`.room ORDER BY `surname`');
        $stmt->execute();

        return $this->m->render("klice", ['login' => $_SESSION['login'], "klice" => $stmt, 'admin' => $_SESSION['admin']]);
    }
}

(new CurrentPage())->render();
