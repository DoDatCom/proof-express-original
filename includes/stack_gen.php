<script>
	function stackProjects(p){
		var xmlhttp;
		var params = 'p=' + p;
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				document.getElementById("stack").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("POST", "ajax/stack_gen_projects.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(params);
	}
	function stackHome(){
		var xmlhttp;
		var params = '';
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4) {
				document.getElementById("stack").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("POST", "ajax/stack_gen_home.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.send(params);
	}
</script>