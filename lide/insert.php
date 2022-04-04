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
        $sended = filter_input(INPUT_POST, 'sended', FILTER_VALIDATE_BOOLEAN) ?? false;
        $name = filter_input(INPUT_POST, 'name') ?? '';
        $surname = filter_input(INPUT_POST, 'surname') ?? '';
        $job = filter_input(INPUT_POST, 'job') ?? '';
        $wage = filter_input(INPUT_POST, 'wage', FILTER_VALIDATE_INT);
        $room = filter_input(INPUT_POST, 'room', FILTER_VALIDATE_INT);
        $login = filter_input(INPUT_POST, 'login') ?? '';
        $password = filter_input(INPUT_POST, 'password') ?? '';
        $admin = filter_input(INPUT_POST, 'admin', FILTER_VALIDATE_BOOLEAN) ?? false;

        $stmtrooms = $this->pdo->prepare('SELECT room_id, name FROM room');
        $stmtrooms->execute();

        if ($sended) {
            if (!$name) {
                $error = 'Špatně zadané jméno';
            } elseif (!$surname) {
                $error = 'Špatně zadané příjmení';
            } elseif (!$job) {
                $error = 'Špatně zadaná pozice';
            } elseif (!$wage) {
                $error = 'Špatně zadaná mzda';
            } elseif (!$room) {
                $error = 'Špatně zadaná místnost';
            } elseif (!$login) {
                $error = 'Špatně zadaný login';
            } elseif (!$password) {
                $error = 'Špatně zadané heslo';
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO employee (name, surname, job, wage, room, login, password, admin) VALUES (:name, :surname, :job, :wage, :room, :login, :password, :admin)');
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':job', $job);
                $stmt->bindParam(':wage', $wage);
                $stmt->bindParam(':room', $room);
                $stmt->bindParam(':login', $login);
                $password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $password);
                if (!$admin) $admin = false;
                $stmt->bindParam(':admin', $admin);
                if ($stmt->execute()) {
                    $succes = 'Zaměstnanec přidán';
                } else {
                    $error = 'Nepodařilo se přidat zaměstnance';
                }
            }
        }

        return $this->m->render("insert_employee", ['session_login' => $_SESSION['login'], 'error' => $error, 'succes' => $succes, 'name' => $name, 'surname' => $surname, 'job' => $job, 'wage' => $wage, 'room' => $room, 'stmtrooms' => $stmtrooms, 'login' => $login, 'password' => $password, 'admin' => $admin]);
    }
}

(new CurrentPage())->render();
