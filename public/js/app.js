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

function onload_tags() {
	var editTags = document.querySelector('.editTags');
	var list = editTags.querySelector('span');
	var tags = document.getElementById('tags');
	var addTag = document.getElementById('addTag');
	var pick = document.querySelector('.pick-tag');
	var pick_tags = pick.querySelectorAll('span');
	function update_tags_input() {
		var as = editTags.querySelectorAll('.tag');
		var arr = [];
		for (var i=0; i<as.length; i++) { arr.push(as[i].innerHTML); }
		tags.value = arr.join(',');
	}
	function remove_tags(e) {
		e.target.parentNode.removeChild(e.target);
		update_tags_input();
		return false;
	}
	function append_tag(tag) {
		var a = document.createElement('a');
		a.href = '#';
		a.className = 'tag';
		a.innerHTML = tag;
		list.appendChild(a);
		a.onclick = remove_tags;
	}
	function add_tag() {
		if (addTag.value !== '') {
			append_tag(addTag.value);
			addTag.value = '';
			update_tags_input();
		}
	}
	function update_tags() {
		if (tags.value !== '') {
			var arr = tags.value.split(/,/);
			for (var i=0; i<arr.length; i++) { append_tag(arr[i]); }
		}
	}
	var keepFocus = false;
	pick.onmousedown = function(e) {
		if (e.target.className == 'visible') {
			// on n'a pas cliqué sur la barre de défilemenent ni sur la bordure
			// mais bien sur un nom de tag
			addTag.value = e.target.innerHTML;
			add_tag();
			keepFocus = true; // on veut que addTag garde le focus
		}
	};
	addTag.onkeydown = function(e) {
		if ((('keyCode' in e) && (e.keyCode == 13 || e.keyCode == 188)) ||
			(('key' in e) && (e.key == 'Enter' || e.key == ','))) {
			add_tag();
			addTag.blur();
			addTag.focus();
			return false;
		}
		if (('keyCode' in e && e.keyCode == 9) ||
			('key' in e && e.key == 'Tab')) {
			var elm = form.list.querySelector('.visible');
			if (elm !== null) {
				// on récupère le premier élément de la liste déroulante
				addTag.value = elm.innerHTML;
				add_tag();
				addTag.blur();
				addTag.focus();
			}
			return false;
		}
	};
	addTag.onfocus = function() {
		var pos = addTag.getBoundingClientRect();
		pick.style.left = pos.left+'px';
		pick.style.top = pos.bottom+'px';
		addTag.onkeyup(); // On initialise la liste en fonction de addTag
	};
	addTag.onblur = function(e) {
		if (!keepFocus) {
			pick.style.left = '-9999px';
			pick.style.top = '-9999px';
		}
		else {
			keepFocus = false;
			// pour Firefox qui ne doit pas apprécier qu'on empêche cette action
			setTimeout("document.getElementById('addTag').focus()", 10);
		}
	};
	addTag.onkeyup = function() {
		var val = addTag.value;
		for (var i=0; i<pick_tags.length; i++) {
			if (pick_tags[i].innerHTML.indexOf(val) === -1) {
				pick_tags[i].className = '';
			}
			else {
				pick_tags[i].className = 'visible';
			}
		}
	};
	tags.onupdate = update_tags;
	update_tags();
}
if (isset(document.querySelector('.editTags'))) { onload_tags(); }
