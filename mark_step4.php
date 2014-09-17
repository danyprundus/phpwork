<?php
include 'inc/bootstrap.php';

?>
<!DOCTYPE HTML>
<html>
<head>
	<?php include 'inc/head.php' ?>
	<title>Iptica</title>
	<meta name="description" lang="en-us" content="Iptica">
	<meta name="keywords" lang="en-us" content="Iptica">
</head>
<body>		
	<?php include 'inc/header.php' ?>
	
	<section class="main-content process">
		<div class="wrapper">
			<?php include 'inc/checkout_mark_steps.php' ?>
			
			<h1>Register Account</h1>			
			
			<form action="mark_step5.php" method="post" class="register" onsubmit="return validateForm()">
				<table>				
					<thead>
						<tr>
							<th>
								<input type="radio" name="account" value="new" onchange="formToggle()" checked required>
								<label>New Account</label>
							</th>
							<th colspan="3">
								<input type="radio" name="account" value="existing" onchange="formToggle()" required>
								<label>Existing Account</label>
							</th>
						</tr>
					</thead>
					<tbody>					
						<tr id="email">
							<td>
								<label>Email Address</label>
							</td>
							<td colspan="3">
								<input type="email" name="email" required>
							</td>
						</tr>
						<tr id="passwords">
							<td>
								<label>Password</label>
							</td>
							<td>
								<input type="password" name="password" required>
							</td>
							<td>
								<label>Confirm Password</label>
							</td>
							<td>
								<input type="password" name="password-conf" required>
							</td>
						</tr>
						<tr>
							<td>
								<input type="radio" name="account-type" value="applicant" checked required>
								<label>I am the applicant</label>
							</td>
							<td colspan="3">
								<input type="radio" name="account-type" value="agent" required>
								<label>I am an agent</label>
							</td>
						</tr>
						<tr>
							<td><label>First Name</label></td>
							<td><input type="text" name="first_name" required></td>
							<td><label>Surname</label></td>
							<td><input type="text" name="surname" required></td>
						</tr>
						<tr>
							<td><label>Telephone #</label></td>
							<td><input type="text" name="telephone" required></td>
							<td><label>Mobile #</label></td>
							<td><input type="text" name="mobile" required></td>
						</tr>
						<tr>
							<td><label>Building Number</label></td>
							<td><input type="text" name="building" required></td>
							<td><label>Street Name</label></td>
							<td><input type="text" name="street" required></td>
						</tr>
						<tr>
							<td><label>City</label></td>
							<td><input type="text" name="city" required></td>
							<td><label>State</label></td>
							<td><input type="text" name="state" required></td>
						</tr>
						<tr>
							<td><label>Country</label></td>
							<td>
								<select name="country" style="max-width: 300px" required>
									<option value="">- Select -</option>
									<?php include 'inc/world_countries.php' ?>
									<?php foreach($world_countries as $country): ?>
										<option value="<?php echo $country ?>"><?php echo $country ?></option>
									<?php endforeach ?>
								</select>
							</td>
							<td><label>Zip/Post Code</label></td>
							<td><input type="text" name="zip" size="8" required></td>
						</tr>
						<?php if($_SESSION['order']['vat']): ?>
							<tr id="optional">
								<td><label>VAT number</label></td>
								<td><input type="text" name="vat_id"></td>
								<td><label>Company name</label></td>
								<td><input type="text" name="company"></td>
							</tr>
						<?php endif ?>
						<tr>
							<td><label>Other Instructions</label></td>
							<td colspan="3"><textarea cols="40" rows="5" name="comments"></textarea></td>
						</tr>
					</tbody>
				</table>
				
				<input type="submit" class="btn right" value="Proceed to Checkout ›">
				
			</form>	
			
		</div><!-- end wrapper -->				
	</section><!-- end main-content -->	
	
	<?php include 'inc/footer.php' ?>
<script>
function formToggle(){
	$('tbody tr').hide();
	$('tbody input, tbody select').removeAttr('required');
	
	if($('input[name=account]:checked').val() == 'new'){
		$('tbody tr, tbody td').show();
		$('tbody input, tbody select').attr('required', 'required');
		$('#passwords td:eq(0) label').html('Choose Password');
		}
	else{
		$('#email, #passwords').show();
		$('#passwords td:eq(2), #passwords td:eq(3)').hide();
		$('input[name=email], input[name=password]').attr('required', 'required');
		$('#passwords td:eq(0) label').html('Password');
		}
	$('#optional input').removeAttr('required');	
	//$('tbody input, tbody select').removeAttr('required');
	}
function validateForm(){
	if($('input[name=account]:checked').val() == 'new'){
		if($('input[name=password]').val().length < 6){
			vex.dialog.alert('Password must be at least 6 characters long');
			return false;
			}
		if($('input[name=password]').val() != $('input[name=password-conf]').val()){
			vex.dialog.alert('Passwords don\'t match');
			return false;
			}
		var accountExists = false;
		$.ajax({
			url:'step3_ajax_check_email.php?email=' + encodeURI($('input[name=email]').val()),
			success: function(data){
				accountExists = (data != 'ok');
				},
			async: false
			});
		if(accountExists){
			vex.dialog.alert('Email account already exists');
			return false;
			}
		}
	else{
		var login = false;
		$.ajax({
			url:'step3_ajax_login.php?email=' + encodeURI($('input[name=email]').val()) + '&password=' + encodeURI($('input[name=password]').val()),
			success: function(data){
				login = (data == 'ok');
				},
			async: false
			});
		if(!login){
			vex.dialog.alert('Login failed');
			return false;
			}
		}
	var valid = true;
	$('input:required').each(function(){
		if(!$(this).val()){
			valid = false;
			}
		});
	if(!valid){
		vex.dialog.alert('All fields are required');
		return false;
		}
	return true;
	}
$(function(){
	$('input[name=account]:checked').focus();
	formToggle();
	});
</script>
</body>
</html>
