<?php
require 'validate.inc'; 

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateName($errors, $_POST, 'surname');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Person Details Form</title>
</head>
<body>
    <fieldset>
        <legend>Person Details</legend>
        <?php
        // Menampilkan pesan error jika validasi gagal
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($errors) {
                echo '<div style="color: red; margin-bottom: 15px;"><strong>Errors:</strong><br/>';
                foreach ($errors as $field => $error) {
                    echo "- $field $error<br/>";
                }
                echo '</div>';
            } else {
                echo '<div style="color: green; margin-bottom: 15px;"><strong>Data OK!</strong></div>';
            }
        }

        // Menyisipkan file form.inc
        include 'form.inc';
        ?>
    </fieldset>
</body>
</html>