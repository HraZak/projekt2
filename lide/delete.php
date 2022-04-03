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
    protected string $title = "Smazání osoby";

    protected function body(): string
    {
        $error = '';
        $succes = '';
        $employee_id = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

        if ($employee_id) {

            $stmt = $this->pdo->prepare('DELETE FROM `key` WHERE employee=:employee_id');
            $stmt->bindParam(':employee_id', $employee_id);
            if ($stmt->execute()) {
                $stmt = $this->pdo->prepare('DELETE FROM employee WHERE employee_id=:employee_id');
                $stmt->bindParam(':employee_id', $employee_id);
                if ($stmt->execute()) {
                    $succes = 'Zaměstnanec smazán';
                } else {
                    $error = 'Nepodařilo se smazat zaměstnance';
                }
            } else {
                $error = 'Nepodařilo se smazat klíče';
            }
        } else {
            $error = 'Špatný požadavek';
            http_response_code(400);
        }

        return $this->m->render("delete_employee", ['login' => $_SESSION['login'], 'error' => $error, 'succes' => $succes]);
    }
}

(new CurrentPage())->render();
