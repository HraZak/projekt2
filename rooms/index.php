<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

require "../includes/bootstrap.inc.php";

final class CurrentPage extends BaseDBPage
{
    protected string $title = "Výpis místností";

    protected function body(): string
    {
        $stmt = $this->pdo->prepare("SELECT * FROM `room` ORDER BY `name`");
        $stmt->execute([]);

        return $this->m->render("roomList", ['login' => $_SESSION['login'], "rooms" => $stmt, 'admin' => $_SESSION['admin']]);
    }
}

(new CurrentPage())->render();
