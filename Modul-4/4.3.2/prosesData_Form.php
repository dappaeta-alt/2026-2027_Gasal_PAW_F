<?php
require 'validate.inc'; 
$errors = array();

// 1. Cek apakah form sudah disubmit menggunakan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    validateName($errors, $_POST, 'surname');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Person Details Form</title>
</head>
<body>
    <fieldset>
        <legend>Person Details</legend>
        <?php
        // 2. Tampilkan pesan error atau sukses jika form sudah dikirim
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

        // 3. Selalu sertakan form agar tampil di halaman
        include 'form.inc';
        ?>
    </fieldset>
</body>
</html>