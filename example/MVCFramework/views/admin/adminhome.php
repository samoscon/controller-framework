<?php
$user = $request->get('user');
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title><?=$request->get('title')?></title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
                <b>Your title</b>
        </div>
        <div class="container mt-3">
            <p>Welkom <?=$user->name?>. You are registered as an administrator !!!</p>
        </div>
    </body>
</html>
