<?php
if (isset($_GET["patient_id"])) {
    $id = $_GET["patient_id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "medical_consultation";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $stmt = $connection->prepare("DELETE FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $connection->close();
}

header("Location: http://localhost:8080/UAS_EAI/index.php");
exit;
?>
