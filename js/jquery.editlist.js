
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

(function($){
	$.fn.editlist = function(options){
		var defaults = {
			'edit' : '',
			'delete' : '',
			'submit_url' : '',
			'primarykey' : 'id',
			'noedit' : false,
			'attr' : [],
			'buttons' : {'edit':'编辑', 'delete':'删除'},
			'confirm_deletion_prompt' : '您确认删除吗？',
			'onSubmit' : function(){ return false; },
			'onSucceed' : function(result){ makeToast(result); }
		};

		var editlist = this;
		options = $.extend(defaults, options);

		options.ajax_edit = options.edit + (options.edit.indexOf('?') == -1 ? '?' : '&') + 'ajax=1';
		options.ajax_delete = options.delete + (options.delete.indexOf('?') == -1 ? '?' : '&') + 'ajax=1';

		var display_operations = function(operation_td){
			operation_td.html('');
			for(var i in options.buttons){
				var button = $('<button></button>');
				button.attr('type', 'button');
				button.attr('class', i);
				button.html(options.buttons[i]);
				operation_td.append(button);
			}
		}

		var operation_td = this.find('tbody tr:not(:last-child) td:last-child');
		display_operations(operation_td);

		if(!options.noedit){
			this.on('dblclick', 'tbody tr:not(:last-child) td:not(:last-child)', function(e){
				var td = $(e.target);

				var index = td.index();
				var tbody = td.parent().parent();

				var input = tbody.children(':last-child').children().eq(index).find('input,select,textarea').clone();

				if(input.length == 0){
					return false;
				}

				if(td.data('realvalue') != undefined){
					input.val(td.data('realvalue'));
				}else{
					input.val(td.html());
				}

				td.html('');
				td.append(input);
				input.focus();

				if(input.is('select')){
					var select_options = input.children('option');
					if(select_options.length == 2){
						var opposite_value = input.children('option:not(:checked)').attr('value');
						input.val(opposite_value);
						input.blur();
						input.hide();
					}
				}
			});
		}

		this.on('blur', 'tbody tr:not(:last-child) td input, tbody tr:not(:last-child) td textarea, tbody tr:not(:last-child) td select', function(e){
			var input = $(e.target);
			var td = input.parent();
			var tr = td.parent();
			var index = td.index();
			var attr = options.attr[index];
			var value = input.val();

			if(attr == ''){
				return false;
			}

			var data = {};
			data[options.primarykey] = tr.data('primaryvalue');
			data[attr] = value;

			function show_result(data){
				if(input.is('select')){
					td.data('realvalue', value);
					td.html(input.children(':selected').html());
				}else{
					td.html(value);
				}

				var tds = tr.children();
				for(var i = 0; i < options.attr.length; i++){
					var attr = options.attr[i];
					if(typeof data[attr] != 'undefined'){
						var current_input = tr.parent().children(':last-child').children().eq(i).find('input,select,textarea');
						if(current_input.is('select')){
							if(typeof data[attr] == 'boolean'){
								data[attr] = data[attr] ? 1 : 0;
							}
							tds.eq(i).data('realvalue', data[attr]);
							var current_input = current_input.clone();
							current_input.val(data[attr]);
							tds.eq(i).html(current_input.children(':selected').html());
						}else{
							tds.eq(i).html(data[attr]);
						}
					}
				}
			}

			if(options.edit){
				$.post(options.ajax_edit, data, function(data){
					show_result(data);
				}, 'json');
			}else{
				show_result(data);
			}
		});

		this.on('click', '.add', function(e){
			var button = $(e.target);
			var empty_tr = button.parent().parent();
			var new_tr = empty_tr.clone();

			var data = {};

			for(var i = 0; i < options.attr.length; i++){
				var attr = options.attr[i];
				var td = empty_tr.children().eq(i);
				var input = td.find('input,select,textarea');
				var value = input.val();

				data[attr] = value;
			}

			function add_row(data){
				new_tr.data('primaryvalue', data[options.primarykey]);

				for(var i = 0; i < options.attr.length; i++){
					var attr = options.attr[i];
					if(!attr){
						continue;
					}
					var td = new_tr.children().eq(i);
					var input = empty_tr.children().eq(i).find('input,select,textarea');
					if(input.is('select')){
						if(typeof data[attr] == 'boolean'){
							data[attr] = data[attr] ? 1 : 0;
						}
						input.val(data[attr]);
						td.html(input.find(':selected').html());
						td.data('realvalue', data[attr]);
					}else{
						if(input.data('realvalue') != undefined){
							td.data('realvalue', input.data('realvalue'));
						}
						td.html(data[attr]);
					}
				}

				empty_tr.find('input:not([readonly]), select:not([readonly]), textarea:not([readonly])').val('');
				empty_tr.before(new_tr);

				display_operations(new_tr.children('td:last-child'));
			}
			if(options.edit){
				$.post(options.ajax_edit, data, function(data){
					add_row(data);
				}, 'json');
			}else{
				add_row(data);
			}
		});

		this.on('click', '.edit', function(e){
			var button = $(e.target);
			var tr = button.parent().parent();
			var primaryvalue = tr.data('primaryvalue');
			if(primaryvalue)
				location.href = options.edit + (options.edit.indexOf('?') == -1 ? '?' : '&') + options.primarykey + '=' + primaryvalue;
		});

		this.on('click', '.delete', function(e){
			if(!confirm(options.confirm_deletion_prompt)){
				return;
			}

			var button = $(e.target);
			var tr = button.parent().parent();

			var primaryvalue = tr.data('primaryvalue');
			var data = {};
			if(primaryvalue){
				data[options.primarykey] = primaryvalue;
			}else{
				tr.remove();
				return;
			}

			if(options.delete){
				$.post(options.ajax_delete, data, function(){
					tr.remove();
				});
			}else{
				tr.remove();
			}
		});

		this.on('click', 'button.submit', function(){
			if(typeof options.onSubmit == 'function'){
				var broken = options.onSubmit();
				if(broken){
					return;
				}
			}

			var trs = editlist.find('tbody tr:not(:last-child)');
			var content = [];
			for(var i = 0; i < trs.length; i++){
				var tr = trs.eq(i);
				var tds = tr.children();
				var row = {};
				for(var j = 0; j < options.attr.length; j++){
					var td = tds.eq(j);
					var attr = options.attr[j];
					if(!attr){
						continue;
					}
					if(td.data('realvalue') != undefined){
						row[attr] = td.data('realvalue');
					}else{
						row[attr] = td.text();
					}
				}
				content.push(row);
			}

			var input = {};
			editlist.find('.editlist_input input, .editlist_input textarea, .editlist_input select, .editlist_input hidden').each(function(){
				var name = $(this).attr('name');
				if(name){
					var value = $(this).val();
					input[name] = value;
				}
			});
			input['content'] = content;

			if(options.submit_url){
				$.post(options.submit_url + '&ajax=1', JSON.stringify(input), options.onSucceed, 'json');
			}
		});
	}
})(jQuery);
