<?php
if( isset( $_GET[ 'Login' ] ) ) {
    // Get username
    $user = $_GET[ 'username' ];
    // Get password
    $pass = $_GET[ 'password' ];

    //Хранение паролей (CWE-256)
    //md5 - устарела, лучше использовать bcrypt, SHA или другие современные методы
    $pass = md5( $pass ); 
    // Check the database

    //SQL-инъекции (CWE-89)
    //использование прямых пользовательских данных (переменной $user) в SQL-запросе без предварительной очистки или параметризации делает программу уязвимой к SQL-инъекциям.
    $query = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";

    //Необработанные ошибки базы данных (CWE-209)
    //программа выводит сообщение об ошибке базы данных, которое может раскрыть внутренние данные о структуре базы данных и логике приложения.
    $result = mysqli_query($GLOBALS["___mysqli_ston"],
$query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : 
(($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>');
    if( $result && mysqli_num_rows( $result ) == 1 ) {
        // Get users details
        $row = mysqli_fetch_assoc( $result );
        $avatar = $row["avatar"];
        // Login successful

        //Уязвимость к XSS (CWE-79)
        //код не экранирует переменные, вставляемые в HTML (например, имя пользователя $user и аватар $avatar), что может привести к уязвимости XSS.
        $html .= "<p>Welcome to the password protected area {$user}</p>";
        $html .= "<img src=\"{$avatar}\" />";
    }
    else {
        // Login failed
        $html .= "<pre><br />Username and/or password incorrect.</pre>";
    }
    ((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}
?>

//Неограниченное количество попыток ввода пароля (CWE-307)
//без ограничения количества попыток ввода пароля система становится уязвимой к подбору паролей (брутфорсу).
