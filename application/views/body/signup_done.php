<div class="container-fluid">

    <div class="row-fluid">
        <div class="span3 spacer">
        </div>
        <div class="span6">
            <div class="well login">
                <h2 class="shadow">Your registration was successful!</h2>
                <?php if (!is_null($msg)) echo $msg; ?>
                <hr />
                <h4 style="text-align:center;">
                    Thank you for signing up, you are registered with the username:
						<h3><?php echo $user; ?></h3>
                </h4>
                <br />
                <p>
                    <?php echo anchor(base_url(), 'Continue to your calendar', array('class' => 'btn btn-primary'));?>
                </p>    
            </div>
        </div><!--/span-->
    </div><!--/row-->