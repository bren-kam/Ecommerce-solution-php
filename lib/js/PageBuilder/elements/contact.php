<?php
if (isset($_GET['name']) && isset($_GET['email'])  && isset($_GET['message'])) 
{
 
  $name = $_GET['name'];
  $email = $_GET['email'];
  $subject = $_GET['subject'];
  $message = $_GET['message'];
  ob_start();
  if (!empty($name) && !empty($subject) && !empty($email)  && !empty($message)) {
  
  $to = 'mr.jaspreetbhatia@gmail.com';
  $headers = "FROM: <".$email.">";
  $body = 'New client enquiry received as follows:-'."\n\n".'Name = '.$name."\n\n".'Message = '.$message."\n\n";
  mail($to, $subject, $body, $headers); 
  echo 'Your Message Has been Sent Successfully';
  } 
  else
  {
  echo 'Please fill in all the fields';
  }
  }
  
 
?>