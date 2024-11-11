<div id="response"></div>
<div id="error_response" class="alert alert-danger" style="display: none"></div>
<div id="loader" style="display: none;"></div>

<form id="members">
    <div class="birthDate">
        <h1>BirthDate</h1>
        <div id="primaryInfo" class="infoContainer mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="primary" class="form-label">Primary:</label>
                <input type="date" id="primary" name="primary" class="form-control" required>
            </div>
            <div class="gender mt-2">
                <label class="form-label">Gender:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="primaryMale" name="primaryGender" value="Male" required>
                    <label class="form-check-label" for="primaryMale">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="primaryFemale" name="primaryGender" value="Female" required>
                    <label class="form-check-label" for="primaryFemale">Female</label>
                </div>
            </div>
        </div>

        <div id="spouseInfo" class="infoContainer mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="spouseDate1" class="form-label">Spouse:</label>
                <input type="date" class="form-control" id="spouseDate1" name="spouseDate1" required>
            </div>
            <div class="gender mt-2">
                <label class="form-label">Gender:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="spouseMale" name="spouseGender" value="Male" required>
                    <label class="form-check-label" for="spouseMale">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" id="spouseFemale" name="spouseGender" value="Female" required>
                    <label class="form-check-label" for="spouseFemale">Female</label>
                </div>
            </div>
        </div>
    </div>

    <div class="tobacco-user">
        <h1 class="text-center">Tobacco User</h1>
        <p class="text-center">Has anyone applying for coverage smoked cigarettes or used any tobacco products in the past 6 months?</p>
        <div id="primaryTobacco">
            <div class="primaryTobacco d-flex justify-content-between align-items-center">
                <label for="primary_tobacco_month">Primary</label>
                <input type="number" name="primary_tobacco_month" id="primary_tobacco_month" class="form-control ml-4 mt-4" placeholder="Tobacco Last Used Months" required>
            </div>
        </div>
        <div id="spouseTobacco">
            <div class="spouseTobacco d-flex justify-content-between align-items-center">
                <label for="spouse_tobacco_month">Spouse</label>
                <input type="number" name="spouse_tobacco_month" id="spouse_tobacco_month" class="form-control ml-4 mt-4" placeholder="Tobacco Last Used Months" required>
            </div>
        </div>
    </div>

    <div class="InHouseholdIndicator">
        <h1>In Household Indicator</h1>
        <div class="d-flex justify-content-between mt-3">
            <label for="primary_InHouseholdIndicator">Primary</label>
            <input type="checkbox" name="primary_InHouseholdIndicator" id="primary_InHouseholdIndicator">InHouseholdIndicator
        </div>
        <div class="d-flex justify-content-between mt-3">
            <label for="spouse_InHouseholdIndicator">Spouse</label>
            <input type="checkbox" name="spouse_InHouseholdIndicator" id="spouse_InHouseholdIndicator">InHouseholdIndicator
        </div>
    </div>
    <input type="submit" value="Submit" class="mt-3 btn btn-success">
</form>

<div id="plansContainer" class="mt-3"></div>
