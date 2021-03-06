
/***********************************************************************
Orchard Hut Online Shop
Copyright (C) 2013-2015  Kazuichi Takashiro

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.

takashiro@qq.com
************************************************************************/

function getcookie(Name){
	Name = cookiepre + Name;

	var search = Name + "=";
	if(document.cookie.length > 0){
		offset = document.cookie.indexOf(search);
		if(offset != -1){
			offset += search.length;
			end = document.cookie.indexOf(";", offset);
			if(end == -1){
				end = document.cookie.length;
			}
			return unescape(document.cookie.substring(offset, end));
		}
	}

	return "";
}

function setcookie(name, value){
	name = cookiepre + name;

	var argv = setcookie.arguments;
	var argc = setcookie.arguments.length;
	var expires = (argc > 2) ? argv[2] : 9999;
	if(expires != null){
		var LargeExpDate = new Date ();
		LargeExpDate.setTime(LargeExpDate.getTime() + (expires * 1000 * 3600 * 24));
	}
	document.cookie = name + "=" + escape (value)+((expires == null) ? "" : ("; expires=" +LargeExpDate.toGMTString()));
}

function in_array(needle, arr){
	if(typeof arr != 'object' || arr.length == undefined)
		return false;

	for(var i = 0; i < arr.length; i++){
		if(needle == arr[i]){
			return true;
		}
	}

	return false;
}

function popup_message(title, message){
	var popup_message = $('<div></div>');
	popup_message.addClass('popup_message');

	var header = $('<header></header>');
	var h4 = $('<h4></h4>');
	h4.html(title);
	var remove_button = $('<button></button>');
	remove_button.attr('type', 'button');
	remove_button.addClass('remove');
	header.append(h4);
	header.append(remove_button);

	var content = $('<div></div>');
	content.addClass('content');
	content.html(message);

	popup_message.append(header);
	popup_message.append(content);

	var wrapper = $('<div></div>');
	wrapper.addClass('popup_message_wrapper');
	popup_message.appendTo(wrapper);

	remove_button.click(function(){
		wrapper.remove();
	});

	wrapper.click(function(){
		$(this).remove();
	});

	$('body').append(wrapper);

	wrapper.fadeIn();
}

function makeToast(data){
	if(typeof data == 'string'){
		data = {
			'message' : data,
			'url_forward' : ''
		};
	}

	var toast = $('<div class="toast"></div>');
	toast.html(data.message);
	toast.appendTo($('body'));

	toast.css({
		'top' : ($(window).height() - toast.outerHeight()) / 2,
		'left' : ($(window).width() - toast.outerWidth()) / 2
	});
	toast.animate({
		top : '-=40px',
		opacity : 1
	}, 300);

	setTimeout(function(){
		toast.fadeOut(500, function(){
			toast.remove();
			if(data.url_forward){
				if(data.url_forward == 'refresh'){
					location.reload();
				}else if(data.url_forward == 'back'){
					toast.remove();
				}else{
					location.href = data.url_forward;
				}
			}
		});
	}, 1500);
}

(function($){
	$.fn.numbernotice = function(val){
		this.text(val);
		if(val == 0){
			this.hide();
		}else{
			this.show();
		}
	}
})(jQuery);

$(function(){
	$('input.number').each(function(){
		var number_box = $('<div class="numberbox"></div>');
		var increase_button = $('<button type="button" class="increase">+</button>');
		var decrease_button = $('<button type="button" class="decrease">-</button>');
		$(this).wrap(number_box);
		$(this).before(decrease_button);
		$(this).after(increase_button);

		$(this).change(function(){
			var value = parseInt($(this).val(), 10);
			if(value > 0){
				decrease_button.css('visibility', 'visible');
			}else{
				decrease_button.css('visibility', 'hidden');
			}

			var maxvalue = $(this).data('maxvalue');
			if(maxvalue){
				maxvalue = parseInt(maxvalue, 10);
				if(value > maxvalue){
					$(this).val(maxvalue);
				}
			}
		});

		increase_button.click(function(e){
			var button = $(e.target);
			var box = button.parent();
			var input = box.children('input');
			var number = parseInt(input.val(), 10);
			if(isNaN(number)){
				number = 1;
			}else{
				number = parseInt(number, 10);
				number++;
			}
			input.val(number);
			input.change();

			decrease_button.css('visibility', 'visible');
		});

		decrease_button.click(function(e){
			var button = $(e.target);
			var box = button.parent();
			var input = box.children('input');
			var number = parseInt(input.val(), 10);
			if(!isNaN(number) && number > 1){
				number--;
			}else{
				number = '';
				decrease_button.css('visibility', 'hidden');
			}
			input.val(number);
			input.change();
		});

		$(this).change();
	});

	$('ul.mselect li').click(function(){
		var li = $(this);
		var radio = li.children('input[type="radio"]');
		if(!radio.is(':disabled')){
			var mselect = li.parent();
			mselect.children('li').removeClass('checked');
			li.addClass('checked');
			mselect.find('input[type="radio"]:checked').prop('checked', false).change();
			radio.prop('checked', true).change();
		}
	});

	$('ul.mselect').each(function(){
		var ul = $(this);
		var li = ul.children();
		li.each(function(){
			var input = li.find('input[type="radio"]');
			if(input.is(':checked')){
				$(this).addClass('checked');
				return false;
			}
		});
		li.eq(0).click();
	});

	var checkboxes = $('.checkbox_list label input[type="checkbox"]');
	checkboxes.change(function(){
		var input = $(this);
		var label = input.parent();
		if(input.is(':checked')){
			label.addClass('checked');
		}else{
			label.removeClass('checked');
		}
	});
	checkboxes.change();

	$('.mpage .current').click(function(e){
		if($(this).attr('href') == '###')
			e.preventDefault();
	});

	var current_url = location.href;
	var base_url = current_url.substr(0, current_url.lastIndexOf('/'));
	$('ul.nav li').each(function(){
		var li = $(this);
		var a = li.children('a');
		var href = base_url + '/' + a.attr('href');
		if(href == current_url){
			li.addClass('active');
			return false;
		}
	});

	$('form.toast').submit(function(){
		var form = $(this);
		var data = form.serialize();
		var url = form.attr('action');
		if(url == '###'){
			url = location.href;
		}
		url += (url.indexOf('?') >= 0 ? '&' : '?') + 'ajaxform=1';
		$.post(url, data, function(response){
			makeToast(response);
		}, 'json');
		return false;
	});

	$('a.toast').click(function(e){
		e.preventDefault();
		var url = $(e.target).attr('href');
		if(url == '###'){
			url = location.href;
		}
		url += (url.indexOf('?') >= 0 ? '&' : '?') + 'ajaxform=1';
		if(url){
			$.get(url, {}, function(response){
				makeToast(response);
			}, 'json');
		}
	});
});
