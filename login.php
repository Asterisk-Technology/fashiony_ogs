<?php require_once('header.php'); ?>
<?php
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_login = $row['banner_login'];
}
?>

<?php
if (isset($_POST['form1'])) {
    $error_message = '';
    if (empty($_POST['cust_email']) || empty($_POST['cust_password'])) {
        $error_message .= LANG_VALUE_132 . '<br>';
    } else {
        // Validate CAPTCHA
        $secretKey = '6LcfUZwqAAAAAGITOX8cY0BqaeyUUuyczxwt0UqL';
        $captchaResponse = $_POST['g-recaptcha-response'];
        $captchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

        if (empty($captchaResponse)) {
            $error_message .= 'Please complete the CAPTCHA.<br>';
        } else {
            $captchaValidation = file_get_contents($captchaUrl . '?secret=' . $secretKey . '&response=' . $captchaResponse);
            $captchaResponseKeys = json_decode($captchaValidation, true);

            if (!isset($captchaResponseKeys['success']) || !$captchaResponseKeys['success']) {
                $error_message .= 'CAPTCHA verification failed. Please try again.<br>';
            } else {
                // CAPTCHA passed, proceed with user authentication
                $cust_email = strip_tags($_POST['cust_email']);
                $cust_password = strip_tags($_POST['cust_password']);

                $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
                $statement->execute(array($cust_email));
                $total = $statement->rowCount();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($result as $row) {
                    $cust_status = $row['cust_status'];
                    $row_password = $row['cust_password'];
                }

                if ($total == 0) {
                    $error_message .= LANG_VALUE_133 . '<br>';
                } else {
                    // Check password
                    if ($row_password != md5($cust_password)) {
                        $error_message .= LANG_VALUE_139 . '<br>';
                    } else {
                        if ($cust_status == 0) {
                            $error_message .= LANG_VALUE_148 . '<br>';
                        } else {
                            $_SESSION['customer'] = $row;
                            header("location: " . BASE_URL . "dashboard.php");
                            exit;
                        }
                    }
                }
            }
        }
    }
}
?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<div class="page-banner" style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_login; ?>);">
    <div class="inner">
        <h1><?php echo LANG_VALUE_10; ?></h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">

                    
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>                  
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>
                                <div class="form-group">
                                    <label for=""><?php echo LANG_VALUE_94; ?> *</label>
                                    <input type="email" class="form-control" name="cust_email">
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo LANG_VALUE_96; ?> *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="form-group">
                                <div class="g-recaptcha" data-sitekey="6LcfUZwqAAAAAMoNl2A82V_b_YMtSj-FeHz8lSTn"></div><br>    
                                <label for=""></label>
                                    <input type="submit" class="btn btn-primary" value="<?php echo LANG_VALUE_4; ?>" name="form1">
                                </div>
                                <a href="forget-password.php" style="color:#e4144d;"><?php echo LANG_VALUE_97; ?></a>
                            </div>
                        </div>                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>