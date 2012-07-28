<div class="container-fluid">

	<div class="row-fluid">
      <div class="span4 spacer">
	  </div>
        <div class="span4">
			<form class="well form form-vertical login" action="<?php echo base_url();?>login/process" method="post" name="process">
				<h2 class="shadow">Simplecalendar login</h2>
				<hr />
				<table>
					<tr>
						<td><label>Login:</label></td>
						<td><input type="text" name="username" class="input-large" placeholder="Login"></td>
					</tr>
					<tr>
						<td><label>Password:</label></td>
						<td><input type="password" name="password" class="input-large" placeholder="Password"></td>
					</tr>
				</table>  
				  <button type="submit" class="btn btn-primary" value="login">Sign in</button>
			</form>
        </div><!--/span-->
      </div><!--/row-->
