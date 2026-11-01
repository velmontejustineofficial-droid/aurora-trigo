<?php
require_once dirname(__DIR__) . '/config/db_connect.php';

class UserRepository {

    public function getByUsername($username) : ? array {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM users WHERE (email=? OR phone=?)");
        $stmt->bind_param('sss', $username, $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if (!$res->num_rows > 0) {
            return null;
        }
        return $res->fetch_assoc();
    }
}