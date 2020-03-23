<?php 
    require("functions.php");

    if (ctype_alnum($_POST["username"]) && ctype_alnum($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        if ($_SESSION["user"] == $username) {
            header("Location: my_account.php");
        }
        else {
            // Try to do the following code. It might generate an exception (error)
            try {
                require("database_connection.php");

                $statement = $conn->prepare("SELECT * FROM poi_users WHERE username = ? AND password = ?");
                $statement->bind_param("ss", $username, $password);
                $statement->execute();

                if($row=$statement->get_result()->fetch_assoc()) {
                    $_SESSION["user"] = $row["username"];
                    if ($row["isadmin"] == 1) {
                        $_SESSION["admin"] = $row["isadmin"];
                    };
                    header("Location: my_account.php");
                }
                else {
                    head("Log In");
                    echo "<div class='container'><h3 class='wide' style='text-align:center'>Username or Password incorrect.</h3></div>";
                    foot();
                }
                $statement->close();
            }
            // Catch any exceptions (errors) thrown from the 'try' block
            catch(Exception $e) {
                echo "Error: $e";
            }
        }
    }
    else {
        head("Log In");
        echo "<div class='container'><h3 class='wide' style='text-align:center'>We only allow letters, numbers, and spaces. Please try again.</h3></div>";
        foot();
    }
?>