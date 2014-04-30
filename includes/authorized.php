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
<script type="text/javascript" src="/assets/js/authorized.js"></script>