jQuery(document).ready(function($) {

    home_url = url.home_url;

    // zip code submission
    $("#zip-code-form").on("submit", function(event) {
        event.preventDefault();

        var zipCode = $('#zipCode').val();
        

        $('#loader').show();
        $("#zip-code-form").hide();
        $('#response').text('');

        $.ajax({
            url: url.ajax_url,
            type: 'POST',
            data: {
                action: "zip_code_validation",
                zipCode: zipCode 
            },
            contentType: 'application/x-www-form-urlencoded',
            success: function(response) {
                $('#loader').hide();
                $("#zip-code-form").show();

                console.log(response);
                var xmlDoc = $.parseXML(response.data);
                var $xml = $(xmlDoc);

                var errorMessage = $xml.find('ErrorMessage').text();
                var errorCode = $xml.find('ReturnCode').text();
                var fips = $xml.find('FipsCode').text();
                var countyName = $xml.find('CountyName').text();
                var state = $xml.find('StateCode').text();

                //if zip code in invalid
                if(errorCode == 'Error'){
                    $('#response').text("Invalide Zip Code: " + errorMessage );
                }
                else{
                    
                    
                    window.location.href = home_url+'/user-detail?zipcode=' + encodeURIComponent(zipCode) +
                    '&fips=' + encodeURIComponent(fips) +
                    '&county=' + encodeURIComponent(countyName) +
                    '&state=' + encodeURIComponent(state);

                }
                },
            error: function(error) {
                $('#loader').hide();
                console.log('Error:', error);
                $('#response').text('Invalid ZIP code');
            }
        });
    });

    // user detail form submission to get plans
    $("#members").on("submit", function(event) {
        event.preventDefault();

        $('#loader').show();
        $('#members').hide();

        var formData = $('#members').serialize();

        // getting data from query string
        var zipCode = new URLSearchParams(window.location.search).get('zipcode');
        var fips = new URLSearchParams(window.location.search).get('fips');
        var county = new URLSearchParams(window.location.search).get('county');
        var state = new URLSearchParams(window.location.search).get('state');

        var spouse_InHouseholdIndicatorValue = $("#spouse_InHouseholdIndicator").is(":checked") ? true : false;
        var primary_InHouseholdIndicatorValue = $("#primary_InHouseholdIndicator").is(":checked") ? true : false;

       

        $.ajax({
            url: url.ajax_url,
            type: 'POST',
            data: {
                action: 'members_data',
                formData: formData,
                zipCode: zipCode,
                county:county,
                fips:fips,
                state:state,
                spouse_InHouseholdIndicator:spouse_InHouseholdIndicatorValue,
                primary_InHouseholdIndicator:primary_InHouseholdIndicatorValue


            },
            success: function(response) {


                var xml = response.data;
                displayPlans(xml);


                if (response.data === "All members must be at least 15 years old.") {
                    $('#loader').hide();
                    $('#members').show();

                    $('#error_response').show();
                    $('#error_response').text(response.data);
                } else {
                    $('#loader').hide();
                    $('#members').show();
                    // Handle other success cases
                    $('#response').text('Form submitted successfully');
                }
                
                console.log(response.data);
            },
            error: function(error) {
                $('#error_response').show();
                $('#error_response').text(error);
            }
        });
    });

    function displayPlans(xml) {
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(xml, "application/xml");

        var plans = xmlDoc.getElementsByTagName("ns2:Plan");
        var cards = '';

        for (var i = 0; i < plans.length; i++) {
            var planID = plans[i].getElementsByTagName("PlanID")[0].textContent;
            var planName = plans[i].getElementsByTagName("PlanNameText")[0].textContent;
            var baseRate = plans[i].getElementsByTagName("BaseRateAmount")[0].textContent;
            var metalTier = plans[i].getElementsByTagName("MetalTierType")[0].textContent;

            cards += `
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                        <h3 class="plan_number">PLAN ${i + 1}:</h3>
                            <h5 class="card-title"> ${planName}</h5>
                            <h6 class="card-subtitle mb-2 text-muted">${metalTier} Plan</h6>
                            <p class="card-text">
                                <strong>Plan ID:</strong> ${planID}<br>
                                <strong>Base Rate:</strong> $${baseRate}
                            </p>
                        </div>
                    </div>
                </div>`;
        }

        $('#plansContainer').html('<div class="row">' + cards + '</div>');
    }
    
});
