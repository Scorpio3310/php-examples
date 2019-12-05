<?php 

function post_captcha($user_response) {
    $fields_string = '';
    $fields = array(
        'secret' => '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe', // Google test key, change later with your own key
        'response' => $user_response
    );
    foreach($fields as $key=>$value)
    $fields_string .= $key . '=' . $value . '&';
    $fields_string = rtrim($fields_string, '&');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
    curl_setopt($ch, CURLOPT_POST, count($fields));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

// Call the function post_captcha
$res = post_captcha($_POST['g-recaptcha-response']);

if (!$res['success']) {
   // echo 'reCAPTCHA error: Check to make sure your keys match the registered domain and are in the correct locations. You may also want to doublecheck your code for typos or syntax errors.';

} else {
    //echo '<br><p>CAPTCHA was completed successfully!</p><br>';

    // Php mailer file Include
    require('phpmailer/PHPMailerAutoload.php');

    // Form Field Data
    $name = trim($_POST['name']);
    $user_email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Recipient Data
    $to_email = 'email@email.email'; 
    $to_name = 'email@email.email';

    // Success Message
    $success_msg = 'Message sent! We will respond as soon as possible.';

    
    if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) 
    {
        $signal = 'bad';
        $msg = 'Enter right e-mail!';
    }elseif (empty($name)) {
        $signal = 'bad';
        $msg = 'Name is empty!';
    }elseif (strlen($message)<5) {
        $signal = 'bad';
        $msg = 'Short too message!';
    }
    else{
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';

        //  ******************************************
        //  Use these Settings if email is not working.
        //  ******************************************

        /*$mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'yourusername@gmail.com';                 // SMTP username
        $mail->Password = 'Your Gmail password';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587; */                                   // TCP port to connect to

        //  ******************************************
        //  Use these Settings if email is not working.
        //  ******************************************
        

        $mail->From = $user_email;
        $mail->FromName = $name;
        $mail->addAddress($to_email, $to_name);
        $mail->addReplyTo($user_email, $name);

        $mail->isHTML(true);                                  // Set email format to HTML
        
        $mail->Subject = 'New email from your Website';
        $mail->Body    = 'Name: '.$name.' <br />Message: '.$message;
        
        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $signal = 'ok';
            $msg = $success_msg;
        }
    }

    $data = array(
        'signal' => $signal,
        'msg' => $msg
    );
    echo json_encode($data);

}

