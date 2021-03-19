<?php
	include('../includes/inc.php');
	echo '
		<table class="table table-striped">
			<thead style="display:block;">
				<tr style="display:table;width:100%;">
					<th style="width:20%;text-align:left;">User</th>
					<th style="width:30%;text-align:center;">Role</th>
					<th style="width:30%;text-align:center;">Status</th>
					<th style="width:20%;text-align:center;">
						<input id="userSearch" type="text" class="form-control" placeholder="Quick search" onkeyup="uSearch()">
					</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="scrolltable" style="overflow-y:scroll;display:block;height:250px;">';
	$query=mysqli_query($con,"SELECT * FROM users ORDER BY fullname");
	while($row = mysqli_fetch_assoc($query)){
		echo '	<tr id="user'.$row["id"].'" style="display:table;width:100%;">
					<td style="width:20%;text-align:left;"><a onmouseover="document.body.style.cursor = \'pointer\';" onmouseout="document.body.style.cursor = \'auto\';" onclick="showUsers('.$row["id"].')">'.$row["fullname"].'</a></td>
					<td style="width:30%;text-align:center;">'.$row["role"].'</td>
					<td style="width:30%;text-align:center;">'.$row["status"].'</td>
					<td style="width:20%;text-align:center;"><span class="glyphicons glyphicons-delete" style="color:red;" onclick="deleteUser('.$row["id"].',\''.$row["fullname"].'\')" onMouseOver="this.style.cursor=\'pointer\'"></span></td>
				</tr>
		';
	}
	echo '	</tbody>
		</table>';
?>


								