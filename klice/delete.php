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
    protected string $title = "Smazání klíče";

    protected function body(): string
    {
        $error = '';
        $succes = '';
        $key_id = filter_input(INPUT_POST, 'key_id', FILTER_VALIDATE_INT);

        if ($key_id) {

            $stmt = $this->pdo->prepare('DELETE FROM `key` WHERE key_id=:key_id');
            $stmt->bindParam(':key_id', $key_id);
            if ($stmt->execute()) {
                $succes = 'Klíč smazán';
            } else {
                $error = 'Nepodařilo se smazat klíče';
            }
        } else {
            $error = 'Špatný požadavek';
            http_response_code(400);
        }

        return $this->m->render("delete_key", ['login' => $_SESSION['login'], 'error' => $error, 'succes' => $succes]);
    }
}

(new CurrentPage())->render();
