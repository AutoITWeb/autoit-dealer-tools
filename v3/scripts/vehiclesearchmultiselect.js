/**
 * The main vehicle search script
 *
 */

// Global flag to track initialization status
var biltorvetInitializationComplete = false;

function Biltorvet($) {

    var vehicleSearch = $(document).find('.bdt .vehicle_search');
    var vehicleSearchResults = $(document).find('.bdt .vehicle_search_results');
    var root_url = "";
    var bdt_hidden = document.getElementById("bdt-loading-filters");
    var frontpageSearch = document.getElementById("frontpage_vehicle_search");
    var frontpageSearchButton = document.getElementById("vehicle_search_frontpage_button");
    var searchFilterOptionsXHR = null;
    var loadingAnimation = vehicleSearch.find('.lds-ring');
    var emptyFilter = null;
    var priceRangeSlider = null;
    var consumptionRangeSlider = null;
	//jlk
	var electricRangeSlider = null;
    var sliderAlternativeNamespace = false;
    var leasingAlternativeName = document.getElementById("LeasingAlternativeName");
    var filter = {
        CompanyIds: null,
        Propellant: null,
        Makes: null,
        Models: null,
        BodyTypes: null,
        ProductTypes: null,
        VehicleStates: null,
        FullTextSearch: null,
        PriceTypes: null,
        CustomVehicleTypes: null,
        PriceMin: null,
        PriceMax: null,
        ConsumptionMin: null,
        ConsumptionMax: null,
		//jlk
        ElectricRangeMin: null,
        ElectricRangeMax: null,		
        Start: null, // will be overwritten by paging
        Limit: null, // will be overwritten by paging
        OrderBy: null, // OrderByEnum
        Ascending: null, // Bool
    };

    // Select2 init
    $(document).ready(function(e) {

        $('.multiple').select2(
            {
                dropdownParent: $('.vehicle_search'),
                containerCssClass: '.multiple',
            });

        var filter = {
            FullTextSearch: null,
        }

        var searchInput = '';

        $(".quicksearch").select2({
            ajax: {
                url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/quicksearch',
                method: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    searchInput = params.term;
                    return {
                        action: 'vehicle_quicksearch',
                        q: [params.term] // Query - will be handled by the endpoint
                    };
                },
                processResults: function (response) {
                    return {
                        results: response.vehicles
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: "Søg efter køretøjer...",
            //allowClear: true,
            language: {
                noResults: function () {
                    return "Søgningen '" + searchInput + "' gav ingen resultater.";
                },
                inputTooShort: function(args) {
                    return "Indtast " + args.minimum + " eller flere tegn for at starte din søgning";
                },
                inputTooLong: function(args) {
                    return "Du har indtastet for mange tegn. " + args.maximum + " er maks antal tegn der kan indtastes";
                },
                errorLoading: function() {
                    return "Der er desværre sket en fejl - prøv venligst igen";
                },
                searching: function() {
                    return "Søger...";
                }
            },
            escapeMarkup: function (markup) { return markup; },
            templateResult: Vehicles,
            templateSelection: VehiclesSelection
        });
    })

    // To avoid splitting the code up in a frontpage search and main search
    // checks to see if the user is on the frontpage or not are done throughout the code
    if(frontpageSearch)
    {
        root_url = document.getElementById("root_url").textContent;
    }

    this.Init = function() {

        

        // There can be a situation, namely with AVADA themes, where there's another .slider bound to the jQuery object. IF that's the case, we'll switch to an alternative namespace.
        // This alternative namespace only exists if there's been a conflict, so it can't be always used by default.
        if($.bootstrapSlider)
        {
            sliderAlternativeNamespace = true;
        }
        if($('#priceRange').length > 0)
        {
            var prsC = {
                id: $(this).attr('id'),
                min: 0,
                max:10000000,
                range: true,
                value: [0,10000000],
                step:1,
                tooltip: 'hide' // Bootstrap v4 tooltips not supported by this plugin.
            };
            priceRangeSlider = sliderAlternativeNamespace ? $('#priceRange').bootstrapSlider(prsC) : $('#priceRange').slider(prsC);
        }
        if($('#consumptionRange').length > 0)
        {
            var crsC = {
                id: $(this).attr('id'),
                min: 0,
                max:10,
                range: true,
                value: [0,10],
                step:1,
                tooltip: 'hide' // Bootstrap v4 tooltips not supported by this plugin.
            };
            consumptionRangeSlider = sliderAlternativeNamespace ? $('#consumptionRange').bootstrapSlider(crsC) : $('#consumptionRange').slider(crsC);
        }
		//jlk
        if($('#electricRange').length > 0)
        {
            var crsC = {
                id: $(this).attr('id'),
                min: 0,
                max:10,
                range: true,
                value: [0,10],
                step:1,
                tooltip: 'hide' // Bootstrap v4 tooltips not supported by this plugin.
            };
            electricRangeSlider = sliderAlternativeNamespace ? $('#electricRange').bootstrapSlider(crsC) : $('#electricRange').slider(crsC);
        }		

        this.ReloadUserFilterSelection(true);
        this.VehicleSearch(true);
        this.PlaceholderShuffler();
    }

    // Fulltextsearch suffle placeholder
    this.PlaceholderShuffler = function()
    {
        //quickSearch = vehicleSearch.find('input[name=quicksearch]');
        fullTextSearch = vehicleSearch.find('input[name=fullTextSearch]');

        var owner = this;
        var placeholders = [
            'Søg på mærke, model, farve, udstyr mm...          ',
            'Søg på stationcar anhængertræk...                        ',
            'Søg på hvid varevogn...                                  ',
            'Søg på diesel SUV...                                     '
        ];

        var randomIndex = Math.floor(Math.random() * 4);
        var placeholder = placeholders[randomIndex];
        var i = 0;
        var interval = setInterval(function(){
            if(i === placeholder.length) {
                clearInterval(interval);
                setTimeout(() => owner.PlaceholderShuffler(), 400)
                return;
            }

            // Quicksearch
            $('.select2-search__field').attr('placeholder', placeholder.substr(0, i +1));

            // Full-Text Search
            fullTextSearch.attr('placeholder', placeholder.substr(0, i +1));

            i++;
        }, 100)
    }

    /**
     * Starts the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     * @param {bool} True = empty filter, false the current filter
     *
     */
    this.ReloadUserFilterSelection = function(getFromSession)
    {
        // Default getFromSession to false if undefined
        getFromSession = getFromSession !== undefined ? getFromSession : false;

        if(vehicleSearch.length > 0)
        {
            if(searchFilterOptionsXHR !== null)
            {
                searchFilterOptionsXHR.abort();
            }

            StartLoadingAnimation();
            // Get the current filters set by the user
            GetUserFilterSettings();
            // Deactive filters
            DeactivateSearchFields();

            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            searchFilterOptionsXHR = $.ajax({
                url: ajax_config.restUrl + 'autoit-dealer-tools/v1/filteroptions',
                method: 'POST',
                dataType: 'json',
                data: {
                    'action': 'get_filter_options',
                    'filter': getFromSession ? null : filter // On page reload, let server use session data by passing null
                },
                cache: false,
                success: function(response){

                    SetFilters(response);


                    // Select the previously selected values
                    if(response.values)
                    {
                        if(response.values.companyIds && response.values.companyIds[0])
                        {
                            $('#company').val(response.values.companyIds);
                            HandleSelect2SelectionChange($('#company'));
                        }
                        else {
                            ReinitSelect2Placeholders('#company');
                        }
                        if(response.values.fullTextSearch && response.values.fullTextSearch[0])
                        {
                            vehicleSearch.find('input[name=fullTextSearch]').val(response.values.fullTextSearch[0]);
                        }
                        if(response.values.makes && response.values.makes[0])
                        {
                            $('#make').val(response.values.makes);
                            HandleSelect2SelectionChange($('#make'));
                        }
                        else {
                            ReinitSelect2Placeholders('#make');
                        }
                        if(response.values.models && response.values.models[0])
                        {
                            $('#model').val(response.values.models);
                            HandleSelect2SelectionChange($('#model'));
                        }
                        else {
                            ReinitSelect2Placeholders('#model');
                        }
                        if(response.values.propellants && response.values.propellants[0])
                        {
                            $('#propellant').val(response.values.propellants);
                            HandleSelect2SelectionChange($('#propellant'));
                        }
                        else {
                            ReinitSelect2Placeholders('#propellant');
                        }
                        if(response.values.bodyTypes && response.values.bodyTypes[0])
                        {
                            $('#bodyType').val(response.values.bodyTypes);
                            HandleSelect2SelectionChange($('#bodyType'));
                        }
                        else {
                            ReinitSelect2Placeholders('#bodyType');
                        }
                        if(response.values.productTypes && response.values.productTypes[0])
                        {
                            $('#productType').val(response.values.productTypes);
                            HandleSelect2SelectionChange($('#productType'));
                        }
                        else {
                            ReinitSelect2Placeholders('#productType');
                        }
                        if(response.values.vehicleStates && response.values.vehicleStates[0])
                        {
                            $('#vehicleState').val(response.values.vehicleStates);
                            HandleSelect2SelectionChange($('#vehicleState'));
                        }
                        else {
                            ReinitSelect2Placeholders('#vehicleState');
                        }
                        if(response.values.priceTypes && response.values.priceTypes[0])
                        {
                            $('#priceType').val(response.values.priceTypes);
                            HandleSelect2SelectionChange($('#priceType'));
                        }
                        else {
                            ReinitSelect2Placeholders('#priceType');
                        }
                        if(response.values.gearTypes && response.values.gearTypes[0])
                        {
                            $('#geartype').val(response.values.gearTypes);
                            HandleSelect2SelectionChange($('#geartype'));
                        }
                        else {
                            ReinitSelect2Placeholders('#geartype');
                        }
                        if(response.values.customVehicleTypes && response.values.customVehicleTypes[0])
                        {
                            // Custom Vehicle Types
                            var cvtElements = document.getElementsByClassName("car-icon-container")

                            if(cvtElements.length > 0)
                            {
                                Array.from(cvtElements).forEach(function(cvt) {

                                    if(response.values.customVehicleTypes[0] === cvt.dataset.customVehicleType)
                                    {
                                        const cvtcvtPreviouslySelectedSpanName = "cvt-checkmark-" + cvt.dataset.customVehicleType;
                                        const cvtcvtPreviouslySelectedCheckmark = $('[name="'  + cvtcvtPreviouslySelectedSpanName + '"]');

                                        cvtcvtPreviouslySelectedCheckmark[0].style.display = '';

                                        cvt.classList.add("cvt-selected");
                                    }
                                });
                            }
                        }
                    }
                },
                complete: function()
                {
                    searchFilterOptionsXHR = null;
                    StopLoadingAnimation();
                    StopLoadingAnimationPaging();

                    // Show filters when everything is done loading
                    bdt_hidden.classList.remove("hide-bdt");
                    
                    // Mark initialization as complete
                    biltorvetInitializationComplete = true;

                    // Update query parameters (getting ready for QP searching)
                    const params = new URLSearchParams(window.location.search);

                    if(params.has('scroll') && params.get('scroll') === 'true')
                    {
                        $('html, body').animate({
                            scrollTop: $('.vehicle-row').offset().top - 150
                        }, 500);

                        var url = location.href;
                        window.history.pushState({}, '', url.replace("?scroll=true", ""));
                    }
                }
            });
        }
    }

    function ReinitSelect2Placeholders(HtmlElement)
    {
        var selectionContainer = $(HtmlElement).next('.select2-container').find('.select2-selection__rendered');
        $(selectionContainer).parent().find('.select2-search__field').show();

        // Remove special label
        $(selectionContainer).parent().find('.select2-selection__label').remove();

        $(HtmlElement).parent().find('.selectDropDownLabel').show();
    }

    // Select 2
    function RetrieveSelect2Values(id)
    {
        if ($(id).hasClass("select2-hidden-accessible")) {

            const selectValues = [];

            $(id).select2('data').forEach(values => {

                let data = {
                    val: values.id,
                }

                selectValues.push(data.val);
            })

            return selectValues;
        }
    }

    // Select2
    // When an option is selected, calc if the "pill" or some  custom text showing amount of selected options should be shown
    function HandleSelect2SelectionChange(htmlElement, response)
    {
        // Notify Select2 about the change
        $(htmlElement).trigger('change.select2');

        var selectionContainer = $(htmlElement).next('.select2-container').find('.select2-selection__rendered');
        var selectionContainerChildren = $(selectionContainer).children('li');

        // Reset rendering
        $(selectionContainer).show();
        $(selectionContainer).parent().find('.select2-selection__label').remove();

        // Initialize widths
        var selectionContainerWidth = $(selectionContainer).width();
        var selectionContainerChildrenWidth = -20;

        // Hide search container and placeholder when filter has active selections
        if (selectionContainerChildren.length)
        {
            $(selectionContainer).parent().find('.select2-search__field').hide();
            $(htmlElement).parent().find('.selectDropDownLabel').hide()
        }
        else
        {
            $(selectionContainer).parent().find('.select2-search__field').show();
            $(htmlElement).parent().find('.selectDropDownLabel').show()
        }

        // Replace selections with a label when selections overflow its container
        $(selectionContainerChildren).each(function ()
        {
            //selectionContainerChildrenWidth += 15;
            selectionContainerChildrenWidth += selectionContainerChildren.outerWidth();
        })

        if (selectionContainerChildrenWidth > selectionContainerWidth)
        {
            $(selectionContainer).hide();
            $(selectionContainer).parent().append('<span class="select2-selection__label">' + selectionContainerChildren.length + ' valgte ' + $(htmlElement).data('contenttype') + '</span>');
        }
        else
        {
            $(selectionContainer).show();
            $(selectionContainer).parent().find('.select2-selection__label').remove();
        }
    }

    // Appends select options and selects previous selected options if any
    function SetFilterValues(id, selectedValues, allValues)
    {
        allValues.forEach(value => {
            let data = {
                text: value.name ?? value.value,
                value: value.key ?? value.name
            }

            let selected = false;

            if(selectedValues !== null && selectedValues.length !== 0)
            {
                if(selectedValues.includes(value))
                {
                    selected = true;
                }
            }

            var newOption = new Option(data.text, data.value, false, selected);

            if($(id).find("option[value='" + data.value + "']").length) {

            }
            else {
                $(id).append(newOption).trigger('change.select2');
            }
        })
    }

    this.ResetFilters = function()
    {
        return $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/resetfilteroptions',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'reset_filter_options'
            },
            cache: false,
            success: function(response){

                SetFilters(response);

                $('.multiple').each(function(i, element)
                {
                    // Reinit placeholders (labels) !!
                    var selectionContainer = $(element).next('.select2-container').find('.select2-selection__rendered');

                    $(selectionContainer).parent().find('.select2-selection__label').remove();
                    $(element).parent().find('.selectDropDownLabel').show()
                })

            },
            complete: function()
            {
                StopLoadingAnimation();
            }
        });
    }

    this.ResetFilter = function()
    {
        StartLoadingAnimation();

        DeactivateSearchFields();

        filter = null;

        // Custom Vehicle Types
        const customVehicleTypeSelected = document.querySelector('.cvt-selected');

        if(customVehicleTypeSelected !== null)
        {
            const cvtcvtPreviouslySelectedDataSet = customVehicleTypeSelected.dataset.customVehicleType;
            const cvtcvtPreviouslySelectedSpanName = "cvt-checkmark-" + cvtcvtPreviouslySelectedDataSet;
            const cvtcvtPreviouslySelectedCheckmark = $('[name="'  + cvtcvtPreviouslySelectedSpanName + '"]');

            cvtcvtPreviouslySelectedCheckmark[0].style.display = 'none';

            customVehicleTypeSelected.classList.remove("cvt-selected");
        }

        // The VehicleSearch() function is called directly as we don't want to scrollTop when resetting the filter
        this.ResetFilters();
    }

    this.StartVehicleSearch = function ()
    {
        StartLoadingAnimation();

        // Fetch results, append to dom and then scrollTop
        $.when(this.VehicleSearch(false)).then(function(){
            $('html, body').animate({
                scrollTop: $('.vehicle-row').offset().top - 150
            }, 500);
        });
    }

    /**
     * Search button has been clicked - time to fetch some vehicles
     * @return {response} - replaces the current vehicles with the ones from this result
     *
     */
    this.VehicleSearch = function(getFromSession)
    {
        // Default getFromSession to false if undefined
        getFromSession = getFromSession !== undefined ? getFromSession : false;
       /*const filterParam = new URLSearchParams({
            filter: getFromSession ? emptyFilter : filter
        })

        let response = await fetch(ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search', filterParam);

        var data = await response.text();
        console.log(data);

        $('#bdt_vehicle_search_results').html(data);

        StopLoadingAnimationPaging();
        StopLoadingAnimation();*/

        return $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search',
                'filter': getFromSession ? null : filter // Use session data when reloading from session
            },
            cache: false,
            success: function(response){

                $('#bdt_vehicle_search_results').html(response);

            },
            complete: function()
            {
                StopLoadingAnimationPaging();
                StopLoadingAnimation();
            }
        });
    }

    this.SaveUserFilterSettings = function()
    {
        StartLoadingAnimation();
        GetUserFilterSettings();

        SaveFilter();

        StopLoadingAnimation();
    }

    this.StartOrderByAndAscDesc = function()
    {
        OrderByAndAscDesc();
    }

    /**
     * Handles order_by and ascending / descending options
     *
     */
    function OrderByAndAscDesc()
    {
        // Start loader / spinner?
        StartLoadingAnimationPaging();

        GetUserFilterSettings();

        filter.OrderBy = $('#select-orderby').val() ===  null ? null : $('#select-orderby').val();
        filter.Ascending = $('#select-asc-desc').val() === 'asc' ? true : false;

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search',
                'filter': filter
            },
            cache: false,
            success: function(response){

                $('#vehicle_search_results').html(response);
            },
            complete: function()
            {
                StopLoadingAnimationPaging();
            }
        });
    }

    this.PagingFetchMore = function()
    {
        FetchMore();
    };

    /**
     * Returns more vehicles from the current filter
     *
     */
    function FetchMore(){

        // Load start spinner?
        StartLoadingAnimationPaging();
        
        // Ensure filter variable is up to date with current form state
        GetUserFilterSettings();

        // Get all current elements with the animate class
        var currentVehicleCards = document.querySelectorAll(".animate__animated");

        // Remove the animate class on all elements to avoid flickering when new vehicles are appended
        currentVehicleCards.forEach(vehicleCard => {
            vehicleCard.classList.remove('animate__animated');
        });

        // Get paging data from the paging button
        const pagingData = document.querySelector('#paging-button');

        // Get Custom Vehicle Type data
        const customVehicleTypeSelected = document.querySelector('#cvt-selected');

        var currentPage = parseInt(pagingData.dataset.currentPage);
        var amountOfPages = parseInt(pagingData.dataset.amountOfPages);
        var limit = parseInt(pagingData.dataset.limit);
        var start = parseInt(pagingData.dataset.end);
        var cvt = GetCustomVehicleType();

        if(cvt)
        {
            filter.CustomVehicleTypes = [cvt];
        }

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/vehiclesearch/search_paging',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'vehicle_search_paging',
                'filter': filter,
                'currentPage': currentPage,
                'start': start,
                'limit': limit,
            },
            cache: false,
            success: function(response){

                // Append data
                const vehicleList = document.querySelector('#vehicle-row');
                //vehicleList.append(response);
                vehicleList.innerHTML += response;

                // Should I remove the paging button?
                if(amountOfPages !== currentPage +1) {
                    pagingData.dataset.currentPage = String(currentPage +1);
                    pagingData.dataset.end = String((start + limit));

                } else {
                    pagingData.remove();
                }
            },
            complete: function()
            {
                // Load stop animation / spinner?
                StopLoadingAnimationPaging();
            }
        });
    }

    /**
     * Used by the frontpage search functionality
     * Saves the current filter and redirects the user to vehicle search page (set in the plugin settings
     *
     */
    function SaveFilter()
    {
        StartLoadingAnimation();

        $.ajax({
            url: ajax_config.restUrl + 'autoit-dealer-tools/v1/filteroptions/savefilter',
            method: 'POST',
            dataType: 'json',
            data: {
                'action': 'save_filter',
                'filter': filter
            },
            cache: false,
            success: function(response){

                window.location.href = root_url + "/?scroll=true";
            },
            complete: function()
            {
                StopLoadingAnimation();
            }
        });
    }

    function DeactivateSearchFields()
    {
        if(sliderAlternativeNamespace)
        {
            consumptionRangeSlider.bootstrapSlider("disable");
            priceRangeSlider.bootstrapSlider("disable");
			//jlk
			electricRangeSlider.bootstrapSlider("disable");
        } else {
            consumptionRangeSlider.slider("disable");
            priceRangeSlider.slider("disable");
			//jlk
			electricRangeSlider.slider("disable");
        }
        vehicleSearch.find('select').prop('disabled', true);

        // Custom Vehicle Types
        var cvtElements = document.getElementsByClassName("car-icon-container")

        if(cvtElements.length > 0)
        {
            Array.from(cvtElements).forEach(function(cvt) {
                cvt.classList.add("cvt-disable-clicking")
            });
        }
    }

    /**
     * Starts the main spinner (the one in the filters area)
     *
     */
    function StartLoadingAnimation()
    {
        if(loadingAnimation.hasClass('d-none'))
        {
            loadingAnimation.css('opacity', 0).css('display', 'block').removeClass('d-none');
        }
        loadingAnimation.animate({opacity: 1}, 1000);
    }

    /**
     * Stops the main spinner (the one in the filters area)
     *
     */
    function StopLoadingAnimation()
    {
        loadingAnimation.animate({opacity: 0}, 200, function(){ $(this).css('display', 'none').addClass('d-none'); });
    }

    /**
     * Starts the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     *
     */
    function StartLoadingAnimationPaging()
    {

        var vehicleSearchResultsLoading = $(document).find('.bdt .vehicle_search_results');
        var loadingAnimationPaging = vehicleSearchResultsLoading.find('.lds-ring-paging');

        if(loadingAnimationPaging.hasClass('d-none'))
        {
            loadingAnimationPaging.css('opacity', 0).css('display', 'block').removeClass('d-none');
        }
        loadingAnimationPaging.animate({opacity: 1}, 1000);
    }

    /**
     * Stops the "paging" spinner which is also used when a user interacts with the order_by and filter_by selects
     *
     */
    function StopLoadingAnimationPaging()
    {
        var vehicleSearchResultsLoading = $(document).find('.bdt .vehicle_search_results');
        var loadingAnimationPaging = vehicleSearchResultsLoading.find('.lds-ring-paging');

        if (leasingAlternativeName) {
          $('#select-orderby option:contains("Leasingpris")').text(leasingAlternativeName.innerText);
        }

        loadingAnimationPaging.animate({opacity: 0}, 200, function(){ $(this).css('display', 'none').addClass('d-none'); });
    }

    function FormatDKPrice(nbr) {
        return nbr.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function FindNumberSeries(nbr)
    {
        var numberSeries = '1';
        for(var i = 1; i < (''+nbr+'').length; i++)
        {
            numberSeries += '0';
        }
        return parseInt(numberSeries);
    }

    // Easy solution to handle duplicate option elements created by Select2 (Root cause not found 2022-02-11)
    function RemoveDuplicateValues(selectName, responseValues)
    {
        var optionsArray = vehicleSearch.find('select[name=' + selectName + ']')[0].options;

        const optionsVal = [...optionsArray].map(el => el.value);

        let findDuplicates = arr => arr.filter((item, index) => arr.indexOf(item) != index);

        if(findDuplicates(optionsVal).length > 0 || responseValues.length !== optionsVal.length)
        {
            document.getElementById(selectName).options[0].remove();
        }
    }

    function SetFilters(response)
    {
        // The frontpage search doesn't respect language settings - refactoring needed
        if(frontpageSearch)
        {
            frontpageSearchButton.setAttribute("data-labelpattern", "Vis " + response.totalResults + " resultater");
            frontpageSearchButton.innerText = "Vis " + response.totalResults + " resultater";
        }
        else
        {
            vehicleSearch.find('.search').text(vehicleSearch.find('.search').data('labelpattern').replace('%u', response.totalResults));
        }

        // Update filters
        var fullTextSearch = '';
        for(var i in response.fullTextSearch)
        {
            fullTextSearch += response.fullTextSearch.value;
        }

        vehicleSearch.find('input[name=fullTextSearch]').val(fullTextSearch);
        if(fullTextSearch !== '')
        {
            vehicleSearch.find('input[name=fullTextSearch]').removeAttr('disabled');
        }

        var companies = '';
        for(var i in response.companies)
        {
            companies += '<option value="' + response.companies[i].key + '">' + response.companies[i].value + '</option>';
        }
        vehicleSearch.find('select[name=company]').find('option:not(:first-child)').remove().end().append(companies);
        RemoveDuplicateValues('company', response.companies);

        if(companies !== '')
        {
            vehicleSearch.find('select[name=company]').removeAttr('disabled');
        }

        var makes = '';
        for(var i in response.makes)
        {
            makes += '<option value="' + response.makes[i].name + '">' + response.makes[i].name + '</option>';
        }
        vehicleSearch.find('select[name=make]').find('option:not(:first-child)').remove().end().append(makes);
        RemoveDuplicateValues('make', response.makes);

        if(makes !== '')
        {
            vehicleSearch.find('select[name=make]').removeAttr('disabled');
        }

        var models = '';
        for(var i in response.models)
        {
            models += '<option value="' + response.models[i].name + '">' + response.models[i].name + '</option>';
        }
        vehicleSearch.find('select[name=model]').find('option:not(:first-child)').remove().end().append(models);
        RemoveDuplicateValues('model', response.models);

        if(models !== '')
        {
            vehicleSearch.find('select[name=model]').removeAttr('disabled');
        }

        var vehicleStates = '';
        for(var i in response.vehicleStates)
        {
            vehicleStates += '<option value="' + response.vehicleStates[i].name + '">' + response.vehicleStates[i].name + '</option>';
        }
        vehicleSearch.find('select[name=vehicleState]').find('option:not(:first-child)').remove().end().append(vehicleStates);
        RemoveDuplicateValues('vehicleState', response.vehicleStates);
        if(vehicleStates !== '')
        {
            vehicleSearch.find('select[name=vehicleState]').removeAttr('disabled');
        }

        var priceTypes = '';
        for(var i in response.priceTypes)
        {
            // Neltoft specific "hack"
            if (leasingAlternativeName && response.priceTypes[i].name == 'Leasing') {
                priceTypes += '<option value="' + response.priceTypes[i].name + '">' + leasingAlternativeName.innerText + '</option>';
            } else {
                priceTypes += '<option value="' + response.priceTypes[i].name + '">' + response.priceTypes[i].name + '</option>';
            }
        }
        vehicleSearch.find('select[name=priceType]').find('option:not(:first-child)').remove().end().append(priceTypes);
        RemoveDuplicateValues('priceType', response.priceTypes);
        if(priceTypes !== '')
        {
            vehicleSearch.find('select[name=priceType]').removeAttr('disabled');
        }

        var productTypes = '';
        for(var i in response.productTypes)
        {
            productTypes += '<option value="' + response.productTypes[i].name + '">' + response.productTypes[i].name + '</option>';
        }
        vehicleSearch.find('select[name=productType]').find('option:not(:first-child)').remove().end().append(productTypes);
        RemoveDuplicateValues('productType', response.productTypes);
        if(productTypes !== '')
        {
            vehicleSearch.find('select[name=productType]').removeAttr('disabled');
        }

        var bodyTypes = '';
        for(var i in response.bodyTypes)
        {
            bodyTypes += '<option value="' + response.bodyTypes[i].name + '">' + response.bodyTypes[i].name + '</option>';
        }
        vehicleSearch.find('select[name=bodyType]').find('option:not(:first-child)').remove().end().append(bodyTypes);
        RemoveDuplicateValues('bodyType', response.bodyTypes);
        if(bodyTypes !== '')
        {
            vehicleSearch.find('select[name=bodyType]').removeAttr('disabled');
        }

        var propellants = '';
        for(var i in response.propellants)
        {
            propellants += '<option value="' + response.propellants[i].name + '">' + response.propellants[i].name + '</option>';
        }
        vehicleSearch.find('select[name=propellant]').find('option:not(:first-child)').remove().end().append(propellants);

        RemoveDuplicateValues('propellant', response.propellants);
        if(propellants !== '')
        {
            vehicleSearch.find('select[name=propellant]').removeAttr('disabled');
        }

        // Geartype - static options (always available)
        var geartypes = '<option value="Automatic">Automatisk</option><option value="Manual">Manuel</option>';
        vehicleSearch.find('select[name=geartype]').find('option').remove().end().append(geartypes);
        vehicleSearch.find('select[name=geartype]').removeAttr('disabled');

        if(consumptionRangeSlider !== null)
        {
            var crsI = sliderAlternativeNamespace ? consumptionRangeSlider.bootstrapSlider(response.consumptionMin === response.consumptionMax ? "disable" : "enable") : consumptionRangeSlider.slider(response.consumptionMin === response.consumptionMax ? "disable" : "enable");
            crsI.data('slider')
                .setAttribute('min', response.consumptionMin)
                .setAttribute('max', response.consumptionMax)
                .setValue([response.values.consumptionMin === null ? response.consumptionMin : response.values.consumptionMin, response.values.consumptionMax === null ? response.consumptionMax : response.values.consumptionMax], true, true);
        }
        if(priceRangeSlider !== null)
        {
            var prsI = sliderAlternativeNamespace ? priceRangeSlider.bootstrapSlider(response.priceMin === response.priceMax ? "disable" : "enable") : priceRangeSlider.slider(response.priceMin === response.priceMax ? "disable" : "enable");
            prsI.data('slider')
                .setAttribute('min', response.priceMin)
                .setAttribute('max', response.priceMax)
                .setValue([response.values.priceMin === null ? response.priceMin : response.values.priceMin, response.values.priceMax === null ? response.priceMax : response.values.priceMax], true, true);
        }
		//jlk
        if(electricRangeSlider !== null)
        {
            var crsI = sliderAlternativeNamespace ? electricRangeSlider.bootstrapSlider(response.electricRangeMin === response.electricRangeMax ? "disable" : "enable") : electricRangeSlider.slider(response.electricRangeMin === response.electricRangeMax ? "disable" : "enable");
            crsI.data('slider')
                .setAttribute('min', response.electricRangeMin)
                .setAttribute('max', response.electricRangeMax)
                .setValue([response.values.electricRangeMin === null ? response.electricRangeMin : response.values.electricRangeMin, response.values.electricRangeMax === null ? response.electricRangeMax : response.values.electricRangeMax], true, true);
        }		

        // Custom Vehicle Types
        var cvtElements = document.getElementsByClassName("car-icon-container")

        if(cvtElements.length > 0)
        {
            Array.from(cvtElements).forEach(function(cvt) {
                cvt.classList.remove("cvt-disable-clicking")
            });
        }
    }

    /**
     * Fetches the curren filters settings
     *
     */
    function GetUserFilterSettings()
    {
        // Don't send min/max values if the selected values equal min and max.
        // This allows to return results which have no values set on these fields.
        var priceMin = priceRangeSlider !== null ? priceRangeSlider.data('slider').getValue()[0] : null;
        priceMin = priceMin === -1 ? null : priceMin;
        var priceMax = priceRangeSlider !== null ? priceRangeSlider.data('slider').getValue()[1] : null;
        priceMax = priceMax === -1 ? null : priceMax;
        var consumptionMin = consumptionRangeSlider !== null ? consumptionRangeSlider.data('slider').getValue()[0] : null;
        consumptionMin = consumptionMin === -1 ? null : consumptionMin;
        var consumptionMax = consumptionRangeSlider !== null ? consumptionRangeSlider.data('slider').getValue()[1] : null;
        consumptionMax = consumptionMax === -1 ? null : consumptionMax;
		//jlk
        var electricRangeMin = electricRangeSlider !== null ? electricRangeSlider.data('slider').getValue()[0] : null;
        electricRangeMin = electricRangeMin === -1 ? null : electricRangeMin;
        var electricRangeMax = electricRangeSlider !== null ? electricRangeSlider.data('slider').getValue()[1] : null;
        electricRangeMax = electricRangeMax === -1 ? null : electricRangeMax;

        filter = {
            CompanyIds: RetrieveSelect2Values('#company') ?? null,
            FullTextSearch: vehicleSearch.find('input[name=fullTextSearch]').val() === '' ? null : [vehicleSearch.find('input[name=fullTextSearch]').val()],
            Propellants: RetrieveSelect2Values('#propellant') ?? null,
            Makes: RetrieveSelect2Values('#make') ?? null,
            Models: RetrieveSelect2Values('#model') ?? null,
            BodyTypes: RetrieveSelect2Values('#bodyType') ?? null,
            ProductTypes: RetrieveSelect2Values('#productType') ?? null,
            VehicleStates: RetrieveSelect2Values('#vehicleState') ?? null,
            PriceTypes: RetrieveSelect2Values('#priceType') ?? null,
            GearTypes: RetrieveSelect2Values('#geartype') ?? null,
            PriceMin: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('min') !== priceMin ? priceMin : null) : null,
            PriceMax: priceRangeSlider !== null ? (priceRangeSlider.data('slider').getAttribute('max') !== priceMax ? priceMax : null) : null,
            ConsumptionMin: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('min') !== consumptionMin ? consumptionMin : null) : null,
            ConsumptionMax: consumptionRangeSlider !== null ? (consumptionRangeSlider.data('slider').getAttribute('max') !== consumptionMax ? consumptionMax : null) : null,
			//jlk
            ElectricRangeMin: electricRangeSlider !== null ? (electricRangeSlider.data('slider').getAttribute('min') !== electricRangeMin ? electricRangeMin : null) : null,
            ElectricRangeMax: electricRangeSlider !== null ? (electricRangeSlider.data('slider').getAttribute('max') !== electricRangeMax ? electricRangeMax : null) : null,
            Start: null,
            Limit: null,
            OrderBy: vehicleSearchResults.find('select[name=orderBy]').val() === '' ? null : vehicleSearchResults.find('select[name=orderBy]').val(),
            Ascending: vehicleSearchResults.find('select[name=ascDesc]').val() === 'asc' ? true : false,
        }

        var cvt = GetCustomVehicleType();

        if(cvt)
        {
            filter.CustomVehicleTypes = [cvt];
        }
    }

    this.IsInitializationComplete = function() {
        return biltorvetInitializationComplete;
    };

    // Fire the "Constructor"
    this.Init();
}

function GetCustomVehicleType()
{
    const customVehicleTypeSelected = document.querySelector('.cvt-selected');
    if(customVehicleTypeSelected !== null)
    {
        var cvt = customVehicleTypeSelected.dataset.customVehicleType;

        return cvt;
    }
}

function FormatPrice(x, suffix)
{
    if(x === -1)
    {
        return x;
    }
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + (suffix ? ',-' : '');
}

function Vehicles(vehicle) {

    if(vehicle.loading)
    {
        return "Søger...";
    }

    var setPrice = vehicle.cashPrice !== null ? "Kontantpris " + vehicle.cashPrice : vehicle.leasingPrice !== null ? "Leasingpris " + vehicle.leasingPrice : vehicle.financePrice !== null ? "Finansieringspris " + vehicle.financePrice : "Ring for pris";

    var setVariant = vehicle.variant;

    if(setVariant.length > 30)
    {
        setVariant = setVariant.slice(0, 27) + "...";
    }

    var markup =
        "<div class='bdt_intellisense-list'>" +
        "<a href='" + vehicle.uri + "'>" +
        "<span class='bdt_intellisense-list-image'>" +
        "<img src='" + vehicle.vehicleImage + "' width='110px' alt='" + vehicle.makeName + "'/>" +
        "</span>" +
        "<span class='bdt_intellisense-list-data'>" +
        "<span class='bdt_intellisense-list-name'>" + vehicle.makeName + " " + vehicle.model + "<br/>" +
        "<span>" + setVariant + "</span>" +
        "</span>" +
        "<span class='bdt_intellisense-list-price'>" + setPrice ?? '' + "</span>" +
        "</span>" +
        "</span>" +
        "</a>" +
        "</div>";

    return markup;
}

function VehiclesSelection (data) {
    return data.makeModelVariant;
}

/**
 * This part listens to changes in the frontend - .on('click', 'change') etc.
 *
 */
jQuery(function($) {
    var bdt = new Biltorvet($);

    function cvtSelectedResetOtherFilters()
    {
        var vehicleSearch = $(this).closest('.bdt .vehicle_search');

        vehicleSearch.find('select[name=company]').val('');
        vehicleSearch.find('select[name=vehicleState]').val('');
        vehicleSearch.find('select[name=make]').val('');
        vehicleSearch.find('select[name=model]').val('');
        vehicleSearch.find('select[name=bodyType]').val('');
        vehicleSearch.find('select[name=productType]').val('');
        vehicleSearch.find('select[name=priceType]').val('');
        vehicleSearch.find('select[name=propellant]').val('');
        vehicleSearch.find('select[name=priceMinMax]').val('');
        vehicleSearch.find('select[name=priceMinMax]').val('');
        vehicleSearch.find('select[name=consumptionMin]').val('');
        vehicleSearch.find('select[name=consumptionMax]').val('');
		//jlk
		vehicleSearch.find('select[name=electricRangeMin]').val('');
        vehicleSearch.find('select[name=electricRangeMax]').val('');

        $('.multiple').each(function(i, element)
        {
            // Reinit placeholders (labels) !!
            var selectionContainer = $(element).next('.select2-container').find('.select2-selection__rendered');

            $(selectionContainer).parent().find('.select2-selection__label').remove();
            $(element).parent().find('.selectDropDownLabel').show()
        })
    }

    $(document)
        .on('change', '.bdtSlider', function(e){
            var min = e.value.newValue[0];
            var max = e.value.newValue[1];
            if($(e.target).attr('id') === 'priceRange')
            {
                min = FormatPrice(min, false);
                max = FormatPrice(max, true);
            }
            if(min === -1)
            {
                min = '';
            }
            if(max === -1)
            {
                max = '';
            }

            $(this).closest('.bdtSliderContainer')
                .find('.bdtSliderMinVal')
                .text(min)
                .end()
                .find('.bdtSliderMaxVal')
                .text(max);
        })
        .on('slideStop', '.bdtSlider', function(e){

            bdt.ReloadUserFilterSelection(false);
        })
        .on('click', '.bdt .reset', function(e){
            e.preventDefault();

            bdt.ResetFilter();
        })
        .on('change', '.bdt .multiple', function (e)
        {
            e.preventDefault();

            // Hide selected value to avoid select tag overflow
            var selectionContainer = $(e.target).next('.select2-container').find('.select2-selection__rendered');
            var selectionContainerChildren = $(selectionContainer).children('li');

            if(selectionContainerChildren.length > 1)
            {
                var getLastElementIndex = selectionContainerChildren.length - 1;
                selectionContainerChildren[getLastElementIndex].style.display = 'none';
            }

            // Hide / show labels (Placeholder value)
            if (selectionContainerChildren.length)
            {
                $(selectionContainer).parent().find('.select2-search__field').hide();
                $(e.target).parent().find('.selectDropDownLabel').hide()
            }
            else
            {
                $(selectionContainer).parent().find('.select2-search__field').show();
                $(e.target).parent().find('.selectDropDownLabel').show()
            }

            bdt.ReloadUserFilterSelection(false);
        })
        .on('click', '.bdt .search', function(e){
            e.preventDefault();

            bdt.StartVehicleSearch();
        })
        .on('click', '#vehicle_search_frontpage_button', function(e){
            e.preventDefault();

            bdt.SaveUserFilterSettings();
        })
        .on('change', '.searchFilter select', function(e) {
            e.preventDefault();

            bdt.StartOrderByAndAscDesc();
        })
        .on('click', '.bdt .paging-button', function(e){
            e.preventDefault();

            bdt.PagingFetchMore();
        })
        .on('click', '.bdt .car-icon-container', function(e){
            e.preventDefault();

            // When a CVT is selected, all other filters needs to be reset
            const cvtClicked = e.target.closest(".car-icon-container");
            const cvtClickedDataSet = cvtClicked.dataset.customVehicleType;

            const cvtSpanName = "cvt-checkmark-" + cvtClickedDataSet;
            const cvtCheckmark = $('[name="'  + cvtSpanName + '"]');

            const getcvtClickedClassList = cvtClicked.classList;

            // CVT needs to be unselected
            if(getcvtClickedClassList.value.includes("cvt-selected"))
            {
                cvtCheckmark[0].style.display = 'none';
                cvtClicked.classList.remove("cvt-selected")
            }
            // CVT needs to be selected
            else {
                // Remove preveiously selected CVT and select the newly clicked CVT
                const cvtPreviouslySelected = document.getElementsByClassName("cvt-selected")

                if(cvtPreviouslySelected.length > 0)
                {
                    const cvtcvtPreviouslySelectedDataSet = cvtPreviouslySelected[0].dataset.customVehicleType;
                    const cvtcvtPreviouslySelectedSpanName = "cvt-checkmark-" + cvtcvtPreviouslySelectedDataSet;
                    const cvtcvtPreviouslySelectedCheckmark = $('[name="'  + cvtcvtPreviouslySelectedSpanName + '"]');

                    cvtcvtPreviouslySelectedCheckmark[0].style.display = 'none';
                    cvtPreviouslySelected[0].classList.remove("cvt-selected");
                }

                cvtCheckmark[0].style.display = '';
                cvtClicked.classList.add("cvt-selected")
            }

            // Update filters
            //cvtSelectedResetOtherFilters();
            var vehicleSearch = $(this).closest('.bdt .vehicle_search');

            vehicleSearch.find('input[name=fullTextSearch]').val('');
            vehicleSearch.find('select[name=company]').val('');
            vehicleSearch.find('select[name=vehicleState]').val('');
            vehicleSearch.find('select[name=make]').val('');
            vehicleSearch.find('select[name=model]').val('');
            vehicleSearch.find('select[name=bodyType]').val('');
            vehicleSearch.find('select[name=productType]').val('');
            vehicleSearch.find('select[name=priceType]').val('');
            vehicleSearch.find('select[name=propellant]').val('');
            vehicleSearch.find('select[name=priceMinMax]').val('');
            vehicleSearch.find('select[name=priceMinMax]').val('');
            vehicleSearch.find('select[name=consumptionMin]').val('');
            vehicleSearch.find('select[name=consumptionMax]').val('');
			//jlk
            vehicleSearch.find('select[name=electricRangeMin]').val('');
            vehicleSearch.find('select[name=electricRangeMax]').val('');			

            bdt.ReloadUserFilterSelection(false);
        })
        .on('blur', '.fullTextSearch', function(){

            bdt.ReloadUserFilterSelection(false);
        })
		

//dynamic scroll pagination
$(document).ready(function() {
  let button = $('.paging-button-scroll')[0];
  let observer = null;
  let initCheckInterval = null;

  function handleButtonClick() {
    // Only allow pagination if initialization is complete
    if (typeof bdt !== 'undefined' && biltorvetInitializationComplete) {
      bdt.PagingFetchMore();
    }
  }

  function handleIntersection(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        handleButtonClick();
      }
    });
  }

  function updateObserverTarget() {
    const newButton = $('.paging-button-scroll')[0];
    if (newButton !== button) {
      // If the button has changed, disconnect the previous observer
      if (observer) {
        observer.disconnect();
        observer = null;
      }
      button = newButton;
      if (button) {
        // Only start observing if initialization is complete
        if (biltorvetInitializationComplete) {
          observer = new IntersectionObserver(handleIntersection, {
            threshold: 0.5,
          });
          observer.observe(button);
        }
      }
    }
  }

  function waitForInitialization() {
    // Check every 100ms if initialization is complete
    initCheckInterval = setInterval(function() {
      if (biltorvetInitializationComplete) {
        clearInterval(initCheckInterval);
        updateObserverTarget();
        $(window).on('scroll', updateObserverTarget);
        $(button).on('click', handleButtonClick);
      }
    }, 100);
  }

  // Start checking for initialization completion
  waitForInitialization();
});


});