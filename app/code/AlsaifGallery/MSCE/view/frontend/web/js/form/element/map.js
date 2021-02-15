'use strict';

define([
	'jquery',
	'Magento_Ui/js/form/element/abstract',
	'ko',
], function ($, Element, ko) {
	'use strict';
	return Element.extend({
		defaults: {
			CheckVal            : ko.observable(false),
			additionalClasses   : {},
			cities              : ko.observableArray([]),
			cityId              : null,
			cityText            : null,
			content             : null,
			dependOnCity        : true,
			districtText        : null,
			districts           : ko.observableArray([]),
			error               : false,
			ignoreTmpls         : {
				content: true
			},
			isDistrictAvailable : ko.observable(false),
			loading             : false,
			selectedCity        : ko.observable(),
			selectedDistrict    : ko.observable(),
			showSpinner         : false,
			visible             : true,
			formattedAddress    : null
		},
     initMap: function () {
			var self = this;
      $('#map').show();
			// $("[name='country_id']").prop('selectedIndex',0);
			// $("[name='region_id']").prop('selectedIndex',0);
			// $('[name="custom_attributes[district_id]"]').prop('selectedIndex',0);
      var geocoder = new google.maps.Geocoder();
      var map = new google.maps.Map(document.getElementById('map'), {
        center: {lat: -34.397, lng: 150.644},
        zoom: 15
      });
      var infoWindow = new google.maps.InfoWindow;

      // Try HTML5 geolocation.
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };

          //infoWindow.setPosition(pos);
          //infoWindow.setContent('Click on the map to change your location');
          //infoWindow.open(map);
          var marker = new google.maps.Marker({ //on créé le marqueur
                      position: pos,
                      map: map,
                      title: "Drag the map to change your location"
                  });
          marker.setPosition(pos);
          map.setCenter(pos);
          getAddress(pos);
          // document.getElementById('lat').value=position.coords.latitude;
          // document.getElementById('lng').value=position.coords.longitude;

          google.maps.event.addListener(
            map,
            'drag',
            function () {
              marker.setPosition( map .getCenter() );
              // $('#delivery_map_latlng').html("Lat: " + map.getCenter().lat() + ' , Lng: ' + map.getCenter().lng());
              // $('#delivery_map_latlng').show();
            }
          );

          google.maps.event.addListener(map, 'dragend', function(event) {
              // placeMarker(event.latLng);
              marker.setPosition(map.getCenter());
              var latlng = {lat: this.getCenter().lat(), lng: this.getCenter().lng()};
              getAddress(latlng);
          });


          function placeMarker(location) {
              if(marker){ //on vérifie si le marqueur existe
                  marker.setPosition(location); //on change sa position
              }else{
                  marker = new google.maps.Marker({ //on créé le marqueur
                      position: location,
                      map: map
                  });
              }
              // document.getElementById('lat').value=location.lat();
              // document.getElementById('lng').value=location.lng();
              getAddress(location);
          }
          function getAddress(latLng) {
              geocoder.geocode( {'latLng': latLng},
              function(results, status) {
                  if(status == google.maps.GeocoderStatus.OK) {
                  if(results[0]) {
											var city , district, country;
											for (var i=0; i<results[0].address_components.length; i++)
					            {
													if (results[0].address_components[i].types[0] == "country") {
															country = results[0].address_components[i];
													}
													if (results[0].address_components[i].types[0] == "political") {
			                        district = results[0].address_components[i];
			                    }
					                if (results[0].address_components[i].types[0] == "locality") {
			                        city = results[0].address_components[i];
			                    }
					            }

											console.log(country);
											console.log(city);
											console.log(district);

											$("[name='country_id'] option").each(function() {
												if(country) {
												  if($(this).val() == country.short_name || $(this).text() == country.long_name) {
														$(this).prop("selected", true).change();
												  }
												}
											});

											if($("[name='region_id'] option").find('option').length < 1){
												$("[name='region']").val(city.long_name);
												$("[name='region']").trigger('keyup');
											}
											$("[name='region_id'] option").each(function() {
												if(city) {
												  if($(this).text() == city.long_name) {
												    $(this).prop("selected", true).change();
												  }else{
														$("[name='region']").val(city.long_name);
														$("[name='region']").trigger('keyup');
													}
												}
											});

											if($("[name='city-select'] option").find('option').length < 1){
												$("[name='city']").val(district.long_name);
												$("[name='city']").trigger('keyup');
											}
											$("[name='city-select'] option").each(function() {
												if(district) {
												  if($(this).text() == district.long_name) {
												    $(this).prop("selected", true).change();
												  }else{
														$("[name='city']").val(district.long_name);
														$("[name='city']").trigger('keyup');
													}
												}
											});

                      $(document).find("div[name = 'shippingAddress.street.0'] input[name = 'street[0]']").val(results[0].formatted_address);
											$(document).find("div[name = 'shippingAddress.street.0'] input[name = 'street[0]']").trigger('keyup');

											// if($('[name="custom_attributes[district_id]"]').find('option').length < 1){
													// $('[name="custom_attributes[district_id]"]').hide();
													// $("[name='shippingAddress.custom_attributes.district_id']").hide();
													// $("[name='shippingAddress.custom_attributes.district']").show();
													// $('[name="custom_attributes[district]"]').show();
													// $('[name="custom_attributes[district]"]').val(district.long_name);
													// $('[name="custom_attributes[district]"]').trigger('keyup');
											// }

											// $("[name='custom_attributes[district_id]'] option").each(function() {
											// if(district) {
											// 	  if($(this).text() == district.long_name) {
											// 	    $(this).attr('selected', 'selected');
											// 			$('[name="custom_attributes[district_id]"]').show();
											// 			$("[name='shippingAddress.custom_attributes.district_id']").show();
											// 			$("[name='shippingAddress.custom_attributes.district']").hide();
											// 			$('[name="custom_attributes[district]"]').hide();
											// 	  }else{
											// 			$('[name="custom_attributes[district_id]"]').hide();
											// 			$("[name='shippingAddress.custom_attributes.district_id']").hide();
											// 			$("[name='shippingAddress.custom_attributes.district']").show();
											// 			$('[name="custom_attributes[district]"]').show();
											// 			$('[name="custom_attributes[district]"]').val(district.long_name);
											// 			$('[name="custom_attributes[district]"]').trigger('keyup');
											// 		}
											// 	}
											// });


											window.street
                  }else {
                  alert("No results");
                  }
              }
              else {
                  alert(status);
              }
          });
      }
        }, function() {
          self.handleLocationError(true, infoWindow, map.getCenter());
        });
      } else {
        // Browser doesn't support Geolocation
        self.handleLocationError(false, infoWindow, map.getCenter());
      }
    },
    handleLocationError: function (browserHasGeolocation, infoWindow, pos) {
      infoWindow.setPosition(pos);
      infoWindow.setContent(browserHasGeolocation ?
                            'Error: The Geolocation service failed.' :
                            'Error: Your browser doesn\'t support geolocation.');
      infoWindow.open(map);
    }
	});
});
