<?php
  // Database credentials
  $host = "localhost";
  $user = "root";
  $password = "";
  $database = "student_db";

  // Create PDO connection
  $dsn = "mysql:host=$host;dbname=$database;charset=utf8mb4";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  try {
    $pdo = new PDO($dsn, $user, $password, $options);
  } catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }

  // Handle form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $gender = $_POST["gender"];

    // Validate form data
    $errors = array();
    if (empty($full_name)) {
      $errors[] = "Full name is required";
    }
    if (empty($email)) {
      $errors[] = "Email address is required";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email address";
    }
    if (empty($gender)) {
      $errors[] = "Gender is required";
    }

    // Insert data into database if no errors
    if (count($errors) == 0) {
      $stmt = $pdo->prepare("INSERT INTO students (full_name, email, gender) VALUES (:full_name, :email, :gender)");
      $stmt->execute([
        'full_name' => $full_name,
        'email' => $email,
        'gender' => $gender,
      ]);
      echo "Student information has been saved successfully!";
    } else {
      // Display error messages
      foreach ($errors as $error) {
        echo $error . "<br>";
      }
    }
  }

  // Retrieve the data from the database
  $stmt = $pdo->prepare("SELECT * FROM students");
  $stmt->execute();
  $result = $stmt->fetchAll();

  // Display the data in a table
  echo "<h1>Registered Students</h1>";
  echo "<table border='1'>
  <tr>
  <th>ID</th>
  <th>Full Name</th>
  <th>Email</th>
  <th>Gender</th>
  </tr>";
  foreach ($result as $row) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['full_name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['gender'] . "</td>";
    echo "</tr>";
  }
  echo "</table>";

  // Close database connection
  $pdo = null;
?>
