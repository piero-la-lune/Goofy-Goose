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


function desc_episode(e) {
	var div_episode = document.querySelectorAll('.div-episode');
	for (var j = div_episode.length - 1; j >= 0; j--) {
		div_episode[j].style.display = 'none';
	}
	document.getElementById(e.target.dataset.no).style.display = 'block';
	return false;
}
var a_episode = document.querySelectorAll('.ul-episodes a');
for (var i = a_episode.length - 1; i >= 0; i--) {
	a_episode[i].onclick = desc_episode;
}
function episode_watched(e) {
	var src = e.target;
	var no = src.dataset.no;
	var ajax = new Ajax(undefined, 'watched');
	ajax.addParam('id', src.dataset.id);
	ajax.addParam('no', no);
	ajax.send(function(ans) {
		var a = document.getElementById('a'+no);
		var div = document.getElementById(no);
		var cl = 'watched';
		if (a.className == 'watched') {
			var span_date = div.querySelector('.span-date').textContent;
			var date = new Date();
			var str_date = date.getFullYear()+'-'+('0'+(date.getMonth()+1)).slice(-2)+'-'+('0' + date.getDate()).slice(-2);
			if (span_date < str_date && span_date !== '') {
				cl = 'released';
			}
			else {
				cl = '';
			}
		}
		a.className = cl;
		div.className = 'div-episode '+cl;
	});
	return false;
}
a_episode = document.querySelectorAll('.div-episode a');
for (var i = a_episode.length - 1; i >= 0; i--) {
	a_episode[i].onclick = episode_watched;
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
}
var a_season = document.querySelectorAll('.a-season');
for (var i = a_season.length - 1; i >= 0; i--) {
	a_season[i].onclick = season_watched;
}

a_episode = document.querySelectorAll('.div-season .span-no');
function episode_popup(e) {
	var src = e.target;
	var p = src.parentNode;
	var w = p.querySelector('.div-desc');
	var pos = src.getBoundingClientRect();
	// Ajax
	var ajax = new Ajax(undefined, 'subtitles');
	ajax.addParam('id', src.dataset.id);
	ajax.addParam('no', src.textContent);
	ajax.send(function(ans) {
		w.querySelector('.div-subtitles').innerHTML = ans.ans;
	});
	// Display
	w.classList.add('no-transition');
	w.style.width = pos.width+'px';
	w.style.height = pos.height+'px';
	w.style.top = pos.top+'px';
	w.style.left = pos.left+'px';
	setTimeout(function() {
		w.classList.remove('no-transition');
		w.style.removeProperty('width');
		w.style.removeProperty('height');
		w.style.removeProperty('top');
		w.style.removeProperty('left');
		p.classList.add('open');
	}, 25);
	p.querySelector('.span-close').onclick = function() {
		var pos = src.getBoundingClientRect();
		w.style.width = pos.width+'px';
		w.style.height = pos.height+'px';
		w.style.top = pos.top+'px';
		w.style.left = pos.left+'px';
		p.classList.remove('open');
	};
	p.querySelector('.span-watched').onclick = function() {
		var ajax = new Ajax(undefined, 'watched');
		ajax.addParam('id', src.dataset.id);
		ajax.addParam('no', src.textContent);
		ajax.send(function(ans) {
			p.parentNode.removeChild(p);
		});
	};
}
for (var i = a_episode.length - 1; i >= 0; i--) {
	a_episode[i].onclick = episode_popup;
}
