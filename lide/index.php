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
        $stmt = $this->pdo->prepare('SELECT employee.employee_id, employee.name, employee.surname, employee.job, room.name AS room_name, room.phone FROM employee LEFT JOIN room ON employee.room=room.room_id ORDER BY `surname`');
        $stmt->execute();

        return $this->m->render("zamestnanci", ['login' => $_SESSION['login'], "lide" => $stmt, 'admin' => $_SESSION['admin']]);
    }
}

(new CurrentPage())->render();
