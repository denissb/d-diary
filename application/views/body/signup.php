<div class="container-fluid">
    <div class="row-fluid">
        <div class="span4 spacer">
        </div>
        <div class="span4">
            <form class="well form form-vertical login" id="registration" 
                  action="<?php echo base_url(); ?>signup/process" method="post" name="process">
                <h2 class="shadow">Registration</h2>
                <hr />
                <?php if(! is_null($msg)) echo $msg; ?>
                <table class="register">
                    <tr>
                        <td><label>Login:</label></td>
                        <td><input type="text" name="username" class="input-large" id="login-input" placeholder="Login" size="50"></td>
                    </tr>
                    <tr>
                        <td><label class="opt">First name*:</label></td>
                        <td><input type="text" name="f_name" class="input-large" placeholder="First name" size="50"></td>
                    </tr>
                    <tr>
                        <td><label class="opt">Last name*:</label></td>
                        <td><input type="text" name="l_name" class="input-large" placeholder="Last name" size="50"></td>
                    </tr>
                    <tr>
                        <td><label>Password:</label></td>
                        <td><input type="password" name="password" class="input-large" id="pass-input" placeholder="Password" size="32">
                        </td>
                    </tr>
                    <tr>
                        <td><label>Confirm pass:</label></td>
                        <td><input type="password" name="password-confirm" class="input-large" id="pass-confirm" placeholder="Confirm password" size="32"></td>
                    </tr>
                    <tr>
                        <td><label>E-mail:</label></td>
                        <td><input type="text" name="email" class="input-large" id="email-input" placeholder="E-mail" size="64"></td>
                    </tr>
                    <tr>
                        <td><label>Confirm e-mail:</label></td>
                        <td><input type="text" name="email-confirm" class="input-large" id="email-confirm" placeholder="Confirm e-mail" size="64"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
							<button type="button" id="signup" class="btn btn-primary" value="signup">Signup</button>
							<button type="button" class="btn" value="signup" onclick="window.location='<?php echo base_url(); ?>'">Cancel</button>
						</td>
                    </tr>
                </table>
            </form>
        </div><!--/span-->
    </div><!--/row-->