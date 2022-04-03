<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Karta osoby";

    protected function body(): string
    {
        $error = '';
        $employeeId = filter_input(INPUT_GET, 'employee_id', FILTER_VALIDATE_INT);

        if ($employeeId) {
            $stmt = $this->pdo->prepare('SELECT employee.name, employee.surname, employee.job, employee.wage, room.room_id, room.name AS room_name FROM employee LEFT JOIN room ON employee.room=room.room_id WHERE employee.employee_id=:employee_id');
            $stmt->bindParam(':employee_id', $employeeId);
            $stmt->execute();

            if ($stmt->rowCount()) {
                $employee = $stmt->fetch();

                $stmtkeys = $this->pdo->prepare('SELECT room.room_id, room.name FROM `key` LEFT JOIN room ON `key`.room=room.room_id WHERE `key`.employee=:employee_id');
                $stmtkeys->bindParam(':employee_id', $employeeId);
                $stmtkeys->execute();
            } else {
                $error = 'Místnost nenalezena';
                http_response_code(404);
            }
        } else {
            $error = 'Špatný požadavek';
            http_response_code(400);
        }

        return $this->m->render("clovek", ['login' => $_SESSION['login'], 'error' => $error, 'employee' => $employee, 'stmtkeys' => $stmtkeys]);
    }
}

(new CurrentPage())->render();
