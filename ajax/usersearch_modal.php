<?php
	include('../includes/inc.php');
	$result=mysqli_query($con,"SELECT * FROM users WHERE fullname LIKE '%".$_POST['us']."%' ORDER BY fullname");
	if($result) {
		echo '
			<tbody>
		';
		while($row = mysqli_fetch_assoc($result)){
			echo '	
				<tr>
					<td style="text-align:left;">
						<div class="checkbox"><label><input type="checkbox" name="addUser" value="'.$row["id"].':">'.$row["fullname"].'</label>
					</td>
				<tr>';
		}
	}
	echo '	</tbody>';
?>