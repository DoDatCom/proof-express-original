<?php
	include('../includes/inc.php');
	$project=$_POST["p"];
	echo '
		<table class="table table-striped">
			<thead>
				<tr id="tabletop">
					<th style="text-align:left;">User</th>
					<th style="text-align:center;">Role</th>
					<th style="text-align:center;">Remove User</th>
				</tr>
			</thead>
			<tbody>';
	$userList=mysqli_query($con,"SELECT * FROM users WHERE ring LIKE '%p".$project.":%' OR role = 'Administrator' ORDER BY fullname") or die(mysql_errno($userList));
	while($user=mysqli_fetch_assoc($userList)) {
		echo '	<tr>
					<td align="left">'.$user["fullname"].'</td>
					<td>'.$user["role"].'</td>';
		if($user["role"]=="Administrator"){
			echo '	<td></td>';
		} else {
			echo '	<td><span class="glyphicons glyphicons-delete" style="color:red;" onclick="removeUser('.$user["id"].',\''.$project.'\',\''.$user["fullname"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>';
		}
		echo '	</tr>';
	}
	echo '	</tbody>
		</table>';
?>