jQuery(document).ready(function(){
	jQuery.ajax({
		url:AD.base_url+'index.php/dashboard/loadDashboardAnalytics',
		dataType: 'json',
		success: function(result){
			if(result && result.response){
				var pageView = result.pageView;
				var visits = result.visits;
				var analyticHtml = "";
				analyticHtml += "<div>Page view: "+pageView[0]+"</div>"; 
				analyticHtml += "<div>Visits: "+visits[0]+"</div>";
				jQuery(".analyticsContent").html(analyticHtml).removeClass('loader');
				
			}
		}
	});


});
