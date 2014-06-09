<div id="testDiv"></div>
<input type="text" value="2012-09-28 08:24:00" id="testInput"/>
<button id="addDiv">Add Div</button>
<script type="text/javascript">
    jQuery(function(){
       jQuery("#addDiv").click(function(){
           jQuery("#testDiv").text(time_difference_seconds(jQuery("#testInput").val()));
       })
    });

    function time_difference_seconds(date1){
        var difference = null;
        if (empty(date1)) {
            return "No date provided";
        }

        var periods = new Array("s", "m", "h", "day", "week", "month", "year", "decade");
        var lengths = new Array("60", "60", "24", "7", "4.35", "12", "10");

        var split1 = date1.split(' ');
        var datestr = split1[0].split('-');
        var timestr = split1[1].split(':');
        var serverdateobj = new Date(datestr[0], parseInt(datestr[1], 10) - 1, datestr[2], timestr[0], timestr[1], timestr[2], 0);
        var serverdateobj1 = new Date(datestr[0], (parseInt(datestr[1], 10) - 1).toString(), datestr[2], timestr[0], (parseInt(timestr[1]) - serverdateobj.getTimezoneOffset()), timestr[2], 0);

        var now = time();
        var unix_date = Math.round(parseInt(serverdateobj1.getTime()) / 1000);

        var currDateTimeObj = new Date();
        var curr_timestamp = (currDateTimeObj.getTime()- currDateTimeObj.getTimezoneOffset())/1000;
        var time_difference_in_sec = Math.round(curr_timestamp - unix_date);
        console.log(unix_date);
        console.log(curr_timestamp);
        console.log(time_difference_in_sec);

        return time_difference_in_sec;
    }
</script>