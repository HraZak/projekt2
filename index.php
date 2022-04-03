<?php
session_start();

if (isset($_SESSION['login'])) {
    header('Location: ./prohlizec.php');
    exit;
}

require "./includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Přihlášení";

    protected function body(): string
    {
        $error = '';
        $login = filter_input(INPUT_POST, 'login') ?? '';
        $password = filter_input(INPUT_POST, 'password') ?? '';

        if ($login && $password) {
            $stmt = $this->pdo->prepare("SELECT password, admin FROM employee WHERE login=:login");
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $dbData = $stmt->fetch();

            if ($dbData) {
                if (password_verify($password, $dbData->password)) {
                    $_SESSION['login'] = $login;
                    $_SESSION['admin'] = $dbData->admin;

                    header('Location: ./prohlizec.php');
                    exit;
                } else
                    $error = 'Špatné heslo';
            } else
                $error = 'Špatné jméno';
        }

        return $this->m->render("login", ['login' => $login, 'error' => $error]);
    }
}

(new CurrentPage())->render();
