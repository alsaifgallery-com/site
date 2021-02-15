var $j = '';
var $tr = '';
var inp = '';
require(
    [
        'jquery',
        'mage/translate'
    ],
    function ($, $t) {
        $j = $;
        $tr = $t;
        $j(document).ready(function () {
            /** Check if extension is enabled or not */
            if(window.is_city_enabled == 1){
                /** Check if state is enabled or exists on website then load regions */
                /** If load cities is based on region **/
                $j(document).on("change", "[name='region_id']", function (e) {
                    var region_id = $j(this).val();
                    var main_id = 'edit';
                    if(window.current_page!="edit"){
                        main_id = $j(this).parent().parent().parent().attr('id');
                    }
                    if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id!=""){
                        if(window.current_page!="edit") {
                            var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCities(region_id,main_id);
                        }else{
                            var city_id = $j('#city').attr('id');
                            inp = document.getElementById(city_id);
                            getRegionCitiesAddress(region_id,main_id);
                        }

                    }
                });
                if(window.current_page!="edit") {
                    setTimeout(reloadCities(),10000);
                    $j(document).on("change", "[name='country_id']", function (e) {
                        var main_id = $j(this).parent().parent().parent().attr('id');
                        if(window.is_state_available == 1){
                            $j('#region_id').removeAttr('disabled');
                            var region_id = $j('#' + main_id).find("[name='region_id']").val();
                            if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id != "") {
                                var city_id = $j('#' + main_id + ' [name="city"]').attr('id');
                                inp = document.getElementById(city_id);
                                getRegionCities(region_id, main_id);
                            } else {
                                resetForms(main_id);
                            }
                        }else{
                            resetForms(main_id);
                            if($(this).val()!=""){
                                getRegionCities('',main_id);
                            }
                        }
                    });
                }else{
                    setTimeout(reloadCities(),10000);
                    $j(document).on("change", "[name='country_id']", function (e) {
                        var main_id = 'edit';
                        if(window.is_state_available == 1){
                            $j('#region_id').removeAttr('disabled');
                            var region_id = $j('#region_id').val();
                            if (typeof(region_id) != 'undefined' && region_id != null && region_id != '' && main_id != "") {
                                var city_id = $j('#city').attr('id');
                                inp = document.getElementById(city_id);
                                getRegionCitiesAddress(region_id, main_id);
                            } else {
                                resetForms(main_id);
                            }
                        }else{
                            resetForms(main_id);
                            if($(this).val()!=""){
                                getRegionCitiesAddress('', main_id);
                            }
                        }
                    });
                }
            }

        });
    });
var reloadCities = function () {
    var country_id = $j("[name='country_id']").val();
    if (typeof(country_id) != 'undefined' && country_id != null && country_id != "") {
        var main_id = $j("[name='country_id']").parent().parent().parent().attr('id');
        var city_id = $j("[name='city']").attr('id');
        inp = document.getElementById(city_id);
        if(window.is_state_available == 1){
            if($j("[name='region_id']").length > 0 && $j("[name='region_id']").val() != ""){
                if(window.current_page!="edit") {
                    getRegionCities($j("[name='region_id']").val(), main_id);
                }else{
                    getRegionCitiesAddress($j("[name='region_id']").val(),main_id);
                }
            }
        }else{
            if(window.current_page!="edit") {
                getRegionCities('', main_id);
            }else{
                getRegionCitiesAddress('',main_id);
            }
        }
    }else{
        setTimeout(reloadCities,2000);
    }
}
var resetForms = function (main_id) {
    if(window.current_page!="edit") {
        var city_id =  $j('#'+main_id+' [name="city"]').attr('id');
        $j('#'+city_id+'-select').remove();
        $j('#'+main_id+' [name="city"]').show();
        $j('#'+main_id+' [name="city"]').val('');
        var postcode_id =  $j('#'+main_id+' [name="postcode"]').attr('id');
        $j('#'+postcode_id+'-select').remove();
        $j('#'+main_id+' .postcode_billing_notinlist').remove();
        $j('#'+main_id+' .postcode_br_billing_notinlist').remove();
        $j('#'+main_id+' [name="postcode"]').show();
        $j('#'+main_id+' [name="postcode"]').val('');
        $j('#'+main_id+' .billing_notinlist').remove();
        $j('#zip-error').remove();
        $j('#city-select-error').remove();
    }else{
        $j('#city-select').remove();
        $j('#city').show();
        $j('#city').val('');
        $j('#zip-select').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#zip').show();
        $j('#zip').val('');
        $j('.billing_notinlist').remove();
        $j('#city-select-error').remove();
    }
}
/** Get cities dropdown */
var ajaxLoading = false;
var getRegionCities = function(region_value,main_id) {
    var country = $j('#'+main_id).find('[name="country_id"]').val();
    if(!ajaxLoading && typeof(country) != 'undefined'  && country != '') {
        ajaxLoading = true;
        var city_id =  $j('#'+main_id+' [name="city"]').attr('id');
        var url = window.data_city_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="'+$tr('Loading')+'..." style="position: absolute;text-align:center;"></div>'+$tr('Loading')+'...</div>';
        if($j('#'+main_id+' .city_loading_mask').length==0){
            $j('#'+main_id+' [name="city"]').after(loader);
        }
        var city = $j('#'+main_id+' [name="city"]').val();
        emptyInput('',main_id);
        $j('#error-'+city_id).hide();
        $j('.mage-error').hide();
        $j('#'+main_id+' [name="city"]').hide();
        $j('#'+city_id+'-select').remove();
        $j('#'+main_id+' .billing_notinlist').remove();
        $j('#'+main_id+' .br_billing_notinlist').remove();
        $j('#'+main_id+' .postcode_billing_notinlist').remove();
        $j('#'+main_id+' .postcode_br_billing_notinlist').remove();
        $j('#'+main_id+' [name="zip-select"]').remove();
        $j('#'+main_id+' [name="postcode"]').show();
        $j.ajax({
            url : url,
            type: "get",
            data:"state="+region_value+'&country_id='+country,
            dataType: 'json',
            timeout:15000
        }).done(function (response) {
            ajaxLoading = false;
            $j('#error-'+city_id).show();
            $j('.mage-error').show();
            $j('#'+main_id+' .city_loading_mask').remove();
            $j('#'+main_id+' [name="city"]').show();
            var options = '<select onchange="getCityState(this.value,\''+main_id+'\'),getZipcodes(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="select" title="'+$tr('City')+'" name="city-select" ><option value="">'+$tr('Please select city')+'</option>';
            var cities = response.cities;
            var cities_indexes = response.cities_indexes;
            if (cities.length > 0) {
                for (var i = 0; i < cities.length; i++) {
                    var selected = '';
                    if(city.toLowerCase()==cities[i].toLowerCase()){
                        selected = "selected='selected'";
                    }
                    options += '<option '+selected+' value="' + cities_indexes[i] + '">' + cities[i] + '</option>';
                }
                options += "</select>";
                if(window.data_city_link!=0){
                    var title = $tr(window.data_city_title);
                    options+= "<br class='br_billing_notinlist' /><a onclick='cityNotInList(\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                $j('#'+main_id+' [name="city"]').hide();
                if($j('#'+city_id+'-select').length==0){
                    $j('#'+main_id+' [name="city"]').after(options);
                }
            } else {
                $j('#'+main_id+' [name="city"]').html(inp);
                $j('#'+main_id+' .billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            $j('#error-'+city_id).show();
            $j('#'+main_id+' .city_loading_mask').remove();
            $j('#'+main_id+' [name="city"]').show();
        });
    }
}


var emptyInput = function(val,main_id){
    if(window.current_page=='edit'){

        $j('#city').focus();
        $j('#city').val(val);
        if(val!=""){
            $j('#zip-error').remove();
            $j('#city-select-error').remove();
            $j('#city-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#city').trigger(e);
    }else{

        $j('#'+main_id+' [name="city"]').focus();
        $j('#'+main_id+' [name="city"]').val(val);
        if(val!=""){
            $j('#city-select-error').remove();
            $j('#'+main_id+' [name="city"]').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#'+main_id+' [name="city"]').trigger(e);
    }
}
/** Get cities on address page */
var getRegionCitiesAddress = function (value,main_id) {
    var main_id = 'edit';
    if(!ajaxLoading) {
        ajaxLoading = true;
        var city_id =  "city";
        var url = window.data_city_url;
        var loader = '<div data-role="loader" class="loading-mask city_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="'+$tr('Loading')+'..." style="position: absolute;text-align:center;"></div>'+$tr('Loading')+'...</div>';
        if($j('.city_loading_mask').length==0){
            $j('#city').after(loader);
        }
        var city = $j('#city').val();
        emptyInput('',main_id);
        $j('#error-'+city_id).hide();
        $j('#city-select-error').remove();
        $j('.mage-error').hide();
        $j('#city').hide();
        $j('#'+city_id+'-select').remove();
        $j('.billing_notinlist').remove();
        $j('.br_billing_notinlist').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#zip-select,#zip-select-error,#zip-error').remove();
        $j('#zip').removeClass('mage-error');
        $j('#zip').show();
        $j.ajax({
            url : url,
            type: "get",
            data:"state="+value+'&country_id='+$j('#country').val(),
            dataType: 'json',
            timeout:15000
        }).done(function (response) {
            ajaxLoading = false;
            $j('#error-'+city_id).show();
            $j('.mage-error').show();
            $j('.city_loading_mask').remove();
            $j('#city').show();
            var cities = response.cities;
            var cities_indexes = response.cities_indexes;
            var options = '<select onchange="getCityState(this.value,\''+main_id+'\'),getZipcodes(this.value,\''+main_id+'\')" id="'+city_id+'-select" class="validate-select select" title="'+$tr('City')+'" name="city-select" ><option value="">'+$tr('Please select city')+'</option>';
            var loadZipCodes = false;
            if (cities.length > 0) {
                for (var i = 0; i < cities.length; i++) {
                    var selected = '';
                    if(city.toLowerCase()==cities[i].toLowerCase()){
                        selected = "selected='selected'";
                        loadZipCodes = true;
                    }
                    options += '<option '+selected+' value="' + cities_indexes[i] + '">' + cities[i] + '</option>';
                }
                options += "</select>";
                if(window.data_city_link!=0){
                    var title = window.data_city_title;
                    options+= "<br class='br_billing_notinlist' /><a onclick='cityNotInList(\""+main_id+"\")' class='billing_notinlist' href='javascript:void(0)' class='notinlist'>"+title+"</a>";
                }
                $j('#city').hide();
                if($j('#'+city_id+'-select').length==0){
                    $j('#city').after(options);
                }
                if(loadZipCodes){
                    getZipcodes(city,main_id);
                }
            } else {
                $j('#city').html(inp);
                $j('.billing_notinlist').remove();
            }
        }).fail( function ( error )
        {
            ajaxLoading = false;
            $j('#error-'+city_id).show();
            $j('.city_loading_mask').remove();
            $j('#city').show();
        });
    }
}
/* Get Zip codes */
var getPostcodes = function(value,main_id) {
    var postcode_id =  $j('#'+main_id+' [name="postcode"]').attr('id');
    inp = document.getElementById(postcode_id);
    var url = window.data_zip_url;
    var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="'+$tr('Loading')+'..." style="position: absolute;text-align:center;"></div>'+$tr('Loading')+'...</div>';
    if($j('#'+main_id+' .postcode_loading_mask').length==0){
        $j('#'+main_id+' [name="postcode"]').after(loader);
    }
    emptyInputZip('',main_id);
    $j('#error-'+postcode_id).hide();
    $j('.mage-error').hide();
    $j('#'+main_id+' [name="postcode"]').hide();
    $j('#'+postcode_id+'-select').remove();
    $j('#'+main_id+' .postcode_billing_notinlist').remove();
    $j('#'+main_id+' .postcode_br_billing_notinlist').remove();
    $j.ajax({
        url : url,
        type: "get",
        data:"city="+value+'&state='+$j('#'+main_id+' [name="region_id"]').val()+'&country_id='+$j('#'+main_id+' [name="country_id"]').val(),
        dataType: 'json',
        timeout:15000
    }).done(function (response) {
        $j('#error-'+postcode_id).show();
        $j('.mage-error').show();
        $j('#'+main_id+' .postcode_loading_mask').remove();
        $j('#'+main_id+' [name="postcode"]').show();
        var options = '<select onchange="getZipState(this.value,\''+main_id+'\')" id="'+postcode_id+'-select" class="validate-select select" title="'+$tr('Postcode')+'" name="zip-select" ><option value="">'+$tr('Please select zip code')+'</option>';
        if (response.length > 0) {
            for (var i = 0; i < response.length; i++) {
                options += '<option value="' + response[i] + '">' + response[i] + '</option>';
            }
            options += "</select>";
            if(window.data_zip_link!=0){
                var title = $tr(window.data_zip_title);
                options+= "<br class='postcode_br_billing_notinlist' /><a onclick='notInListZip(\""+main_id+"\")' class='postcode_billing_notinlist' href='javascript:void(0)' class='postcode_notinlist'>"+title+"</a>";
            }
            $j('#'+main_id+' [name="postcode"]').hide();
            if($j('#'+postcode_id+'-select').length==0){
                $j('#'+main_id+' [name="postcode"]').after(options);
            }
        } else {
            $j('#'+main_id+' [name="postcode"]').html(inp);
            $j('#'+main_id+' .postcode_billing_notinlist').remove();
        }
    }).fail( function ( error )
    {
        $j('#error-'+postcode_id).show();
        $j('#'+main_id+' .postcode_loading_mask').remove();
        $j('#'+main_id+' [name="postcode"]').show();
    });
}
var getPostcodesForAddress = function (value,main_id) {
    var postcode_id =  $j('#zip').attr('id');
    var zipCode = $j('#zip').val();
    inp = document.getElementById(postcode_id);
    var url = window.data_zip_url;
    var loader = '<div data-role="loader" class="loading-mask postcode_loading_mask" style="position: relative;text-align:right;"><div class="loader"><img src="'+window.loading_url+'" alt="'+$tr('Loading')+'..." style="position: absolute;text-align:center;"></div>'+$tr('Loading')+'...</div>';
    if($j('.postcode_loading_mask').length==0){
        $j('#zip').after(loader);
    }
    emptyInputZip('',main_id);
    $j('#error-'+postcode_id).hide();
    $j('.mage-error').hide();
    $j('#zip').hide();
    $j('#zip-select-error').remove();
    $j('#zip-select').remove();
    $j('.postcode_billing_notinlist').remove();
    $j('.postcode_br_billing_notinlist').remove();
    var state = '';
    if($j("[name='region_id']").css('display')!="none"){
        state = $j('#region_id').val();
    }
    $j.ajax({
        url : url,
        type: "get",
        data:"city="+value+'&state='+state+'&country_id='+$j('#country').val(),
        dataType: 'json',
        timeout:15000
    }).done(function (response) {
        $j('#error-'+postcode_id).show();
        $j('.mage-error').show();
        $j('.postcode_loading_mask').remove();
        $j('#zip').show();
        var options = '<select onchange="getZipState(this.value,\''+main_id+'\')" id="'+postcode_id+'-select" class="validate-select select" title="'+$tr('Postcode')+'" name="zip-select" ><option value="">'+$tr('Please select zip code')+'</option>';
        if (response.length > 0) {
            for (var i = 0; i < response.length; i++) {
                var selected = '';
                if(zipCode==response[i]){
                    selected = 'selected="selected"';
                }
                options += '<option '+selected+' value="' + response[i] + '">' + response[i] + '</option>';
            }
            options += "</select>";
            if(window.data_zip_link!=0){
                var title = window.data_zip_title;
                options+= "<br class='postcode_br_billing_notinlist' /><a onclick='notInListZip(\""+main_id+"\")' class='postcode_billing_notinlist' href='javascript:void(0)' class='postcode_notinlist'>"+title+"</a>";
            }
            $j('#zip').hide();
            if($j('#zip-select').length==0){
                $j('#zip').after(options);
            }
        } else {
            $j('#zip').html(inp);
            $j('.postcode_billing_notinlist').remove();
        }
    }).fail( function ( error )
    {
        $j('#error-'+postcode_id).show();
        $j('.postcode_loading_mask').remove();
        $j('#zip').show();
    });
}

var getZipcodes = function (value,type){
    if(window.current_page!='edit'){
        if (value != '' && $j('#'+type+' [name="city-select"]').length > 0 &&  $j('#'+type+' [name="city-select"]').is('select')) {
            getPostcodes(value,type);
        }
    }else{
        if (value != '' && $j('#city-select').length > 0 &&  $j('#city-select').is('select')) {
            getPostcodesForAddress(value,type);
        }
    }
}
/* Zip not in list */
var notInListZip = function (main_id){
    if(window.current_page=='edit'){
        var postcode_id =  "postcode";
        $j('#'+postcode_id+'-select').remove();
        $j('.postcode_billing_notinlist').remove();
        $j('.postcode_br_billing_notinlist').remove();
        $j('#postcode').show();
    }else{
        var postcode_id =  $j('#'+main_id+' [name="postcode"]').attr('id');
        $j('#'+postcode_id+'-select').remove();
        $j('#'+main_id+' .postcode_billing_notinlist').remove();
        $j('#'+main_id+' .postcode_br_billing_notinlist').remove();
        $j('#'+main_id+' [name="postcode"]').show();
    }
}
var cityNotInList = function(main_id){
    if(window.current_page=='edit'){
        var city_id =  "city";
        $j('#'+city_id+'-select').remove();
        $j('.billing_notinlist').remove();
        $j('.br_billing_notinlist').remove();
        $j('#city').show();
    }else{
        var city_id =  $j('#'+main_id+' [name="city"]').attr('id');
        $j('#'+city_id+'-select').remove();
        $j('#'+main_id+' .billing_notinlist').remove();
        $j('#'+main_id+' .br_billing_notinlist').remove();
        $j('#'+main_id+' [name="city"]').show();
    }
}
var emptyInput = function(val,main_id){
    if(window.current_page=='edit'){
        $j('#city').focus();
        $j('#city').val(val);
        if(val!=""){
            $j('#zip-error').remove();
            $j('#city-select-error').remove();
            $j('#city-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#city').trigger(e);

    }else{
        $j('#'+main_id).find('[name="city"]').focus();
        $j('#'+main_id).find('[name="city"]').val(val);
        if(val!=""){
            $j('#city-select-error').remove();
            $j('#'+main_id).find('[name="city"]').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#'+main_id).find('[name="city"]').trigger(e)
    }
}
var emptyInputZip = function (val,main_id){
    if(window.current_page=='edit'){
        $j('#zip').focus();
        $j('#zip').val(val);
        if(val!=""){
            $j('#zip-select-error').remove();
            $j('#zip-error').remove();
            $j('#zip-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#zip').trigger(e);
    }else{
        $j('#'+main_id+' [name="postcode"]').focus();
        $j('#'+main_id+' [name="postcode"]').val(val);
        if(val!=""){
            $j('#zip-select-error').remove();
            $j('#'+main_id+' [name="postcode"]').removeClass('mage-error');
            $j('#zip-select').removeClass('mage-error');
        }
        var e = $j.Event('keyup');
        e.keyCode= 13; // enter
        $j('#'+main_id+' [name="postcode"]').trigger(e);
    }
}
var getCityState = function(val,main_id){
    emptyInput(val,main_id);
}
var getZipState = function(val,main_id){
    emptyInputZip(val,main_id);
}
