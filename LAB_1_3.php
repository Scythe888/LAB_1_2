<?php

$max_attempts = 5; 
$block_duration = 300; 

if (isset($_GET['Login'])) {
    $user = $_GET['username'];
    $pass = $_GET['password'];
    $pass_hashed = password_hash($pass, PASSWORD_BCRYPT);

    $query_attempts = "SELECT login_attempts, last_attempt_time FROM `users` WHERE user = ?;";
    $stmt_attempts = $mysqli->prepare($query_attempts);
    $stmt_attempts->bind_param('s', $user);
    $stmt_attempts->execute();
    $result_attempts = $stmt_attempts->get_result();

    if ($result_attempts && mysqli_num_rows($result_attempts) == 1) {
        $row_attempts = $result_attempts->fetch_assoc();
        $login_attempts = $row_attempts["login_attempts"];
        $last_attempt_time = $row_attempts["last_attempt_time"];

        if ($login_attempts >= $max_attempts && time() - strtotime($last_attempt_time) < $block_duration) {
            die('<pre>User is blocked. Please try again later.</pre>');
        } else {
            $query = "SELECT * FROM `users` WHERE user = ? AND password = ?;";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ss', $user, $pass_hashed);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && mysqli_num_rows($result) == 1) {
                
                $reset_attempts_query = "UPDATE `users` SET login_attempts = 0, last_attempt_time = NULL WHERE user = ?;";
                $stmt_reset = $mysqli->prepare($reset_attempts_query);
                $stmt_reset->bind_param('s', $user);
                $stmt_reset->execute();

                $row = $result->fetch_assoc();
                $avatar = $row["avatar"];
                $html .= "<p>Welcome to the password protected area " . htmlspecialchars($user) . "</p>";
                $html .= "<img src=\"" . htmlspecialchars($avatar) . "\" />";
            } else {
                
                $new_login_attempts = min($login_attempts + 1, $max_attempts);
                $update_attempts_query = "UPDATE `users` SET login_attempts = ?, last_attempt_time = NOW() WHERE user = ?;";
                $stmt_update = $mysqli->prepare($update_attempts_query);
                $stmt_update->bind_param('is', $new_login_attempts, $user);
                $stmt_update->execute();

                die('<pre>Username and/or password incorrect.</pre>');
            }
        }
    }
}
?>
