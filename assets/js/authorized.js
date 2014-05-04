console.log = (console.log) ? console.log : function() {};
var clock;
var timeSet = 1500;
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
            
            clock.setTime(timeSet);
            clock.start();
        }
    });
    request.fail(function( jqXHR, textStatus ) {
        console.log( "Request failed: " + textStatus );
        return false;
    });
}

function getCookie(name) {
    var value = "; " + document.cookie;
    var parts = value.split("; " + name + "=");
    if (parts.length == 2) return parts.pop().split(";").shift();
}