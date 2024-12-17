

(function () {

  var queue = Promise.resolve();
  // Starting point..


  var queryString = window.location.search;
  var urlParams = new URLSearchParams(queryString);
  var report = urlParams.get("report") === "true";
  var global_from_date,global_to_date;

  if (report) {
    (function (w, d, s, g, js, fjs) {
      g = w.gapi || (w.gapi = {});
      g.analytics = {
        q: [], ready: function (cb) {
          this.q.push(cb)
        }
      };
      js = d.createElement(s);
      fjs = d.getElementsByTagName(s)[0];
      js.src = 'https://apis.google.com/js/platform.js';
      fjs.parentNode.insertBefore(js, fjs);
      js.onload = function () {
        g.load('analytics')
      };
    }(window, document, 'script'));

    google.charts.load('current', {'packages': ['corechart', 'line', 'table']});
    gapi.analytics.ready(function () {

    // Put your application code here...

      // Register on authorization success event

      gapi.analytics.auth.on('signIn', function (response) {
        // if (typeof ga_reports.ga_view_id === 'undefined') {
        //dateRangeSelector.execute();
      });

      // Call the authenticate function that will take care to authorize the user
      authenticate().then(r => {
        // Set default date range for the reports
        var fromDate = moment().subtract(30,'days').startOf('day').toDate();
        var toDate = moment().endOf('day').toDate();
        global_from_date = moment(fromDate).format('YYYY-MM-DD');
        global_to_date = moment(toDate).format('YYYY-MM-DD');

        //Display the date range selector
        jQuery('#daterange').daterangepicker({
          datepickerOptions : {
            numberOfMonths : 2,
            minDate: '-12M',
            maxDate: '+0',
            altFormat: "yyyy-mm-dd"
          }
        });

        jQuery('#daterange').daterangepicker("setRange",{start: fromDate, end: toDate });
        jQuery('#daterange').daterangepicker({change: function(event, data) {
            var dateRange = jQuery('#daterange').daterangepicker("getRange");
            global_from_date = moment(dateRange.start).format('YYYY-MM-DD');
            global_to_date = moment(dateRange.end).format('YYYY-MM-DD');
            refresh_report();
          }
        });

        refresh_report();
      });
    });
  }

  function refresh_report() {

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const currentPage = urlParams.get("page");

    if (typeof ga_reports === 'undefined' || typeof ga_reports.ga_view_id === 'undefined'){
      return;
    }
    if (currentPage === "wp-google-analytics-events") {
      displayGeneralReports();
    } else if (currentPage === "wp-google-analytics-events-click") {
      displayClicksReports();
    } else if (currentPage === "wp-google-analytics-events-scroll") {
       displayScrollReports();
    }
  }


  function redirect2AuthProxy() {
    var redirect_url = btoa(window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search);
    // we need to actually redirect to allow redirection to google
    window.location = 'https://auth.wpflow.com/ga/auth/' + redirect_url;
  }

  // This function authenticates the user. It first checks if the user is authenticated already in this session by checking
  // for client code. If there is no client code it redirects to the authentication proxy to get a client code

  const authenticate = async () => {

    const queryString = window.location.search;
    queryString;
    const urlParams = new URLSearchParams(queryString);

    if (sessionStorage.getItem('gaAccessToken')) {
      accessToken = sessionStorage.getItem('gaAccessToken');
      tokenCreated = sessionStorage.getItem('gaTokenCreated')
    } else if (urlParams.get('gaToken')) {
      accessToken = urlParams.get('gaToken');
      tokenCreated = urlParams.get('gaTokenCreated') * 1000; // to miliseconds
      sessionStorage.setItem('gaAccessToken', accessToken);
      sessionStorage.setItem('gaTokenCreated', tokenCreated);
    } else {
      accessToken = null;
      tokenCreated = null;
    }

    // First check if we have already an access token in the session.

    if (!accessToken) { // if there is no access token then let's get it from the proxy
      jQuery('.ga-reports-no-auth').toggle();
    } else { // There is a token, let's check if it is still valid.
             timeElapsed = (Date.now() - tokenCreated) / 1000; //to seconds
             timeRemaining = 3600 - timeElapsed;      // 3600 seconds TTL for access token
             if (timeRemaining < 120) {     //
                   sessionStorage.removeItem('gaAccessToken');
                   sessionStorage.removeItem('gaTokenCreated');
                   redirect2AuthProxy();
             } else if (typeof ga_reports === 'undefined' || typeof ga_reports.ga_view_id === 'undefined') {
                   await gapi.analytics.auth.authorize({
                       serverAuth: {
                           access_token: accessToken
                       }
                   });
                   // Token is valid, check if the view is selected, if not present the view form

                   var viewSelector = new gapi.analytics.ViewSelector({container: 'view-selector'});
                   jQuery('.ga-view-form').toggle();

                   viewSelector.execute();    // Run the view selector after a successful authentication

                   viewSelector.on('change', function (ids) {
                      jQuery('#ga-save-view').val(ids);
                   });
             } else {
                   jQuery('.ga-reports-section').css("display", "flex");
                   // Now authorize the user for analytics API given the access token to the gapi object
                   await gapi.analytics.auth.authorize({
                       serverAuth: {
                          access_token: accessToken
                       }
                   });
             }
    }

    jQuery('#ga-auth').on('click', function (e) {
      e.preventDefault();
      redirect2AuthProxy();
    });

  }

// Starting point..

  async function displayGeneralReports() {
    var allEventsReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], dimensions: [{
        name: 'ga:date',
      }],
    }

    var top10DownloadsReportRequest = {
      viewId: ga_reports.ga_view_id,  includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: 'ga:eventAction==Download', dimensions: [{
        name: 'ga:eventLabel',
      }], orderBys: [{
        fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    var top10EmailLinksReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: 'ga:eventAction==Email Link', dimensions: [{
        name: 'ga:eventLabel',
      }], orderBys: [{
        fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }


    var generalReportBatchRequests = [allEventsReportRequest, top10DownloadsReportRequest, top10EmailLinksReportRequest];
    reportResponse = await queryReports(generalReportBatchRequests);
    drawLineChart("All Event Clicks", "Date", "date", "Events", "number", reportResponse.result.reports[0].data.rows, 'all-events-chart');
    drawTableChart("Top 10 Downloads", "Top 10 Downloads", "string", "Downloads", "number", reportResponse.result.reports[1].data.rows, 'top10-downloads-chart');
    drawTableChart("Top 10 Email Links", "Top 10 Email Links", "string", "Clicks", "number", reportResponse.result.reports[2].data.rows, 'top10-email-links-chart');
  }

  async function displayClicksReports() {
    // Since there are other categories/labels/actions which are not related to clicks we need to filter the clicks only according to the
    // relevant defined categories/labels/actions. In case they are not defined we need to define a dummy filter

    var filterExpressionArray = new Array;
    var filterExpression = "";

    if (ga_reports.clicksCategories) filterExpressionArray.push(ga_reports.clicksCategories);
    if (ga_reports.clicksLabels) filterExpressionArray.push(ga_reports.clicksLabels);
    if (ga_reports.clicksActions) filterExpressionArray.push(ga_reports.clicksActions);
    if (filterExpression.length = 0) filterExpressionArray.push ("ga:eventCategory==-9999999"); // Dummy expression in case all are blanks

    var allClicksReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpressionArray.join(), dimensions: [{
        name: 'ga:date',
      }],
    }

    filterExpression = ga_reports.clicksCategories ? ga_reports.clicksCategories : "ga:eventCategory==-9999999";
    var top10ClicksByCategoryReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression , dimensions: [{
        name: 'ga:eventCategory',
      }], orderBys: [{
        fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    filterExpression = ga_reports.clicksLabels ? ga_reports.clicksLabels : "ga:eventLabel==-9999999";
    var top10ClicksByLabelReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression, dimensions: [{
        name: 'ga:eventLabel',
      }], orderBys: [{
        fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    filterExpression = ga_reports.clicksActions ? ga_reports.clicksActions : "ga:eventAction==-9999999";
    var top10ClicksByActionReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression, dimensions: [{
        name: 'ga:eventAction',
      }], orderBys: [{
         fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    var allClicksReportBatchRequests = [allClicksReportRequest, top10ClicksByCategoryReportRequest,top10ClicksByLabelReportRequest,top10ClicksByActionReportRequest];
    reportResponse = await queryReports(allClicksReportBatchRequests);
    drawLineChart("All Clicks", "Date", "date", "Events", "number", reportResponse.result.reports[0].data.rows, 'all-clicks-chart');
    drawTableChart("", "Top 10 Clicks by Category", "string", "Clicks", "number", reportResponse.result.reports[1].data.rows, 'top10-clicks-by-category-chart');
    drawTableChart("", "Top 10 Clicks by Label", "string", "Clicks", "number", reportResponse.result.reports[2].data.rows, 'top10-clicks-by-label-chart');
    drawTableChart("", "Top 10 Clicks by Action", "string", "Clicks", "number", reportResponse.result.reports[3].data.rows, 'top10-clicks-by-action-chart');
  }

  async function displayScrollReports() {
    // Since there are other categories/labels/actions which are not related to clicks we need to filter the clicks only according to the
    // relevant defined categories/labels/actions. In case they are not defined we need to define a dummy filter

    var filterExpressionArray = new Array;
    var filterExpression = "";

    if (ga_reports.scrollCategories) filterExpressionArray.push(ga_reports.scrollCategories);
    if (ga_reports.scrollLabels) filterExpressionArray.push(ga_reports.scrollLabels);
    if (ga_reports.scrollActions) filterExpressionArray.push(ga_reports.scrollActions);
    if (filterExpression.length = 0) filterExpressionArray.push ("ga:eventCategory==-9999999"); // Dummy expression in case all are blanks

    var allScrollEventsReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpressionArray.join(), dimensions: [{
        name: 'ga:date',
      }],
    }

    filterExpression = ga_reports.scrollCategories ? ga_reports.scrollCategories : "ga:eventCategory==-9999999";
    var top10ScrollByCategoryReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression , dimensions: [{
        name: 'ga:eventCategory',
      }], orderBys: [{
         fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    filterExpression = ga_reports.scrollLabels ? ga_reports.scrollLabels : "ga:eventLabel==-9999999";
    var top10ScrollByLabelReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression, dimensions: [{
        name: 'ga:eventLabel',
      }], orderBys: [{
         fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    filterExpression = ga_reports.scrollActions ? ga_reports.scrollActions : "ga:eventAction==-9999999";
    var top10ScrollByActionReportRequest = {
      viewId: ga_reports.ga_view_id, includeEmptyRows: true, dateRanges: [{
        startDate: global_from_date, endDate: global_to_date
      }], metrics: [{
        expression: 'ga:totalEvents'
      }], filtersExpression: filterExpression, dimensions: [{
        name: 'ga:eventAction',
      }], orderBys: [{
         fieldName: 'ga:totalEvents', orderType: 'VALUE', sortOrder: "DESCENDING"
      }], pageSize: 10
    }

    var allScrollReportBatchRequests = [allScrollEventsReportRequest, top10ScrollByCategoryReportRequest,top10ScrollByLabelReportRequest,top10ScrollByActionReportRequest];
    reportResponse = await queryReports(allScrollReportBatchRequests);
    drawLineChart("All Scroll Events", "Date", "date", "Events", "number", reportResponse.result.reports[0].data.rows, 'all-scroll-events-chart');
    drawTableChart("", "Top 10 Scroll Events by Category", "string", "Events", "number", reportResponse.result.reports[1].data.rows, 'top10-scroll-by-category-chart');
    drawTableChart("", "Top 10 Scroll Events by Label", "string", "Events", "number", reportResponse.result.reports[2].data.rows, 'top10-scroll-by-label-chart');
    drawTableChart("", "Top 10 Scroll Events by Action", "string", "Events", "number", reportResponse.result.reports[3].data.rows, 'top10-scroll-by-action-chart');
  }



// Query the API and print the results to the page.
  function queryReports(reportBatchRequests) {
    return gapi.client.request({
      path: '/v4/reports:batchGet', root: 'https://analyticsreporting.googleapis.com/', method: 'POST', body: {
        reportRequests: reportBatchRequests,
      }
    });
  }

  function toDate(stringDate) {
    var year = stringDate.substr(0, 4), month = stringDate.substr(4, 2) - 1,    // -1 as months are 0 indexed
      day = stringDate.substr(6, 2);
    var D = new Date(year, month, day);
    return (D.getFullYear() == year && D.getMonth() == month && D.getDate() == day) ? D : 'invalid date';
  }

  function drawLineChart(title, xAxisName, xAxisType, yAxisName, yAxisType, reportRows, chartContainer) {

    var data = new google.visualization.DataTable();

    data.addColumn(xAxisType, xAxisName);
    data.addColumn(yAxisType, yAxisName);
    if ((reportRows != null) && (reportRows != undefined) && (reportRows.length > 0)) {
      reportRows.forEach(row => data.addRow([toDate(row.dimensions[0]), parseInt(row.metrics[0].values[0])]));
    }
    var options = {
      //title: title,
      curveType: 'function', legend: {position: 'bottom', textStyle: {fontSize:12}},
    };

    var chart = new google.charts.Line(document.getElementById(chartContainer));
    chart.draw(data, google.charts.Line.convertOptions(options));
  }

  function drawTableChart(title, col1Name, col1Type, col2Name, col2Type, reportRows, chartContainer) {

    var data = new google.visualization.DataTable();


    var options;
    if ((reportRows != null) && (reportRows != undefined) && (reportRows.length > 0)) {
      data.addColumn(col1Type, col1Name);
      data.addColumn(col2Type, col2Name);
      data.setColumnProperty(0, 'className', 'ga-top-chart-text');
      reportRows.forEach(row => data.addRow([row.dimensions[0], parseInt(row.metrics[0].values[0])]));
      options = {
        showRowNumber: true, width: '100%', height: '100%',
      };
    } else {
      data.addColumn("string", col1Name);
      data.setColumnProperty(0, 'className', 'ga-top-chart-text-empty');
      data.addRows( [[ "No events found"]]);
      options = {
        showRowNumber: false, width: '100%', height: '391px', minWidth: '100px'
      };
    }



    var table = new google.visualization.Table(document.getElementById(chartContainer));
    table.draw(data, options);
  }


})();
