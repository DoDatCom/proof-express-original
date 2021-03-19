<?php
	include('../includes/inc.php');
	$result=mysqli_query($con,"SELECT * FROM projects WHERE name LIKE '%".$_POST['qs']."%'");
	if(mysqli_num_rows($result)>0) {
		echo '<tbody>';
		while($row = mysqli_fetch_assoc($result)){
			echo '<tr>
					<td style="text-align:left;">
						<div class="checkbox"><label><input type="checkbox" name="addProject" value="p'.$row["id"].':">'.$row["name"].'</label>
					</td>
				<tr>';
		}
		echo '</tbody>';
	}
?>