function btnSaveEn(u) {
	document.getElementById("btnSave").className = "btn btn-info";
}

function qSearch() {
	var xmlhttp;
	var qs = document.getElementById("search").value;
	var params = 'qs=' + qs;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if(xmlhttp.responseText !== "") {
				document.getElementById("group").innerHTML = xmlhttp.responseText;
				document.getElementById("search").style.backgroundColor = "white";
			} else {
				document.getElementById("search").style.backgroundColor = "red";
			}
		}
	};
	xmlhttp.open("POST", "ajax/quicksearch.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}

function uSearch() {
	var xmlhttp;
	var us = document.getElementById("userSearch").value;
	var params = 'us=' + us;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == 4) {
			if(xmlhttp.responseText !== "") {
				document.getElementById("scrolltable").innerHTML = xmlhttp.responseText;
				document.getElementById("userSearch").style.backgroundColor = "white";
			} else {
				document.getElementById("userSearch").style.backgroundColor = "red";
			}
		}
	};
	xmlhttp.open("POST", "ajax/usersearch.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
}