/*jshint esversion: 6 */
/*jshint browser: true */

function iwm_redrawcrop() {

	var marginhorizontal = document.getElementsByName('iwm_left')[0].value;
	var marginvertical = document.getElementsByName('iwm_top')[0].value;
	var percentagesize = document.getElementsByName('iwm_size')[0].value;
	var hsize = document.getElementsByName('iwm_hsize')[0].value;
	var hovercolor = document.getElementsByName('hovercolor')[0].value.toLowerCase();
	var inactivecolor = document.getElementsByName('ina_color')[0].value.toLowerCase();

	var showcursor = '';
	if (document.getElementsByName('showcursor')[0].checked) {
		showcursor = 1;
	}

	var bordercolor = document.getElementsByName('bcolor')[0].value.toLowerCase();
	var borderwidth = document.getElementsByName('bwidth')[0].value;
	var borderinawidth = document.getElementsByName('biwidth')[0].value;
	var bgimage = document.getElementsByName('bgimage')[0].value;

	var bgrepeat = '';
	if (document.getElementsByName('bgrepeat')[0].checked) {
		bgrepeat = 1;
	}

	var tooltipfontfamily = document.getElementsByName('tooltipfontfamily')[0].value;
	var tooltipfontsize = document.getElementsByName('tooltipfontsize')[0].value;
	var tooltipbg = document.getElementsByName('tooltipbg')[0].value;
	var tooltipminwidth = document.getElementsByName('tooltipminwidth')[0].value;

	var tooltipbordercolor = document.getElementsByName('tooltipbordercolor')[0].value;
	var tooltipborderwidth = document.getElementsByName('tooltipborderwidth')[0].value;

	var tooltiphidetitle = '';
	if (document.getElementsByName('tooltiphidetitle')[0].checked) {
		tooltiphidetitle = 1;
	}

	var tooltipnowrap = '';
	if (document.getElementsByName('tooltipnowrap')[0].checked) {
		tooltipnowrap = 1;
	}

	var mobilemarker = document.getElementsByName('mobilemarker')[0].value;

	var fontawesomeapply = '';
	if (document.getElementsByName('fontawesomeapply')[0].checked) {
		fontawesomeapply = 1;
	}
	var fontawesomeinclude = '';
	if (document.getElementsByName('fontawesomeinclude')[0].checked) {
		fontawesomeinclude = 1;
	}

	// to create styles for preview
	var mapstyle = document.getElementById("visualization").style;

	// zoom effect
	if (marginhorizontal !== '') {
		mapstyle.marginLeft = marginhorizontal + "%";
	} else {
		mapstyle.marginLeft = '0%';
	}
	if (marginvertical !== '') {
		mapstyle.marginTop = marginvertical + "%";
	} else {
		mapstyle.marginTop = '0%';
	}
	if (percentagesize !== '') {
		mapstyle.width = percentagesize + "%";
		mapstyle.height = percentagesize + "%";
	} else {
		mapstyle.width = '100%';
		mapstyle.height = '100%';
	}

	//we remove the temp style
	var tempcss = document.getElementById("tempcss");
	if (tempcss) {
		tempcss.parentNode.removeChild(tempcss);
	}

	// styles with advanced selectors, we need to add them to the css file
	tempcss = (function () {
		// Create the <style> tag
		var style = document.createElement("style");
		style.setAttribute("id", "tempcss");

		// WebKit hack :(
		style.appendChild(document.createTextNode(""));

		// Add the <style> element to the page
		document.head.appendChild(style);

		return style.sheet;

	})();

	// height 1 (1)
	if (hsize !== '' && hsize !== '61,7') {
		tempcss.insertRule('#visualization-wrap-responsive:after { padding-top: ' + hsize + '% !important}', 0);
	} else {
		tempcss.insertRule('#visualization-wrap-responsive:after {}', 0);
	}

	// hover effect 6 (7)
	if (hovercolor !== '') {

		tempcss.insertRule('#visualization circle:hover { fill:' + hovercolor + '; }', 0);
		tempcss.insertRule('#visualization text:hover { fill:' + hovercolor + '; }', 0);

		tempcss.insertRule('#visualization path[stroke-width^="3"] + path { display:none; }', 0);
		tempcss.insertRule('#visualization path[stroke-width^="3"] + path + path:not([fill^="' + inactivecolor + '"]) { display:none; }', 0);
		tempcss.insertRule('#visualization path[stroke-width^="3"] { fill:' + hovercolor + ';  }', 0);

		tempcss.insertRule('#visualization path[fill^="' + inactivecolor + '"] { pointer-events: none; }', 0);
		tempcss.insertRule('#visualization path[fill^="none"] { pointer-events: none; }', 0);

		var bw = 1;
		if (borderwidth !== '') {
			bw = borderwidth;
		}

		tempcss.insertRule('#visualization path:not([fill^="' + inactivecolor + '"]) + path[stroke-width^="3"] { stroke-width:' + bw + '; stroke-opacity:0; stroke:' + hovercolor + '  }', 0);
	}

	//hand cursor 3 (10)
	if (showcursor === 1) {
		tempcss.insertRule('#visualization path:not([fill^="' + inactivecolor + '"]):hover { cursor:pointer; }', 0);
		tempcss.insertRule('#visualization circle:hover { cursor:pointer; }', 0);
		tempcss.insertRule('#visualization text:hover { cursor:pointer; }', 0);
	} else {
		tempcss.insertRule('#visualization path:not([fill^="#f5f5f5"]):hover {}', 0);
		tempcss.insertRule('#visualization circle:hover { }', 0);
		tempcss.insertRule('#visualization text:hover { }', 0);
	}

	//borders colour 1 (11)
	if (bordercolor !== '') {
		tempcss.insertRule('#visualization path { stroke:' + bordercolor + ' }', 0);
	} else {
		tempcss.insertRule('#visualization path { }', 0);
	}

	//borderwidth 1 (12)
	if (borderwidth !== '') {
		tempcss.insertRule('#visualization path { stroke-width:' + borderwidth + ' }', 0);
	} else {
		tempcss.insertRule('#visualization path { }', 0);
	}

	//inactive border width 3 (15)
	if (borderinawidth !== '') {
		tempcss.insertRule('#visualization path[fill^="' + inactivecolor + '"] { stroke-width:' + borderinawidth + ' }', 0);
		tempcss.insertRule('#visualization path[fill^="' + inactivecolor + '"]:hover { stroke-width:' + borderinawidth + ' }', 0);
		tempcss.insertRule('#visualization path[fill^="none"] { stroke-opacity:0; }', 0);

	} else {
		tempcss.insertRule('#visualization path { }', 0);
		tempcss.insertRule('#visualization path { }', 0);
		tempcss.insertRule('#visualization path { }', 0);
	}


	//background image
	if (bgimage !== '') {
		document.getElementsByName('bg_color')[0].value = 'transparent';
		mapstyle.backgroundImage = "url('" + bgimage + "')";
	}
	if (bgimage === '') {
		mapstyle.backgroundImage = "none";
	}

	//background repeat
	if (bgimage !== '' && bgrepeat === 1) {
		mapstyle.backgroundRepeat = "repeat";
		mapstyle.backgroundSize = "auto";
	}

	if (bgimage !== '' && bgrepeat === '') {
		mapstyle.backgroundSize = "100%";
		mapstyle.backgroundRepeat = "no-repeat";
	}

	//Tooltip CSS

	// font family 1 (16)
	if (tooltipfontfamily !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { font-family:"' + tooltipfontfamily + '"  !important; }', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}

	// font size 1 (17)
	if (tooltipfontsize !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { font-size:' + tooltipfontsize + '}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}
	// background color 1 (18)
	if (tooltipbg !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { background-color:' + tooltipbg + '}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}
	// min width 1 (19)
	if (tooltipminwidth !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { width:' + tooltipminwidth + '}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}
	// border colout 1 (20)
	if (tooltipbordercolor !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { border-color:' + tooltipbordercolor + '}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}
	// border width 1 (21)
	if (tooltipborderwidth !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip { border-width:' + tooltipborderwidth + '}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}
	// hide title 1 (22)
	if (tooltiphidetitle !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip-item:first-child { display:none;}', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}

	// hide title 1 (23)
	if (tooltipnowrap !== '') {
		tempcss.insertRule('#visualization .google-visualization-tooltip-item { white-space:nowrap; }', 0);
	} else {
		tempcss.insertRule('#visualization .google-visualization-tooltip {}', 0);
	}

	// 2 (24)
	if (fontawesomeapply !== '') {
		tempcss.insertRule('#visualization text { font-family:fontAwesome;}', 0);
		tempcss.insertRule('#add-table { font-family: fontAwesome;}', 0);
	} else {
		tempcss.insertRule('#visualization text {}', 0);
		tempcss.insertRule('#add-table { font-family:inherit;}', 0);
	}

	// set the hidden field content
	document.getElementsByName('customcss')[0].value = '{"iwm_size":"' + percentagesize + '","iwm_hsize":"' + hsize + '","iwm_left":"' + marginhorizontal + '","iwm_top":"' + marginvertical + '","hovercolor":"' + hovercolor + '","showcursor":"' + showcursor + '","bcolor":"' + bordercolor + '","bwidth":"' + borderwidth + '","biwidth":"' + borderinawidth + '","bgimage":"' + bgimage + '","bgrepeat":"' + bgrepeat + '","tooltipfontfamily":"' + tooltipfontfamily + '","tooltipfontsize":"' + tooltipfontsize + '","tooltipbg":"' + tooltipbg + '","tooltipminwidth":"' + tooltipminwidth + '","tooltiphidetitle":"' + tooltiphidetitle + '","tooltipnowrap":"' + tooltipnowrap + '","mobilemarker":"' + mobilemarker + '","tooltipbordercolor":"' + tooltipbordercolor + '","tooltipborderwidth":"' + tooltipborderwidth + '","fontawesomeinclude":"' + fontawesomeinclude + '","fontawesomeapply":"' + fontawesomeapply + '"}';
	iwm_drawVisualization();

}

function iwm_csscontrol(control) {
	if (control === "widthplus") {
		document.getElementsByName('iwm_size')[0].value = ( parseInt(document.getElementsByName('iwm_size')[0].value, 10 ) || 100) + 5;
	}
	if (control === "widthminus") {
		document.getElementsByName('iwm_size')[0].value = (parseInt(document.getElementsByName('iwm_size')[0].value, 10) || 100) - 5;
	}
	if (control === "up") {
		document.getElementsByName('iwm_top')[0].value = (parseInt(document.getElementsByName('iwm_top')[0].value, 10) || 0) - 5;
	}
	if (control === "down") {
		document.getElementsByName('iwm_top')[0].value = (parseInt(document.getElementsByName('iwm_top')[0].value, 10) || 0) + 5;
	}
	if (control === "left") {
		document.getElementsByName('iwm_left')[0].value = (parseInt(document.getElementsByName('iwm_left')[0].value, 10) || 0) - 5;
	}
	if (control === "right") {
		document.getElementsByName('iwm_left')[0].value = (parseInt(document.getElementsByName('iwm_left')[0].value, 10) || 0) + 5;
	}
	if (control === "verticalplus") {
		document.getElementsByName('iwm_hsize')[0].value = (parseInt(document.getElementsByName('iwm_hsize')[0].value, 10) || 62) + 5;
	}
	if (control === "verticalminus") {
		document.getElementsByName('iwm_hsize')[0].value = (parseInt(document.getElementsByName('iwm_hsize')[0].value, 10) || 62) - 5;
	}

	iwm_redrawcrop();
}

function iwm_expandcustomcss() {

	document.getElementById("iwm-custom-css").style.display = 'block';
	document.getElementById("iwmexpandcss").innerHTML = '<a onclick="iwm_closecustomcss()"><i class="fa fa-chevron-circle-down fa-lg"></i></i>' + iwmlocal.closeCssBox + '</a>';
}

function iwm_closecustomcss() {
	document.getElementById("iwm-custom-css").style.display = 'none';
	document.getElementById("iwmexpandcss").innerHTML = '<a onclick="iwm_expandcustomcss()"><i class="fa fa-chevron-circle-right fa-lg"></i></i>' + iwmlocal.expandCssBox + '</a>';
}


function iwm_clearCssValues() {

	document.getElementsByName('iwm_size')[0].value = '';
	document.getElementsByName('iwm_top')[0].value = '';
	document.getElementsByName('iwm_left')[0].value = '';
	document.getElementsByName('iwm_hsize')[0].value = '';
	document.getElementsByName('hovercolor')[0].value = '';
	document.getElementsByName('showcursor')[0].checked = false;
	document.getElementsByName('bcolor')[0].value = '';
	document.getElementsByName('bwidth')[0].value = '';
	document.getElementsByName('biwidth')[0].value = '';
	document.getElementsByName('bgimage')[0].value = '';
	document.getElementsByName('bgrepeat')[0].checked = false;
	document.getElementsByName('customcss')[0].value = '';
	iwm_redrawcrop();

}


function iwm_clearCropValues() {

	document.getElementsByName('iwm_size')[0].value = '';
	document.getElementsByName('iwm_top')[0].value = '';
	document.getElementsByName('iwm_left')[0].value = '';
	document.getElementsByName('iwm_hsize')[0].value = '';

	iwm_redrawcrop();

}

function iwm_getAddress() {

	var latlonspan = document.getElementById("latlonvalues");

	var geocoder = new google.maps.Geocoder();
	var address = document.getElementById('mapsearch').value;

	geocoder.geocode({
		'address': address
	}, function (results, status) {

		if (status === google.maps.GeocoderStatus.OK) {
			var glatitude = results[0].geometry.location.lat();
			var glongitude = results[0].geometry.location.lng();

			latlonspan.innerHTML = address + ": " + glatitude + " " + glongitude + " [<a href='javascript:void(0);' title='" + iwmlocal.copyToRegionCode + "' onclick='iwm_usethis(" + glatitude + "," + glongitude + ")'>" + iwmlocal.useThis + "</a>]";

		} else {
			latlonspan.innerHTML = iwmlocal.errorFinding;
		}
	});
}


function iwm_usethis(lat, lon) {
	var inp = document.addimap.cd;
	inp.value = lat + " " + lon;
}

function iwm_addPlaceToTable() {

	var code = document.addimap.cd.value.replace(/;/g, "");
	var title = document.addimap.c.value.replace(/;/g, "&#59");
	var tooltip = document.addimap.t.value.replace(/;/g, "&#59");
	var action = document.addimap.u.value.replace(/;/g, "&#59"); // special char &#59 = ;
	var color = document.addimap.cl.value.replace(/;/g, "");

	code = code.replace(/,/g, " ");
	title = title.replace(/,/g, "&#44");
	tooltip = tooltip.replace(/,/g, "&#44");
	action = action.replace(/,/g, "&#44"); // special char &#44 = ,
	color = color.replace(/,/g, "");

	var newtext = code + ',' + title + ',' + tooltip + ',' + action + ',' + color + ';\n';
	document.addimap.places.value += newtext;
	document.addimap.cd.value = "";
	document.addimap.c.value = "";
	document.addimap.t.value = "";
	document.addimap.u.value = "";

	iwm_dataToTable();
}

function iwm_dataToTable() {


	var oldText = document.getElementById("places").value;
	var span = document.getElementById("htmlplacetable");
	var categories_count_label = '';

	if (oldText === 'categories_count') {
		categories_count_label = '<strong>' + iwmlocal.categoriesCount + '</strong><br>' + iwmlocal.categoriesCountMessage01 + '<br>' + iwmlocal.categoriesCountMessage02;
		span.innerHTML = '<div class="iwm_data_table_warning">' + categories_count_label + '</div>';
		return;
	}


	var oldTextArr = oldText.split(";");
	oldTextArr.pop();
	var newText = "";
	var i = 0;
	for (i = 0; i < oldTextArr.length; i++) {
		oldTextArr[i] = oldTextArr[i].replace(/\r/, "");
		oldTextArr[i] = oldTextArr[i].replace(/^\'/, "");
		oldTextArr[i] = oldTextArr[i].replace(/^\"/, "");
		oldTextArr[i] = oldTextArr[i].replace(/"$/, "");
		oldTextArr[i] = oldTextArr[i].replace(/'$/, "");



		var entry = oldTextArr[i].split(",");
		var colori = entry[4];

		//to disable html
		var rendertags = document.getElementById('rendertags');
		var renderhtml = false;
		if (rendertags.checked === true) {
			renderhtml = true;
		}
		if (!renderhtml) {
			oldTextArr[i] = jQuery("<div/>").text(oldTextArr[i]).html();
		}

		oldTextArr[i] = oldTextArr[i] + "<div class='colorsample' style='background-color:" + colori + "'></div>";

		oldTextArr[i] = "<tr><td>" + oldTextArr[i] + "</td><td style='white-space:nowrap;'><i alt='edit' title='" + iwmlocal.editEntry + "' class='fa fa-pencil-square' aria-hidden='true' onclick='iwm_editPlace(" + i + ");'></i><i class='fa fa-plus-circle' alt='Delete' title='Delete' aria-hidden='true' onclick='iwm_deletePlace(" + i + ");'></i></td></tr>";
	}

	var linesep = ",";


	for (i = 0; i < oldTextArr.length; i++) {


		oldTextArr[i] = oldTextArr[i].replace(new RegExp(linesep, "gi"), "</td><td>");
		newText = newText + oldTextArr[i] + "\n";
		newText = newText.replace(new RegExp("&#59", "gi"), ";");
		newText = newText.replace(new RegExp("&#44", "gi"), ",");


	}

	var header = "<tr><th style='width:10%;'>" + iwmlocal.regionCode + "</th><th style='width:20%;'>" + iwmlocal.tooltipTitle + "</th><th style='width:20%;'>" + iwmlocal.tooltipText + "</th><th style='width:23%;'>" + iwmlocal.actionValue + "</th><th style='width:10%;'>" + iwmlocal.color + "</th><th style='width:7%;'>&nbsp;</th></tr>";
	newText = "<table class='data-content-table'>\n" + header + "\n" + newText + "</table>\n";


	span.innerHTML = newText; // clear existing

	iwm_drawVisualization();
}

function iwm_updatePlace(placeid) {

	//Get old text
	var oldText = document.getElementById("places").value;
	//Split into lines
	var oldTextArr = oldText.split(";");
	oldTextArr.pop();
	var newText = "";
	for (i = 0; i < oldTextArr.length; i++) {

		if (i === placeid) {
			var updatecode = document.getElementById("input-" + placeid + "-0").value.replace(/,/g, " ");
			var updatetitle = document.getElementById("input-" + placeid + "-1").value.replace(/,/g, "&#44");
			var updatetooltip = document.getElementById("input-" + placeid + "-2").value.replace(/,/g, "&#44");
			var updateaction = document.getElementById("input-" + placeid + "-3").value.replace(/,/g, "&#44");
			var updatecolor = document.getElementById("input-" + placeid + "-4").value.replace(/,/g, "");



			updatecode = updatecode.replace(/;/g, " ");
			updatetitle = updatetitle.replace(/;/g, "&#59");
			updatetooltip = updatetooltip.replace(/;/g, "&#59");
			updateaction = updateaction.replace(/;/g, "&#59");
			updatecolor = updatecolor.replace(/;/g, "");

			//if tinymce is enabled
			var editor = document.getElementById('editor').value;
			if ( parseInt( editor, 10) === 1 ) {
				var mapaction = document.getElementsByName('map_action')[0].value;
				if (mapaction !== 'i_map_action_open_url' && mapaction !== 'i_map_action_open_url_new' && mapaction !== 'i_map_action_alert' && mapaction !== 'i_map_action_colorbox_iframe' && mapaction !== 'i_map_action_colorbox_inline' && mapaction !== 'i_map_action_colorbox_image' && mapaction !== 'none' && mapaction !== 'i_map_action_custom') {
					mapaction = true;
				} else {
					mapaction = false;
				}
				var tooltip = parseInt(usehtml, 10);
				if (mapaction) {
					var action = tinyMCE.get("input-" + placeid + "-3").getContent();
					action = action.replace(/,/g, "&#44");
					updateaction = action.replace(/;/g, "&#59");
				}
				if (parseInt(tooltip,10) === 1) {
					tooltip = tinyMCE.get("input-" + placeid + "-2").getContent();
					tooltip = tooltip.replace(/,/g, "&#44");
					updatetooltip = tooltip.replace(/;/g, "&#59");
				}
			}

			newText = newText + "\n" + updatecode + "," + updatetitle + "," + updatetooltip + "," + updateaction + ",#" + updatecolor + ";";
		} else {
			newText = newText + oldTextArr[i] + ";";
		}
	}
	document.getElementById("places").value = newText;
	iwm_dataToTable();
}

function iwm_deletePlace(placeid) {

	var conf = confirm( iwmlocal.confirmDelete );
	if (conf === true) {
		var oldText = document.getElementById("places").value;
		var oldTextArr = oldText.split(";");
		oldTextArr.pop();
		oldTextArr.splice(placeid, 1);

		var newText = "";
		for (i = 0; i < oldTextArr.length; i++) {
			newText = newText + oldTextArr[i] + ";";
		}
		document.getElementById("places").value = newText;
		iwm_dataToTable();
	}
}




function iwm_editPlace(placeid) {

	var oldText = document.getElementById("places").value;
	var ixvalue;
	var colorinput;
	var oldTextArr = oldText.split(";");
	oldTextArr.pop();
	var newText = "";
	for (i = 0; i < oldTextArr.length; i++) {
		oldTextArr[i] = oldTextArr[i].replace(/\r/, "");
		oldTextArr[i] = oldTextArr[i].replace(/^\'/, "");
		oldTextArr[i] = oldTextArr[i].replace(/^\"/, "");
		oldTextArr[i] = oldTextArr[i].replace(/"$/, "");
		oldTextArr[i] = oldTextArr[i].replace(/'$/, "");

		if ( parseInt( placeid, 10 ) === i) {

			var editArr = oldTextArr[i].split(",");
			oldTextArr[i] = "<tr class='editing-map-entry'>";
			for (ix = 0; ix < editArr.length; ix++) {
				if (ix !== 4 && ix !== 3 && ix !== 2) {
					ixvalue = editArr[ix].replace(/'/g, "&#39;");
					oldTextArr[i] = oldTextArr[i] + "<td><input type='text' id='input-" + placeid + "-" + ix + "' value='" + ixvalue + "'></td>\n";
				}
				if (ix === 3 || ix === 2) {
					ixvalue = editArr[ix].replace(/'/g, "&#39;");
					oldTextArr[i] = oldTextArr[i] + "<td><textarea class='tinymce-enabled' id='input-" + placeid + "-" + ix + "'>" + ixvalue + "</textarea></td>\n";
				}

				if (ix === 4) {
					colorinput = document.createElement("INPUT");
					var inputname = 'input-' + placeid + '-' + ix;
					var inputvalue = '#' + editArr[ix];
					colorinput.type = 'text';
					colorinput.id = String(inputname);
					colorinput.value = String(inputvalue);
					var col = new jscolor.color(colorinput);
					oldTextArr[i] = oldTextArr[i] + "<td><span id='colori'></span></td>\n";

				}
			}
			oldTextArr[i] = oldTextArr[i] + "</td><td><i onclick='iwm_updatePlace(" + placeid + ");' class='fa fa-check-square' title='Submit Changes' aria-hidden='true'></i><i class='fa fa-undo' aria-hidden='true' title='Cancel' onclick='iwm_dataToTable();'></i></td></tr>";

		} else {

			var entry = oldTextArr[i].split(",");
			var colori = entry[4];

			// to disable html
			var rendertags = document.getElementById('rendertags');
			var renderhtml = false;
			if (rendertags.checked === true) {
				renderhtml = true;
			}
			if (!renderhtml) {
				oldTextArr[i] = jQuery("<div/>").text(oldTextArr[i]).html();
			}

			oldTextArr[i] = oldTextArr[i] + "<div class='colorsample' style='background-color:" + colori + "'></div>";
			oldTextArr[i] = "<tr><td>" + oldTextArr[i] + "</td><td style='white-space:nowrap;'><i alt='edit' title='Edit Entry' class='fa fa-pencil-square' aria-hidden='true' onclick='iwm_editPlace(" + i + ");'></i><i class='fa fa-plus-circle' alt='Delete' title='Delete' aria-hidden='true' onclick='iwm_deletePlace(" + i + ");'></i></td></tr>";
		}

	}

	var linesep = ",";


	for (i = 0; i < oldTextArr.length; i++) {
		oldTextArr[i] = oldTextArr[i].replace(new RegExp(linesep, "gi"), "</td><td>");
		newText = newText + oldTextArr[i] + "\n";
		newText = newText.replace(new RegExp("&#59", "gi"), ";");
		newText = newText.replace(new RegExp("&#44", "gi"), ",");
	}

	var header = "<tr><th style='width:10%;'>" + iwmlocal.regionCode + "</th><th style='width:20%;'>" + iwmlocal.tooltipTitle + "</th><th style='width:20%;'>" + iwmlocal.tooltipText + "</th><th style='width:23%;'>" + iwmlocal.actionValue + "</th><th style='width:10%;'>" + iwmlocal.color + "</th><th style='width:7%;'>&nbsp;</th></tr>";
	newText = "<table class='data-content-table'>\n" + header + "\n" + newText + "</table>\n";

	var span = document.getElementById("htmlplacetable");
	var actionvaluetip = document.getElementById("actionvaluetip");
	span.innerHTML = newText; // clear existing

	document.getElementById("colori").appendChild(colorinput);


	iwm_tinymce(placeid);

}

google.charts.load('42', {
	packages: ['geochart']
});

google.charts.setOnLoadCallback(iwm_initmap);

function iwm_drawVisualization() {

	var usehtml = document.getElementsByName('usehtml')[0].value;
	var bgcolor = document.getElementsByName('bg_color')[0].value;
	var stroke = document.getElementsByName('border_stroke')[0].value;
	var bordercolor = document.getElementsByName('border_color')[0].value;
	var incolor = document.getElementsByName('ina_color')[0].value;
	var actcolor = document.getElementsByName('act_color')[0].value;
	var width = document.getElementsByName('width')[0].value;
	var height = document.getElementsByName('height')[0].value;
	var aspratio = document.getElementById('aspratio');
	var responsivemode = document.getElementsByName('responsivemode')[0].value;
	var interact = document.getElementById('interactive');
	var tooltipt = document.getElementsByName('tooltipt')[0].value;
	var areacombo = document.getElementsByName('region')[0].value;
	var areashow = areacombo.split(",");
	var region = areashow[0];
	var resolution = areashow[1];
	var markersize = document.getElementsByName('marker_size')[0].value;
	var displaym = document.getElementsByName('display_mode')[0].value;
	var placestxt = document.getElementsByName('places')[0].value.replace(/(\r\n|\n|\r)/gm, "");
	var places = placestxt.split(";");
	var projection = document.getElementsByName('mapprojection')[0].value;
	var imageiconurl = document.getElementsByName('imageicon')[0].value;
	var imageiconposition = document.getElementsByName('imageicon_position')[0].value;
	var i;
	var index;
	var pl = places;
	var newplaces = [];

	for (i = 0; i < pl.length; i++) {

		var ple = pl[i].split(",");

		if (ple[0].indexOf('group:') > -1) {


			var tempindex = ple[0].replace('group:', '');

			temparray = tempindex.split('|');

			for (y = 0; y < temparray.length; y++) {
				var newentry = temparray[y] + ',' + ple[1] + ',' + ple[2] + ',' + ple[3] + ',' + ple[4];
				newplaces.unshift(newentry);
			}
		} else {
			var sameentry = ple[0] + ',' + ple[1] + ',' + ple[2] + ',' + ple[3] + ',' + ple[4];
			newplaces.push(sameentry);
		}

	}

	places = newplaces;

	if (responsivemode === "on") {
		width = null;
		height = null;
	}

	var displaymode = "regions";

	if (displaym === "markers" || displaym === "markers02" || displaym === "customicon") {
		displaymode = "markers";
	}

	if (displaym === "text" || displaym === "text02") {
		displaymode = "text";
	}

	var ratio = false;
	if (aspratio.checked === true) {
		ratio = true;
	}

	var interactive = 'true';
	if (interact.checked !== true) {
		interactive = 'false';
	}

	var toolt = 'focus';
	if ( parseInt( tooltipt,10 ) === 0 ) {
		toolt = 'none';
	}
	if ( parseInt(tooltipt,10) === 2) {
		toolt = 'selection';
	}

	var data = new google.visualization.DataTable();

	if (displaym === "markers02" || displaym === "text02" || displaym === "customicon") {
		data.addColumn('number', 'Lat');
		data.addColumn('number', 'Long');
	}

	data.addColumn('string', 'Country'); // Implicit domain label col.
	data.addColumn('number', 'Value'); // Implicit series 1 data col.
	data.addColumn({
		type: 'string',
		role: 'tooltip',
		p: {
			html: true
		}
	}); //
	var ivalue = [];
	var colorsmap = [];
	var colorsmapecho = "";
	var defmaxvalue;

	//places.length-1 to eliminate empty value at the end
	for (i = 0; i < places.length - 1; i++) {

		var entry = places[i].split(",");
		var ttitle = entry[1].replace(/&#59/g, ";");
		ttitle = ttitle.replace(/&#44/g, ",");
		var ttooltip = entry[2].replace(/&#59/g, ";");
		ttooltip = ttooltip.replace(/&#44/g, ",");

		//If data !== markers02
		if (displaym !== "markers02" && displaym !== "text02" && displaym !== "customicon") {

			data.addRows([
				[{
					v: entry[0],
					f: ttitle
				}, i, ttooltip]
			]);

			index = entry[0];
		} else {
			var trim = entry[0].replace(/^\s+|\s+$/g, "");
			var latlon = trim.split(/ /);
			var lat = parseFloat(latlon[0]);
			var lon = parseFloat(latlon[1]);

			data.addRows([
				[lat, lon, ttitle, i, ttooltip]
			]);

			index = lat;

		}
		var colori = entry[4];

		ivalue[index] = entry[3].replace(/&#59/g, ";");
		ivalue[index] = ivalue[index].replace(/&#44/g, ",");

		colorsmapecho = colorsmapecho + "'" + colori + "',";
		colorsmap.push(colori);
		ivalue.push(ivalue);
	}

	defmaxvalue = 0;
	if ((places.length - 2) > 0) {
		defmaxvalue = places.length - 2;
	}

	var htmltooltip = false;
	if (parseInt( usehtml, 10 ) === 1) {
		htmltooltip = true;
	}

	var magglass = true;

	if (displaym === 'customicon' && imageiconurl !== '') {
		magglass = false;
	}

	var options = {
		projection: projection,
		backgroundColor: {
			fill: bgcolor,
			stroke: bordercolor,
			strokeWidth: stroke
		},
		colorAxis: {
			minValue: 0,
			maxValue: defmaxvalue,
			colors: colorsmap
		},
		legend: 'none',
		datalessRegionColor: incolor,
		displayMode: displaymode,
		enableRegionInteractivity: interactive,
		resolution: resolution,
		sizeAxis: {
			minValue: 1,
			maxValue: 1,
			minSize: markersize,
			maxSize: markersize
		},
		region: region,
		keepAspectRatio: ratio,
		width: width,
		height: height,
		tooltip: {
			trigger: toolt,
			isHtml: htmltooltip
		},
		magnifyingGlass: {
			enable: magglass,
		},
		domain: 'IN'
	};

	var chart = new google.visualization.GeoChart(document.getElementById('visualization'));

	google.visualization.events.addListener(chart, 'select', function () {
		var selection = chart.getSelection();

		if (selection.length === 1) {
			var selectedRow = selection[0].row;
			var selectedRegion = data.getValue(selectedRow, 0);
			if (ivalue[selectedRegion] !== '') {
				alert(ivalue[selectedRegion]);
			}
		}
	});

	var iwm_img_div = document.getElementById('iwm_image_map');
	var iwm_img_field = document.getElementById('mapimage');

	google.visualization.events.addListener(chart, 'ready', function () {
		if (iwm_img_field) {
			iwm_img_field.value = chart.getImageURI();
		}

		//to replace markers with custom icon
		if (displaym === 'customicon' && imageiconurl !== '') {

			var imageurl = imageiconurl;
			var width = markersize;

			//default center position
			var intwidth = -(parseInt(width, 10) / 2);
			var intheight = -(parseInt(width, 10) / 2);
			//top position
			if (imageiconposition === 'top') {
				intheight = -(parseInt(width, 10));
			}

			var transform = 'translate(' + intwidth + ',' + intheight + ')';
			var imageicon = document.createElementNS('http://www.w3.org/2000/svg', 'image');
			imageicon.setAttributeNS('http://www.w3.org/1999/xlink', 'href', imageurl);
			imageicon.setAttribute('transform', transform);
			imageicon.setAttribute('height', width);
			imageicon.setAttribute('width', width);
			imageicon.setAttribute('preserve', 'xMaxYMax meet');

			jQuery('#visualization circle').each(function () {

				var x = jQuery(this).attr('cx');
				var y = jQuery(this).attr('cy');

				jQuery(this).replaceWith(jQuery(imageicon).clone().attr('x', x).attr('y', y));

			});
		}

	});

	chart.draw(data, options);

}

function iwm_showsimple() {
	document.getElementById('simple-table').style.display = 'block';
	document.getElementById('advanced-table').style.display = 'none';
	document.getElementById('populate-automatically-div').style.display = 'none';
	document.getElementById("shsimple").setAttribute("class", "activeb");
	document.getElementById("shadvanced").setAttribute("class", "inactiveb");
	document.getElementById('shpopulate').setAttribute("class", 'inactiveb');
}

function iwm_showadvanced() {
	document.getElementById('simple-table').style.display = 'none';
	document.getElementById('populate-automatically-div').style.display = 'none';
	document.getElementById('advanced-table').style.display = 'block';
	document.getElementById("shsimple").setAttribute("class", "inactiveb");
	document.getElementById("shadvanced").setAttribute("class", "activeb");
	document.getElementById('shpopulate').setAttribute("class", 'inactiveb');
}

function iwm_showpopulate() {
	document.getElementById('simple-table').style.display = 'none';
	document.getElementById('populate-automatically-div').style.display = 'block';
	document.getElementById('advanced-table').style.display = 'none';
	document.getElementById("shsimple").setAttribute("class", "inactiveb");
	document.getElementById("shadvanced").setAttribute("class", "inactiveb");
	document.getElementById('shpopulate').setAttribute("class", 'activeb');
}

function iwm_hidecustomsettings() {
	var e = document.getElementById('default-settings-table-add');
	e.style.display = 'none';
}

function iwm_showcustomsettings() {

	if (document.getElementsByName('use_defaults')[1].checked) {

		var e = document.getElementById('default-settings-table-add');
		e.style.display = 'block';
	}

}

function iwm_customoptionshow() {
	var e = document.getElementById('custom-action');
	e.style.display = 'block';
}

function iwm_customoptionhide() {
	var e = document.getElementById('custom-action');
	e.style.display = 'none';
}

function iwm_latlonshow() {
	var e = document.getElementById('latlondiv');
	e.style.display = 'block';
}

function iwm_latlonhide() {
	var e = document.getElementById('latlondiv');
	e.style.display = 'none';
}

function iwm_isolink() {

	var display = document.getElementsByName('display_mode')[0].value;
	var areacombo = document.getElementsByName('region')[0].value;
	var mapaction = document.getElementsByName('map_action')[0].value;
	var areashow = areacombo.split(",");
	var region = areashow[0];
	var resolution = areashow[1];
	var span = document.getElementById("iso-code-msg");

	if (resolution === 'countries' && display === "regions") {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - ' + iwmlocal.isoInfo;
		iwm_latlonhide();
	}

	if (resolution === 'provinces' && display === "regions") {
		var ct = areashow[0].length;
		var linkiso;
		if (ct > 2) {
			linkiso = "<a href='http://en.wikipedia.org/wiki/ISO_3166-2:US'>ISO-3166-2:US</a>";
		} else {
			linkiso = "<a target='_blank' href='http://en.wikipedia.org/wiki/ISO_3166-2:" + areashow[0] + "'>ISO-3166-2:" + areashow[0] + "</a>";
		}
		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - ' + iwmlocal.isoCodes + linkiso;
		iwm_latlonhide();
	}

	if (resolution === 'metros' && display === "regions") {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - ' + iwmlocal.metroCodes;
		iwm_latlonhide();
	}

	if (resolution === 'continents' && display === "regions") {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - ' + iwmlocal.continents; //
		iwm_latlonhide();

	}

	if (resolution === 'subcontinents' && display === "regions") {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + document.getElementById('region')[document.getElementById('region').selectedIndex].innerHTML + '</b> - ' + iwmlocal.subContinents; //

		iwm_latlonhide();

	}

	if (display === 'text') {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + iwmlocal.textMarkers + '</b> - ' + iwmlocal.textMarkersInfo; //

		iwm_latlonhide();
	}

	if (display === 'markers') {

		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + iwmlocal.roundMarkers + '</b> - ' + iwmlocal.roundMarkersInfo; //
		iwm_latlonhide();
	}

	if (display === 'markers02') {
		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + iwmlocal.roundMarkersCoordinates + '</b> - ' + iwmlocal.roundMarkersCoordinatesInfo; //
		iwm_latlonshow();
	}

	if (display === 'customicon') {
		span.innerHTML = '<b><i class="fa fa-question-circle"></i>' + iwmlocal.customIcon + '</b> - ' + iwmlocal.customIconInfo; //
		iwm_latlonshow();
	}

	if (display === 'text02') {
		span.innerHTML = '<b><i class="fa fa-question-circle"></i> ' + iwmlocal.textLabels + '</b> - ' + iwmlocal.textLabelsInfo; //
		iwm_latlonshow();
	}

	if (mapaction === 'i_map_action_open_url') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.actionOpenUrl + ' </b> - ' + iwmlocal.openUrlDesc; //
		actionvaluetip.innerHTML = iwmlocal.urlToOpen;
		iwm_customoptionhide();
	}
	if (mapaction === 'i_map_action_open_url_new') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.actionOpenUrlNewWindow + ' </b> - ' + iwmlocal.openUrlNewDesc; //
		actionvaluetip.innerHTML = iwmlocal.urlToOpen;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_alert') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i>' + iwmlocal.actionAlert + '</b> - ' + iwmlocal.actionAlertDescription; //
		actionvaluetip.innerHTML = iwmlocal.messageToDisplay;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_content_below') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i>' + iwmlocal.displayContentBelowMap + '</b> - ' + iwmlocal.displayContBelowDescription; //
		actionvaluetip.innerHTML = iwmlocal.contentToDisplayBelow;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_content_below_scroll') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i>' + iwmlocal.displayContBelowScroll + '</b> - ' + iwmlocal.displayContBelowScrollDesc; //
		actionvaluetip.innerHTML = iwmlocal.contentToDisplayBelow;
		iwm_customoptionhide();
	}


	if (mapaction === 'i_map_action_content_above') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.displayContentAbove + '</b> - ' + iwmlocal.displayContentAboveDesc; //
		actionvaluetip.innerHTML = iwmlocal.contentToDisplayAbove;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_colorbox_content') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.displayContentLightbox + '</b> - ' + iwmlocal.displayContentLightboxDesc; //
		actionvaluetip.innerHTML = iwmlocal.contentToDisplayLightbox;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_colorbox_iframe') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.displayUrlLightbox + '</b> - ' + iwmlocal.urlLightboxDesc; //
		actionvaluetip.innerHTML = iwmlocal.iframeURL;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_colorbox_inline') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.displayInlineContent + '</b> - ' + iwmlocal.inlineDesc; //
		actionvaluetip.innerHTML = iwmlocal.inlineHelper;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_colorbox_image') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.displayImageLightbox + ' </b> - ' + iwmlocal.imageLightboxDesc; //
		actionvaluetip.innerHTML = iwmlocal.fullUrlLightbox;
		iwm_customoptionhide();
	}

	if (mapaction === 'i_map_action_custom') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> ' + iwmlocal.customAction + '</b> - ' + iwmlocal.customActionDesc; //
		actionvaluetip.innerHTML = iwmlocal.customActionContent;
		iwm_customoptionshow();
	}

	if (mapaction === 'i_map_action_content_right_1_4' || mapaction === 'i_map_action_content_right_1_3') {
		span.innerHTML = span.innerHTML + '<br /><br /><b><i class="fa fa-question-circle"></i> '+ iwmlocal.displayContentRight + '</b> - ' + iwmlocal.contentRightDesc; //
		actionvaluetip.innerHTML = iwmlocal.contentRight;
		//customoptionshow();
	}

	if (mapaction === 'none') {
		iwm_customoptionhide();
	}
}

function iwm_isolinkcheck() {
	iwm_drawVisualization();
	iwm_isolink();
}

function iwm_initmap() {
	iwm_isolink();
	iwm_dataToTable();
	iwm_showcustomsettings();
	iwm_redrawcrop();
	iwm_expandcustomcss();
}

function iwm_addslashes(str) {
	return (str + '').replace(/([\\"'])/g, "\\$1").replace(/\0/g, "\\0");
}

function iwm_tinymce(placeid) {
	var editor = parseInt( document.getElementById('editor').value, 10 );
	if (editor === 1) {
		var mapaction = document.getElementsByName('map_action')[0].value;
		if (mapaction !== 'i_map_action_open_url' && mapaction !== 'i_map_action_open_url_new' && mapaction !== 'i_map_action_alert' && mapaction !== 'i_map_action_colorbox_iframe' && mapaction !== 'i_map_action_colorbox_inline' && mapaction !== 'i_map_action_colorbox_image' && mapaction !== 'none' && mapaction !== 'i_map_action_custom') {
			mapaction = true;
		} else {
			mapaction = false;
		}
		var tooltip = parseInt(usehtml, 10);
		for (var i = tinymce.editors.length - 1; i > -1; i--) {
			var ed_id = tinymce.editors[i].id;
			tinyMCE.execCommand("mceRemoveEditor", true, ed_id);
		}
		tinymce.init({
			selector: "input-" + placeid + "-2", // change this value according to your HTML
			plugins: "image fullscreen lists media wordpress wpautoresize wpeditimage wplink wpdialogs wpview charmap compat3x",
			menubar: false,
			force_p_newlines: false,
			forced_root_block: '',
			relative_urls: false,
			remove_script_host: false,
			convert_urls: false,
			toolbar: "bold italic underline alignleft aligncenter alignright alignjustify bullist numlist link unlink image fullscreen code "
		});

		if ( parseInt( tooltip )  === 1 ) {
			tinymce.execCommand('mceAddEditor', false, "input-" + placeid + "-2");
		}
		if ( mapaction ) {
			tinymce.execCommand('mceAddEditor', false, "input-" + placeid + "-3");
		}
	}
}

//set local storage for rendertags checkbox
jQuery(function () {
	var data = localStorage.getItem("rendertags");
	if (data !== null) {
		jQuery("input[name='rendertags']").attr("checked", "checked");
	}
});

//store rendertags checkbox state onclick
jQuery("input[name='rendertags']").click(function () {
	if (jQuery(this).is(":checked")) {
		localStorage.setItem("rendertags", jQuery(this).val());
	} else {
		localStorage.removeItem("rendertags");
	}
});

//Prepare populate automatically actions
jQuery(document).ready(function () {

	var datadir = jQuery('#data-directory').val();

	jQuery('.automatebutton').each(function () {
		var thisbutton = jQuery(this);
		thisbutton.on('click', function () {
			var current = jQuery('#places');
			var id = jQuery(this).attr('id');
			if (current.val() !== '') {
				if (confirm('You will loose your current data. Are you sure?')) {
					iwm_populate(id, datadir, current, thisbutton);
				}
			} else {
				iwm_populate(id, datadir, current, thisbutton);
			}
		});
	});
});

function iwm_populate(id, datadir, current, thisbutton) {

	thisbutton.addClass('automatebuttonsucess');
	setTimeout(function () {
		thisbutton.removeClass('automatebuttonsucess');
	}, 2000);

	var options = {
		"us-labels": {
			"region": "US,provinces",
			"display_mode": "text02",
		},
		"us-states": {
			"region": "US,provinces",
			"display_mode": "regions",
		},
		"world-countries": {
			"region": "world,countries",
			"display_mode": "regions",
		},
		"categories_count": {
			"region": "world,countries",
			"places": "categories_count",
			"display_mode": "regions",
		}
	};

	if (options.hasOwnProperty(id)) {
		for (var key in options[id]) {
			if (options[id].hasOwnProperty(key)) {
				document.getElementsByName(key)[0].value = options[id][key];
			}
		}

		//if we change the places value, then we don't need to fetch the file
		if (options[id].hasOwnProperty('places')) {
			iwm_dataToTable();
			return;
		}
	}

	fetch(datadir + '/' + id + '.txt')
		.then(response => response.text())
		.then(text => { current.val(text); iwm_dataToTable(); });
}