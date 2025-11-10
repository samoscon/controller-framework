<?php
$user = $request->get('user');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Admin Homepage</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a id="logo" href="<?= _HOMEPAGE ?>"><img src="<?= _APPDIR . 'assets/apple-touch-icon.png' ?>" 
                            style="width:70px; height:70px; padding-top: 10px; padding-left: 20px; padding-bottom: 10px"></a>
                <p class="text-center"><b>Admin homepage</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-3">
            <p>Welkom <?=$user->name?>. You are registered as an administrator !!!</p>
            <p>From here onward you can build your project with the controller framework.</p>
        </div>
    </body>
</html>
