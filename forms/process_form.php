<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if file is uploaded
    if(isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = array('pdf', 'docx'); // allowed file types
        $maxFileSize = 1024 * 1024 * 5; // 5MB

        $fileType = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $fileSize = $_FILES['file']['size'];

        if(in_array($fileType, $allowedTypes)) {
            if($fileSize <= $maxFileSize) {
                // File type and size are valid, proceed with upload
                $firstName = $_POST['firstName'];
                $lastName = $_POST['lastName'];
                $mobileNumber = $_POST['mobileNumber'];
                $email = $_POST['email'];

                // HR manager's email address
                $to = "hrm@almadinagroup.ae";

                // Email subject
                $subject = "New job application from $firstName $lastName";

                // Email body
                $message = "First Name: $firstName\n";
                $message.= "Last Name: $lastName\n";
                $message.= "Mobile Number: $mobileNumber\n";
                $message.= "Email: $email\n";

                // Read the file content
                $file = $_FILES['file']['tmp_name'];

                if (!is_uploaded_file($file)) {
                    echo "File upload error: File not found or not uploaded.";
                    exit;
                }

                $file_name = $_FILES['file']['name'];
                $file_content = file_get_contents($file);

                if ($file_content === false) {
                    echo "File upload error: Unable to read file content.";
                    exit;
                }

                $encoded_content = chunk_split(base64_encode($file_content));

                // Unique boundary
                $boundary = md5("sanwebe");

                // Headers
                $headers = "From: $email\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n\r\n";

                // Plain text part
                $body = "--$boundary\r\n";
                $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
                $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                $body .= $message . "\r\n";

                // Attachment part
                $body .= "--$boundary\r\n";
                $body .= "Content-Type: application/octet-stream; name=\"$file_name\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n\r\n";
                $body .= $encoded_content . "\r\n";
                $body .= "--$boundary--";

                // Send email
                if (mail($to, $subject, $body, $headers)) {
                    echo "Email sent successfully!";
                } else {
                    echo "Failed to send email.";
                }
            } else {
                echo "File size exceeds the maximum allowed size of 5MB.";
            }
        } else {
            echo "Invalid file type. Only PDF and DOCX files are allowed.";
        }
    } else {
        // Handle file upload errors
        $uploadError = $_FILES['file']['error'];
        switch ($uploadError) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                echo "File size exceeds the maximum allowed size.";
                break;
            case UPLOAD_ERR_PARTIAL:
                echo "File was only partially uploaded.";
                break;
            case UPLOAD_ERR_NO_FILE:
                echo "No file was uploaded.";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                echo "Missing a temporary folder.";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                echo "Failed to write file to disk.";
                break;
            case UPLOAD_ERR_EXTENSION:
                echo "File upload stopped by a PHP extension.";
                break;
            default:
                echo "Unknown file upload error.";
                break;
        }
    }
} else {
    echo "Form submission method not allowed.";
}
?>
