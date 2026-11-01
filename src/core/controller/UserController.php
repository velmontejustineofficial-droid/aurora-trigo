<?php
require_once dirname(__DIR__) . '/service/LoginService.php';
require_once dirname(__DIR__) . '/repository/UserRepository.php';

header('Content-Type: application/json');

class UserController {
    public function __construct(
        private LoginService $loginService,
    ) {}

    public function loginUser() : void{
        $role = $_POST['role'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $this->loginService->validate($role, $email, $password);

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'username' => $email
        ]);
    }
}
$controll = new UserController(new LoginService(new UserRepository()));
$controll->loginUser();