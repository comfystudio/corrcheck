( function( $ ) {

	// Hide all report sections by default
	$(".corr-report .rep-section").hide();	
	$(".extra-emails").hide();	

	// Show first section of survey
	$(".veh_dets").show();

	// Initialise the datepicker | hide when date clicked
	var surveyDate = $('.datepicker').datepicker().on('changeDate', function(ev) {
	  surveyDate.hide();
	}).data('datepicker');


    $('[data-toggle="tooltip"]').tooltip({html: true});

	/* Show / Hide extra emails on company page */
	
	
	$(".email-ctrl").click(function(e){
		e.preventDefault();
		$(".extra-emails").toggle();
	});

    /** ========================================
     * Vehicle-dashboard Back and Forward functionality
     * ======================================== */

    $( "#dashboard-back" ).click(function() {
        $.ajax({
            url: '_ajax-vehicle-dashboard.php',
            type: 'POST',
            data: $('#vehicle-dashboard-form').serialize()+'&direction=back',
            success: function(data){
                $('#ajax-dashboard').remove();
                $('#ajax-parent').append(data);
                dashboardBack();
                dashboardForward();
                $('[data-toggle="tooltip"]').tooltip({html: true});
            },
            error: function(){
                alert('failure');
            }
        });
    });

    $( "#dashboard-forward" ).click(function() {
        $.ajax({
            url: '_ajax-vehicle-dashboard.php',
            type: 'POST',
            data: $('#vehicle-dashboard-form').serialize()+'&direction=forward',
            success: function(data){
                $('#ajax-dashboard').remove();
                $('#ajax-parent').append(data);
                dashboardBack();
                dashboardForward();
                $('[data-toggle="tooltip"]').tooltip({html: true});
            },
            error: function(){
                alert('failure');
            }
        });
    });

    function dashboardBack(){
        $( "#dashboard-back" ).click(function() {
            $.ajax({
                url: '_ajax-vehicle-dashboard.php',
                type: 'POST',
                data: $('#vehicle-dashboard-form').serialize()+'&direction=back',
                success: function(data){
                    $('#ajax-dashboard').remove();
                    $('#ajax-parent').append(data);
                    dashboardBack();
                    dashboardForward();
                },
                error: function(){
                    alert('failure');
                }
            });
        });
    }

    function dashboardForward(){
        $( "#dashboard-forward" ).click(function() {
            $.ajax({
                url: '_ajax-vehicle-dashboard.php',
                type: 'POST',
                data: $('#vehicle-dashboard-form').serialize()+'&direction=forward',
                success: function(data){
                    $('#ajax-dashboard').remove();
                    $('#ajax-parent').append(data);
                    dashboardBack();
                    dashboardForward();
                },
                error: function(){
                    alert('failure');
                }
            });
        });
    }


    /** ========================================
     * user-management.php Deals with ajax post when user changes vehicle or dashboard permissions
     * ======================================== */

    $( ".toggle-vehicle-permission" ).click(function() {
        var state = $(this).data("state");
        var id = $(this).data("id");
        if(state == 1){
            $(this).data("state", 2);
            $(this).find($(".fa")).removeClass('fa-check').addClass('fa-times');
            $(this).removeClass('btn-success').addClass('btn-danger');
            state = 2;
        }else{
            $(this).data("state", 1);
            $(this).find($(".fa")).removeClass('fa-times').addClass('fa-check');
            $(this).removeClass('btn-danger').addClass('btn-success');
            state = 1;
        }
        $.ajax({
            url: '_ajax-toggle-vehicle-permission.php',
            type: 'POST',
            data: { state: state, id: id},
            success: function(){

            },
            error: function(){
                alert('failure');
            }
        });
    });

    $( ".toggle-dashboard-permission" ).click(function() {
        var state = $(this).data("state");
        var id = $(this).data("id");
        if(state == 1){
            $(this).data("state", 2);
            $(this).find($(".fa")).removeClass('fa-check').addClass('fa-times');
            $(this).removeClass('btn-success').addClass('btn-danger');
            state = 2;
        }else{
            $(this).data("state", 1);
            $(this).find($(".fa")).removeClass('fa-times').addClass('fa-check');
            $(this).removeClass('btn-danger').addClass('btn-success');
            state = 1;
        }
        $.ajax({
            url: '_ajax-toggle-dashboard-permission.php',
            type: 'POST',
            data: { state: state, id: id},
            success: function(){

            },
            error: function(){
                alert('failure');
            }
        });
    });

    $( ".toggle-user-permission" ).click(function() {
        var state = $(this).data("state");
        var id = $(this).data("id");
        if(state == 1){
            $(this).data("state", 2);
            $(this).find($(".fa")).removeClass('fa-check').addClass('fa-times');
            $(this).removeClass('btn-success').addClass('btn-danger');
            state = 2;
        }else{
            $(this).data("state", 1);
            $(this).find($(".fa")).removeClass('fa-times').addClass('fa-check');
            $(this).removeClass('btn-danger').addClass('btn-success');
            state = 1;
        }
        $.ajax({
            url: '_ajax-toggle-user-permission.php',
            type: 'POST',
            data: { state: state, id: id},
            success: function(){

            },
            error: function(){
                alert('failure');
            }
        });
    });

    /** ======================================================================================
     * create-report.php / edit-report.php. show / hide psv and comments based on selection
     * ====================================================================================== */
    $( document ).ready(function() {
        if($('#veh_dets_175').length) {
            $("label[for='veh_dets_175']").first().html('Scheduled');
            if ($('#veh_dets_174').prop('checked') == false) {
                $('#veh_dets_175').parent().parent().parent().hide();
                $('#veh_dets_176').parent().parent().hide();
                $('#veh_dets_177').parent().parent().hide();
                $('#veh_dets_175').attr('checked', false);
                $('.app-col-main').animate({
                    backgroundColor: "#e7e7e8"
                }, 1000);
                $('#section-nav').animate({
                    backgroundColor: "#242424"
                }, 1000);
            }
            $('#veh_dets_174').change(function () {
                if ($('#veh_dets_174').prop('checked') == false) {
                    $('#veh_dets_175').parent().parent().parent().hide();
                    $('#veh_dets_176').parent().parent().hide();
                    $('#veh_dets_177').parent().parent().hide();
                    $('#veh_dets_175').attr('checked', false);
                    $('.app-col-main').animate({
                        backgroundColor: "#e7e7e8"
                    }, 1000);
                    $('#section-nav').animate({
                        backgroundColor: "#242424"
                    }, 1000);
                } else {
                    $('#veh_dets_175').parent().parent().parent().show();
                }
            })


            //Set background and style to some sort of warning if PSV
            if ($('#veh_dets_175').prop('checked') == true) {
                $('.app-col-main').animate({
                    backgroundColor: "#ff0000"
                }, 1000);
                $('#section-nav').animate({
                    backgroundColor: "#ff0000"
                }, 1000);
            }else{
                $('#veh_dets_176').parent().parent().hide();
                $('#veh_dets_177').parent().parent().hide();
            }

            $('#veh_dets_175').change(function () {
                if ($('#veh_dets_175').prop('checked') == false) {
                    $('.app-col-main').animate({
                        backgroundColor: "#e7e7e8"
                    }, 1000);
                    $('#section-nav').animate({
                        backgroundColor: "#242424"
                    }, 1000);
                    $('#veh_dets_176').parent().parent().hide();
                    $('#veh_dets_177').parent().parent().hide();

                } else {
                    $('.app-col-main').animate({
                        backgroundColor: "#ff0000"
                    }, 1000);
                    $('#section-nav').animate({
                        backgroundColor: "#ff0000"
                    }, 1000);

                    $('#veh_dets_176').parent().parent().show();
                    $('#veh_dets_177').parent().parent().show();
                }
            })
        }

        // ajax the create / edit inspection company selection, so it updates the vehicle regs
        $('#veh_dets_13').change(function (){
            var id = $(this).val();
            $.ajax({
                url: '_ajax-get-reg-with-companyid.php',
                type: 'POST',
                data: { id: id},
                success: function(response){
                    //console.log(response);

                    $('#veh_dets_12').empty();
                    $('#veh_dets_12').append(response);
                    $('.selectpicker').selectpicker('refresh');
                },
                error: function(){
                    alert('failure');
                }
            });
        });

        //When user selects vehicle reg auto update type and make
        $('.selectpicker').change(function (){
            var make = $(this).find("option:selected").data("make");
            var type = $(this).find("option:selected").data("type");
            $('#veh_dets_11').val(type);
            $('#veh_dets_14').val(make);
        })


    });


	// ===================================== //
	// =========== FORM VALIDATION ========= //
	// ===================================== //
	
	// Initialise jQuery Validate - main vehicle survey
	$(".corrCheck_form").validate(
        {ignore: "#veh_dets_178"}
    );

    $('.bs-searchbox').find('input').attr("id", "veh_dets_12");

	// Only allow show/hide panels if all required fields in panel 1 are complete
	$("#section-nav").on('click', 'a', function () {
        var error_count = -1;

        // Go through each input in the first panel
		$(".veh_dets input").each(function(){
            var thisVal = $(this).val();
            // go through each input field
            // - Seems we need to add exceptions for inputs we don't want validated here and in the above validate.
            if (thisVal == "" && $(this).attr('id') != 'veh_dets_178') {
                // If a field is empty increment the counter
                //
                error_count++;


                // The problem is that when all but one field are filled in and if there
                // was at least one error that was fixed then this is skipped and
                // does not validate that last field ** ...
            }


		}); // end each()	
		if(error_count >= 1){
            //alert("error count is greater than or equal to 1 | error count is "+error_count);
			validate_veh_dets();

		}else{
            //alert("At the else clause");
			// ... ** we end up here
			// do one last validation of everything
			validate_veh_dets();
			
			// Assuming everything is now ok, show/hide panels based on button clicked
			$(".rep-section").hide();		

		    var thisSection = $(this).attr("data");
		    var thisSectionString = "."+$(this).attr("data");

		    $(thisSectionString).show();
		}


	});	// end click function

	// Validate each input in the vehicle details section
	function validate_veh_dets(){
		$( ".veh_dets input" ).each(function() {
            if (typeof $(this).attr('id') !== typeof undefined && $(this).attr('id') !== false) {
                var thisID = $(this).attr('id');
                $("#"+thisID).valid();
            }
		});
	}

	// Validate form-create-user
	//$(".form-create-user").validate();
	

	/** ======================================== 
	 * Show/Hide panels | Survey Navigation
	 * NOTE: This no longer used - code is 
	 * contained within form validation section
	 * ======================================== */
	function navigate_panels(){
		// Show section based on navigation button clicked
		$("#section-nav").on('click', 'a', function () {

			$(".rep-section").hide();		

		    var thisSection = $(this).attr("data");
		    var thisSectionString = "."+$(this).attr("data");

		    $(thisSectionString).show();

		});

	} // end navigate_panels	


	/** ======================================== 
	 * Hide/Show questions based on vehicle type choice	 
	 * ======================================== */
	
	$("#veh_dets_11").change(function() {
		var veh_type = $(this).val();
		
		// If trailer
		if(veh_type == "trailer"){			
			$(".not_trailers").hide("fast");
		}

		// If lorry (show all)
		if(veh_type == "lorry"){			
			$(".not_trailers").show("fast");
		}
		
	});


	/** ======================================== 
	 * Hide/Show based on choices
	 * Section 4 only
	 * ======================================== */
	
	$( ".section_lub select" ).change(function() {

		var thisVal = $(this).val();		

		if(thisVal == 'not-ok'){
			$(this).parent().parent().find(".group_details").show("fast");
			$(this).addClass("");
		}

		if(thisVal == 'ok'){
			$(this).parent().parent().find(".group_details").hide("fast");
		}		
	  
	});


	/** ======================================== 
	 * Hide/Show based on choices
	 * Standard sections
	 * ======================================== */	
	
	$(".section_std select.display-control").change(function() {

		var thisVal = $(this).val();

		if(thisVal == "significant_defect" || thisVal == "slight_defect"){

			var thisName = $(this).attr("name");				// question name
			var dets_to_show = "." + thisName + "_details";		// details class name to show/hide
			var rect_by_to_show = "." + thisName + "_rect_by";	// rectifed by class name to show/hide
		
			$(dets_to_show).show("fast");		// show details form group
			$(rect_by_to_show).show();	// show rectified by div

		}

		if(thisVal == "satisfactory" || thisVal == "not_applicable"){

			var thisName = $(this).attr("name");				// question name
			var dets_to_show = "." + thisName + "_details";		// details class name to show/hide
			var rect_by_to_show = "." + thisName + "_rect_by";	// rectifed by class name to show/hide
		
			$(dets_to_show).hide("fast");		// show details form group
			$(rect_by_to_show).hide();	// show rectified by div

		}

	});

	/** ======================================== 
	 * Colour code the responses
	 * Class: has-error - red
	 * Class: has-warning - orange
	 * Class: has-success - green
	 * ======================================== */
	
	// ===================================== //
	// ======= standard questions ========== //
	// ===================================== //
	
	// Add has-success by default
	$(".section_std select.display-control").closest(".form-group").addClass("has-success");
	
	
	// Process on status change
	$(".section_std select.display-control").change(function() {

		var thisVal = $(this).val(); 					// Get the value
		var thisName = $(this).attr("name");			// Question name
		var dets_to_show = "." + thisName + "_details";	// Details class name to add/remove classes

		// If significant defect - RED
		if(thisVal == "significant_defect"){

			// Remove all potential colour classess			
			$(this).closest(".form-group").removeClass("has-warning has-success ");
			$(dets_to_show).removeClass("has-warning has-success ");

			
			// Add error class to the containing form-group
			$(this).closest(".form-group").addClass("has-error");
			$(dets_to_show).addClass("has-error");		// show details form group
		}

		// if slight defect - ORANGE
		if(thisVal == "slight_defect"){

			// Remove all potential colour classess			
			$(this).closest(".form-group").removeClass("has-error has-success ");
			$(dets_to_show).removeClass("has-error has-success ");
			
			// Add error class
			$(this).closest(".form-group").addClass("has-warning");
			$(dets_to_show).addClass("has-warning");
		}

		// if everything is awesome - GREEN
		if(thisVal == "satisfactory"){

			// Remove all potential colour classess			
			$(this).closest(".form-group").removeClass("has-error has-warning ");
			$(dets_to_show).removeClass("has-error has-warning ");
			
			// Add error class
			$(this).closest(".form-group").addClass("has-success");
			$(dets_to_show).addClass("has-success");
		}

		// if not applicable - none
		if(thisVal == "not_applicable"){

			// Remove all potential colour classess			
			$(this).closest(".form-group").removeClass("has-error has-warning has-success");
			$(dets_to_show).removeClass("has-error has-warning has-success");
		}
	});

	// ===================================== //
	// ======== section 4 questions ======== //
	// ===================================== //
	
	// Add has-success by default
	$( ".section_lub select" ).closest(".form-group").addClass("has-success");

	// Process on status change
	$(".section_lub select").change(function() {

		var thisVal = $(this).val(); // Get the value		

		if(thisVal == "ok"){
			$(this).closest(".form-group").removeClass("has-error");
			$(this).closest(".form-group").addClass("has-success");
		}

		if(thisVal == "not-ok"){
			$(this).closest(".form-group").removeClass("has-success");
			$(this).closest(".form-group").addClass("has-error");
		}

	});

	/*	
	 * Initialise the issues that require attention on edit report load: lube
	 */	
	$(".edit-report .section_lub option").each(function(){		

		// if this option is 'selected'
		if($(this).is(':selected'))
		{
			var thisVal = $(this).val(); // Get the value		

			if(thisVal == "not-ok"){
				$(this).closest(".form-group").removeClass("has-success");
				$(this).closest(".form-group").addClass("has-error");

				$(this).parent().parent().parent().find(".group_details").show("fast");
				$(this).addClass("");
			}				
		} // end first if

	}); // end .section_lub each

	$(".edit-report .section_std option").each(function(){		

		// if this option is 'selected'
		if($(this).is(':selected'))
		{

			var thisVal = $(this).val(); // Get the value
			var thisName = $(this).closest("select").attr("name");			// Question name
			var dets_to_show = "." + thisName + "_details";	// Details class name to add/remove classes
			var rect_by_to_show = "." + thisName + "_rect_by";	// rectifed by class name to show/hide			

			// If significant defect - RED
			if(thisVal == "significant_defect"){

				// Remove all potential colour classess			
				$(this).closest(".form-group").removeClass("has-warning has-success ");
				$(dets_to_show).removeClass("has-warning has-success ");

				
				// Add error class to the containing form-group
				$(this).closest(".form-group").addClass("has-error");
				$(dets_to_show).addClass("has-error");		// show details form group

				// Show related fields
				$(dets_to_show).show("fast");	// show details form group
				$(rect_by_to_show).show();		// show rectified by div
			}

			// if slight defect - ORANGE
			if(thisVal == "slight_defect"){

				// Remove all potential colour classess			
				$(this).closest(".form-group").removeClass("has-error has-success ");
				$(dets_to_show).removeClass("has-error has-success ");
				
				// Add error class
				$(this).closest(".form-group").addClass("has-warning");
				$(dets_to_show).addClass("has-warning");

				// Show related fields
				$(dets_to_show).show("fast");	// show details form group
				$(rect_by_to_show).show();		// show rectified by div
			}

		} // end first if

	}); // end .section_std each


	// ===================================== //
	// ======== FORM SUMMARY =============== //
	// ===================================== //
	
	

	$( "a#section-12" ).click(function() {

		do_summary();
			
	}); // end the click event!

	// Clear down any current report summary details, then run through entire
	// report and set responses to be whatever is currently set for each
	function do_summary(){

		// console.log("I WAS CLICKED!");

		var veh_type 	= $('#veh_dets_11 option:selected').text();
		var veh_reg 	= $('#veh_dets_12').val();
		var co_name		= $('#veh_dets_13 option:selected').text();
		var make_model	= $('#veh_dets_14').val();
		var sur_date	= $('#veh_dets_15').val();
		var odo_rd		= $('#veh_dets_16').val();
		var odo_type	= $('#veh_dets_17 option:selected').text();
		var ps_rmks		= $('#veh_dets_18').val();	
		var notes_pts_list = $('#rep_dets_notes').val();

		$(".section_summary .sum_veh_type").text(veh_type);
		$(".section_summary .sum_veh_reg").text(veh_reg);
		$(".section_summary .sum_co_name").text(co_name);
		$(".section_summary .sum_make_model").text(make_model);
		$(".section_summary .sum_sur_date").text(sur_date);
		$(".section_summary .sum_odo_rd").text(odo_rd);
		$(".section_summary .sum_odo_type").text(odo_type);
		$(".section_summary .sum_ps_rmks").text(ps_rmks);
		$(".section_summary .notes_pts_list").text(ps_rmks);

		// AXLES: BRAKE PERFORMANCE
		do_axle_bk_perf_summary();

		// AXLES: TYRE THREAD
		do_axle_ty_thread_summary();

		// LUBRICATION
		do_axle_lubrication_summary();

		// Lights
		show_summary_std(".sum_lights", ".section_lights", "Lights");

		// Tacho
		show_summary_std(".sum_tacho", ".section_tacho", "Tachograph");

		// Inside Cab
		show_summary_std(".sum_inside_cab", ".section_insdecab", "Inside Cab");

		// Ground Level
		show_summary_std(".sum_grd_level", ".section_groundlevel", "Ground Level");

		// Small Service
		show_summary_std(".sum_sm_service", ".section_smallservice", "Small Service");

		// Additional
		show_summary_std(".sum_additional", ".section_additional", "Additional");	





	}// Close a#section-12 click event

	
	// AXLES BRAKE PERFORMANCE
	function do_axle_bk_perf_summary(){

		// console.log("########################### Do axle summary ###########################");

		// Reset the div contents
		$(".sum_bk_perf").html("");

		// Boolean used to flag if there are any responses
		var is_bk_perf = false; 

		// Check if there are any brake perf responses filled in
		$(".bk_perf input").each(function(){

			// console.log("Loop through axle responses");

			var curr_val = $(this).val();
			if(curr_val != ""){
				is_bk_perf = true;
			}
			return false; // As soon as we find any completed fields here stop the loop
		});	

		// console.log("Value of is_bk_perf: " + is_bk_perf);

		// If responses are found
		if(is_bk_perf){

			// console.log("Axle details have been found");

			// Section heading
			var axle_bk_perf = "<h3>Axles (Brake Performance)</h3>"; 

			// Begin unordered list
			axle_bk_perf += "<ul>";

			// Loop through each input
			$(".bk_perf input").each(function(){

				var curr_val = $(this).val(); // Get the input value

				// If valus is not blank
				if(curr_val != ""){

					// Get the label
					var curr_label = $(this).closest(".form-group").find("label").html();

					// Append li to html string
					axle_bk_perf += "<li>"+ curr_label + ": " + curr_val +"</li>";	

				} // if not blank

			});

			// Close unordered list
			axle_bk_perf += "</ul>";

			$(".sum_bk_perf").html(axle_bk_perf);

		} // End if Brake Perf

	} // End function: do_axle_bk_perf_summary()


	// AXLES TYRE THREAD
	function do_axle_ty_thread_summary(){

		// console.log("################################## Do axle summary ##################################");

		// Reset the div contents
		$(".sum_tyre_thread").html("");

		// Boolean used to flag if there are any responses
		var is_tyre_thread = false;

		// Check if there are any brake perf responses filled in
		$(".section_tyre_thread input").each(function(){

			// console.log("Loop through axle responses");

			var curr_val = $(this).val();
			if(curr_val != ""){
				is_tyre_thread = true;
			}
			return false; // As soon as we find any completed fields here stop the loop
		});	

		// console.log("Value of is_tyre_thread: " + is_tyre_thread);

		// If responses are found
		if(is_tyre_thread){

			// console.log("Axle details have been found");

			// Section heading
			var axle_tyre_thread = "<h3>Tyre Thread Remaining</h3>"; // Section 

			// Begin unordered list
			axle_tyre_thread += "<ul>";

			// Loop through each input
			$(".section_tyre_thread input").each(function(){			

				var curr_val = $(this).val(); // Get the input value

				// If valus is not blank
				if(curr_val != ""){

					// Get the label
					var curr_label = $(this).closest(".form-group").find("label").html();

					// Append li to html string
					axle_tyre_thread += "<li>"+ curr_label + ": " + curr_val +"</li>";	

				} // if not blank

			});

			// Close unordered list
			axle_tyre_thread += "</ul>";

			$(".sum_tyre_thread").html(axle_tyre_thread );

		} // End if Brake Perf

	} // End function: do_axle_bk_perf_summary()

	// LUBRICATION
	function do_axle_lubrication_summary(){

		// console.log("############# DO LUBRICATION ######################");

		// Reset the div contents
		$(".sum_lubrication").html("");
		var is_lube = false; // Boolean used to flag if there are any responses
		

		// Check if there are any lub responses filled in were not ok
		$(".section_lub option:selected").each(function(){

			// console.log("Looping lubrication responses");			
		
			var curr_val = $(this).val();

			if(curr_val == "not-ok"){

				is_lube = true;
				return;
			}
			
		});

		console.log("Value of is_lube is: " + is_lube);
		

		// If responses are found
		if(is_lube){

			var html = "<h3>Lubrication</h3>"; // Section Heading
			html += "<ul>";

			// Loop through each input
			$(".section_lub option:selected").each(function(){	

				var curr_val = $(this).val(); // Get the input value
				// console.log("Value of curr_val is: " + curr_val);

				// If value is not blank
				if(curr_val == "not-ok"){	

					// console.log("++++ Value not-ok found ++++");

					// Get the label
					var curr_label = $(this).closest(".form-group").find("label").html();

					// Get the details
					var curr_dets = $(this).closest(".form-group").find(".group_details input").val();	

					// Append li to html string
					html += "<li>"+ curr_label + ": " + curr_dets +"</li>";			

				} // Close if: curr_val not ok
			});

			html += "</ul>";

		} // Close if: is_lube


		$(".sum_lubrication").html(html);

	} // End function: do_axle_lubrication_summary()




	// STANDARD SECTIONS
	function show_summary_std(sum_class, section_class, section_title){	

		console.log("##################### Processing: " + section_title + " #############################");

		$(sum_class).html(""); console.log("Reset contents of DIV: " + sum_class);

		var is_response = false; // Boolean used to flag if there are any 

		// Check if there are any defects selected
		$(section_class + " option:selected").each(function(){

			// console.log("Begin loop of selected option elements for: " + section_class);

			var curr_val = $(this).val();
			// console.log("Current value is: " + curr_val);

			var curr_text = $(this).text();
			// console.log("Current text is: " + curr_text);

			if((curr_val == "significant_defect") || (curr_val == "slight_defect")){				

				is_response = true;
				// console.log("issue found: " + section_class + " " + curr_text);

			}
			return; // Break	
		}); // close each() option:Selected


		// If responses are found
		if(is_response){	

			console.log("Issue found: " + section_class);
			console.log("++++++++++++++++++++++ BEGIN LISTING FOUND FAULTS +++++++++++++++++++++++++");

			var html = "<h3>" + section_title + "</h3>"; // Section Heading
			html += "<ul>";

			// Loop through each input
			$(section_class + " .display-control option:selected").each(function(){	

				var curr_val = $(this).val(); 
				var curr_text = $(this).text();

				// If valus is a defect
				if((curr_val == "significant_defect") || (curr_val == "slight_defect")){	

					console.log("Curr val is: " + curr_val);

					// label
					var curr_label = $(this).closest(".form-group").find("label").html();
					console.log("Current Label is: " + curr_label);

					// Name of select element
					var select_name = $(this).closest("select").attr("name");
					console.log("Current Select name attr is: " + select_name);

					// Get ID of container for related details section
					var details_class = "." + select_name + "_details";		
					console.log("Current Details ID is: " + details_class);

					// Use detailsID to find the text for the details field
					var details_text = $(details_class).find("input").val();
					console.log("Current Details recorded text is: " + details_text);

					// Append li to html string
					html += "<li>"+ curr_label + " (" + curr_text + ") " + details_text +"</li>";				

					console.log(curr_label + " (" + curr_text + ") " + details_text);
					console.log("++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++");

				} // Close if curr_Val 				

			}); // close each option:selected

			html += "</ul>";

			$(sum_class).html(html);

		} // Close loop: if is_response
		else{
			console.log("No issues found for section: " + section_title);
		}
	} // Close function: show_summary_std()

} )( jQuery );