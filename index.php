<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE-edge" />
    <meta name="viewport" content="width=device-width, intial-scale=1.0" />
    <title> Informasi Layanan </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css
    " />

</head>

<!-- Navigation bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="http://localhost:5004">Home</a>
                </li>
        <a class="navbar-brand" href="http://localhost:8080/UAS_EAI/index.php">Patients Info</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:5004/add_patient">Add New Patients</a>
                </li>
            </ul>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="http://localhost:8080/UAS_EAI/medical_records.php">Medical History</a>
                </li>
            </ul>
        </div>
    </nav>

<body>
    <div class="container my-5">
        <h2>List of Patients</h2>
        <a class="btn btn-primary" href="http://localhost:5004/add_patient" role="button">New Patient</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>patient_id</th>
                    <th>name</th>
                    <th>dob</th>
                    <th>gender</th>
                    <th>address</th>
                    <th>phone</th>
                    <th>email</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $database = "medical_consultation";

                $connection = new mysqli($servername, $username, $password, $database);

                if ($connection->connect_error) {
                    die("Connection failed: " . $connection->connect_error);
                }

                $sql = "SELECT * FROM patients";
                $result = $connection->query($sql);

                if (!$result) {
                    die("invalid query: " . $connection->error);
                }

                while($row = $result->fetch_assoc()) {
                    echo "
                    <tr>
                        <td>$row[patient_id]</td>
                        <td>$row[name]</td>
                        <td>$row[dob]</td>
                        <td>$row[gender]</td>
                        <td>$row[address]</td>
                        <td>$row[phone]</td>
                        <td>$row[email]</td>
                        <td>
                            <a class='btn btn-primary btn-sm' href='http://localhost:8080/UAS_EAI/edit.php?patient_id=$row[patient_id]'>Edit</a>
                            <a class='btn btn-danger btn-sm' href='http://localhost:8080/UAS_EAI/delete.php?patient_id=$row[patient_id]'>Delete</a>
                        </td>
                    </tr>
                    ";
                }
                ?>

                
            </tbody>
        </table>

    </div>
    
</body>
</html>