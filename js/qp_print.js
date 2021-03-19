function loadProof(){

	document.body.style.cursor = 'auto';
	var ctx = canvas.getContext('2d');
	var iViewX = 600 / proof.naturalWidth;
	var iViewY = 600 / proof.naturalHeight;
	var iZoom = Math.min(iViewX,iViewY);
	var iPosX = ((600 / iZoom) / 2) - (proof.naturalWidth / 2);
	var iPosY = ((600 / iZoom) / 2) - (proof.naturalHeight / 2);
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
			ctx.fillStyle = hlColor[markLoop];
			ctx.fillRect(hlx[markLoop],hly[markLoop],hlw[markLoop],hlh[markLoop]);
			ctx.font = "150px Arial";
			ctx.fillText(markLoop + 1,hlx[markLoop]+50,hly[markLoop]+150);
			markLoop++;
		}
		ctx.save();
		ctx.restore();
	} // end function redraw()
	
	redraw();
		
	var lastX=canvas.width/2, lastY=canvas.height/2; // establishes the center of the canvas.
	var dragStart,dragged;
	
/*	btnApply.addEventListener('mouseup',function(){
		hlText[mCurrent] = document.getElementById("mTextBox").value;
		document.getElementById("mWindow").style.visibility = "hidden";
		redraw();
	},false);*/
	
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

function savemarks(proof,page){
	hlText[mCurrent] = document.getElementById("mTextBox").value;
	var xmlhttp = new XMLHttpRequest();
	var URIText = encodeURIComponent(hlText[mCurrent]);
	var encText = URIText.replace(/'/g, "%5C%27");
	var postData = "act=savemark&proof=" + proof + "&page=" + page + "&m=" + mCurrent + "&hlx=" + hlx[mCurrent] + "&hly=" + hly[mCurrent] + "&hlw=" + hlw[mCurrent] + "&hlh=" + hlh[mCurrent] + "&text=" + encText;
	xmlhttp.open("POST", "ajax.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", postData.length);
	xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlhttp.readyState == 4 && xmlhttp == 500) {
			window.alert(xmlhttp.responseText);
		}
    }
    xmlhttp.send(postData);
	document.getElementById("mWindow").style.visibility = "hidden";
	redraw();
}
	
function navThumbs(proof,page){
	document.body.style.cursor = 'auto';
	var s = document.getElementById('tnScroll').scrollTop;
	window.location="proof.php?proof=" + proof + "&p=" + page + "&s=" + s;
}																				// end savemarks
	
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