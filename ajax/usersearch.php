<?php
	include('../includes/inc.php');
	$result=mysqli_query($con,"SELECT * FROM users WHERE fullname LIKE '%".$_POST['us']."%' ORDER BY fullname");
	if($result) {
		while($row = mysqli_fetch_assoc($result)){
			echo '	
				<tr id="user'.$row["id"].'" style="display:table;width:100%;">
					<td style="width:20%;text-align:left;"><a onclick="showUsers('.$row["id"].')">'.$row["fullname"].'</a></td>
					<td style="width:30%;text-align:center;">'.$row["role"].'</td>
					<td style="width:30%;text-align:center;">'.$row["status"].'</td>
					<td style="width:20%;text-align:center;"><span class="glyphicons glyphicons-delete" style="color:red;" onclick="deleteUser('.$row["id"].',\''.$row["fullname"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
				</tr>
			';
		}
	}
?>