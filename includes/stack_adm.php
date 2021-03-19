<script>
//	function stackProjects(p,w){
//		var xmlhttp;
//		var params = 'p=' + p + '&w=' + w;
//		xmlhttp=new XMLHttpRequest();
//		xmlhttp.onreadystatechange = function() {
//			if (xmlhttp.readyState == 4) {
//				document.getElementById("stack").innerHTML = xmlhttp.responseText;
//			}
//		};
//		xmlhttp.open("POST", "ajax/stack_adm_projects.php", true);
//		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//		xmlhttp.send(params);
//	}
	function stackHome(){
		var xmlhttp;
		var params = '';
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				document.getElementById("stack").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("POST", "ajax/stack_adm_home.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(params);
	}
</script>