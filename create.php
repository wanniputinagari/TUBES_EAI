<?php
$servename ="localhost";
$username = "root";
$password = "";
$database = "medical_consultation";

// Create connection
$connection = new mysqli($servename, $username, $password, $database);


$patient_id = "";
$name = "";
$dob = "";
$gender = "";
$address = "";
$phone = "";
$email = "";


$errorMessage = "";
$successMessage = "";

if ( $_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST["patient_id"];
    $name = $_POST["name"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];

    do {
        if ( empty($patient_id) || empty($name) || empty($dob) || empty($gender) || empty($address) || empty($phone) || empty($email)) {
            $errorMessage = "All t fields are required";
            break;
        }

        $sql = "INSERT INTO clients (patient_id, name, dob, gender, address, phone, email) " .
               "VALUES ('$patient_id', '$name', '$dob', '$gender', '$address', '$phone', '$email')";
        $result = $connection->query($sql);

        if (!$result){
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        $patient_id = "";
        $name = "";
        $dob = "";
        $gender = "";
        $address = "";
        $phone = "";
        $email = "";

        $successMessage = "Client added correctly";

        header("location: ");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width-device-width, initial-scale-1.0">
    <title>Informasi Layanan</title>
    <link rel="stylesheet" href=""> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>List of Patients</h2>

        <?php
        if ( empty($errorMessage)) {
            echo "
            <div class='alert alert-wawrning alert-dismissible fade show' role='alert>'
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label-'Close'></button>
            ";
        }
        ?>

        <form method="post">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">patient_id</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="patient_id" value="<?php echo $patient_id; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="name" value="<?php echo $name; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">dob</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="dob" value="<?php echo $dob; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">gender</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="gender" value="<?php echo $gender; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">adress</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="address" value="<?php echo $address; ?>">
                </div>
            </div>
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">phone</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="phone" value="<?php echo $phone; ?>">
                </div>
            </div>

            <?php
            if ( empty($successMessage)) {
                echo "
                <div class='alert alert-wawrning alert-dismissible fade show' role='alert>'
                <strong>$succesMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label-'Close'></button>
                ";
            }
            ?>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">email</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control"  name="email" value="<?php echo $email; ?>">
                </div>
            </div>
        </form>
    </div>
</body>
</html>