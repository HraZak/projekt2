<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Karta místnosti";

    protected function body(): string
    {
        $error = '';
        $roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);

        if (!$roomId) {
            $error = 'Špatný požadavek';
            http_response_code(400);
        } else {
            $stmt = $this->pdo->prepare('SELECT no, name, phone FROM room WHERE room_id=:room_id');
            $stmt->bindParam(':room_id', $roomId);
            $stmt->execute();

            if ($stmt->rowCount()) {
                $room = $stmt->fetch();

                $stmtemployee = $this->pdo->prepare('SELECT employee_id, name, surname FROM employee WHERE room=:room');
                $stmtemployee->bindParam(':room', $roomId);
                $stmtemployee->execute();

                $stmtwage = $this->pdo->prepare('SELECT AVG(wage) AS wage FROM employee WHERE room=:room');
                $stmtwage->bindParam(':room', $roomId);
                $stmtwage->execute();
                $wage = $stmtwage->fetch();

                $stmtkeys = $this->pdo->prepare('SELECT employee.employee_id, employee.name, employee.surname FROM `key` LEFT JOIN employee on `key`.employee=employee.employee_id WHERE `key`.room=:room');
                $stmtkeys->bindParam(':room', $roomId);
                $stmtkeys->execute();
            } else {
                $error = 'Místnost nenalezena';
                http_response_code(404);
            }
        }

        return $this->m->render("room", ['login' => $_SESSION['login'], 'error' => $error, 'room' => $room, 'stmtemployee' => $stmtemployee, 'wage' => $wage->wage, 'stmtkeys' => $stmtkeys]);
    }
}

(new CurrentPage())->render();
