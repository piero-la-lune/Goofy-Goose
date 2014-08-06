function isset(elm) {
	return (typeof(elm) != 'undefined' && elm !== null);
}

function Ajax(elm, action) {
	this.post = [];
	this.elm = undefined;
	this.loader = undefined;
	if (elm) {
		this.elm = elm;
		this.loader = document.createElement('span');
		this.loader.className = 'loading';
		this.loader.innerHTML = '<i class="n1"></i><i class="n2"></i><i class="n3"></i>';
		this.elm.parentNode.replaceChild(this.loader, this.elm);
	}
	this.post.push('action='+action);
	this.post.push('page='+page);
	this.addParam = function(name, value) {
		this.post.push(name+'='+encodeURIComponent(value));
	};
	this.send = function(callback_success, callback_error) {
		var ajax = this;
		var xhr = new XMLHttpRequest();
		xhr.open('POST', ajax_url);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		xhr.send(this.post.join('&'));
		xhr.onreadystatechange = function() {
			if (xhr.readyState == xhr.DONE) {
				if (xhr.status == 200) {
					console.log(xhr.responseText);
					var ans = JSON.parse(xhr.responseText);
					if (ans['status'] == 'success') {
						if (typeof callback_success != 'undefined') {
							callback_success(ans);
						}
					}
					else {
						if (typeof callback_error != 'undefined') {
							callback_error(ans);
						}
					}
				}
				else if (xhr.status == 403) {
					alert(m_error_login);
				}
				else {
					alert(m_error_ajax);
				}
				ajax.cancel();
			}
		};
	};
	this.cancel = function() {
		if (this.loader) {
			this.loader.parentNode.replaceChild(this.elm, this.loader);
		}
	};
}

if (isset(document.getElementById('logout'))) {
	document.getElementById('logout').onclick = function() {
		document.getElementById('form-logout').submit();
		return false;
	};
}

if (isset(document.getElementById('series'))) {
	document.getElementById('series').onclick = function() {
		document.getElementById('nav').className = "display-series";
		return false;
	};
}
if (isset(document.getElementById('back'))) {
	document.getElementById('back').onclick = function() {
		document.getElementById('nav').className = "";
		return false;
	};
}

function season_watched(e) {
	var src = e.target;
	var snb = src.dataset.s;
	var ajax = new Ajax(undefined, 'swatched');
	ajax.addParam('snb', snb);
	ajax.addParam('id', src.dataset.id);
	ajax.send(function(ans) {
		document.location.reload();
	});
	return false;
}
var a_season = document.querySelectorAll('.a-season');
for (var i = a_season.length - 1; i >= 0; i--) {
	a_season[i].onclick = season_watched;
}

function episode_watched(e) {
	var src = e.target;
	var p = src.parentNode.parentNode.parentNode.parentNode.parentNode;
	var a_no = p.querySelector('.a-no');
	var span_no = p.querySelector('.span-no');
	var ajax = new Ajax(undefined, 'watched');
	ajax.addParam('id', a_no.dataset.id);
	ajax.addParam('no', a_no.textContent);
	ajax.send(function(ans) {
		if (span_no.classList.contains('watched')) {
			var span_date = p.querySelector('.span-date').textContent;
			var date = new Date();
			var str_date = date.getFullYear()+'-'+('0'+(date.getMonth()+1)).slice(-2)+'-'+('0' + date.getDate()).slice(-2);
			if (span_date < str_date && span_date !== '') {
				span_no.classList.add('released');
			}
			span_no.classList.remove('watched');
		}
		else {
			span_no.classList.remove('released');
			span_no.classList.add('watched');
			if (page == 'home') {
				p.parentNode.removeChild(p);
			}
		}
	});
	return false;
}
a_watched = document.querySelectorAll('.div-episode .a-watched');
for (var i = a_watched.length - 1; i >= 0; i--) {
	a_watched[i].onclick = episode_watched;
}

function open_popup(e) {
	var src = e.target;
	var p = src.parentNode;
	// Retrieve subtitles
	var ajax = new Ajax(undefined, 'subtitles');
	ajax.addParam('id', src.dataset.id);
	ajax.addParam('no', src.textContent);
	ajax.send(function(ans) {
		p.querySelector('.div-subtitles').innerHTML = ans.ans;
	});
	// Open popup
	p.classList.add('open');
	return false;
}
var a_episode = document.querySelectorAll('.div-episode .a-no');
for (var i = a_episode.length - 1; i >= 0; i--) {
	a_episode[i].onclick = open_popup;
}

function close_popup(e) {
	var src = e.target;
	src.parentNode.parentNode.parentNode.parentNode.parentNode.classList.remove('open');
	return false;
}
var a_close = document.querySelectorAll('.a-close');
for (var i = a_close.length - 1; i >= 0; i--) {
	a_close[i].onclick = close_popup;
}

function more(e) {
	var src = e.target;
	src.parentNode.classList.add('more');
	return false;
}
var a_more = document.querySelectorAll('.a-more');
for (var i = a_more.length - 1; i >= 0; i--) {
	a_more[i].onclick = more;
}