<?php

$isPostback = (isset($_POST['isPostBack']) && $_POST['isPostBack'] == 1);

$memberEmail = null;
if (isset($_POST['memberEmail']))
{
    $memberEmail = $_POST['memberEmail'];
}

$memberPassword = null;
if (isset($_POST['memberPassword']))
{
    $memberPassword = $_POST['memberPassword'];
}

$rememberMe = true;
if ($isPostback && !isset($_POST['rememberMe']))
{
    $rememberMe = false;
}

$loginError = false;
if ($isPostback)
{
    // login form submitted

    require_once 'php/member_authentication.php';
    $memberAuthentication = new memberAuthentication();
    if ($memberAuthentication->login($memberEmail, $memberPassword, $rememberMe))
    {
        header("Location: runlog.php");
        exit();
    }
    else
    {
        $loginError = true;
    }
}

require_once 'php/html_page_init.php';
?>
<!DOCTYPE HTML>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<html dir="RTL">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>הרוח השניה - כניסה</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/3.5.1/build/cssreset/cssreset-min.css">
<link href='./css/runlog.css?v=<?php echo CSS_VERSION;?>' rel='stylesheet' type='text/css'/>
<script src="./js/jquery.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#memberEmail').bind('invalid', function(){
            this.setCustomValidity("");
            if (!this.validity.valid) {
                if ($(this).val() == '')
                {
                    this.setCustomValidity("נא להזין אימייל");
                }
                else
                {
                    this.setCustomValidity("נא להזין אימייל חוקי");
                }
            }
        });
        $('#memberPassword').bind('invalid', function(){
            this.setCustomValidity("");
            if (!this.validity.valid) {
                this.setCustomValidity("נא להזין סיסמא");
            }
        });
    })

</script>
<style>
    #loginContainer
    {
        padding: 20px;
        border: solid 1px #e2e2e2;
        border-radius: 5px;
        margin-top: 100px;
        margin-left: auto;
        margin-right: auto;
        width: 300px;
    }
    #login .formRow
    {
        margin-top:10px;
    }
    #login .formRowCaption
    {
        display: inline-block;
        width: 60px;
    }
    #memberEmail,
    #memberPassword
    {
        width: 194px;
    }
    #errorMsg
    {
        color: #8b0000;
    }
</style>
</head>
<body>
    <?php require 'widgets/header.php'; ?>
    <div id="loginContainer">
        <form id="login" method="post">
            <div class="formRow">
                <span class="formRowCaption">אימייל:</span>
                <input type="email" id="memberEmail" name="memberEmail" value="<?php echo $memberEmail; ?>" required dir="ltr">
            </div>
            <div class="formRow">
                <span class="formRowCaption">סיסמא:</span>
                <input type="password" id="memberPassword" name="memberPassword" value="<?php echo $memberPassword; ?>" required dir="ltr">
            </div>
            <div class="formRow">
                <span id="errorMsg" <?php if (!$loginError) {?>style="display:none;"<?php } ?>>אימייל או סיסמא שגויים</span>
            </div>
            <div class="formRow">
                <input type="checkbox" id="rememberMe" name="rememberMe" <?php if ($rememberMe){ ?>checked<?php } ?>>
                <label for="rememberMe">זכור אותי במחשב זה</label>
            </div>
            <div class="formRow">
                <input type="submit" value="כניסה">
            </div>
            <input type="hidden" id="isPostBack" name="isPostBack" value="1">
        </form>
    </div>
</body>
</html>