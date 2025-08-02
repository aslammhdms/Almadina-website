<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $mobileNumber = $_POST['mobileNumber'];
    $email = $_POST['email'];
    
    // HR manager's email address
    $to = "design@almadinagroup.ae";
    
    // Email subject
    $subject = "New job application from $firstName $lastName";
    
    // Email body
    $message = "First Name: $firstName\n";
    $message .= "Last Name: $lastName\n";
    $message .= "Mobile Number: $mobileNumber\n";
    $message .= "Email: $email\n";
    
    // Send email
    $headers = "From: $email\r\n";
    
    // Check if a file is uploaded
    if(isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_size = $_FILES['file']['size'];
        
        // Read the file content
        $file_content = file_get_contents($file);
        
        // Encode and attach the file to the email
        $attachment = chunk_split(base64_encode($file_content));
        
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"boundary\"\r\n";
        $headers .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
        $headers .= "--boundary\r\n";
        $headers .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
        $headers .= "Content-Transfer-Encoding: base64\r\n";
        $headers .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
        $headers .= "$attachment\r\n";
        $headers .= "--boundary--\r\n";
    }
    
    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email.";
    }
} else {
    echo "Form submission method not allowed.";
}
?>
