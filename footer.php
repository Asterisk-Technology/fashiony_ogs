<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row)
{
	$footer_about = $row['footer_about'];
	$contact_email = $row['contact_email'];
	$contact_phone = $row['contact_phone'];
	$contact_address = $row['contact_address'];
	$footer_copyright = $row['footer_copyright'];
	$total_recent_post_footer = $row['total_recent_post_footer'];
    $total_popular_post_footer = $row['total_popular_post_footer'];
    $newsletter_on_off = $row['newsletter_on_off'];
    $before_body = $row['before_body'];
}
?>

<?php if($newsletter_on_off == 1): ?>
<section class="home-newsletter">
	<div class="container">
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="single">
					<?php
			if(isset($_POST['form_subscribe']))
			{
                
				if(empty($_POST['email_subscribe'])) 
			    {
			        $valid = 0;
			        $error_message1 .= LANG_VALUE_131;
			    }
			    else
			    {
			    	
				    $statement = $pdo->prepare("SELECT * FROM tbl_subscriber WHERE subs_email=?");
				    $statement->execute(array($_POST['email_subscribe']));
				    $total = $statement->rowCount();							
				    if($total)
				    {
				    	$valid = 0;
				       	$error_message1 .= LANG_VALUE_147;
				    }
				    else
				    {
				    	// Sending email to the requested subscriber for email confirmation
				    	// Getting activation key to send via email. also it will be saved to database until user click on the activation link.
				    	$key = md5(uniqid(rand(), true));
				    		// Getting current date
			    		$current_date = date('Y-m-d');
				    		// Getting current date and time
			    		$current_date_time = date('Y-m-d H:i:s');
				    		// Inserting data into the database
			    		$statement = $pdo->prepare("INSERT INTO tbl_subscriber (subs_email,subs_date,subs_date_time,subs_hash,subs_active) VALUES (?,?,?,?,?)");
			    		$statement->execute(array($_POST['email_subscribe'],$current_date,$current_date_time,$key,0));
				    		// Sending Confirmation Email
			    		$to = $_POST['email_subscribe'];
						$subject = 'Subscriber Email Confirmation';
						
						// Getting the url of the verification link
						$verification_url = BASE_URL.'verify.php?email='.$to.'&key='.$key;

						$message = '
Thanks for your interest to subscribe our newsletter!<br><br>
Please click this link to confirm your subscription:
					'.$verification_url.'<br><br>
This link will be active only for 24 hours.
					';

						$headers = 'From: ' . $contact_email . "\r\n" .
							   'Reply-To: ' . $contact_email . "\r\n" .
							   'X-Mailer: PHP/' . phpversion() . "\r\n" . 
							   "MIME-Version: 1.0\r\n" . 
							   "Content-Type: text/html; charset=ISO-8859-1\r\n";

						// Sending the email
						mail($to, $subject, $message, $headers);

						$success_message1 = LANG_VALUE_136;
			    	}
				    
			    }
			}
			if($error_message1 != '') {
				echo "<script>alert('".$error_message1."')</script>";
			}
			if($success_message1 != '') {
				echo "<script>alert('".$success_message1."')</script>";
			}
			?>
				<form action="" method="post">
					<?php $csrf->echoInputField(); ?>
					<h2><?php echo LANG_VALUE_93; ?></h2>
					<div class="input-group">
			        	<input type="text" class="form-control" placeholder="<?php echo LANG_VALUE_95; ?>" name="email_subscribe">
			         	<span class="input-group-btn">
			         	<button class="btn btn-theme" type="submit" name="form_subscribe"><?php echo LANG_VALUE_92; ?></button>
			         	</span>
			        </div>
				</div>
				</form>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>




<div class="footer-bottom">
	<div class="container">
		<div class="row">
			<div class="col-md-12 copyright">
				<?php echo $footer_copyright; ?>
			</div>
		</div>
	</div>
</div>


<a href="#" class="scrollup">
	<i class="fa fa-angle-up"></i>
</a>

<style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Chatbot Window Styles */
        #chatbot-widget {
            z-index: 2;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            height: 400px;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        #chat-header {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            text-align: center;
            cursor: pointer;
        }

        #chatbox {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        #input {
            display: flex;
            padding: 10px;
            background-color: #fff;
            border-top: 1px solid #ccc;
        }

        #input input {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #input button {
            padding: 8px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 5px;
            border-radius: 4px;
        }

        /* Minimized Widget Styles */
        #chatbot-widget.minimized {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            bottom: 20px;
            right: 20px;
        }

        #chat-header.minimized {
            text-align: center;
            background-color: #007BFF;
            color: white;
            padding: 15px;
            cursor: pointer;
        }

        #minimized-btn {
            font-size: 20px;
            text-align: center;
            cursor: pointer;
        }
    </style>
	<!-- Chatbot Widget -->
    <div id="chatbot-widget">
        <div id="chat-header" onclick="toggleChat()">Chatbot</div>
        <div id="chatbox"></div>
        <div id="input">
            <input type="text" id="message" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>
	<script>
        let isMinimized = false;

        // Function to toggle between minimized and expanded view
        function toggleChat() {
            const widget = document.getElementById('chatbot-widget');
            const header = document.getElementById('chat-header');
            const chatbox = document.getElementById('chatbox');
            const input = document.getElementById('input');

            if (isMinimized) {
                widget.classList.remove('minimized');
                header.textContent = 'Chatbot';
                chatbox.style.display = 'flex';
                input.style.display = 'flex';
                isMinimized = false;
            } else {
                widget.classList.add('minimized');
                header.innerHTML = '<span id="minimized-btn">+</span>';
                chatbox.style.display = 'none';
                input.style.display = 'none';
                isMinimized = true;
            }
        }

        // Function to append a message to the chatbox
        function appendMessage(text, sender = 'user') {
            const div = document.createElement('div');
            div.textContent = text;
            div.style.textAlign = sender === 'user' ? 'right' : 'left';
            document.getElementById('chatbox').appendChild(div);
            document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
        }

        // Function to send a message
        function sendMessage() {
            const message = document.getElementById('message').value;
            if (!message) return;
            appendMessage(message, 'user');
            document.getElementById('message').value = '';

            // Prepare the payload
            const payload = { message };
            if (message.toLowerCase() === 'yes') {
                payload.ip = '127.0.0.1'; // Add the IP if the message is 'yes'
            }

            // Send the request
            fetch('chatbot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload) // Send the payload with or without IP
            })
            .then(response => response.json())
            .then(data => appendMessage(data.response, 'bot'))
            .catch(error => appendMessage('Error: ' + error.message, 'bot'));
        }

        document.addEventListener('DOMContentLoaded', () => {
            appendMessage("Hi there! How can I assist you today?", 'bot');
        });

    </script>




<script src="assets/js/jquery-3.7.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/megamenu.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/owl.animate.js"></script>
<script src="assets/js/jquery.bxslider.min.js"></script>
<script src="assets/js/jquery.magnific-popup.min.js"></script>
<script src="assets/js/rating.js"></script>
<script src="assets/js/jquery.touchSwipe.min.js"></script>
<script src="assets/js/bootstrap-touch-slider.js"></script>
<script src="assets/js/select2.full.min.js"></script>
<script src="assets/js/custom.js"></script>
</script>
</body>
</html>