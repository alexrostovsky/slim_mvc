<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$config = [
    'settings' => [
        'db' => [
            'engine' => 'mysql',
            'host' => 'localhost',
            'dbname' => 'mvc',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci'
        ],
    ],
];

$app = new \Slim\App($config);

// Get container
$container = $app->getContainer();

// Register component on container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../views/', []);
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container->get('router'), $basePath));
    //return component
    return $view;
};

// Register component on container
$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};



// $container['NAME'] = function($c) {
	//if we need some dependencies:
    // $db = $c->get('db');
    // $somethingElse = $c->get('nameOfDependency')
    // return new NAME();
// };


$app->get('/', function (Request $request, Response $response, array $args) {
    return $response->getBody()->write("Hello, MVC practice");
});


$app->get('/list', function (Request $request, Response $response, array $args) {
    $query = $this->db->prepare('SELECT * FROM mvc.game');
    $query->execute();
    $games = $query->fetchAll(\PDO::FETCH_ASSOC);
    
    $format = isset($request->getQueryParams()['format'])
        ? $request->getQueryParams()['format']
        : 'json';
    if ($format == 'json') {
        return $response->withJson([
            'games' => $games
        ], 200);
    }

    if ($format == 'html') {
        return $this->view->render($response, 'list.html', [
            'games' => $games
        ]);
    }
});

$app->get('/{id}', function (Request $request, Response $response, array $args) {
	
	$id = $args['id'];

    $query = $this->db->prepare('SELECT * FROM mvc.game where id = :id');
    $query->bindParam(':id', $id, \PDO::PARAM_INT);
    $query->execute();
    $game = $query->fetchAll(\PDO::FETCH_ASSOC);

    if(isset($game[0])) {
    	$game = $game[0];
		$format = isset($request->getQueryParams()['format'])
	        ? $request->getQueryParams()['format']
	        : 'json';
	    if ($format == 'json') {
	        return $response->withJson([
	            'game' => $game
	        ], 200);
	    }

	    if ($format == 'html') {
	        return $this->view->render($response, 'index.html', [
	            'game' => $game
	        ]);
	    }
    } else {
        throw new \Exception('not found game with id: '. $id, 422);
    }
   
});

// require __DIR__ . '/../src/router.php';

$app->run();