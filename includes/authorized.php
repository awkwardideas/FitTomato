<div class="pull-right"><a class="btn btn-warning" href="/deauthorize" role="button">Deauthorize</a></div>
<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="row">
			<div class="col-lg-12 pull-center">
				<div class="btn-group options" data-toggle="buttons">
					<label class="btn btn-lg btn-primary active">
						<input type="radio" name="options" id="pomodoro"  data-time-value="1500"> Pomodoro
					</label>
					<label class="btn btn-lg btn-primary">
						<input type="radio" name="options" id="shortBreak" data-time-value="300"> Short Break
					</label>
					<label class="btn btn-lg btn-primary">
						<input type="radio" name="options" id="longBreak" data-time-value="1800"> Long Break
					</label>
				</div>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="col-lg-12 pull-center">
				<div class="countDown"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 pull-center">
				<button type="button" class="btn btn-lg btn-success" onclick="startTimer()">
				<span class="glyphicon glyphicon-play"></span> Start
				</button>
				<button type="button" class="btn btn-lg btn-danger" onclick="stopTimer()">
				<span class="glyphicon glyphicon-stop"></span>  Stop
				</button>
				<button type="button" class="btn btn-lg btn-default" onclick="resetTimer()">
				<span class="glyphicon glyphicon-stop"></span>  Reset
				</button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var clock;
	var fitbit;
	var timeSet = 1500;
	$(function(){
		fitbit = $.fn.Fitbit(getCookie("oauth_token"), getCookie("oauth_verifier"),{"apiBaseURL":"http://fittomato.awkwardideas.com/fitbit"});
		console.log(fitbit.getDevices());
	
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
</script>
<script type="text/javascript" src="/assets/js/fitbit.jquery.js"></script>