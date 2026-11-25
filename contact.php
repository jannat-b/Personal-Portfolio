
<?php

header("Content-Type: application/json");

require_once "db.php";

// Accept only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}

// Receive form information
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$contact_method = trim($_POST["contact_method"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

// Validate required fields
if (
    $name === "" ||
    $email === "" ||
    $contact_method === "" ||
    $subject === "" ||
    $message === ""
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please complete all fields."
    ]);

    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Please enter a valid email address."
    ]);

    exit;
}

// Validate selected options
if (
    !in_array($contact_method, ["Email", "Phone"], true) ||
    !in_array($subject, [
        "General Inquiry",
        "Project Discussion",
        "Collaboration",
        "Other"
    ], true)
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid selection."
    ]);

    exit;
}

// Insert message using prepared statement
$sql = "INSERT INTO contact_messages
        (name, email, contact_method, subject, message)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $name,
    $email,
    $contact_method,
    $subject,
    $message
);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Thank you! Your message has been submitted successfully."
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Unable to save your message. Please try again."
    ]);

}

$stmt->close();
$conn->close();

?>
