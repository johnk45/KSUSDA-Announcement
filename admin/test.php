<?php
$new_hash = password_hash('admin123', PASSWORD_DEFAULT);
echo "New hash generated: " . $new_hash . "<br>";
if (password_verify('admin123', $new_hash)) {
    echo "Fresh hash verifies OK.<br>";
} else {
    echo "Fresh hash does NOT verify – big problem!<br>";
}
?>