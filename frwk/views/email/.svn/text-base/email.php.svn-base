<?php
$sent = '';
$error = '';
$from = '';
$message = '';
$phone = '';
$name = '';
$to = 'chad@delogen.com';
    
if($_POST['sendit'])
{
    $from = $_POST['email'];
    $message = $_POST['message'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $domain = $_POST['domain'];
    if ($from == '' || $message == '' || $phone == '' || $name == '' || $domain == '')
    {
        $error = "Missing Parameter. Message not sent. Try again.";
    }
    else
    {
        //$m .= "To: ".$to;
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'From: <$from>' . "\r\n";


        if ($bcc)
        {
            $m .= "BCC: $bcc\n";
        }
        
        if (count($attachments))
        {
            $m .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n\n";
            $m .= "--$boundary\n";
            if ($html)
            {
                $m .= "Content-Type: text/html; charset=ISO-8859-15\n";
            }
            else
            {
                $m .= "Content-Type: text/plain\n";
            }
        }
        else if ($html)
        {
            $m .= "Content-Type: text/html; charset=ISO-8859-15\n";
        }
        else
        {
            $m .= "Content-Type: text/plain\n";
        }
        
        //Attach the body of the message
        $m .= "Name- $name\n";
        $m .= "Phone- $phone\n\n";
        $m .= "Message- $message\r\n";
        $m .= "Domain- $domain";
        
        if (count($attachments))
        {
            $count = 0;
            foreach ($attachments as $attachment)
            {
                if (is_array($attachment) && count($attachment) === 3)
                {
                    $name1 = $attachment[0];
                    $type = $attachment[1];
                    $file = $attachment[2];
                    
                    if ($name && $type && $file)
                    {
                        $m .= "\n\n--$boundary\n";
                        $m .= "Content-Type: $type\n";
                        $m .= "Content-Transfer-Encoding: base64\n";
                        $m .= "Content-Disposition: attachment; filename=\"$name1\"\n\n";
                        $m .= base64_encode($file);
                        $count++;
                    }
                }
            }
            
            if ($count > 0)
            {
                $m .= "--$boundary--\n";
            }
        }
        
        //$m = EscapeMailMessage($m);
        $m = str_replace("'", "'\\''", $m);
        mail($to, 'site request', $m , $headers);
        //`echo -n '$m' | sendmail -i -t -f '$from'`;
        $sent = "Your Message was sent.";
        //echo echo -n '$m' | sendmail -i -t -f '$from'; 
    }
}
?>
                            <table width="300" border="0" cellspacing="0" cellpadding="0">
                            <?
                            if ($sent)
                            {
                                ?><tr><td width="100"><span>
                                <?
                                echo $sent;
                                ?>
                                </span></td></tr>
                                <?
                            }
                            else
                            {
                                if ($_POST['sendit'] && $error != '')
                                {
                                ?><tr><td colspan="2"><span>
                                <?
                                echo $error;
                                ?>
                                </span></td></tr>
                                <?
                                }
                            ?> 
                                <p> All fields are required.</p>
                                <form method="post" action="#">
                              <tr>
                                  
                                <td width="100"><span>Name </span></td>
                                <td width="202"><input type="text" name="name" style="height:18px; width:201px; font-size:12px "></td>
                              </tr>
                              <tr>
                                <td height="42">Your E-mail </td>
                                <td><input type="text" name="email" style="height:18px; width:201px; font-size:12px "></td>
                              </tr>
                              <tr>
                                <td valign="top">Message </td>
                                <td><textarea name="message" style="font-size:12px; overflow:auto "></textarea></td>
                              </tr>
                              <tr>
                                <td height="42">Site Name </td>
                                <td><input type="text" name="domain" style="height:18px; width:201px; font-size:12px "></td>
                              </tr>                              
                              <tr>
                                <td height="42">Phone# </td>
                                <td><input type="text" name="phone" style="height:18px; width:201px; font-size:12px "></td>
                              </tr>
                              <tr>
                                <td height="20">&nbsp;</td>
                                <td><strong><img src="images/spacer.gif" width="40" height="1"><img src="images/spacer.gif" width="45" height="8"> <input type="submit" name="sendit" value="Submit"> </strong></td>
                              </tr>
                            <?
                            }
                            ?>
                            </table>  