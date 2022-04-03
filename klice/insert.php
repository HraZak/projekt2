<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}
if (!$_SESSION['admin']) {
    header('Location: ./index.php');
    exit;
}

require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Nový zaměstnanec";

    protected function body(): string
    {
        $error = '';
        $succes = '';
        $sended = filter_input(INPUT_POST, 'sended', FILTER_VALIDATE_BOOLEAN);
        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);
        $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);

        $stmtemployees = $this->pdo->prepare('SELECT employee_id, name, surname FROM employee');
        $stmtemployees->execute();
        $stmtrooms = $this->pdo->prepare('SELECT room_id, name FROM room');
        $stmtrooms->execute();

        if ($sended) {
            if (!$employee_id) {
                $error = 'Špatně zadaný zaměstnanec';
            } elseif (!$room_id) {
                $error = 'Špatně zadaná místnost';
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO `key` (employee, room) VALUES (:employee_id, :room_id)');
                $stmt->bindParam(':employee_id', $employee_id);
                $stmt->bindParam(':room_id', $room_id);
                try {
                    if ($stmt->execute()) {
                        $succes = 'Klíč přidán';
                    } else {
                        $error = 'Nepodařilo se přidat Klíč';
                    }
                } catch (Exception $ex) {
                    $error = 'Nepodařilo se přidat Klíč';
                }
            }
        }

        return $this->m->render("insert_key", ['session_login' => $_SESSION['login'], 'error' => $error, 'succes' => $succes, 'stmtemployees' => $stmtemployees, 'stmtrooms' => $stmtrooms]);
    }
}

(new CurrentPage())->render();
