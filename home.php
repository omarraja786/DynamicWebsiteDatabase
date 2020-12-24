<?php
session_start();
include_once('_class/database.class.php');
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<!-- Datatable CSS -->
		<link href='DataTables/datatables.min.css' rel='stylesheet' type='text/css'>

		<!-- jQuery Library -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

		<!-- Datatable JS -->
		<script src="DataTables/datatables.min.js"></script>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" >

		<!-- Bootstrap CSS -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>
	</head>

	<body class="loggedin">
		<nav class="navtop">
			<div>
			<h1>Mohammed Omar Raja</h1>
			<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>

		<div class="content">
			<h2>Database</h2>

		

			

			<div class='container'>

				<button type="button" name="add" id="add" class="btn btn-success ">Add Staff</button>
				<br>
				<br>


				<!-- Modal -->
				<div id="updateModal" class="modal fade" role="dialog">
				<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">

				<div class="modal-header">
					<h4 class="modal-title">Update</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button> 
				</div>

				<div class="modal-body">
					<div class="form-group">
						<label for="name" >First Name</label>
						<input type="text" class="form-control" id="firstname" placeholder="Enter first name" required> 
					</div>

					<div class="form-group">
						<label for="name" >Last Name</label>
						<input type="text" class="form-control" id="lastname" placeholder="Enter last name" required> 
					</div>

					<div class="form-group">
						<label for="dob" class="form-control">Date of Birth</label>
						<input class="form-control" type="date" id="dob" placeholder="Select Date of Birth">
             		</div> 

					<div class="form-group">
						<label for="is_user" >Is User</label>
						<select id='is_user' class="form-control">
						<option value='1'>Yes</option>
						<option value='0'>No</option>
						</select> 
					</div>

					<div class="form-group" id="otherFieldDivUN">
						<label for="username" >Username</label>
						<input type="text" class="form-control" id="username" placeholder="Enter username" required> 
					</div>

					<div class="form-group" id="otherFieldDivPw">
						<label for="password" >Password</label>
						<input type="password" class="form-control" id="password" placeholder="Enter Password" required> 
					</div>

				</div>

				<div class="modal-footer">
					<input type="hidden" id="txt_staffid" value="0">
					<button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button>
					<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
				</div>
				</div>
				</div>
				</div>

				<!-- Table -->
				<table id='staffTable' class='display dataTable' width='100%'>
					<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>Date of Birth</th>
						<th>Created</th>
						<th>Last Updated</th>
						<th>Is User</th>
						<th>Action</th>
					</tr>
					</thead>
				</table>

			</div>
		</div>



		<script> 
			$(document).ready(function(){
				

			// DataTable
			var staffDataTable = $('#staffTable').DataTable({
				'processing': true,
				'serverSide': true,
				'serverMethod': 'post',
				'ajax': {
				'url':'ajaxfile.php'
			},
			'columns': [
				//{ data: 'sid' },
				{ data: 'firstname' },
				{ data: 'lastname' },
				{ data: 'dob' },
				{ data: 'created' },
				{ data: 'last_updated' },
				{ data: 'is_user' },
				{ data: 'action' },
			]
			});


			 $('#add').click(function(){
	
			   var html = '<tr>';
			   html += '<td contenteditable id="data1"></td>';
			   html += '<td contenteditable id="data2"></td>';
			   html += '<td contenteditable id="data3"></td>';
			   html += '<td contenteditable="false" id="data4"></td>';
			   html += '<td contenteditable="false" id="data5"></td>';
			   html += '<td contenteditable="true" id="data6"> <select id="userSelection"><option value="1">1</option><option value="0">0</option></select> </td>';
			   html += '<td><button type="button" name="insert" id="insert" class="btn btn-success btn-xs">Insert</button></td>';
			   html += '</tr>';
			   $('#staffTable tbody').prepend(html);
			  });
			  
			  $(document).on('click', '#insert', function(){
			   var firstname = $('#data1').text();
			   var lastname = $('#data2').text();
			   var dob = $('#data3').text();
			   var is_user = $("#userSelection").val();
			   location.reload();
			   if(firstname != '' && lastname != '' && dob != "")
			   {
			    $.ajax({
			     url:"insert.php",
			     method:"POST",
			     data:{firstname:firstname, lastname:lastname, dob:dob, is_user:is_user},
			     success:function(data)
			     {
			      $('#alert_message').html('<div class="alert alert-success">'+data+'</div>');
			      $('#staffTable').DataTable().destroy();
			      $('#staffTable').DataTable().reload();

			     }
			    });
			    setInterval(function(){
			     $('#alert_message').html('');
			    }, 5000);
			   }
			   else
			   {
			    alert("Both Fields is required");
			   }
			  });


			// Update record
			$('#staffTable').on('click','.updateUser',function(){
				var id = $(this).data('id');

				$('#txt_staffid').val(id);

				// AJAX request
				$.ajax({
				url: 'ajaxfile.php',
				type: 'post',
				data: {request: 2, id: id},
				dataType: 'json',
				success: function(response){
				if(response.status == 1){
					$('#id').val(response.data.id);
					$('#firstname').val(response.data.firstname);
					$('#lastname').val(response.data.lastname);
					$('#dob').val(response.data.dob);
					$('#is_user').val(response.data.is_user);
					$('#username').val(response.data.username);
					$('#password').val(response.data.password);

				}
				else{
					alert("Invalid ID.");
					}
				}
				});



				$("#is_user").change(function() {
				if ($(this).val() == '1') {
					$('#otherFieldDivUN').show();
					$('#username').attr('required', '');
					$('#username').attr('data-error', 'This field is required.');
					$('#otherFieldDivPw').show();
					$('#password').attr('required', '');
					$('#password').attr('data-error', 'This field is required.');



				} else {
					$('#otherFieldDivUN').hide();
					$('#username').removeAttr('required');
					$('#username').removeAttr('data-error');
					$('#otherFieldDivPw').hide();
					$('#password').removeAttr('required');
					$('#password').removeAttr('data-error');
				}
			});
				$("#is_user").trigger("change");
			});


			// Save user 
			$('#btn_save').click(function(){
				var id = $('#txt_staffid').val();

				var firstname = $('#firstname').val().trim();
				var lastname = $('#lastname').val().trim();
				var dob = $('#dob').val().trim();
				var is_user = $('#is_user').val().trim();
				var username = $('#username').val().trim();
				var password = $('#password').val().trim();

				if(firstname !='' && lastname != '' && dob != ''){

					// AJAX request
					$.ajax({
					url: 'ajaxfile.php',
					type: 'post',
					data: {request: 3, id: id,firstname: firstname, lastname: lastname, dob: dob, is_user: is_user, username:username, password:password},
					dataType: 'json',
					success: function(response){
					if(response.status == 1){
						alert(response.message);

						// Empty the fields
						$('#firstname','#lastname','#dob', '#username', '#password').val('');
						$('#is_user').val(0);
						$('#txt_staffid').val(0);

						// Reload DataTable
						staffDataTable.ajax.reload();

						// Close modal
						$('#updateModal').modal('toggle');
					}
					else{
						alert(response.message);
						}
					}
					});

				}
				else{
					alert('Please fill all fields.');
					}
				});
			});


		</script>



	</body>
</html>