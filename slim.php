<?php
require('database_connection.php');


// Import classes from the Psr library (standardised HTTP requests and responses)
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Factory\AppFactory;

// Create our app.
$app = AppFactory::create();

// Add routing functionality to Slim. This is not included by default and
// must be turned on.
$app->addRoutingMiddleware();

// Add error handling functionality. The three 'true's indicate:
// - first argument: display full error details
// - second argument: call Slim error handler
// - third argument: log error details

$app->addErrorMiddleware(true, true, true);
 
// For the routes to work correctly, you must set your base path.
// This is the relative path of your webspace on the server, including the
// folder you're using but NOT public_html. Here we are assuming the Slim app
// is saved in the 'slimapp' folder within 'public_html' 
$app->setBasePath('/slim');

// Create our PHP renderer object
$view = new \Slim\Views\PhpRenderer('views');

$app->post('/add_review', function (Request $req, Response $res) use($conn) {
    $post = $req->getParsedBody();

    if (ctype_alnum(trim(str_replace(' ','',$post["review"])))) {
        $check = $conn->prepare("SELECT * FROM pointsofinterest WHERE ID=?");
        $check->bind_param("i", $post["poi_id"]);
        $check->execute();
        $result = $check->get_result();

        if ($row=$result->fetch_assoc()) {
            $stmt = $conn->prepare("INSERT INTO poi_reviews (review, poi_id, approved) VALUES (?, ?, 0)");
            $stmt->bind_param("si", $post["review"], $post["poi_id"]);
            $stmt->execute();

            $res->getBody()->write("<p class='wide'>Review added successfuly. Please wait for it to be approved.</p>");
        }
        else {
            $res->getBody()->write("<p class='wide'>Something went wrong please try again.</p>");
        };
        $check->close();
    }
    else {
        $res->getBody()->write("<p class='wide'>We only allow letters, numbers, and spaces. Please try again.</p>");
    }
    
    return $res;
});

$app->post('/recommend', function (Request $req, Response $res) use($conn) {
    $post = $req->getParsedBody();

    $statement = $conn->prepare("SELECT * FROM pointsofinterest WHERE name=?");
    $statement->bind_param("s", $post["name"]);
    $statement->execute();
    $result = $statement->get_result();

    if($row=$result->fetch_assoc()) {
        $recommended = $row["recommended"] + 1;
    };

    $statement->close();

    // Send an SQL query to the database server
    $statement_two = $conn->prepare("UPDATE pointsofinterest SET recommended=$recommended WHERE name=?" );
    $statement_two->bind_param("s", $post["name"]);
    $statement_two->execute();
    $statement_two->close();


    $res->getBody()->write("<input disabled type='submit' value='&check;' title='Recommend Me'>");
    return $res;
});

$app->post('/approve_review', function (Request $req, Response $res) use($conn) {
    $post = $req->getParsedBody();

    $stmt = $conn->prepare("UPDATE poi_reviews SET approved=1 WHERE id = ?");
    $stmt->bind_param("s", $post["id"]);
    $stmt->execute();

    $res->getBody()->write("<input type='submit' value='Approved' disabled class='card'/>");

    $stmt->close();
    
    return $res;
});

$app->get('/search', function (Request $req, Response $res, array $args) use($conn) {
    $stmt = $conn->query("SELECT * FROM pointsofinterest");

    $response = array();

    while($row=$stmt->fetch_assoc()) {
        array_push($response, $row["name"]);
    };

    $stmt->close();
    
    return $res->withJson($response);
});

$app->get('/search/{search}', function (Request $req, Response $res, array $args) use($conn) {
    $stmt = $conn->prepare("SELECT * FROM pointsofinterest WHERE region = ?");
    $stmt->bind_param("s", $args["search"]);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = array();

    while($row = $result->fetch_assoc()) {
        $response[] = $row;
    };
    
    $stmt->close();

    return $res->withJson($response);
});

$app->run();