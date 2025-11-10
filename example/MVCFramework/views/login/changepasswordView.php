<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php';?>
        <title>Paswoord aanpassen</title>
    </head>
    <body>
    	<div class="container-fluid p-3 bg-secondary text-light">
    	    <div class="d-flex w-100 justify-content-between">
	  	<a id="MMClogo1" href="<?=_HOMEPAGE?>"><img src="<?=_APPDIR.'assets/apple-touch-icon.png'?>" 
             		style="width:70px; height:70px; padding-top: 10px; padding-left: 20px; padding-bottom: 10px"></a>
		<p class="text-center"><b>Paswoord aanpassen</b></p>
		<p> </p>
	    </div>
    	</div>
        <div class="container mt-5">
            <form method="POST">
                <p class="text-dark">voor <b><?=$request->get('email')?></b></p>
                
                <div class="form-outline mb-4">
                    <label class="form-label text-primary" for="password">Nieuw paswoord</label>
                    <input type="password" name="password" id="password" class="form-control" required="true"/>
                    <?=$request->get('passwordIsEmpty') ? 
                        '<div class="text-danger">* Geef een paswoord in, aub!</div>' :
                        ''
                    ?>
                </div>
                    
                <div class="form-outline mb-4">
                    <label class="form-label text-primary" for="password2">Confirmeer paswoord</label>
                    <input type="password" name="password2" id="password2" class="form-control" required="true"/>
                    <?php if ($request->get('password2IsEmpty')) { ?>
                        <div class='text-danger'>* Herhaal het nieuwe paswoord, aub!</div>
                    <?php } elseif (!$request->get('passwordIsValid')) { ?>
                        <div class='text-danger'>* Nieuwe paswoorden matchen niet !</div>
                    <?php } ?>                        
                </div>

		<!-- Submit button -->
		<button type="submit" class="btn btn-primary btn-block mb-4">Bevestig</button>
	    </form> 
        </div>
    </body>
</html>