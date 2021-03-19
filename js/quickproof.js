function loadProof(){

	document.body.style.cursor = 'auto';
	var ctx = canvas.getContext('2d');
	var winHeight = window.innerHeight - 268;
	var iViewX = 785 / proof.naturalWidth;
	var iViewY = winHeight / proof.naturalHeight;
	var iZoom = Math.min(iViewX,iViewY);
	var iPosX = ((785 / iZoom) / 2) - (proof.naturalWidth / 2);
	var iPosY = ((winHeight / iZoom) / 2) - (proof.naturalHeight / 2);
	var isClickOnMark = false;
	var isClickOffMark = true;
	trackTransforms(ctx);
	
	function redraw(){
		// Clear the entire canvas
		var p1 = ctx.transformedPoint(0,0);
		var p2 = ctx.transformedPoint(canvas.width,canvas.height);
		ctx.clearRect(p1.x,p1.y,p2.x-p1.x,p2.y-p1.y);

		// Redraw the proof
		if (firstView) {
			ctx.scale(iZoom,iZoom);
			ctx.translate(iPosX,iPosY);
			firstView = false;
		}
		ctx.drawImage(proof,0,0);
		
		var markLoop = 0;
		while (hlx[markLoop]) {
			ctx.fillStyle = "rgba(" + hlColor[markLoop] + ",0.5)";
			ctx.fillRect(hlx[markLoop],hly[markLoop],hlw[markLoop],hlh[markLoop]);
			markLoop++;
		}
		ctx.save();
		ctx.restore();
	} // end function redraw()
	
	redraw();
		
	var lastX=canvas.width/2, lastY=canvas.height/2; // establishes the center of the canvas.
	var dragStart,dragged;
	
	canvas.addEventListener('mousedown',function(evt){
		document.body.style.mozUserSelect = document.body.style.webkitUserSelect = document.body.style.userSelect = 'none';
		if (doMarkup) {
			var pt = ctx.transformedPoint(evt.offsetX,evt.offsetY);
			lastX = pt.x;
			lastY = pt.y;
		} else {
			lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
			lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
		}
		dragStart = ctx.transformedPoint(lastX,lastY);
		dragged = false;
	},false);
	
	canvas.addEventListener('mousemove',function(evt){
		dragged = true;
		if (dragStart){
			if (doMarkup) {
				hlx[markID] = lastX;
				hly[markID] = lastY;
				var pt = ctx.transformedPoint(evt.offsetX,evt.offsetY);
				hlw[markID] = pt.x - hlx[markID];
				hlh[markID] = pt.y - hly[markID];
			} else {
				lastX = evt.offsetX || (evt.pageX - canvas.offsetLeft);
				lastY = evt.offsetY || (evt.pageY - canvas.offsetTop);
			}
			var pt = ctx.transformedPoint(lastX,lastY);
			ctx.translate(pt.x-dragStart.x,pt.y-dragStart.y);
			redraw();
		}
		var pt = ctx.transformedPoint(evt.offsetX,evt.offsetY);
		
		// determine if the cursor is over a markup area
		if (!doMarkup) {
			var mCheck = 0;
			while(hlx[mCheck]) {
				var areaX = hlx[mCheck] + hlw[mCheck];
				var areaY = hly[mCheck] + hlh[mCheck];
				if(pt.x > hlx[mCheck] && pt.x < areaX && pt.y > hly[mCheck] && pt.y < areaY) {
					document.body.style.cursor = 'pointer';
					break;
				} else {
					document.body.style.cursor = 'auto';
				}
				mCheck++;
			} 
		}
	},false);
	
	canvas.addEventListener('mouseup',function(evt){
		dragStart = null;
		if (!dragged) {
			var pt = ctx.transformedPoint(evt.offsetX,evt.offsetY);
			var mCheck = 0;
			while(hlx[mCheck]) {
				var areaX = hlx[mCheck] + hlw[mCheck];
				var areaY = hly[mCheck] + hlh[mCheck];
				if(pt.x > hlx[mCheck] && pt.x < areaX && pt.y > hly[mCheck] && pt.y < areaY) {
					var mWinX = ((document.getElementById("cWrap").clientWidth - 902) / 2) + evt.offsetX - 20;
					var mWinY = evt.offsetY - 20;
					document.getElementById("mWindow").style.top = mWinY + "px";
					document.getElementById("mWindow").style.left = mWinX + "px";
					document.getElementById("mWindow").style.visibility = "visible";
//					document.getElementById("mWindow").style.height = "105px";
					if(hlText[mCheck]){
						document.getElementById("mTextBox").value = hlText[mCheck];
					} else {
						document.getElementById("mTextBox").value = "";
					}
					if(hlColor[mCheck]=="255,0,0"){ document.getElementById("hlRed").className = "btn btn-primary btn-xs"; }else{ document.getElementById("hlRed").className = "btn btn-default btn-xs"; }
					if(hlColor[mCheck]=="255,255,0"){ document.getElementById("hlYellow").className = "btn btn-primary btn-xs"; }else{ document.getElementById("hlYellow").className = "btn btn-default btn-xs"; }
					if(hlColor[mCheck]=="0,255,0"){ document.getElementById("hlGreen").className = "btn btn-primary btn-xs"; }else{ document.getElementById("hlGreen").className = "btn btn-default btn-xs"; }
					if(hlColor[mCheck]=="0,0,255"){ document.getElementById("hlBlue").className = "btn btn-primary btn-xs"; }else{ document.getElementById("hlBlue").className = "btn btn-default btn-xs"; }
					mCurrent = mCheck;
					isClickOnMark = true;
					isClickOffMark = false;
					break;
				} else {
					document.getElementById("mWindow").style.visibility = "hidden";
					isClickOnMark = false;
				}
//				document.getElementById("cursor").innerHTML = "document.getElementById('cWrap').style.width = " + document.getElementById("cWrap").clientWidth;
				mCheck++;
			}
			if(!isClickOnMark) {
				if(isClickOffMark) {
					zoom(evt.shiftKey ? -1 : 1 );
				} else {
					isClickOffMark=true;
				}
			}
		}
		firstView = false;
		if (doMarkup) {
			// Check highlight coordinates for proper formatting
			if(Math.sign(hlw[markID]) == -1) {
				hlx[markID] += hlw[markID];
				hlw[markID] = Math.abs(hlw[markID]);
			}
			if(Math.sign(hlh[markID]) == -1) {
				hly[markID] += hlh[markID];
				hlh[markID] = Math.abs(hlh[markID]);
			}
			document.getElementById("markup").className = "btn btn-primary";
			document.getElementById("markup").innerHTML = '<span class="glyphicon glyphicon-pencil"></span>';
			document.getElementById("hlYellow").className = "btn btn-default btn-xs";
			var hlPrev = hlColor[markID];
			markID++;
			hlColor[markID] = hlPrev;
		}
		
		doMarkup = false;
		document.body.style.cursor = 'auto';
	},false);
			
	zoomReset.addEventListener('mouseup',function(evt){
		dragStart = null;
		firstView = true;
		zoom(evt.shiftKey ? -1 : 1 );
		firstView = false;
		document.body.style.cursor = 'auto';
	},false);
	
	btnRemove.addEventListener('mouseup',function(){
		if(document.getElementById("btnRemove").value == "no") {
			return;
		} else {
			var xmlhttp = new XMLHttpRequest();
			var postData = "p=" + projectID + "&w=" + adName + "&r=" + revNum + "&qp=" + currentImg + "&m=" + mCurrent;
			xmlhttp.open("POST", "ajax/delmark.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", postData.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
				if(xmlhttp.readyState == 4 && xmlhttp == 500) {
					window.alert(xmlhttp.responseText);
				}
				noteStack();
			}
    		xmlhttp.send(postData);
			hlx.splice(mCurrent,1);
			hly.splice(mCurrent,1);
			hlw.splice(mCurrent,1);
			hlh.splice(mCurrent,1);
			hlText.splice(mCurrent,1);
			hlColor.splice(mCurrent,1);
			markID--;
			document.getElementById("mWindow").style.visibility = "hidden";
			redraw();
		}
	},false);
	
	btnZoomIn.addEventListener('mouseup',function(evt){
		dragStart = null;
		zoom(1);
		document.body.style.cursor = 'auto';
	},false);
	
	btnZoomOut.addEventListener('mouseup',function(evt){
		dragStart = null;
		zoom(-1);
		document.body.style.cursor = 'auto';
	},false);
	
/*	btnHiLite.addEventListener('mouseup',function(evt){
		if(hilite){
			document.getElementById("btnHiLite").className = "btn btn-primary";
			document.getElementById("btnHiLite").innerHTML = '<span class="glyphicon glyphicon-eye-close">';
			hilite = false;
		} else {
			document.getElementById("btnHiLite").className = "btn btn-success";
			document.getElementById("btnHiLite").innerHTML = '<span class="glyphicon glyphicon-eye-open">';
			hilite = true;
		}
		zoom(0);
	},false);*/
	
	hlRed.addEventListener('mouseup',function(evt){
		if(document.getElementById("hlRed").value == "no"){
			return;
		} else {
			document.getElementById("hlRed").className = "btn btn-primary btn-xs";
			document.getElementById("hlYellow").className = "btn btn-default btn-xs";
			document.getElementById("hlGreen").className = "btn btn-default btn-xs";
			document.getElementById("hlBlue").className = "btn btn-default btn-xs";
			hlColor[mCurrent] = "255,0,0";
			zoom(0);
		}
	},false);
	
	hlGreen.addEventListener('mouseup',function(evt){
		if(document.getElementById("hlGreen").value =="no"){
			return;
		} else {
			document.getElementById("hlRed").className = "btn btn-default btn-xs";
			document.getElementById("hlYellow").className = "btn btn-default btn-xs";
			document.getElementById("hlGreen").className = "btn btn-primary btn-xs";
			document.getElementById("hlBlue").className = "btn btn-default btn-xs";
			hlColor[mCurrent] = "0,255,0";
			zoom(0);
		}
	},false);
	
	hlBlue.addEventListener('mouseup',function(evt){
		if(document.getElementById("hlBlue").value == "no"){
			return;
		} else {
			document.getElementById("hlRed").className = "btn btn-default btn-xs";
			document.getElementById("hlYellow").className = "btn btn-default btn-xs";
			document.getElementById("hlGreen").className = "btn btn-default btn-xs";
			document.getElementById("hlBlue").className = "btn btn-primary btn-xs";
			hlColor[mCurrent] = "0,0,255";
			zoom(0);
		}
	},false);
	
	hlYellow.addEventListener('mouseup',function(evt){
		if(document.getElementById("hlYellow").value == "no"){
			return;
		} else {
			document.getElementById("hlRed").className = "btn btn-default btn-xs";
			document.getElementById("hlYellow").className = "btn btn-primary btn-xs";
			document.getElementById("hlGreen").className = "btn btn-default btn-xs";
			document.getElementById("hlBlue").className = "btn btn-default btn-xs";
			hlColor[mCurrent] = "255,255,0";
			zoom(0);
		}
	},false);
	
	var scaleFactor = 1.2;
	
	var zoom = function(clicks){
		var pt = ctx.transformedPoint(lastX,lastY);
		ctx.translate(pt.x,pt.y);
		var factor = Math.pow(scaleFactor,clicks);
		if (firstView) {
			ctx.setTransform(1, 0, 0, 1, 0, 0);
			ctx.scale(iZoom,iZoom);
			ctx.translate(iPosX,iPosY);
			firstView = false;
		} else {
			ctx.scale(factor,factor);
			ctx.translate(-pt.x,-pt.y);
		}	
			
		redraw();
	}

	var handleScroll = function(evt){
		var delta = evt.wheelDelta ? evt.wheelDelta/40 : evt.detail ? -evt.detail : 0;
		if (delta) zoom(delta);
		return evt.preventDefault() && false;
	}
	
	canvas.addEventListener('DOMMouseScroll',handleScroll,false);
	canvas.addEventListener('mousewheel',handleScroll,false);
	
}																					// end window.onload

function markProof(){		
	if (doMarkup) {
		document.body.style.cursor = 'auto';
		document.getElementById("markup").className = "btn btn-primary";
		document.getElementById("markup").innerHTML = '<span class="glyphicon glyphicon-pencil">';
		doMarkup = false;
	} else {
		document.body.style.cursor = 'crosshair';
		document.getElementById("markup").className = "btn btn-warning";
		document.getElementById("markup").innerHTML = '<span class="glyphicon glyphicon-remove-circle">';
		doMarkup = true;
		var zoom = function(clicks){
			var pt = ctx.transformedPoint(lastX,lastY);
			ctx.translate(pt.x,pt.y);
			var factor = Math.pow(scaleFactor,clicks);
			redraw();
		}
	}
}

function savemarks(project,wk,pn,an,rev,page,u){
	hlText[mCurrent] = document.getElementById("mTextBox").value;
	var xmlhttp = new XMLHttpRequest();
	var URIText = encodeURIComponent(hlText[mCurrent]);
	var encText = URIText.replace(/'/g, "%5C%27");
	var postData = "p=" + project + "&w=" + wk + "&pn=" + pn + "&an=" + an + "&r=" + rev + "&qp=" + page + "&m=" + mCurrent + "&hlx=" + hlx[mCurrent] + "&hly=" + hly[mCurrent] + "&hlw=" + hlw[mCurrent] + "&hlh=" + hlh[mCurrent] + "&text=" + encText + "&hl=" + hlColor[mCurrent] + "&u=" + u;
	xmlhttp.open("POST", "ajax/savemark.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", postData.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlhttp.readyState == 4 && xmlhttp == 500) {
			window.alert(xmlhttp.responseText);
		}
		noteStack();
    }
    xmlhttp.send(postData);
	document.getElementById("mWindow").style.visibility = "hidden";
//	redraw();
}
																			// end savemarks
	
// Adds ctx.getTransform() - returns an SVGMatrix
// Adds ctx.transformedPoint(x,y) - returns an SVGPoint
function trackTransforms(ctx){
	var svg = document.createElementNS("http://www.w3.org/2000/svg",'svg');
	var xform = svg.createSVGMatrix();
	ctx.getTransform = function(){ return xform; };
		
	var savedTransforms = [];
	var save = ctx.save;
	ctx.save = function(){
		savedTransforms.push(xform.translate(0,0));
		return save.call(ctx);
	};
	var restore = ctx.restore;
	ctx.restore = function(){
		xform = savedTransforms.pop();
		return restore.call(ctx);
	};

	var scale = ctx.scale;
	ctx.scale = function(sx,sy){
		xform = xform.scaleNonUniform(sx,sy);
		return scale.call(ctx,sx,sy);
	};
	var rotate = ctx.rotate;
	ctx.rotate = function(radians){
		xform = xform.rotate(radians*180/Math.PI);
		return rotate.call(ctx,radians);
	};
	var translate = ctx.translate;
	ctx.translate = function(dx,dy){
		xform = xform.translate(dx,dy);
		return translate.call(ctx,dx,dy);
	};
	var transform = ctx.transform;
	ctx.transform = function(a,b,c,d,e,f){
		var m2 = svg.createSVGMatrix();
		m2.a=a; m2.b=b; m2.c=c; m2.d=d; m2.e=e; m2.f=f;
		xform = xform.multiply(m2);
		return transform.call(ctx,a,b,c,d,e,f);
	};
	var setTransform = ctx.setTransform;
	ctx.setTransform = function(a,b,c,d,e,f){
		xform.a = a;
		xform.b = b;
		xform.c = c;
		xform.d = d;
		xform.e = e;
		xform.f = f;
		return setTransform.call(ctx,a,b,c,d,e,f);
	};
	var pt  = svg.createSVGPoint();
	ctx.transformedPoint = function(x,y){
		pt.x=x; pt.y=y;
		return pt.matrixTransform(xform.inverse());
	}
}