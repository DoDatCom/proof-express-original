<?php
//	Process login submission
	include('../includes/inc.php');
	$query="SELECT * FROM groups WHERE id = '".$_POST['g']."'";
	$result=mysqli_query($con,$query);
	if(mysqli_num_rows($result)>0) {
		while($row = mysqli_fetch_assoc($result)){
  			echo '<div class="well">
  				<div class="row">
					<div class="col-sm-11">
						<form class="form-horizontal">
							<div class="row">
								<div class="col-sm-6" style="text-align:left;">
									<div class="form-group">
										<label class="control-label col-sm-4" for="group">Group Name:</label>
										<div class="col-sm-8">
											<input type="text" class="form-control" id="group" value="'.$row["name"].'">
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6">
											<div class="checkbox">
												<label><input type="checkbox" id="admua" value="">Administer Accounts</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" id="view" value="">View</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" id="comment" value="">Comment</label>
											</div>
										</div>
										<div class="col-sm-6">
											<div class="checkbox">
												<label><input type="checkbox" id="marketing" value="">Marketing Only</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" id="download" value="">Download</label>
											</div>
											<div class="checkbox">
												<label><input type="checkbox" id="approve" value="">Approve</label>
											</div>
										</div>
									</div>
								</div>
								<div class="col-sm-6" style="text-align:left;">
									<label class="control-label col-sm-3" for="status">Members:</label>
									<div class="col-sm-9">
										<div class="well well-sm" style="max-height:150px;min-height:34px;overflow-y:auto;background-color:white;">
											<table id="group" style="width:100%;">
												<tbody>';
			$memQuery=mysqli_query($con,"SELECT * FROM users WHERE token LIKE '%g".$_POST['g'].":%'");
			while($mem = mysqli_fetch_assoc($memQuery)){
				echo '								<tr>
														<td style="text-align:left;">'.$mem["fullname"].'</td>
														<td style="float:right;"><span class="glyphicon glyphicon-remove" style="color:red;" onclick="removeUserFromGroup('.$mem["id"].','.$_POST["g"].')"></span></td>
													<tr>';
			}
			echo '								</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-10">
										<button type="submit" class="btn btn-default">Submit</button>
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="col-sm-1">
						<button class="close" data-toggle="tooltip" title="Close detail window" onclick="closeGroupDetail()">&times;</button>
					</div>
				</div>
			</div>';
  		}
  	}
?>