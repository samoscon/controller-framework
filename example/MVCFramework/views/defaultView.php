<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php';?>
        <title>Login adminstratie</title>
    </head>
    <body>
    	<div class="container-fluid p-3 bg-secondary text-light">
    	    <div class="d-flex w-100 justify-content-between">
	  	<a id="logo" href="<?=_HOMEPAGE?>"><img src="<?=_APPDIR.'assets/apple-touch-icon.png'?>" 
             		style="width:70px; height:70px; padding-top: 10px; padding-left: 20px; padding-bottom: 10px"></a>
		<p class="text-center"><b><?=$request->get('title')?></b></p>
		<p> </p>
	    </div>
    	</div>
        <div class="container mt-5">
		<form name="login" method="POST">
		  <!-- Email input -->
		  <div class="form-outline mb-4">
		    <label class="form-label" for="user"><b>Gebruikersnaam:</b></label>
		    <input type="email" name="user" id="user" class="form-control" />
		  </div>

		  <!-- Password input -->
		  <div class="form-outline mb-4">
		    <label class="form-label" for="password"><b>Paswoord:</b></label>
		    <input type="password" name="password" id="password" class="form-control" />
		  </div>
		  <?php if (!$request->get("passwordIsValid")) { ?>
                        <div class="row mb-4 text-danger">
                            * Combinatie gebruikersnaam / paswoord is niet correct !
                        </div>
                  <?php } ?>

		  <!-- 2 column grid layout for inline styling -->
		  <div class="row mb-4">
		    <div class="col d-flex justify-content-center">
		      <!-- Checkbox -->
		      <div class="form-check">
			<input class="form-check-input" type="checkbox" value="Y" id="rememberMe" name="rememberMe" />
			<label class="form-check-label" for="rememberMe"> Onhoud me </label>
		      </div>
		    </div>

		    <div class="col">
		      <a href="initiatePassword">Paswoord vergeten ?</a>
		    </div>
		  </div>

		  <!-- Submit button -->
		  <button type="submit" class="btn btn-primary btn-block mb-4">Log in</button>

		</form> 
	</div>
    </body>
</html>