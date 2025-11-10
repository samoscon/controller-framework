<!DOCTYPE html>
<html>
    <head>
        <?php include 'MVCFramework/views/includes/head.php'; ?>
        <title>Foutmelding</title>
    </head>
    <body>
    	<div class="container-fluid p-3 bg-secondary text-light">
    	    <div class="d-flex w-100 justify-content-between">
	  	<a id="logo" href="<?=_HOMEPAGE?>"><img src="<?=_APPDIR.'assets/apple-touch-icon.png'?>" 
             		style="width:70px; height:70px; padding-top: 10px; padding-left: 20px; padding-bottom: 10px"></a>
		<p class="text-center"><b>Error</b></p>
		<p> </p>
	    </div>
    	</div>
        <div class="container mt-5">
            <div class="row">
                <p class="text-danger">Error:<br> 
                    <b><?=$request->getFeedbackString('<br>')?></b></p>
                <form  action="<?=_APPDIR?>">
                    <button type="submit" class="btn btn-primary btn-block mb-4">OK</button>
                </form>
            </div>
        </div>
    </body>
</html>
