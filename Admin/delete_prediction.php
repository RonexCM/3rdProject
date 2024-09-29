<?php
include("Conn.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];


    $stmt = $conn->prepare("DELETE FROM predictions WHERE SN = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: prediction.php?status=success");
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "No ID provided for deletion.";
}

$conn->close();
?>
