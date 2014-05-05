console.log = (console.log) ? console.log : function() {};
var clock;
var timeSet = 1500;
var alarmID = null;

$(function(){
    $('.options .btn').button();
    $('.options .btn').click(function(){
        setTimer($(this).find("input").attr("data-time-value"));
    });

    clock = $('.countDown').FlipClock(timeSet, {
        countdown: true,
        clockFace: 'MinuteCounter',
        autoStart: false
    });
    
    checkForRunningAlarm();
});

function updateSync(isSynced){
    var unsyncedMessage = "The alarm has not synced with your fitbit device.";
    var unsyncedClass = "palette-alizarin";
    var syncedMessage = "The alarm is synced with your fitbit device.";
    var syncedClass = "palette-peter-river";
    
    if(isSynced){
        $("#syncWatch").removeClass(unsyncedClass).addClass(syncedClass);
        $("#syncWatch").attr("title",syncedMessage);
        $("#syncWatch").attr("data-original-title",syncedMessage);
    }else{
        $("#syncWatch").removeClass(syncedClass).addClass(unsyncedClass);
        $("#syncWatch").attr("title",unsyncedMessage);
        $("#syncWatch").attr("data-original-title",unsyncedMessage);
    }
}

function startTimer(){
    if(!clock.running){
        var requestURL = "/start";
        
        var request = $.ajax({
            type: "POST",
            url: requestURL,
            data: {seconds:clock.time.time},
            dataType: "json"
        });
        request.done(function(result) {
            if(result.success){
                clock.start();
                alarmID=result.alarmID;
                checkIfAlarmIsSynced();
            }
        });
        request.fail(function( jqXHR, textStatus ) {
            console.log( "Request failed: " + textStatus );
            return false;
        });
    }
}

function stopTimer(){
    if(clock.running){
        var requestURL = "/stop";
        
        var request = $.ajax({
            type: "POST",
            url: requestURL,
            dataType: "json"
        });
        request.done(function(result) {
            if(result.success){
                clock.stop();
                alarmID=null;
            }
        });
        request.fail(function( jqXHR, textStatus ) {
            console.log( "Request failed: " + textStatus );
            return false;
        });
    }
}

function resetTimer(){
    clock.setTime(timeSet);    
}

function setTimer(time){
    timeSet = time;
    stopTimer();
    resetTimer();
}

function checkForRunningAlarm(){
    var requestURL = "/check";
        
    var request = $.ajax({
        type: "POST",
        url: requestURL,
        dataType: "json"
    });
    request.done(function(result) {
        if(result.success){
            alarmID=result.alarmID;
            updateSync(result.isSynced);
            var nowTime = new Date();
            var offset = nowTime.getTimezoneOffset();
            var hourOffset = convertOffset(offset/-60);
            var alarmOffset = result.time.substring(5);
            var alarmTime = result.time.substring(0,5) + ":00";
            var currentHour = nowTime.getHours();
            if(currentHour.length == 1){
                currentHour = 0 + "" + currentHour;
            }
            var currentMinutes = nowTime.getMinutes();
            if(currentMinutes.length == 1){
                currentMinutes = 0 + "" + currentMinutes;
            }
            
            var currentSeconds = nowTime.getSeconds();
            if(currentSeconds.length == 1){
                currentSeconds = 0 + "" + currentSeconds;
            }
            
            var currentTime = currentHour + ":" + currentMinutes + ":" + currentSeconds;
            
            var diff = 0;
            if(alarmOffset == hourOffset){
                diff = secondsDiff(alarmTime, currentTime);
            }else{
                diff = secondsDiffConvertTimezones(alarmTime, alarmOffset, currentTime, hourOffset);
            }
            
            console.log(diff);
            
            if(diff<0){
                //If the alarm has passed delete the alarm
                clock.start();
                stopTimer();
            }else{
                clock.setTime(diff);
                clock.start();
                checkIfAlarmIsSynced();
            }            
        }
    });
    request.fail(function( jqXHR, textStatus ) {
        console.log( "Request failed: " + textStatus );
        return false;
    });
}

function checkIfAlarmIsSynced(){
    if(alarmID===null)
        return;
    
    var waitTime = 1000 * 60;
    var t = setTimeout(function(){
        var requestURL = "/sync";
        
        var request = $.ajax({
            type: "POST",
            url: requestURL,
            dataType: "json"
        });
        request.done(function(result) {
            if(result.success){
                if(result.isSynced){
                    updateSync(true);
                }else{
                    checkIfAlarmIsSynced();
                }
            }
        });
        request.fail(function( jqXHR, textStatus ) {
            console.log( "Request failed: " + textStatus );
            return false;
        });
    }, waitTime);
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}

function convertOffset(gmt_offset) {
    var time = gmt_offset.toString().split(".");
    var hour = parseInt(time[0]);
    var negative = hour < 0 ? true : false;
    hour = Math.abs(hour) < 10 ? "0" + Math.abs(hour) : Math.abs(hour);
    hour = negative ? "-" + hour : "+" + hour;
  return time[1] ? hour+":"+(time[1]*6).toString() : hour + ":00";
}

function secondsDiffConvertTimezones(time1, offset1, time2, offset2){
    return secondsDiff(
        timeApplyOffset(time1, offset1),
        timeApplyOffset(time2, offset2)
    );
}

function secondsDiff(time1, time2){
    var t1 = timeToSeconds(time1);
    var t2 = timeToSeconds(time2);
    return t1 - t2;
}

function timeApplyOffset(time, offset){
    var hours = parseInt(time.substring(0,2));    
    var minutes = parseInt(time.substring(3,5));
    var seconds = time.substring(6,8);
    
    var hourOffset = parseInt(offset.substring(0,offset.indexOf(':')));
    var minuteOffset = parseInt(offset.indexOf(':')+1);
    
    hours+=hourOffset;
    minutes+=minuteOffset;
    
    if(minutes>59){
        hours+=1;
        minutes-=60;
    }
    
    hours = "00" + parseInt(hours);
    minutes = "00" + parseInt(minutes);
    seconds = "00" + seconds;
    
    time = hours.substring(hours.length-2) + ":" + minutes.substring(minutes.length-2) + ":" + seconds.substring(seconds.length-2);
    return time;
}

function timeToSeconds(time){
    var hours = parseInt(time.substring(0,2));    
    var minutes = parseInt(time.substring(3,5));
    var seconds = parseInt(time.substring(6,8));
    
    var minutes = (hours*60) + minutes;
    return minutes * 60 + seconds;   
}