<?php
session_start();

if (!isset($_SESSION['login'])) {
    header('Location: ./index.php');
    exit;
}

require "./includes/bootstrap.inc.php";

final class CurrentPage extends BasePage
{
    protected string $title = "Prohlížeč databáze";

    protected function body(): string
    {
        return $this->m->render("prohlizec", ['login' => $_SESSION['login']]);
    }
}

(new CurrentPage())->render();
