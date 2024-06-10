<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE-edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Informasi Layanan </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
</head>

<!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://localhost:5004">Home</a>
                </li>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:8080/UAS_EAI/index.php">Patients Info</a>
                </li>
            </ul>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:5004/add_patient">Add New Patients
                </li>
            </ul>
        <a class="navbar-brand" href="http://localhost:8080/UAS_EAI/medical_records.php">Medical History</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>    
        </div>
    </nav>

<body>
    <div class="container my-5">
        <h2>Medical History</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>history_id</th>
                    <th>patient_id</th>
                    <th>doctor_id</th>
                    <th>visit_date</th>
                    <th>diagnosis</th>
                    <th>prescription</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "medical_record_management";

                // Create a connection to the database
                $connection = new mysqli($servername, $username, $password, $database);

                // Check the connection
                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                // Fetch all records from the database
                $sql = "SELECT * FROM medical_records";
                $result = $connection->query($sql);

                // Check if the query was successful
                if (!$result) {
                    die("Invalid query: " . $connection->error);
                }

                // Display each patient in a table row
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>{$row['history_id']}</td>
                        <td>{$row['patient_id']}</td>
                        <td>{$row['doctor_id']}</td>
                        <td>{$row['visit_date']}</td>
                        <td>{$row['diagnosis']}</td>
                        <td>{$row['prescription']}</td>
                    
                    </tr>
                    ";
                }

                // Close the connection
                $connection->close();
                ?>
            </tbody>
        </table>
    </div>
    
</body>
</html>

