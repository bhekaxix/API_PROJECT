<?php
session_start();

class Dashboard {
    private $session;

    public function __construct($session) {
        $this->session = $session;
    }

    public function checkSession() {
        if (!isset($this->session['user_id'])) {
            header('Location: dashboard.php');
            exit();
        }
    }

    public function displayWelcomeMessage() {
        echo "<h1>Welcome to Your Dashboard!</h1>";
        echo "<p>Your account has been successfully verified.</p>";
    }
}

// Instantiate the Dashboard class and execute methods
$dashboard = new Dashboard($_SESSION);
$dashboard->checkSession();
$dashboard->displayWelcomeMessage();

