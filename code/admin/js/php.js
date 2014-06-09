
function empty (v) {
    var key;
    if (v === "" || v === 0 || v === "0" || v === null || v === false || typeof v === 'undefined') {
        return true;
    }
    if (v.length === 0) {
        return true;
    }
    if (typeof v === 'object') {
        for (key in v) {
            return false;
        }
        return true;
    }
    return false;
}

function time () {
    // Return current UNIX timestamp
    // *     example 1: timeStamp = time();
    // *     results 1: timeStamp > 1000000000 && timeStamp < 2000000000
    return Math.floor(new Date().getTime() / 1000);
}

function strtotime (str, now) {
    // Convert string representation of date and time to a timestamp
    // %        note 1: Examples all have a fixed timestamp to prevent tests to fail because of variable time(zones)
    // *     example 1: strtotime('+1 day', 1129633200);
    // *     returns 1: 1129719600
    // *     example 2: strtotime('+1 week 2 days 4 hours 2 seconds', 1129633200);
    // *     returns 2: 1130425202
    // *     example 3: strtotime('last month', 1129633200);
    // *     returns 3: 1127041200
    // *     example 4: strtotime('2009-05-04 08:30:00');
    // *     returns 4: 1241418600
    var i, match, s, strTmp = '',
        parse = '';

    strTmp = str;
    strTmp = strTmp.replace(/\s{2,}|^\s|\s$/g, ' '); // unecessary spaces
    strTmp = strTmp.replace(/[\t\r\n]/g, ''); // unecessary chars
    if (strTmp == 'now') {
        return (new Date()).getTime() / 1000; // Return seconds, not milli-seconds
    } else if (!isNaN(parse = Date.parse(strTmp))) {
        return (parse / 1000);
    } else if (now) {
        now = new Date(now * 1000); // Accept PHP-style seconds
    } else {
        now = new Date();
    }

    strTmp = strTmp.toLowerCase();

    var __is = {
        day: {
            'sun': 0,
            'mon': 1,
            'tue': 2,
            'wed': 3,
            'thu': 4,
            'fri': 5,
            'sat': 6
        },
        mon: {
            'jan': 0,
            'feb': 1,
            'mar': 2,
            'apr': 3,
            'may': 4,
            'jun': 5,
            'jul': 6,
            'aug': 7,
            'sep': 8,
            'oct': 9,
            'nov': 10,
            'dec': 11
        }
    };

    var process = function (m) {
        var ago = (m[2] && m[2] == 'ago');
        var num = (num = m[0] == 'last' ? -1 : 1) * (ago ? -1 : 1);

        switch (m[0]) {
            case 'last':
            case 'next':
                switch (m[1].substring(0, 3)) {
                    case 'yea':
                        now.setFullYear(now.getFullYear() + num);
                        break;
                    case 'mon':
                        now.setMonth(now.getMonth() + num);
                        break;
                    case 'wee':
                        now.setDate(now.getDate() + (num * 7));
                        break;
                    case 'day':
                        now.setDate(now.getDate() + num);
                        break;
                    case 'hou':
                        now.setHours(now.getHours() + num);
                        break;
                    case 'min':
                        now.setMinutes(now.getMinutes() + num);
                        break;
                    case 'sec':
                        now.setSeconds(now.getSeconds() + num);
                        break;
                    default:
                        var day;
                        if (typeof(day = __is.day[m[1].substring(0, 3)]) != 'undefined') {
                            var diff = day - now.getDay();
                            if (diff == 0) {
                                diff = 7 * num;
                            } else if (diff > 0) {
                                if (m[0] == 'last') {
                                    diff -= 7;
                                }
                            } else {
                                if (m[0] == 'next') {
                                    diff += 7;
                                }
                            }
                            now.setDate(now.getDate() + diff);
                        }
                }
                break;

            default:
                if (/\d+/.test(m[0])) {
                    num *= parseInt(m[0], 10);

                    switch (m[1].substring(0, 3)) {
                        case 'yea':
                            now.setFullYear(now.getFullYear() + num);
                            break;
                        case 'mon':
                            now.setMonth(now.getMonth() + num);
                            break;
                        case 'wee':
                            now.setDate(now.getDate() + (num * 7));
                            break;
                        case 'day':
                            now.setDate(now.getDate() + num);
                            break;
                        case 'hou':
                            now.setHours(now.getHours() + num);
                            break;
                        case 'min':
                            now.setMinutes(now.getMinutes() + num);
                            break;
                        case 'sec':
                            now.setSeconds(now.getSeconds() + num);
                            break;
                    }
                } else {
                    return false;
                }
                break;
        }
        return true;
    };

    match = strTmp.match(/^(\d{2,4}-\d{2}-\d{2})(?:\s(\d{1,2}:\d{2}(:\d{2})?)?(?:\.(\d+))?)?$/);
    if (match != null) {
        if (!match[2]) {
            match[2] = '00:00:00';
        } else if (!match[3]) {
            match[2] += ':00';
        }

        s = match[1].split(/-/g);

        for (i in __is.mon) {
            if (__is.mon[i] == s[1] - 1) {
                s[1] = i;
            }
        }
        s[0] = parseInt(s[0], 10);

        s[0] = (s[0] >= 0 && s[0] <= 69) ? '20' + (s[0] < 10 ? '0' + s[0] : s[0] + '') : (s[0] >= 70 && s[0] <= 99) ? '19' + s[0] : s[0] + '';
        return parseInt(this.strtotime(s[2] + ' ' + s[1] + ' ' + s[0] + ' ' + match[2]) + (match[4] ? match[4] / 1000 : ''), 10);
    }

    var regex = '([+-]?\\d+\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday)' + '|(last|next)\\s' + '(years?|months?|weeks?|days?|hours?|min|minutes?|sec|seconds?' + '|sun\\.?|sunday|mon\\.?|monday|tue\\.?|tuesday|wed\\.?|wednesday' + '|thu\\.?|thursday|fri\\.?|friday|sat\\.?|saturday))' + '(\\sago)?';

    match = strTmp.match(new RegExp(regex, 'gi')); // Brett: seems should be case insensitive per docs, so added 'i'
    if (match == null) {
        return false;
    }

    for (i = 0; i < match.length; i++) {
        if (!process(match[i].split(' '))) {
            return false;
        }
    }

    return (now.getTime() / 1000);
};

function date (format, timestamp) {

    // +      input by: Martin
    // +      input by: Alex Wilson
    // %        note 1: Uses global: php_js to store the default timezone
    // %        note 2: Although the function potentially allows timezone info (see notes), it currently does not set
    // %        note 2: per a timezone specified by date_default_timezone_set(). Implementers might use
    // %        note 2: this.php_js.currentTimezoneOffset and this.php_js.currentTimezoneDST set by that function
    // %        note 2: in order to adjust the dates in this function (or our other date functions!) accordingly
    // *     example 1: date('H:m:s \\m \\i\\s \\m\\o\\n\\t\\h', 1062402400);
    // *     returns 1: '09:09:40 m is month'
    // *     example 2: date('F j, Y, g:i a', 1062462400);
    // *     returns 2: 'September 2, 2003, 2:26 am'
    // *     example 3: date('Y W o', 1062462400);
    // *     returns 3: '2003 36 2003'
    // *     example 4: x = date('Y m d', (new Date()).getTime()/1000);
    // *     example 4: (x+'').length == 10 // 2009 01 09
    // *     returns 4: true
    // *     example 5: date('W', 1104534000);
    // *     returns 5: '53'
    // *     example 6: date('B t', 1104534000);
    // *     returns 6: '999 31'
    // *     example 7: date('W U', 1293750000.82); // 2010-12-31
    // *     returns 7: '52 1293750000'
    // *     example 8: date('W', 1293836400); // 2011-01-01
    // *     returns 8: '52'
    // *     example 9: date('W Y-m-d', 1293974054); // 2011-01-02
    // *     returns 9: '52 2011-01-02'
    var that = this,
        jsdate, f, formatChr = /\\?([a-z])/gi,
        formatChrCb,
        // Keep this here (works, but for code commented-out
        // below for file size reasons)
        //, tal= [],
        _pad = function (n, c) {
            if ((n = n + '').length < c) {
                return new Array((++c) - n.length).join('0') + n;
            }
            return n;
        },
        txt_words = ["Sun", "Mon", "Tues", "Wednes", "Thurs", "Fri", "Satur", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    formatChrCb = function (t, s) {
        return f[t] ? f[t]() : s;
    };
    f = {
        // Day
        d: function () { // Day of month w/leading 0; 01..31
            return _pad(f.j(), 2);
        },
        D: function () { // Shorthand day name; Mon...Sun
            return f.l().slice(0, 3);
        },
        j: function () { // Day of month; 1..31
            return jsdate.getDate();
        },
        l: function () { // Full day name; Monday...Sunday
            return txt_words[f.w()] + 'day';
        },
        N: function () { // ISO-8601 day of week; 1[Mon]..7[Sun]
            return f.w() || 7;
        },
        S: function () { // Ordinal suffix for day of month; st, nd, rd, th
            var j = f.j();
            return j > 4 && j < 21 ? 'th' : {1: 'st', 2: 'nd', 3: 'rd'}[j % 10] || 'th';
        },
        w: function () { // Day of week; 0[Sun]..6[Sat]
            return jsdate.getDay();
        },
        z: function () { // Day of year; 0..365
            var a = new Date(f.Y(), f.n() - 1, f.j()),
                b = new Date(f.Y(), 0, 1);
            return Math.round((a - b) / 864e5) + 1;
        },

        // Week
        W: function () { // ISO-8601 week number
            var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3),
                b = new Date(a.getFullYear(), 0, 4);
            return _pad(1 + Math.round((a - b) / 864e5 / 7), 2);
        },

        // Month
        F: function () { // Full month name; January...December
            return txt_words[6 + f.n()];
        },
        m: function () { // Month w/leading 0; 01...12
            return _pad(f.n(), 2);
        },
        M: function () { // Shorthand month name; Jan...Dec
            return f.F().slice(0, 3);
        },
        n: function () { // Month; 1...12
            return jsdate.getMonth() + 1;
        },
        t: function () { // Days in month; 28...31
            return (new Date(f.Y(), f.n(), 0)).getDate();
        },

        // Year
        L: function () { // Is leap year?; 0 or 1
            return new Date(f.Y(), 1, 29).getMonth() === 1 | 0;
        },
        o: function () { // ISO-8601 year
            var n = f.n(),
                W = f.W(),
                Y = f.Y();
            return Y + (n === 12 && W < 9 ? -1 : n === 1 && W > 9);
        },
        Y: function () { // Full year; e.g. 1980...2010
            return jsdate.getFullYear();
        },
        y: function () { // Last two digits of year; 00...99
            return (f.Y() + "").slice(-2);
        },

        // Time
        a: function () { // am or pm
            return jsdate.getHours() > 11 ? "pm" : "am";
        },
        A: function () { // AM or PM
            return f.a().toUpperCase();
        },
        B: function () { // Swatch Internet time; 000..999
            var H = jsdate.getUTCHours() * 36e2,
                // Hours
                i = jsdate.getUTCMinutes() * 60,
                // Minutes
                s = jsdate.getUTCSeconds(); // Seconds
            return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3);
        },
        g: function () { // 12-Hours; 1..12
            return f.G() % 12 || 12;
        },
        G: function () { // 24-Hours; 0..23
            return jsdate.getHours();
        },
        h: function () { // 12-Hours w/leading 0; 01..12
            return _pad(f.g(), 2);
        },
        H: function () { // 24-Hours w/leading 0; 00..23
            return _pad(f.G(), 2);
        },
        i: function () { // Minutes w/leading 0; 00..59
            return _pad(jsdate.getMinutes(), 2);
        },
        s: function () { // Seconds w/leading 0; 00..59
            return _pad(jsdate.getSeconds(), 2);
        },
        u: function () { // Microseconds; 000000-999000
            return _pad(jsdate.getMilliseconds() * 1000, 6);
        },

        // Timezone
        e: function () { // Timezone identifier; e.g. Atlantic/Azores, ...
            // The following works, but requires inclusion of the very large
            // timezone_abbreviations_list() function.
            /*              return this.date_default_timezone_get();
             */
            throw 'Not supported (see source code of date() for timezone on how to add support)';
        },
        I: function () { // DST observed?; 0 or 1
            // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
            // If they are not equal, then DST is observed.
            var a = new Date(f.Y(), 0),
                // Jan 1
                c = Date.UTC(f.Y(), 0),
                // Jan 1 UTC
                b = new Date(f.Y(), 6),
                // Jul 1
                d = Date.UTC(f.Y(), 6); // Jul 1 UTC
            return 0 + ((a - c) !== (b - d));
        },
        O: function () { // Difference to GMT in hour format; e.g. +0200
            var tzo = jsdate.getTimezoneOffset(),
                a = Math.abs(tzo);
            return (tzo > 0 ? "-" : "+") + _pad(Math.floor(a / 60) * 100 + a % 60, 4);
        },
        P: function () { // Difference to GMT w/colon; e.g. +02:00
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2));
        },
        T: function () { // Timezone abbreviation; e.g. EST, MDT, ...
            // The following works, but requires inclusion of the very
            // large timezone_abbreviations_list() function.
            /*              var abbr = '', i = 0, os = 0, default = 0;
             if (!tal.length) {
             tal = that.timezone_abbreviations_list();
             }
             if (that.php_js && that.php_js.default_timezone) {
             default = that.php_js.default_timezone;
             for (abbr in tal) {
             for (i=0; i < tal[abbr].length; i++) {
             if (tal[abbr][i].timezone_id === default) {
             return abbr.toUpperCase();
             }
             }
             }
             }
             for (abbr in tal) {
             for (i = 0; i < tal[abbr].length; i++) {
             os = -jsdate.getTimezoneOffset() * 60;
             if (tal[abbr][i].offset === os) {
             return abbr.toUpperCase();
             }
             }
             }
             */
            return 'UTC';
        },
        Z: function () { // Timezone offset in seconds (-43200...50400)
            return -jsdate.getTimezoneOffset() * 60;
        },

        // Full Date/Time
        c: function () { // ISO-8601 date.
            return 'Y-m-d\\Th:i:sP'.replace(formatChr, formatChrCb);
        },
        r: function () { // RFC 2822
            return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb);
        },
        U: function () { // Seconds since UNIX epoch
            return jsdate.getTime() / 1000 | 0;
        }
    };
    this.date = function (format, timestamp) {
        that = this;
        jsdate = ((typeof timestamp === 'undefined') ? new Date() : // Not provided
            (timestamp instanceof Date) ? new Date(timestamp) : // JS Date()
                new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
            );
        return format.replace(formatChr, formatChrCb);
    };
    return this.date(format, timestamp);
};


function round (val, precision, mode) {
    // % note 1: Great work. Ideas for improvement:
    // % note 1: - code more compliant with developer guidelines
    // % note 1: - for implementing PHP constant arguments look at
    // % note 1: the pathinfo() function, it offers the greatest
    // % note 1: flexibility & compatibility possible
    // * example 1: round(1241757, -3);
    // * returns 1: 1242000
    // * example 2: round(3.6);
    // * returns 2: 4
    // * example 3: round(2.835,2);
    // * returns 3: 2.84

    /*Declare Variables
     * Retval - temporay holder of the value to be returned
     * V - String representation of val
     * integer - Integer portion of val
     * decimal - decimal portion of val
     * decp - characterindex of . [decimal point] inV
     * negative- was val a negative value?
     *
     * ROUND_HALF_OE - Rounding function for ROUND_HALF_ODD and ROUND_HALF_EVEN
     * ROUND_HALF_UD - Rounding function for ROUND_HALF_UP and ROUND_HALF_DOWN
     * ROUND_HALF - Primary function for round half rounding modes
     */
    var RetVal=0,V='',integer='',decimal='',decp=0,negative=false;
    var ROUND_HALF_OE = function(DtR,DtLa,even){
        if(even === true){
            if(DtLa == 50){
                if((DtR % 2) === 1){
                    if(DtLa >= 5){
                        DtR+=1;
                    }else{
                        DtR-=1;
                    }
                }
            }else if(DtLa >= 5){
                DtR+=1;
            }
        }else{
            if(DtLa == 5){
                if((DtR % 2) === 0){
                    if(DtLa >= 5){
                        DtR+=1;
                    }else{
                        DtR-=1;
                    }
                }
            }else if(DtLa >= 5){
                DtR+=1;
            }
        }
        return DtR;
    };
    var ROUND_HALF_UD = function(DtR,DtLa,up){
        if(up === true){
            if(DtLa>=5){
                DtR+=1;
            }
        }else{
            if(DtLa>5){
                DtR+=1;
            }
        }
        return DtR;
    };
    var ROUND_HALF = function(Val,Decplaces,mode){
        /*Declare variables
         *V - string representation of Val
         *Vlen - The length of V - used only when rounding intgerers
         *VlenDif - The difference between the lengths of the original V
         * and the V after being truncated
         *decp - character in index of . [decimal place] in V
         *integer - Integr protion of Val
         *decimal - Decimal portion of Val
         *DigitToRound - The digit to round
         *DigitToLookAt- The digit to comapre when rounding
         *
         *round - A function to do the rounding
         */
        var V = Val.toString(),Vlen=0,VlenDif=0;
        var decp = V.indexOf('.');
        var DigitToRound = 0,DigitToLookAt = 0;
        var integer='',decimal='';
        var round = null,bool=false;
        switch(mode){
            case 'up':
                bool = true;
            case 'down':
                round = ROUND_HALF_UD;
                break;
            case 'even':
                bool = true;
            case 'odd':
                round = ROUND_HALF_OE;
                break;
        }
        if (Decplaces < 0){ //Int round
            Vlen=V.length;
            Decplaces = Vlen + Decplaces;
            DigitToLookAt = Number(V.charAt(Decplaces));
            DigitToRound = Number(V.charAt(Decplaces-1));
            DigitToRound = round(DigitToRound,DigitToLookAt,bool);
            V = V.slice(0,Decplaces-1);
            VlenDif = Vlen-V.length-1;
            if (DigitToRound == 10){
                V = String(Number(V)+1)+"0";
            }else{
                V+=DigitToRound;
            }
            V = Number(V)*(Math.pow(10,VlenDif));
        }else if(Decplaces > 0){
            integer=V.slice(0,decp);
            decimal=V.slice(decp+1);
            DigitToLookAt = Number(decimal.charAt(Decplaces));
            DigitToRound = Number(decimal.charAt(Decplaces-1));
            DigitToRound = round(DigitToRound,DigitToLookAt,bool);
            decimal=decimal.slice(0,Decplaces-1);
            if(DigitToRound==10){
                V=Number(integer+'.'+decimal)+(1*(Math.pow(10,(0-decimal.length))));
            }else{
                V=Number(integer+'.'+decimal+DigitToRound);
            }
        }else{
            integer=V.slice(0,decp);
            decimal=V.slice(decp+1);
            DigitToLookAt = Number(decimal.charAt(Decplaces));
            DigitToRound = Number(integer.charAt(integer.length-1));
            DigitToRound = round(DigitToRound,DigitToLookAt,bool);
            decimal='0';
            integer = integer.slice(0,integer.length-1);
            if(DigitToRound==10){
                V=Number(integer)+1;
            }else{
                V=Number(integer+DigitToRound);
            }
        }
        return V;
    };

    //precision optional - defaults 0
    if (typeof precision == 'undefined') {
        precision = 0;
    }
    //mode optional - defaults round half up
    if (typeof mode == 'undefined') {
        mode = 'PHP_ROUND_HALF_UP';
    }

    if (val < 0){ //Remember if val is negative
        negative = true;
    }else{
        negative = false;
    }

    V = Math.abs(val).toString(); //Take a string representation of val
    decp = V.indexOf('.'); //And locate the decimal point
    if ((decp == -1) && (precision >=0)){
        /* If there is no deciaml point and the precision is greater than 0
         * there is no need to round, return val
         */
        return val;
    }else{
        if (decp == -1){
            //There are no decimals so intger=V and decimal=0
            integer = V;
            decimal = '0';
        }else{
            //Otherwise we have to split the decimals from the integer
            integer = V.slice(0,decp);
            if(precision >= 0){
                //If the precision is greater than 0 then split the decimals from the integer
                //We truncate the decimals to a number of places equal to the precision requested+1
                decimal = V.substr(decp+1,precision+1);
            }else{
                //If the precision is less than 0 ignore the decimals - set to 0
                decimal = '0';
            }
        }
        if ((precision > 0) && (precision >= decimal.length)){
            /*If the precision requested is more decimal places than already exist
             *there is no need to round - return val
             */
            return val;
        }else if ((precision < 0) && (Math.abs(precision) >= integer.length)){
            /*If the precison is less than 0, and is greater than than the
             *number of digits in integer, return 0 - mimics PHP
             */
            return 0;
        }
        val = Number(integer+'.'+decimal); //After sanitizing recreate val,
    }

    //Call approriate function based on passed mode, fall through for integer constants
    //INTEGER VALUES MAY NOT BE CORRECT, UNABLE TO VERIFY WITHOUT PHP 5.3//
    switch (mode){
        case 0:
        case 'PHP_ROUND_HALF_UP':
            RetVal = ROUND_HALF(val,precision,'up');
            break;
        case 1:
        case 'PHP_ROUND_HALF_DOWN':
            RetVal = ROUND_HALF(val, precision,'down');
            break;
        case 2:
        case 'PHP_ROUND_HALF_EVEN':
            RetVal = ROUND_HALF(val,precision,'even');
            break;
        case 3:
        case 'PHP_ROUND_HALF_ODD':
            RetVal = ROUND_HALF(val,precision,'odd');
            break;
    }
    if(negative){
        return 0-RetVal;
    }else{
        return RetVal;
    }
};

