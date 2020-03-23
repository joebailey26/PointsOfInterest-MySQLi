<?php
    session_start();

    function page($name, $page) {
        if ($name == $page) {
            return "aria-current='page'";
        }
    }

    function user($name) {
        if (isset($_SESSION["admin"])) {
            return "<a href='my_account.php'".page($name, "My Account").">My Account</a>
                    <a href='reviews.php'".page($name, "Reviews").">Reviews</a>
                    <a href='logout.php'>Log Out</a>";
        }
        elseif (isset($_SESSION["user"])) {
            return "<a href='my_account.php'".page($name, "My Account").">My Account</a>
                    <a href='logout.php'>Log Out</a>";
        }
        else {
            return "<a href='login.php'".page($name, "Log In").">Log In</a>
                    <a href='sign_up.php'".page($name, "Sign Up").">Sign Up</a>";
        }
    }

    function head($name) {
        echo "<!DOCTYPE html>
                <html lang='en-GB'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>$name | Points of Interest</title>
                    <link href='assets/css/main.css' rel='stylesheet' />
                </head>
                <body class='".str_replace(" ", "_", $name)."'>
                    <nav>
                        <div class='container'>
                            <div class='main_nav'>
                                <a href='index.php'".page($name, "Home").">Home</a>
                                <a href='add_new.php'".page($name, "Add a new POI").">Add a new POI</a>
                                <a href='search.php'".page($name, "Search").">Search</a>
                            </div>
                            <div class='user_nav'>".user($name)."</div>
                        </div>
                    </nav>
                <main>
                ";
    }

    function foot() {
        echo "</main>
            <footer>
                <div class='container'>
                    <div class='copyright'>© ".date("Y")." Points of Interest. All rights reserved.</div>
                </div>
            </footer>
        </body>
        </html>";
    }