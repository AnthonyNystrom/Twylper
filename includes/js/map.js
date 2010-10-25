$(document).ready(function() {
	var map;
	var shape = [];
	var centerCoord;	
	var out;
	var arrMarkers = [];
	var arrInfoWindows = [];
	var arrPolygons = [];

	function mapInit(){
        $.getJSON("/actions/places.php", {}, function(data){
            var bounds = new google.maps.LatLngBounds();
    		$.each(data.places, function(i, item){
    		    var sub = [];
    		    $.each(item, function(j, coord){
    		        sub[j] = new google.maps.LatLng(coord.lat, coord.lng);
    		        bounds.extend(sub[j]);
    		    });
    		    shape[i] = sub;
    		});
    		centerCoord = new google.maps.LatLng(data.center.lat, data.center.lng);
    		var mapOptions = {
    			zoom: 11,
    			center: centerCoord,
    			mapTypeId: google.maps.MapTypeId.ROADMAP
    		};
    		map = new google.maps.Map(document.getElementById("map"), mapOptions);
    		map.fitBounds(bounds);
            
    		$.each(shape, function(i, item){
    		    area = new google.maps.Polygon({
    			    paths: item,
    			    strokeColor: "#5B98BA",
    			    strokeOpacity: 0.8,
    			    strokeWeight: 1,
    		        fillColor: "#5B98BA",
    			    fillOpacity: 0.3
    		    });
    		    area.setMap(map);
				arrPolygons[i] = area;
    		});
    	    
    	    $("#search_location").html(data.location);

			//initiate new search for nearby tweets when dragged or zoomed
			google.maps.event.addListener(map, 'dragend', function() {
				updateNearby();
			});
			google.maps.event.addListener(map, 'zoom_changed', function() {
				updateNearby();
			});
    		
    		updateNearby();
			
    	});
	}
	
	mapInit();
	
	function updateNearby(){
		var bounds = map.getBounds();
		var lat_min = bounds.getSouthWest().lat();
		var lat_max = bounds.getNorthEast().lat();
		var lng_min = bounds.getSouthWest().lng();
		var lng_max = bounds.getNorthEast().lng();
		
        $.getJSON("/actions/nearby_topics.php", { lat_min: lat_min, lat_max: lat_max, lng_min: lng_min, lng_max: lng_max }, function(data){  
			$("#result_container").html('');
			clearMarkers();
			if (data.topics) {
			    $.each(data.topics, function(i, item){
        		    out = '<div id="topic_row" style="margin-bottom: 5px; padding-bottom: 5px; padding-top: 5px; width: 295px;">';
                	out += '<div id="topic_img"><img src="' + item.profile_image_url + '"></div>';
                    out += '<div id="topic_data" style="width: 220px;">';
                	out += '	<a href="/' + item.string + '" title="' + item.title + ' Replies">' + item.title + '</a>';
                	out += '	<div id="topic_meta_data">By <a href="/profile/' + item.screen_name + '" title="Twitter Comments by ' + item.screen_name + '">';
                	out += '    <b>' + item.screen_name + '</b></a> with <a href="/' + item.string + '" title="' + item.title + ' Replies">' + item.comments + ' comments</a></div>';
                	out += '	</div><br clear=both>';
                	out += '</div>';
           		    $("#result_container").append(out);
           		    
           		    //stupid workaround for sticky footer needs to be called after we populate result_container


           		    var loc = new google.maps.LatLng(item.latitude,item.longitude)
           		    var marker = new google.maps.Marker({
                        position: loc, 
                        map: map, 
                        title:"Topic"
                    });
                    arrMarkers[i] = marker;
                    var infowindow = new google.maps.InfoWindow({
                    	content: out
                    });
                	arrInfoWindows[i] = infowindow;
                    google.maps.event.addListener(marker, 'click', function() {
                		infowindow.open(map, marker);
                	});                
    			});
                //trigger the footer fixer when search results are displayed (in case there are a lot)
                $(window).scroll();
			} else {
			    out = '<br/><h4>Nothing found.</h4> <br/><p>Drag the map around or zoom in and out.</p>';
			    out += '<p>You can also search for a <a href="#" id="none_new_location">new location</a>.</p>';
                $("#result_container").html(out);
                $('#none_new_location').click(function() {
                    $('#places_search_form').toggle();
                });
			}

			
    	});
	}
	
	function clearMarkers() {
		if (arrMarkers) {
			for (i in arrMarkers) {
				arrMarkers[i].setMap(null);
			}
			arrInfoWindows = [];
		}
	}
	
	function clearOverlays() {
	    if (arrPolygons) {
		    for (i in arrPolygons) {
			    arrPolygons[i].setMap(null);
		    }
	    }
    }
	
	$('#search_location_change').click(function() {
        $('#places_search_form').toggle();
        return false;
    });
    
    $('.place_search_submit').click(function() {
        $('.place_search_submit').attr('style', 'background: url("/images/search-loader.gif") no-repeat center #fff !important')
        $('#place_search_results').html('');
        $('#place_search_results').hide();
        var q = $('#place_search_query').val();
        $.getJSON("/actions/places_search.php", {'query': q}, function(data){
            if (data.places == 'none') {
                $("#place_search_results").html('No results found');
            } else {
     		    $.each(data.places, function(i, item){
                    $("#place_search_results").append('<a href="#" class="choose_place" id="' + item.id + '">' + item.full_name + '</a><br/>');
     		    });
     		}
     		$('.place_search_submit').attr('style', '');
     		$('#place_search_results').slideDown('slow');
     		
     		$('a.choose_place').click(function() {
     		    $('#place_search_results').slideUp('slow');
                clearOverlays();
        		var chosen_place = $(this).attr('id');
                $.getJSON("/actions/places.php", {'place_id': chosen_place}, function(data){
                    var bounds = new google.maps.LatLngBounds();
            		$.each(data.places, function(i, item){
            		    var sub = [];
            		    $.each(item, function(j, coord){
            		        sub[j] = new google.maps.LatLng(coord.lat, coord.lng);
            		        bounds.extend(sub[j]);
            		    });
            		    shape[i] = sub;
            		});
            		map.fitBounds(bounds);

            		$.each(shape, function(i, item){
            		    area = new google.maps.Polygon({
            			    paths: item,
            			    strokeColor: "#5B98BA",
            			    strokeOpacity: 0.8,
            			    strokeWeight: 1,
            		        fillColor: "#5B98BA",
            			    fillOpacity: 0.3
            		    });
            		    area.setMap(map);
        				arrPolygons[i] = area;
            		});

            	    $("#search_location").html(data.location);
                    updateNearby();

             	});
             	return false;
            });
     		
     	});
    });
    //Submit search query when enter key pressed

    $('#place_search_query').keydown(function(e) {
        if(e.keyCode == 13) {
            $('.place_search_submit').click();
            return false
        }
    });

});