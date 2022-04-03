<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ./index.php');
    exit;
}

require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Změna hesla";

    protected function body(): string
    {
        $error = '';
        $success = '';
        $oldPassword = filter_input(INPUT_POST, 'oldPassword') ?? '';
        $newPassword = filter_input(INPUT_POST, 'newPassword') ?? '';
        $newPasswordVerify = filter_input(INPUT_POST, 'newPasswordVerify') ?? '';

        $stmt = $this->pdo->prepare("SELECT password FROM employee WHERE login=:login");
        $stmt->bindParam(':login', $_SESSION['login']);
        $stmt->execute();
        $dbData = $stmt->fetch();

        if ($oldPassword && $newPassword && $newPasswordVerify) {
            if (password_verify($oldPassword, $dbData->password)) {
                if ($newPassword === $newPasswordVerify) {
                    $stmt = $this->pdo->prepare("UPDATE employee SET password=:password WHERE login=:login");
                    $stmt->bindParam(':login', $_SESSION['login']);
                    $stmt->bindParam(':password', password_hash($newPassword, PASSWORD_DEFAULT));
                    $stmt->execute();

                    $success = 'Heslo úspěšně změněno';
                } else
                    $error = 'Špatně zadané nové heslo';
            } else
                $error = 'Špatně zadané staré heslo';
        }

        return $this->m->render("zmenaHesla", ['login' => $_SESSION['login'], 'success' => $success, 'error' => $error]);
    }
}

(new CurrentPage())->render();
