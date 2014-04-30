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
});

function startTimer(){
        clock.start();
}

function stopTimer(){
        clock.stop();
}

function resetTimer(){
        clock.setTime(timeSet);
}

function setTimer(time){
        timeSet = time;
        stopTimer();
        resetTimer();
}

function getCookie(name) {
  var value = "; " + document.cookie;
  var parts = value.split("; " + name + "=");
  if (parts.length == 2) return parts.pop().split(";").shift();
}