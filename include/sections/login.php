<div id='loginInterface' class='interface'>
	<table class='feature' id='loginBox'>
		<thead><tr><th colspan='2'>Log in to access Psychomorph</th></tr></thead>
		<tbody>
			<tr>
				<td><label for='login_email'>email</label></td>
				<td><input type='email' id='login_email' value='<?= $_COOKIE['email'] ?>' /></td>
			</tr>
			<tr class='login_item'>
				<td><label for='login_password'>password</label></td>
				<td><input type='password' id='login_password' /></td>
			</tr>
			<tr class='reg_item' title='Access to online psychomorph is currently restricted. Ask Lisa for an invite code if you would like to be an alpha tester.'>
				<td><label for='login_auth'>invite code</label></td>
				<td><input type='text' id='login_auth' /></td>
			</tr>
			<tr class='reg_item'>
				<td></td>
				<td class='smallnote'>The following information is optional, but helps us figure out who our users are.</td>
			</tr>
			<tr class='reg_item'>
				<td><label for='reg_firstname'>first name</label></td>
				<td><input type='text' id='reg_firstname' /></td>
			</tr>
			<tr class='reg_item'>
				<td><label for='reg_lastname'>last name</label></td>
				<td><input type='text' id='reg_lastname' /></td>
			</tr>
			<tr class='reg_item'>
				<td><label for='reg_org'>organisation</label></td>
				<td><input type='text' id='reg_org' /></td>
			</tr>
				<tr class='reg_item'>
				<td><label for='reg_sex'>sex</label></td>
				<td><div id='reg_sex'>
					<input type='radio' id='reg_sex_female' name='reg_sex' value='female' />
					<label for='reg_sex_female'>female</label>
					<input type='radio' id='reg_sex_male' name='reg_sex' value='male' />
					<label for='reg_sex_male'>male</label>
					<input type='radio' id='reg_sex_other' name='reg_sex' value='other' />
					<label for='reg_sex_other'>other</label>
				</div></td>
			</tr>
			</tr>
				<tr class='reg_item'>
				<td><label for='reg_use'>I plan to use psychomorph for</label></td>
				<td><div id='reg_use'>
					<input type='checkbox' id='reg_use_research' name='reg_use_research' value='research' />
					<label for='reg_use_research'>research</label><br>
					<input type='checkbox' id='reg_use_school' name='reg_use_school' value='school' />
					<label for='reg_use_school'>school</label><br>
					<input type='checkbox' id='reg_use_business' name='reg_use_business' value='business' />
					<label for='reg_use_business'>business</label><br>
					<input type='checkbox' id='reg_use_art' name='reg_use_art' value='art' />
					<label for='reg_use_art'>art</label><br>
					<input type='checkbox' id='reg_use_personal' name='reg_use_personal' value='personal' />
					<label for='reg_use_personal'>personal</label>
				</div></td>
			</tr>
			<tr class='reg_item'>
				<td colspan='2' class='smallnote' style='text-align:left;'>In order for this website to work properly, we have to store small files (called cookies) on your computer. Almost all websites do this, but a new EU law requires that we obtain your consent first. By registering, you agree to this.</td>
			</tr>
			<tr class='login_item'>
				<td><input type='checkbox' id='login_keep' /></td>
				<td><label for='login_keep'>Keep me logged in</label></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type='button' data-role="button" id='reset-password-button' value='Reset Password' />
					<input type='button' data-role="button" id='register-button' value='Register' />
					<input type='button' data-role="button" id='login-button' class='ui-button ui-state-focus' value='Login' />
				</td>
			</tr>
<!--
			<tr>
				<td></td>
				<td style='text-align: right;'>
					<span id="signinButton"><span
						class="g-signin"
						data-callback="loginGoogle"
						data-clientid="<?= GOOGLE_CLIENT_ID ?>"
						data-cookiepolicy="single_host_origin"
						data-requestvisibleactions=""
						data-theme="light"
						data-height="short"
						data-scope="profile">
					</span></span>
				</td>
			</tr>
-->
			<tr>
				<td colspan='2'><ol id='login_error'></ol></td>
			</tr>
		</tbody>
	</table>
	
<!--
	<div id='introLinks'>
		<a href="/privacy" target='_blank'>Privacy Policy</a> | 
		<a href="/terms" target='_blank'>Terms of Service</a>
	</div>
-->	
</div>
