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
    protected string $title = "Aktualizovat zaměstnance";

    protected function body(): string
    {
        $error = '';
        $succes = '';

        $password = '';

        $stmtrooms = $this->pdo->prepare('SELECT room_id, name FROM room');
        $stmtrooms->execute();

        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        $stmtemployees = $this->pdo->prepare('SELECT name, surname, job, wage, room, login, admin FROM employee WHERE employee_id=:employee_id');
        $stmtemployees->bindParam(':employee_id', $employee_id);
        $stmtemployees->execute();
        $stmtemployeesData = $stmtemployees->fetch();

        $name = $stmtemployeesData->name;
        $surname = $stmtemployeesData->surname;
        $job = $stmtemployeesData->job;
        $wage = $stmtemployeesData->wage;
        $room = $stmtemployeesData->room;
        $login = $stmtemployeesData->login;
        $admin = $stmtemployeesData->admin;

        $sended = filter_input(INPUT_POST, 'sended', FILTER_VALIDATE_BOOLEAN) ?? false;

        if ($sended) {
            $name = filter_input(INPUT_POST, 'name') ?? '';
            $surname = filter_input(INPUT_POST, 'surname') ?? '';
            $job = filter_input(INPUT_POST, 'job') ?? '';
            $wage = filter_input(INPUT_POST, 'wage', FILTER_VALIDATE_INT);
            $room = filter_input(INPUT_POST, 'room', FILTER_VALIDATE_INT);
            $login = filter_input(INPUT_POST, 'login')  ?? '';
            $password = filter_input(INPUT_POST, 'password')  ?? '';
            $admin = filter_input(INPUT_POST, 'admin', FILTER_VALIDATE_BOOLEAN)  ?? false;

            if (!$name) {
                $error = 'Špatně zadané jméno';
            } elseif (!$surname) {
                $error = 'Špatně zadané příjmení';
            } elseif (!$job) {
                $error = 'Špatně zadaná pozice';
            } elseif (!$wage) {
                $error = 'Špatně zadaná mzda';
            } elseif (!$login) {
                $error = 'Špatně zadaný login';
            } else {
                $stmt = $this->pdo->prepare('UPDATE employee SET name=:name, surname=:surname, job=:job, wage=:wage, login=:login, admin=:admin WHERE employee_id=:employee_id');
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':job', $job);
                $stmt->bindParam(':wage', $wage);
                $stmt->bindParam(':login', $login);
                $stmt->bindParam(':admin', $admin);
                $stmt->bindParam(':employee_id', $employee_id);
                if ($stmt->execute()) {
                    $succes = 'Zaměstnanec aktualizován';
                } else {
                    $error = 'Nepodařilo se aktualizovat zaměstnance';
                }
            }

            if ($password) {
                $stmt = $this->pdo->prepare('UPDATE employee SET password=:password WHERE employee_id=:employee_id');
                $password = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':employee_id', $employee_id);
                $stmt->execute();
            }
            if ($room) {
                $stmt = $this->pdo->prepare('UPDATE employee SET room=:room WHERE employee_id=:employee_id');
                $stmt->bindParam(':room', $room);
                $stmt->bindParam(':employee_id', $employee_id);
                $stmt->execute();
            }
        }

        return $this->m->render("update_employee", ['session_login' => $_SESSION['login'], 'error' => $error, 'succes' => $succes, 'name' => $name, 'surname' => $surname, 'job' => $job, 'wage' => $wage, 'room' => $room, 'stmtrooms' => $stmtrooms, 'login' => $login, 'password' => $password, 'admin' => $admin, 'employee_id' => $employee_id]);
    }
}

(new CurrentPage())->render();
