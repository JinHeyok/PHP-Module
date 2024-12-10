<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <input type="text" id="name" certification-name> 
    <input type="text" id="birthDay" certification-birth> 
    <input type="text" id="phone" certification-phone> 
    <button id="auth">본인인증</button>

</body>
<?php  include $_SERVER['DOCUMENT_ROOT'] . '/portOne/index.php'; ?>
<script>
    document.getElementById("auth").onclick = function(){getAuthcication();}
</script>
</html>