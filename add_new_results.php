<?php 

require("functions.php");

if (isset($_SESSION["user"])) {

?>

<div class="container constrained">
<?php

if (ctype_alnum(trim(str_replace(' ','',$_POST["name"]))) && ctype_alnum(trim(str_replace(' ','',$_POST["type"]))) && ctype_alnum(trim(str_replace(' ','',$_POST["region"]))) && ctype_alnum(trim(str_replace(' ','',$_POST["description"])))) {

    $name = $_POST["name"];
    $type = $_POST["type"];
    $region = $_POST["region"];
    $description = $_POST["description"];

    // Try to do the following code. It might generate an exception (error)
    try {
        require("database_connection.php");

        // Send an SQL query to the database server
        $statement = $conn->prepare("INSERT INTO pointsofinterest (name, type, description, username, region) VALUES (?, ?, ?, ?, ?)" );
        $statement->bind_param("sssss", $name, $type, $region, $description, $_SESSION["user"]);
        $statement->execute();
        $statement->close();

        header("Location: poi.php?name=$name");
    }
    // Catch any exceptions (errors) thrown from the 'try' block
    catch(Exception $e) {
        echo "Error: $e";
    };
}
else {
    echo "<h1 class='wide'>We only allow letters, numbers, and spaces in our fields</h1>";
    echo "<h3 class='wide'><a href='add_new.php'>Go back</a></h3>";
}

?>
</div>
<?php

foot();
}

else {
    header("Location: sign_up.php?ref=add_new");
}

?>