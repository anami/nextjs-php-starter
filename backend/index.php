<?php

// Include the Flight PHP framework
require 'vendor/autoload.php';

use MessagePack\Packer;
use MessagePack\BufferUnpacker;

// Configure Flight to connect to an SQLite database
Flight::register('db', 'PDO', array('sqlite:./database.sqlite'));

// Define a route to display all users from the database
Flight::route('/users', function() {
    // Get the PDO instance for the database
    $db = Flight::db();
    
    // Prepare and execute a query to get all users
    $stmt = $db->prepare('SELECT * FROM users');
    $stmt->execute();
    
    // Fetch the results
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the users as a JSON response
    Flight::json($users);
});

// Define a route to display a single user by ID
Flight::route('/user/@id', function($id) {
    // Get the PDO instance for the database
    $db = Flight::db();
    
    // Prepare and execute a query to get the user by ID
    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch the user
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Return the user data as a JSON response
        Flight::json($user);
    } else {
        // Return a 404 response if the user is not found
        Flight::halt(404, json_encode(array('message' => 'User not found')));
    }
});

Flight::route('/api/message', function() {
  $response = [
    'message' => 'Hello from PHP'
  ];
  Flight::json($response);

});

Flight::route('/json-file', function () {
  $file_path = './data/data.json';

  if (file_exists($file_path)) {
    $jsonData = file_get_contents($file_path);
    $data = json_decode($jsonData, true);

    Flight::json($data);

  } else {
    Flight::halt(404, json_encode(array('message' => 'File mot found')));

  }
});

Flight::route('/msgpack-users', function() {
  // Sample data (this could come from your database or a JSON file)
  $data = [
      ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
      ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane@example.com']
  ];
  
  // Initialize MessagePack Packer
  $packer = new Packer();

  // Encode data to MessagePack format
  $msgpackData = $packer->pack($data);
  
  // Set headers to indicate MessagePack content
  header('Content-Type: application/x-msgpack');
  
  // Output MessagePack encoded data
  echo $msgpackData;
});

// Start the Flight application
Flight::start();
