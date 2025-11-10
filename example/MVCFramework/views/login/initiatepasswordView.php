<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Paswoord aanvragen</title>
    </head>
    <body>
        <div class="container-fluid p-3 bg-secondary text-light">
            <div class="d-flex w-100 justify-content-between">
                <a id="logo" href="<?= _HOMEPAGE ?>"><img src="<?= _APPDIR . 'assets/apple-touch-icon.png' ?>" 
                            style="width:70px; height:70px; padding-top: 10px; padding-left: 20px; padding-bottom: 10px"></a>
                <p class="text-center"><b>Paswoord aanvragen voor</b></p>
                <p> </p>
            </div>
        </div>
        <div class="container mt-5">
            <div class="row">
                <form method="POST">
                    <div class="form-outline mb-4">
                        <input type="email" class="form-control" name="username" 
                               autofocus="" autocomplete="off" placeholder="Uw email adres.." required="true">
                        <?php if ($request->get('userIsEmpty')) { ?>
                            <div class='text-danger'>* Geef een naam in, aub !</div>
                        <?php } elseif (!$request->get('usernameIsFound')) { ?>
                            <div class='text-danger'>* Gebruikersnaam bestaat niet of is niet geauthoriseerd om een paswoord aan te vragen.</div>
                        <?php } ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mb-4">Bevestig</button>
                </form>
            </div>
        </div>
    </body>
</html>