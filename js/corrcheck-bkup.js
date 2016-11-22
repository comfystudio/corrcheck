( function( $ ) {

	// Hide all report sections by default
	$(".corr-report .rep-section").hide();		

	// Show first section of survey
	$(".veh_dets").show();	

	// Initialise the datepicker | hide when date clicked
	var surveyDate = $('.datepicker').datepicker().on('changeDate', function(ev) {
	  surveyDate.hide();
	}).data('datepicker');




	// ===================================== //
	// =========== FORM VALIDATION ========= //
	// ===================================== //
	
	// Initialise jQuery Validate - main vehicle survey
	$(".corrCheck_form").validate();
	
	// Only allow show/hide panels if all required fields in panel 1 are complete
	$("#section-nav").on('click', 'a', function () {

		var error_count = 0;

		// Go through each input in the first panel
		$(".veh_dets input").each(function(){
				
			var thisVal = $(this).val();			

			// go through each input field			
			if (thisVal == ""){
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
			var thisID = $(this).attr('id');	

			$("#"+thisID).valid();
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








		// Vehicle Details
		var veh_type 	= $('#veh_dets_11 option:selected').text();
		var veh_reg 	= $('#veh_dets_12').val();
		var co_name		= $('#veh_dets_13 option:selected').text();
		var make_model	= $('#veh_dets_14').val();
		var sur_date	= $('#veh_dets_15').val();
		var odo_rd		= $('#veh_dets_16').val();
		var odo_type	= $('#veh_dets_17 option:selected').text();
		var ps_rmks		= $('#veh_dets_18').val();		

		$(".section_summary .sum_veh_type").text(veh_type);
		$(".section_summary .sum_veh_reg").text(veh_reg);
		$(".section_summary .sum_co_name").text(co_name);
		$(".section_summary .sum_make_model").text(make_model);
		$(".section_summary .sum_sur_date").text(sur_date);
		$(".section_summary .sum_odo_rd").text(odo_rd);
		$(".section_summary .sum_odo_type").text(odo_type);
		$(".section_summary .sum_ps_rmks").text(ps_rmks);

		





		// ==========================================
		// ========== Axles (Brake Performance)	
		// ==========================================
		
		// Reset the div contents
		$(".sum_bk_perf").html("");
		
		var is_bk_perf = false; // Boolean used to flag if there are any responses

		// Check if there are any brake perf responses filled in
		$(".bk_perf input").each(function(){

			var curr_val = $(this).val();
			if(curr_val != ""){
				is_bk_perf = true;
			}
			return false; // Break	
		});	

		// If responses are found
		if(is_bk_perf){			
			
			var axle_bk_perf = "<h3>Axles (Brake Performance)</h3>"; // Section Heading
			axle_bk_perf += "<ul>";

			// Loop through each input
			$(".bk_perf input").each(function(){			

				var curr_val = $(this).val(); // Get the input value

				// If valus is not blank
				if(curr_val != ""){

					// Get the laebl
					var curr_label = $(this).closest(".form-group").find("label").html();	
					// Append li to html string
					axle_bk_perf += "<li>"+ curr_label + ": " + curr_val +"</li>";				
				}
			}); // close loop

			axle_bk_perf += "</ul>";

			$(".sum_bk_perf").html(axle_bk_perf );

		}// END is_bk_perf

		// ==========================================
		// ========== Axles (Tyre Thread Performance)	
		// ==========================================
		
		// Reset the div contents
		$(".sum_tyre_thread").html("");
		
		var is_tyre_thread = false; // Boolean used to flag if there are any responses

		// Check if there are any brake perf responses filled in
		$(".section_tyre_thread input").each(function(){

			var curr_val = $(this).val();
			if(curr_val != ""){
				is_tyre_thread = true;
			}
			return false; // Break	
		});	

		// If responses are found
		if(is_tyre_thread){			
			
			var axle_tyre_thread = "<h3>Tyre Thread Remaining</h3>"; // Section Heading
			axle_tyre_thread += "<ul>";

			// Loop through each input
			$(".section_tyre_thread input").each(function(){			

				var curr_val = $(this).val(); // Get the input value

				// If valus is not blank
				if(curr_val != ""){

					// Get the laebl
					var curr_label = $(this).closest(".form-group").find("label").html();	
					// Append li to html string
					axle_tyre_thread += "<li>"+ curr_label + ": " + curr_val +"</li>";				
				}
			}); // close loop

			axle_tyre_thread += "</ul>";

			$(".sum_tyre_thread").html(axle_tyre_thread );

		}// END is_tyre_thread


		// ==========================================
		// ========== Lubrication
		// ==========================================

		// Reset the div contents
		$(".sum_lubrication").html("");

		var is_lube = false; // Boolean used to flag if there are any responses

		// Check if there are any lub responses filled in were not ok
		$(".section_lub option:selected").each(function(){

			var curr_val = $(this).val();
			if(curr_val == "not-ok"){

				is_lube = true;
			}
			return false; // Break	
		});	

		// If responses are found
		if(is_lube){				
			
			var html = "<h3>Lubrication</h3>"; // Section Heading
			html += "<ul>";

			// Loop through each input
			$(".section_lub option:selected").each(function(){		

				var curr_val = $(this).val(); // Get the input value

				// If valus is not blank
				if(curr_val == "not-ok"){					

					// Get the label
					var curr_label = $(this).closest(".form-group").find("label").html();

					// Get the details
					var curr_dets = $(this).closest(".form-group").find(".group_details input").val();					

					// Append li to html string
					html += "<li>"+ curr_label + ": " + curr_dets +"</li>";				
				}
			}); // close loop

			html += "</ul>";

			$(".sum_lubrication").html(html);

		}// END is_lube


		// These are all standard sections
		
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

			
	}); // end the click event!


		// ==========================================
		// ========== (standard section)
		// ==========================================	
		

		// Accepts a class foe the div displaying the summary and a class of the section itself
		function show_summary_std(sum_class, section_class, section_title){	

			console.log("Processing: " + section_title);

			$(sum_class).html(""); 

			var is_response = false; // Boolean used to flag if there are any responses (is response)

			// Check if there are any defects selected
			$(section_class + " option:selected").each(function(){

				var curr_val = $(this).val();

				if((curr_val == "significant_defect") || (curr_val == "slight_defect")){				

				  is_response = true;
				  console.log("issue found: " + section_class);

				}
				return false; // Break	

			});


			// If responses are found
		if(is_response){	

		//console.log(section_title);			
			
			var html = "<h3>" + section_title + "</h3>"; // Section Heading
			html += "<ul>";

			// Loop through each input
			$(section_class + " .display-control option:selected").each(function(){		

				var curr_val = $(this).val(); // Get the input value
				
				// If valus is a defect
				if((curr_val == "significant_defect") || (curr_val == "slight_defect")){						

					// Format: 1.label | 2.option value | 3.details value
					
					// label
					var curr_label = $(this).closest(".form-group").find("label").html();

					// Option value (human readable)
					var curr_opt = $(this).text(); // Get the input value

					// Details Text
					var thisName = $(this).closest("select").attr("name");
					var dets_to_show = "." + thisName + "_details";		// details class name to show/hide
					var curr_dets = $(dets_to_show).find("input").val();

					// console.log("label: " + curr_label);
					// console.log("option value: " + curr_opt);
					// console.log("details: " + curr_dets);					

					// Append li to html string
					html += "<li>"+ curr_label + " (" + curr_opt + ") " + curr_dets +"</li>";				
				}
			}); // close loop

			html += "</ul>";

			$(sum_class).html(html);

		}// END is_response
			

		} // end show_summary_std()




	
	


	

	 
	

} )( jQuery );