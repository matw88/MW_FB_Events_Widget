function mw_timer(id,time,d,h,m,s,hs)
{
	var start = Date.parse(time);
	var c = new Date();
	var current = c.getTime();

	var hundredth_second = 10;
	var seconds = 1000;
	var minutes = 60000;
	var hours = 3600000;
	var days = 86400000;
		
	var miliseconds = start - current;
	
	var left = miliseconds;
	
	if (left > 0) {
		if (d) {
			var timer_d = Math.floor(left/days);
			document.getElementById('mw_days'+id).innerHTML = timer_d;
			left -= timer_d*days;
		}
		if (h) {
			var timer_h = Math.floor(left/hours);
			document.getElementById('mw_hours'+id).innerHTML = timer_h;
			left -= timer_h*hours;
		}
		if (m) {
			var timer_m = Math.floor(left/minutes);
			document.getElementById('mw_minutes'+id).innerHTML = timer_m;
			left -= timer_m*minutes;
		}
		if (s) {
			var timer_s = Math.floor(left/seconds);
			document.getElementById('mw_seconds'+id).innerHTML = timer_s;
			left -= timer_s*seconds;
		}
		if (hs) {
			var timer_ms = Math.floor(left/hundredth_second);
			document.getElementById('mw_hundredth_second'+id).innerHTML = timer_ms;
			left -= timer_ms*hundredth_second;
		}
	} else {
		document.getElementById('mw_days'+id).innerHTML = 0;
		document.getElementById('mw_hours'+id).innerHTML = 0;
		document.getElementById('mw_minutes'+id).innerHTML = 0;
		document.getElementById('mw_seconds'+id).innerHTML = 0;
		document.getElementById('mw_hundredth_second'+id).innerHTML = 0;
	}
}