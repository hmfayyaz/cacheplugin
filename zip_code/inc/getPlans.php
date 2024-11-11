<?php

class GetPlans
{

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_style']);

        add_shortcode('zip_code_form', [$this, 'zip_code_form']);
        add_shortcode('user_detail_page', [$this, 'user_detail_page']);

        add_action('wp_ajax_nopriv_zip_code_validation', [$this, 'zip_code_validation']);
        add_action('wp_ajax_zip_code_validation', [$this, 'zip_code_validation']);

        add_action('wp_ajax_nopriv_members_data', [$this, 'members_data']);
        add_action('wp_ajax_members_data', [$this, 'members_data']);
    }

    function enqueue_style()
    {
        wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css');
        wp_enqueue_style('style-css', plugin_dir_url(__FILE__) . 'css/style.css', '1.3.0');
    }

    function enqueue_scripts()
    {

        wp_enqueue_script('jquery');
        wp_enqueue_script('popper-js', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js', array('jquery'), rand(1, 1000), true);
        wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js', array('jquery', 'popper-js'), rand(1, 1000), true);

        wp_enqueue_script('ajax-script', plugin_dir_url(__FILE__) . 'js/custom.js', array('jquery'), rand(1, 1000), true);
        wp_localize_script('ajax-script', 'url', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'home_url' => home_url()
        ));
    }


    function zip_code_form()
    {

        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/template.php';
        return ob_get_clean();
    }

    function user_detail_page()
    {
        ob_start();
        include plugin_dir_path(__FILE__) . 'partials/member_detail.php';
        return ob_get_clean();
    }

    function zip_code_validation() {

        if (isset($_POST['zipCode'])) {
            $zipCode = sanitize_text_field($_POST['zipCode']);
    
            $requestPayload = '<?xml version="1.0" encoding="UTF-8"?>
                <p:ZipCodeValidationRequest xmlns:p="http://hios.cms.org/api" xmlns:p1="http://hios.cms.org/api-types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://hios.cms.org/api hios-api-11.0.xsd ">
                    <p:ZipCode>' . $zipCode . '</p:ZipCode>
                </p:ZipCodeValidationRequest>';
    
            $apiKey = '2cHRdXRe0boQrxHQ4krB2cvStmQEzT6o';
            $url = 'https://api.finder.healthcare.gov/v3.0/getCountiesForZip';
    
            $args = array(
                'body' => $requestPayload,
                'headers' => array(
                    'Content-Type' => 'application/xml',
                    'apikey' => $apiKey
                )
            );
    
            // Make the request
            $response = wp_remote_post($url, $args);
    
            // Check for errors
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                wp_send_json_error("Error: $error_message");
            } else {
                wp_send_json_success($response['body']);
            }
        } else {
            wp_send_json_error('ZIP code not provided');
        }
    
        wp_die();
    }

    function members_data() {

        if (isset($_POST['formData'])) {
            // Parse the form data
            parse_str($_POST['formData'], $formData);
    
            $self_date = sanitize_text_field($formData['primary']);
            $self_gender = sanitize_text_field($formData['primaryGender']);
            $spouses_date = sanitize_text_field($formData['spouseDate1']);
            $spouse_gender = sanitize_text_field($formData['spouseGender']);

            $zipCode = sanitize_text_field($_POST['zipCode']);
            $fips = sanitize_text_field($_POST['fips']);
            $county = sanitize_text_field($_POST['county']);
            $state = sanitize_text_field($_POST['state']);
            $spouse_tobacco = sanitize_text_field($formData['spouse_tobacco_month']);
            $primary_tobacco_month = sanitize_text_field($formData['primary_tobacco_month']);
            $primary_InHouseholdIndicator = sanitize_text_field($_POST['primary_InHouseholdIndicator']);
            $spouse_InHouseholdIndicator = sanitize_text_field($_POST['spouse_InHouseholdIndicator']);


            // check user ages as it must be greater than 15
            $primaryBirthDate = new DateTime($formData['primary']);
            $spouseBirthDate = new DateTime($formData['spouseDate1']);

            $currentDate = new DateTime();

            $primaryAge = $currentDate->diff($primaryBirthDate)->y;
            $spouseAge = $currentDate->diff($spouseBirthDate)->y;

            if ($primaryAge < 15 || $spouseAge < 15) {
                wp_send_json_error('All members must be at least 15 years old.');
                return;
            }

            $xml_payload = '<?xml version="1.0" encoding="UTF-8"?>
            <p:PlanQuoteRequest xmlns:p="http://hios.cms.org/api" xmlns:p1="http://hios.cms.org/api-types" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://hios.cms.org/api hios-api-11.0.xsd ">
              <p:Enrollees>
                <p1:DateOfBirth>' . $self_date . '</p1:DateOfBirth>
                <p1:Gender>' . $self_gender . '</p1:Gender>
                <p1:TobaccoLastUsedMonths>' . $primary_tobacco_month . '</p1:TobaccoLastUsedMonths>
                <p1:Relation>SELF</p1:Relation>
                <p1:InHouseholdIndicator>' . $primary_InHouseholdIndicator . '</p1:InHouseholdIndicator>
              </p:Enrollees>
              <p:Enrollees>
                <p1:DateOfBirth>' . $spouses_date . '</p1:DateOfBirth>
                <p1:Gender>' . $spouse_gender . '</p1:Gender>
                <p1:TobaccoLastUsedMonths>' . $spouse_tobacco . '</p1:TobaccoLastUsedMonths>
                <p1:Relation>SPOUSE</p1:Relation>
                <p1:InHouseholdIndicator>' . $spouse_InHouseholdIndicator . '</p1:InHouseholdIndicator>
              </p:Enrollees>
              <p:Location>
                <p1:ZipCode>' . $zipCode . '</p1:ZipCode>
                <p1:County>
                  <p1:FipsCode>' . $fips . '</p1:FipsCode>
                  <p1:CountyName>' . $county . '</p1:CountyName>
                  <p1:StateCode>' . $state . '</p1:StateCode>
                </p1:County>
              </p:Location>
              <p:InsuranceEffectiveDate>2024-01-01</p:InsuranceEffectiveDate>
              <p:Market>Individual</p:Market>
              <p:IsFilterAnalysisRequiredIndicator>false</p:IsFilterAnalysisRequiredIndicator>
              <p:PaginationInformation>
                <p1:PageNumber>1</p1:PageNumber>
                <p1:PageSize>20</p1:PageSize>
              </p:PaginationInformation>
              <p:SortOrder>
                <p1:SortField>BASE RATE</p1:SortField>
                <p1:SortDirection>ASC</p1:SortDirection>
              </p:SortOrder>
            </p:PlanQuoteRequest>';
    
            $api_endpoint = 'https://api.finder.healthcare.gov/v3.0/getIFPPlanQuotes';
            $apiKey = '2cHRdXRe0boQrxHQ4krB2cvStmQEzT6o';
    
            $args = array(
                'body' => $xml_payload,
                'headers' => array(
                    'Content-Type' => 'application/xml',
                    'apikey' => $apiKey
                )
            );
    
            $response = wp_remote_post($api_endpoint, $args);
    
            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();
                wp_send_json_error("Error: $error_message");
            } else {
                $response_body = wp_remote_retrieve_body($response);
                wp_send_json_success($response_body);
            }
        } else {
            wp_send_json_error('Empty Form');
        }
    
        wp_die();
    }
}
