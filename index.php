<?php
	include('config.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Viewer Test</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-theme.min.css" rel="stylesheet">
	<link href="css/theme.css" rel="stylesheet">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-toggle.css" rel="stylesheet">
	
	<style>
	
		.left-panel {
			margin: 5px;
			width: 270px;
			float:left;
		}
		
		.left-panel>div {
			margin-bottom: 5px;
		}
		
		.right-area {
			margin-left: 240px;
			padding-left: 5px;
			display: block;
		}
		
		.basic-input {
			background-color: #fff;
			border: 1px solid #ddd;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
			height: 30px;
			width: 100%;
		}
		
		.badge {
			display: inline-block;
			padding: 1px 9px 2px;
			margin: 0px 5px;
			-webkit-border-radius: 9px;
			-moz-border-radius: 9px;
			border-radius: 9px;
			font-size: 10.998px;
			font-weight: bold;
			line-height: 14px;
			color: #fff;
			text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
			white-space: nowrap;
			vertical-align: baseline;
			background-color: #999;
		}
		
		.icon-remove-sign {
			background-position: -48px -96px;
			cursor: pointer;
			margin-right: -8px;
			margin-left: 4px;
		}
		
		.icon-white {
			background-image: url("img/glyphicons-halflings-white.png");
			display: inline-block;
			width: 14px;
			height: 14px;
			line-height: 14px;
			vertical-align: text-top;
			background-repeat: no-repeat;
		}
		
		.mini-window .panel-heading {
			height: 24px;
			padding: 3px;
		}
		
		.mini-window .panel-title {
			height: 14px;
			width: 140px;
			font-size: 12px;
		}
		
		.mini-window .glyphicon-remove {
			float:right;
			cursor: pointer;
		}
		
		.mini-window .panel-title.basic-input {
			background-color: #d9edf7;
			border-color: #d9edf7;
		}
		
		.groupNO {
			display: none;
		}
		
		.div-upload {
			padding: 4px 10px;
			height: 30px;
			font-size: 12px;
			line-height: 20px;
			position: relative;
			cursor: pointer;
			background: #fafafa;
			border: 1px solid #ccc;
			border-radius: 4px;
			overflow: hidden;
			display: inline-block;
			width: 110px;
			text-align: center;
		}

		.div-upload  input {
			position: absolute;
			font-size: 100px;
			right: 0;
			top: 0;
			opacity: 0;
			filter: alpha(opacity=0);
			cursor: pointer
		}
		
		.div-upload:hover {
			color: #444;
			background: #eee;
			border-color: #ccc;
			text-decoration: none
		}

		line.box {
			stroke: #000;
			stroke-width: 0.5px;
		}

		.axis {
			font: 10px arial;
		}

		.axis path,
		.axis line {
			fill: none;
			stroke: #000;
			shape-rendering: crispEdges;
		}

		#boxInf, #normalDotInf, #cellLineDotInf, #highlightedDotInf, #groupDotInf, #customDotInf{
			position: absolute;
			width: auto;
			height: auto;
			padding: 5px;
			background-color: white;
			opacity: 0.8;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			-webkit-box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.4);
			-moz-box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.4);
			box-shadow: 4px 4px 5px rgba(0, 0, 0, 0.4);
			pointer-events: none;
		}

		#boxInf.hidden, #normalDotInf.hidden, #cellLineDotInf.hidden, #highlightedDotInf.hidden,#groupDotInf.hidden, #customDotInf.hidden{
			display: none;
		}

		#boxInf p, #normalDotInf, #cellLineDotInf, #highlightedDotInf, #groupDotInf, #customDotInf {
			margin: 0;
			font-family: sans-serif;
			font-size: 12px;
			line-height: 16px;
		}
		
		</style>
		<script src="js/jquery-2.1.1.min.js"></script>
		<script src="doc/script.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-toggle.js"></script>
		<script src="js/bootstrap-typeahead.js"></script>
		<script src="js/d3.v3.min.js"></script>
		<script src="js/jstat.js"></script>
		<script type="text/javascript">
			
			var selected_genes = [];
			var selected_normal_tissues = [];
			var modal_normal_tissues = [];
			var selected_cell_line_tissues = [];
			var modal_cell_line_tissues = [];
			var highlighted_cell_lines = [];
			var cell_line_hash = new Object();
			var is_linear = false;
			var show_samples = true;
			var scatter_distribution = true;
			var by_tissue = true;
			var include_zero = false;
			var change_confirmed = true;
			var group_num = 0;
			var group_tissues_list = [];
			var group_names = [];
			var mutation = '';
			var low_limit = 0.0001;
			var high_limit = 1000000;
			var file_list = [];
			var custom_data = [];
			var custom_sample = "Custom sample";
			var t_test_data;
			
			function upload(){
					$(".alert").hide(500);
					custom_data = [];
					if(file_list.length == 0) {
						change_confirmed = true;
						return;
					}
					var reader=new FileReader();
					var file_index = 0;
					reader.onloadend=function(){
						if(reader.error) {
							$("#alert").html("<strong>Error:</strong> Can not read files.")
							$("#alert").show(500);
						} else {
							var lines = reader.result.split('\n');
							lines = lines.filter(function(item, index, array) { return item != ''; });
							var header = lines[0].split(/\s+/);
							header = header.filter(function(item, index, array) { return item != ''; });
							header.shift();
							lines.shift();
							lines.forEach(function(item, index, array) {
								var words = item.split(/\s+/);
								words = words.filter(function(item, index, array) { return item != ''; });
								var gene = words.shift();
								var not_exist = true;
								for(var i = 0; i < custom_data.length; ++i)
									if(custom_data[i].gene == gene) {
										not_exist = false;
										custom_data[i].data = custom_data[i].data.concat(words.map(function(item, index, array) {
											if(index > header.length) {
												wrong_format(); //Detect an error
												return;
											}
											return {sample: header[index], expr: parseFloat(item)};
										}));
									}
								if(not_exist) {
									custom_data.push({gene: gene, data: words.map(function(item, index, array) {
										if(index >= header.length) {
											wrong_format(); //Detect an error
											return;
										}
										return {sample: header[index], expr: parseFloat(item)};
									})});
								}						
							});
							if(file_index != file_list.length)
								reader.readAsBinaryString(file_list[file_index++]);
							else {
								change_confirmed = true;
								$("#success").html("<strong>Success:</strong> Read " + file_index + " file(s).")
								$("#success").show(500);
							}
						}
					};
					reader.readAsBinaryString(file_list[file_index++]);
				}
				
			$(document).ready(function() {
			
				$(function () {
					$('[data-tooltip="tooltip"]').tooltip()
				})
				
				$('#dataSourceModal').on('show.bs.modal', function (event) {
					var button = $(event.relatedTarget);
					var content = button.data('content');
					var url = button.data('url');
					var modal = $(this);
					modal.find('.modal-body').html('<p>' + content + '</p>' + '<p>All data was downloaded from the public website and is presented "as is", except for the removal of some low quality normal samples based on reported RIN values</p>' + '<p>Please visit <a href="' + url + '">' + url + '</a> for details.</p>');
					$('#goToSource').click(function () {
						window.location.href = url;
					});
				})
				
				$('#gene').typeahead({
					source: function(query, process) {
						$(".alert").hide(500);
						var parameter = { q: query};
						$.post('gene_list.php', parameter, function (data) {
							process(JSON.parse(data));
						});
					}, 
					
					updater: function(item) {
						$(".alert").hide(500);
						if(!selected_genes.some(function(t_item, index, array) { return item==t_item; })) {
							var badge = $('<span class="badge">' + item + '</span>').appendTo('#genePanel');
							$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
								$(".alert").hide(500);
								var item=$(this).parent().text();
								selected_genes=selected_genes.filter(function(t_item, index, array) { return item != t_item; });
								$(this).parent().remove();
							});
							selected_genes.push(item);
						}
						//return item;	//Delete this line and then the text box will be empty after selecting a item.
					}
				});
				
				$('#tissue').typeahead({
					source: function(query, process) {
						$(".alert").hide(500);
						var parameter = { q: query};
						$.post('normal_tissue_list.php', parameter, function (data) {
							process(JSON.parse(data));
						});
					}, 
					
					updater: function(item) {
						$(".alert").hide(500);
						if(!selected_normal_tissues.some(function(t_item, index, array) { return item==t_item; })) {
							var badge = $('<span class="badge">' + item + '</span>').appendTo('#tissuePanel');
							$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
								$(".alert").hide(500);
								var item=$(this).parent().text();
								selected_normal_tissues=selected_normal_tissues.filter(function(t_item, index, array) { return item != t_item; });
								$(this).parent().remove();
							});
							selected_normal_tissues.push(item);
						}
						//return item;
					},
					
					items: 30
				});
				
				$('#normalTissueModal').find('thead').find('input').click(function() {
					if ($(this).is(':checked')) {
						//$(this).prop('checked', true);
						$('#normalTissueModal').find('tbody').find('input').each(function() {
							$(this).prop('checked', true);
							modal_normal_tissues.push($(this).parent().text());
						});
					} else {
						//$(this).prop('checked', false);
						$('#normalTissueModal').find('tbody').find('input').each(function() {
							$(this).prop('checked', false);
						});
						modal_normal_tissues = [];
					}
				});
				
				$('#normalTissueModal').find('tbody').find('input').click(function() {
					var tissue = $(this).parent().text();
					if ($(this).is(':checked')) {
						if (! modal_normal_tissues.some(function(d) { return d == tissue; }))
							modal_normal_tissues.push(tissue);
					} else
						modal_normal_tissues = modal_normal_tissues.filter(function(d) { return d != tissue; });
				});
				
				$('#normalTissueModal').find('.confirm').click(function() {
					selected_normal_tissues = [];
					$('#tissuePanel').children('.badge').remove();
					modal_normal_tissues.forEach(function (item) {
						var badge = $('<span class="badge">' + item + '</span>').appendTo('#tissuePanel');
						$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
							$(".alert").hide(500);
							var item=$(this).parent().text();
							selected_normal_tissues=selected_normal_tissues.filter(function(t_item, index, array) { return item != t_item; });
							$(this).parent().remove();
						});
						selected_normal_tissues.push(item);
					});
					$('#normalTissueModal').modal('hide');
				});
				
				$('#cellLineTissueModal').find('thead').find('input').click(function() {
					if ($(this).is(':checked')) {
						//$(this).prop('checked', true);
						$('#cellLineTissueModal').find('tbody').find('input').each(function() {
							$(this).prop('checked', true);
							modal_cell_line_tissues.push($(this).parent().text());
						});
					} else {
						//$(this).prop('checked', false);
						$('#cellLineTissueModal').find('tbody').find('input').each(function() {
							$(this).prop('checked', false);
						});
						modal_cell_line_tissues = [];
					}
				});
				
				$('#cellLineTissueModal').find('tbody').find('input').click(function() {
					var tissue = $(this).parent().text();
					if ($(this).is(':checked')) {
						if (! modal_cell_line_tissues.some(function(d) { return d == tissue; }))
							modal_cell_line_tissues.push(tissue);
					} else
						modal_cell_line_tissues = modal_cell_line_tissues.filter(function(d) { return d != tissue; });
				});
				
				$('#cellLineTissueModal').find('.confirm').click(function() {
					selected_cell_line_tissues = [];
					$('#cellLinePanel').children('.badge').remove();
					modal_cell_line_tissues.forEach(function (item) {
						var badge = $('<span class="badge">' + item + '</span>').appendTo('#cellLinePanel');
						$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
							$(".alert").hide(500);
							var item=$(this).parent().text();
							selected_cell_line_tissues=selected_cell_line_tissues.filter(function(t_item, index, array) { return item != t_item; });
							$(this).parent().remove();
						});
						selected_cell_line_tissues.push(item);
					});
					$('#cellLineTissueModal').modal('hide');
				});
				
				$('#cellLine').typeahead({
					source: function(query, process) {
						$(".alert").hide(500);
						var parameter = { q: query};
						$.post('cell_line_tissue_list.php', parameter, function (data) {
							process(JSON.parse(data));
						});
					}, 
					
					updater: function(item) {
						$(".alert").hide(500);
						if(!selected_cell_line_tissues.some(function(t_item, index, array) { return item==t_item; })) {
							var badge = $('<span class="badge">' + item + '</span>').appendTo('#cellLinePanel');
							$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
								$(".alert").hide(500);
								var item=$(this).parent().text();
								selected_cell_line_tissues=selected_cell_line_tissues.filter(function(t_item, index, array) { return item != t_item; });
								$(this).parent().remove();
							});
							selected_cell_line_tissues.push(item);
						}
						//return item;
					},
					
					items: 30
				});
				
				$('#mutationText').typeahead({
					source: function(query, process) {
						$(".alert").hide(500);
						var parameter = { q: query};
						$.post('mutation_list.php', parameter, function (data) {
							process(JSON.parse(data));
						});
					}, 
					
					updater: function(item) {
						$(".alert").hide(500);
						$('#mutationGene').text(item);
						$('#mutationBadge').show();
						mutation = item;
						//return item;
					},
					
					items: 30
				});
				
				$('#mutationBadge').children('i').click(function() {
					$(".alert").hide(500);
					mutation = '';
					$('#mutationBadge').hide();
				});
				
				$('#cosmicMenu').children().click(function() {
					$('#cosmicText').html($(this).find('a').text() + '<span class="caret"></span>');
					$('.alert').hide(500);
				});
				
				$("#scaleSelect").change(function() {
					$(".alert").hide(500);
					is_linear = ! is_linear;
				});
				
				$("#individualSelect").change(function() {
					$(".alert").hide(500);
					show_samples = ! show_samples;
				});
				
				$("#scatterSelect").change(function() {
					$(".alert").hide(500);
					scatter_distribution = ! scatter_distribution;
				});
				
				/*$('#groupSelect').change(function() {
					$('.alert').hide(500);
					by_tissue = ! by_tissue;
				});*/
				
				$('#zeroValueSelect').change(function() {
					$(".alert").hide(500);
					include_zero = ! include_zero;
				});
				
				$('#highlightedCellLineText').typeahead({
					source: function(query, process) {
						$(".alert").hide(500);
						var parameter = { q: query};
						$.post('cell_line_list.php', parameter, function (raw_data) {
							var data = JSON.parse(raw_data);
							var list = [];
							data.forEach(function(d) {
								list.push(d.cell_line);
								cell_line_hash[d.cell_line] = d.tissue;
							})
							process(list);
						});
					}, 
					
					highlighter: function(item) {
						return item + ' <span class="label label-info">' + cell_line_hash[item] + '</span>';
					},
					
					updater: function(item) {
						$(".alert").hide(500);
						if(!highlighted_cell_lines.some(function(t_item, index, array) { return item==t_item; })) {
							var badge = $('<span class="badge">' + item + '</span>').appendTo('#highlightedCellLinePanel');
							$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
								$(".alert").hide(500);
								var item=$(this).parent().text();
								highlighted_cell_lines=highlighted_cell_lines.filter(function(t_item, index, array) { return item != t_item; });
								$(this).parent().remove();
							});
							highlighted_cell_lines.push(item);
						}
						//return item;
					},
					
					items: 30
				});
				
				$("#addAGroup").click(function() {
					var currentWindow;
					++group_num;
					group_tissues_list.push([]);
					group_names.push('Group ' + group_num);
					if(group_num == 1) {
						$("#group").prepend('<div class="mini-window panel panel-info"><div class="panel-heading"><span><span class="groupNO">' + group_num + '</span><input type="text" class="basic-input panel-title"></span><span class="groupRemove glyphicon glyphicon-remove"></span></div><div class="panel-body"><input type="text" class="groupTissue basic-input" data-provide="typeahead" autocomplete="off" ></div></div>');
					} else {
						$("#addAGroup").prev().after('<div class="mini-window panel panel-info"><div class="panel-heading"><span><span class="groupNO">' + group_num + '</span><input type="text" class="basic-input panel-title"></span><span class="groupRemove glyphicon glyphicon-remove"></span></div><div class="panel-body"><input type="text" class="groupTissue basic-input" data-provide="typeahead" autocomplete="off" ></div></div>');
					}
					currentWindow = $(this).prev();
					currentWindow.find(".panel-title").val("Group" + group_num);
					currentWindow.find(".panel-title").bind("input", function() {
						var this_NO = currentWindow.find(".groupNO");
						group_names[parseInt(this_NO.text()) - 1] = $(this).val();
					});
					$('.groupTissue').typeahead({						
						source: function(query, process) {
							$(".alert").hide(500);
							var parameter = { q: query};
							$.post('cell_line_list.php', parameter, function (raw_data) {
								var data = JSON.parse(raw_data);
								var list = [];
								data.forEach(function(d) {
									list.push(d.cell_line);
									cell_line_hash[d.cell_line] = d.tissue;
								})
								process(list);
							});
						},
						
						highlighter: function(item) {
							return item + ' <span class="label label-info">' + cell_line_hash[item] + '</span>';
						},
						
						updater: function(item) {
							$(".alert").hide(500);
							var this_NO = currentWindow.find(".groupNO");
							if(!group_tissues_list[parseInt(this_NO.text()) - 1].some(function(t_item, index, array) { return item==t_item; })) {
								var badge = $('<span class="badge">' + item + '</span>').appendTo(currentWindow.children(".panel-body"));
								$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
									$(".alert").hide(500);
									var item=$(this).parent().text();
									group_tissues_list[parseInt(this_NO.text()) - 1]=group_tissues_list[parseInt(this_NO.text()) - 1].filter(function(t_item, index, array) { return item != t_item; });
									$(this).parent().remove();
								});
								group_tissues_list[parseInt(this_NO.text()) - 1].push(item);
							}
							//return item;
						},
						
						items: 30
					});
				});
				
				$(document.body).on("click", ".groupRemove", function() {
					$(".alert").hide(500);
					var current_NO = parseInt($(this).prev().children(".groupNO").text());
					$(".groupNO").each(function() {
						if(parseInt($(this).text()) > current_NO)
							$(this).text(parseInt($(this).text()) - 1);
					});
					$(this).parent().parent().remove();
					group_tissues_list.splice(current_NO - 1, 1);
					group_names.splice(current_NO - 1, 1);
					--group_num;
				});
				
				$("#fileName").change(function() {
					$(".alert").hide(500);
					var file = this.files[0];
					change_confirmed = false;
					if(!file_list.some(function (item, index, array) { item.name == file.name; })) {
						var badge = $('<span class="badge">' + file.name + '</span>').appendTo('#customPanel');
						$('<i class="icon-white icon-remove-sign"></i>').appendTo(badge).click(function() {
							change_confirmed = false;
							$(".alert").hide(500);
							var item=$(this).parent().text();
							file_list=file_list.filter(function(t_item, index, array) { return file.name != t_item.name; });
							$(this).parent().remove();
						});
						file_list.push(file);
					}
				});
				
				function wrong_format() {
					$("#alert").html("<strong>Error:</strong>The file format is wrong.")
					$("#alert").show(500);
				}
				
				$(document.body).on('click', 'li.t-test-gene-item', function() {
					$(this).parent().siblings('button.t-test').text($(this).text());
					$('#tTestCalc').removeClass('disabled');
					$('button.t-test').each(function() {
						if ($(this).text() == 'Invalid')
							$('#tTestCalc').addClass('disabled');
					});
					$('#tTestGroup1,#tTestGroup2').siblings('ul').empty();
					for (var tissue in t_test_data[$(this).text()]) {
						$('#tTestGroup1,#tTestGroup2').siblings('ul').append('<li class="t-test-group-item">' + tissue + '</li>');
					}
				});
								
				$(document.body).on('click', 'li.t-test-group-item', function() {
					$(this).parent().siblings('button.t-test').text($(this).text());
					$('#tTestCalc').removeClass('disabled');
					$('button.t-test').each(function() {
						if ($(this).text() == 'Invalid')
							$('#tTestCalc').addClass('disabled');
					});
				});
				
				function pow2(x) {
					return x * x;
				}
				
				//Return p-value
				function tTestTwoSample(
					sampleX/*: Array<number> */,
					sampleY/*: Array<number> */,
					difference/*: number */) {
					var n = sampleX.length,
						m = sampleY.length;

					// If either sample doesn't actually have any values, we can't
					// compute this at all, so we return `null`.
					if (!n || !m)
						return null; 

					// default difference (mu) is zero
					if (!difference)
						difference = 0;

					var meanX = jStat.mean(sampleX),
						meanY = jStat.mean(sampleY),
						sampleVarianceX = jStat.variance(sampleX, true),//Sample variance
						sampleVarianceY = jStat.variance(sampleY, true);//Sample variance

					if (typeof meanX === 'number' &&
						typeof meanY === 'number' &&
						typeof sampleVarianceX === 'number' &&
						typeof sampleVarianceY === 'number') {
						var weightedVariance = ((n - 1) * sampleVarianceX +(m - 1) * sampleVarianceY) / (n + m - 2);
						var t = (meanX - meanY - difference) / Math.sqrt(weightedVariance * (1 / n + 1 / m));
						var dof = n + m - 2;
						return 2 * jStat.studentt.cdf(- Math.abs(t), dof);
					}
				}

				$('#tTestCalc').click(function() {
					var p = tTestTwoSample(t_test_data[$('#tTestGene').text()][$('#tTestGroup1').text()], t_test_data[$('#tTestGene').text()][$('#tTestGroup2').text()]);
					var significance = "not statistically significant";
					if (p < 0.05) significance = "statistically significant";
					if (p < 0.01) significance = "very significant";
					if (p < 0.001) significance = "extremely significant";
					$('#pValue').text(p.toPrecision(4));
					$('#significance').text(significance);
					$('#tTestResult').show();
				});

				$("#display").click(function() {
					$(".alert").hide(50);
					if(selected_genes.length == 0) {
						$("#alert").html("<strong>Error:</strong> Please select a gene.")
						$("#alert").show(500);
						return;
					}
					/*
					if(selected_normal_tissues.length == 0) {
						$("#alert").html("<strong>Error:</strong> Please select a tissue.")
						$("#alert").show(500);
						return;
					}
					*/
					if(group_tissues_list.some(function(item, index, array) {
						return item.length == 0;
					})) {
						$("#alert").html("<strong>Error:</strong> Please fill out the groups.")
						$("#alert").show(500);
						return;
					}
					if(!change_confirmed) {
						$("#alert").html("<strong>Error:</strong> Please press \"confirm\".")
						$("#alert").show(500);
						return;
					}
					
					d3.select(".right-area").selectAll("svg").remove();
					$('#download').empty();
					
					for(var i = 0; i < custom_data.length; ++i)
						for(var j = 0; j < custom_data[i].data.length; ++j)
							if(custom_data[i].data[j].expr <= low_limit) custom_data[i].data[j].expr = low_limit;
					
					t_test_data = new Object();
					$('tTestCalc').addClass('disabled');
					$('#tTestGene,#tTestGroup1,#tTestGroup2').text('Invalid');
					$('#tTestGene,#tTestGroup1,#tTestGroup2').siblings('ul').empty();
					$('#tTestResult').hide();
					selected_genes.forEach(function(item, index, array) {
						var parameter = {
							include_zero: include_zero,
							gene: item, 
							normal_tissues: JSON.stringify(selected_normal_tissues),
							cell_line_tissues: JSON.stringify(selected_cell_line_tissues),
							highlighted_cell_lines: JSON.stringify(highlighted_cell_lines),
							groups: JSON.stringify(group_tissues_list),
							mutation: mutation,
							cosmic: $('#cosmicText').text()
						};
						$.post('query.php', parameter, display);
					});
				});
				
				function display(raw_data) {
					var data = JSON.parse(raw_data);
					var gene_name = data.shift();
					var normal_data = data.shift();
					var cell_line_data = data.shift();
					var highlighted_cell_line_data = data.shift();
					var group_data = data.shift();
					var normal_plot_data = [];
					var normal_distribution = [];
					var cell_line_plot_data = [];
					var cell_line_distribution = [];
					var highlighted_cell_line_plot_data = [];
					var group_plot_data = [];
					var custom_plot_data = [];
					var colors = new Object;
					var interval_num = 20;
					
					['adipose_tissue','adrenal','adrenal_gland','bladder','blood','blood_vessel','bone','brain','breast','cervix','cervix_uteri','colon','esophagus','fallopian_tube','head-neck','heart','kidney','liver','lung','muscle','nerve','other','ovary','pancreas','pituitary','prostate','salivary_gland','skin','small_intestine','spleen','stomach','testis','thyroid','tongue','uterus','vagina']
						.forEach(function(d, i, a) {
							colors[d] = 360 * i / a.length;
						});
					
					function transform(val) {
						return is_linear ? val : Math.log(val);
					}

					if(include_zero) {
						for(var i = 0; i < normal_data.length; ++i) 
							for(var j = 0; j < 5; ++j) 
								if(normal_data[i].expr[j] <= low_limit) normal_data[i].expr[j] = low_limit;
						for(var i = 0; i < cell_line_data.length; ++i) 
							for(var j = 0; j < 5; ++j) 
								if(cell_line_data[i].expr[j] <= low_limit) cell_line_data[i].expr[j] = low_limit;
						for(var i = 0; i < highlighted_cell_line_data.length; ++ i)
							if (highlighted_cell_line_data[i].expr <= low_limit) highlighted_cell_line_data[i].expr = low_limit;
						for(var i = 0; i < group_data.length; ++i) 
							for(var j = 0; j < group_data[i].length; ++j) 
								if(group_data[i][j].expr <= low_limit) group_data[i][j].expr = low_limit;
							
						for(var i = 0; i < normal_data.length; ++i) 
							for(var j = 5; j < normal_data[i].expr.length;)	//Remember this. Because by splicing the array, the next element will slip forward.
								if(normal_data[i].expr[j] <= low_limit)
									normal_data[i].expr.splice(j, 1);
								else
									++j;
						for(var i = 0; i < cell_line_data.length; ++i) 
							for(var j = 5; j < cell_line_data[i].expr.length;)	//Remember this. Because by splicing the array, the next element will slip forward. 
								if(cell_line_data[i].expr[j] <= low_limit) {
									cell_line_data[i].expr.splice(j, 1);
									cell_line_data[i].header.splice(j - 5, 1);
								}
								else
									++j;

						/*
						for(var i = 0; i < normal_data.length; ++i) 
							for(var j = 0; j < normal_data[i].expr.length; ++j) 
								if(normal_data[i].expr[j] <= low_limit) normal_data[i].expr[j] = low_limit;
						for(var i = 0; i < cell_line_data.length; ++i) 
							for(var j = 0; j < cell_line_data[i].expr.length; ++j) 
								if(cell_line_data[i].expr[j] <= low_limit) cell_line_data[i].expr[j] = low_limit;
						for(var i = 0; i < group_data.length; ++i) 
							for(var j = 0; j < group_data[i].length; ++j) 
								if(group_data[i][j].expr <= low_limit) group_data[i][j].expr = low_limit;
						*/
						
						normal_data.forEach(function(item, index, array) {
							var dist_item = {
								lowest: parseFloat(item.expr[5]),
								interval: (transform(parseFloat(item.expr[item.expr.length - 1]) + low_limit) - transform(parseFloat(item.expr[5]))) / interval_num,
								max: 0,
								distribution: new Array(interval_num)
							};
							for (var i = 0; i < dist_item.distribution.length; ++ i)
								dist_item.distribution[i] = 0;
							for(var i = 5; i < item.expr.length; ++i) {
								normal_plot_data.push({
									tissue: item.tissue,
									expr: item.expr[i]
								});
								t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
								if (t > dist_item.max)
									dist_item.max = t;
							}
							normal_distribution[item.tissue] = dist_item;
						});
						cell_line_data.forEach(function(item, index, array) {
							var dist_item = {
								lowest: parseFloat(item.expr[5]),
								interval: (transform(parseFloat(item.expr[item.expr.length - 1]) + low_limit) - transform(parseFloat(item.expr[5]))) / interval_num,
								max: 0,
								distribution: new Array(interval_num)
							};
							for (var i = 0; i < dist_item.distribution.length; ++ i)
								dist_item.distribution[i] = 0;
							if (item.mutation == undefined)
								for(var i = 5; i < item.expr.length; ++i) {
									cell_line_plot_data.push({
										tissue: item.tissue,
										cell_line: item.header[i - 5],
										expr: item.expr[i]
									});
									t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
									if (t > dist_item.max)
										dist_item.max = t;
								}
							else
								for(var i = 5; i < item.expr.length; ++i) {
									cell_line_plot_data.push({
										tissue: item.tissue,
										cell_line: item.header[i - 5],
										expr: item.expr[i],
										var_reads: item.var_reads[i - 5],
										total_reads: item.total_reads[i - 5],
										var_freq: item.var_freq[i - 5],
										mutation: 1
									});
									t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
									if (t > dist_item.max)
										dist_item.max = t;
								}
							cell_line_distribution[item.tissue] = dist_item;
						});
					} else {
						for(var i = 0; i < normal_data.length;) {
							var delete_this = false;
							for(var j = 0; j < 5; ++j) 
								if(normal_data[i].expr[j] <= low_limit) {
									delete_this =true;
									break;
								}
							if(delete_this) 
								normal_data.splice(i, 1);
							else
								++i;
						}
						
						for(var i = 0; i < cell_line_data.length;) {
							var delete_this = false;
							for(var j = 0; j < 5; ++j) 
								if(cell_line_data[i].expr[j] <= low_limit) {
									delete_this =true;
									break;
								}
							if(delete_this)
								cell_line_data.splice(i, 1);
							else
								++i;
						}
						
						for (var i = 0; i < highlighted_cell_line_data.length;)
							if (highlighted_cell_line_data[i].expr <= low_limit)
								highlighted_cell_line_data.splice(i, 1);
							else 
								++ i;
						for(var i = 0; i < group_data.length; ++i)
							for(var j = 0; j < group_data[i].length;)
								if(group_data[i][j].expr <= low_limit)
									group_data[i].splice(j, 1);
								else
									++j;
						
						for(var i = 0; i < custom_data.length; ++i)
							for(var j = 0; j < custom_data[i].data.length;)
								if(custom_data[i].data[j].expr <= low_limit)
									custom_data[i].data.splice(j, 1);
								else
									++j;
						
						for(var i = 0; i < normal_data.length; ++i) 
							normal_data[i].discard_num = normal_data[i].expr[5].toString() + '/' + (normal_data[i].expr.length + parseInt(normal_data[i].expr[5]) - 6);
						
						for(var i = 0; i < cell_line_data.length; ++i) 
							cell_line_data[i].discard_num = cell_line_data[i].expr[5].toString() + '/' + (cell_line_data[i].expr.length + parseInt(cell_line_data[i].expr[5]) - 6);
						
						normal_data.forEach(function(item, index, array) {
							var dist_item = {
								lowest: parseFloat(item.expr[6]),
								interval: (transform(parseFloat(item.expr[item.expr.length - 1]) + low_limit) - transform(parseFloat(item.expr[6]))) / interval_num,
								max: 0,
								distribution: new Array(interval_num)
							};
							for (var i = 0; i < dist_item.distribution.length; ++ i)
								dist_item.distribution[i] = 0;
							for(var i = 6; i < item.expr.length; ++i) {
								normal_plot_data.push({
									tissue: item.tissue,
									expr: item.expr[i],
								});
								t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
								if (t > dist_item.max)
									dist_item.max = t;
							}
							normal_distribution[item.tissue] = dist_item;
						});
						
						cell_line_data.forEach(function(item, index, array) {
							var dist_item = {
								lowest: parseFloat(item.expr[6]),
								interval: (transform(parseFloat(item.expr[item.expr.length - 1]) + low_limit) - transform(parseFloat(item.expr[6]))) / interval_num,
								max: 0,
								distribution: new Array(interval_num)
							};
							for (var i = 0; i < dist_item.distribution.length; ++ i)
								dist_item.distribution[i] = 0;
							if (item.mutation == undefined)
								for(var i = 6; i < item.expr.length; ++i) {
									cell_line_plot_data.push({
										tissue: item.tissue,
										cell_line: item.header[i - 6],
										expr: item.expr[i]
									});
									t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
									if (t > dist_item.max)
										dist_item.max = t;
								}
							else 
								for(var i = 6; i < item.expr.length; ++i) {
									cell_line_plot_data.push({
										tissue: item.tissue,
										cell_line: item.header[i - 6],
										expr: item.expr[i],
										var_reads: item.var_reads[i - 6],
										total_reads: item.total_reads[i - 6],
										var_freq: item.var_freq[i - 6],
										mutation: 1
									});
									t = dist_item.distribution[Math.floor((transform(parseFloat(item.expr[i])) - transform(dist_item.lowest)) / dist_item.interval)] += 1;
									if (t > dist_item.max)
										dist_item.max = t;
								}
							cell_line_distribution[item.tissue] = dist_item;
						});
					}
					
					for (var i = 0; i < normal_data.length; ++ i) {
						var tissue = normal_data[i].tissue.replace(/ \(.*\)/g, '').toLowerCase();	//Here is a space before \()
						normal_data[i].color = colors[tissue];
					}
					
					for (var i = 0; i < cell_line_data.length; ++ i) {
						var tissue = cell_line_data[i].tissue.replace(/ \(.*\)/g, '').toLowerCase();	//Here is a space before \()
						cell_line_data[i].color = colors[tissue];
					}
					
					highlighted_cell_line_data.forEach(function(item) {
						var tissue = cell_line_hash[item.cell_line].toUpperCase() + ' (CELL LINE)';
						if (cell_line_data.some(function(d) { return d.tissue == tissue; }))
							highlighted_cell_line_plot_data.push({
								tissue: tissue,
								cell_line: item.cell_line,
								expr: item.expr
							});
					});
					
					group_data.forEach(function(item, index, array) {
						var outer_index = index;
						item.forEach(function(item, index, array) {
							group_plot_data.push({
								group: group_names[outer_index],
								cell_line: item.cell_line,
								expr: item.expr
							});
						});
					});
					
					custom_data.some(function(item, index, array) {
						if(item.gene == gene_name) {
							item.data.forEach(function(item, index, array) {
								custom_plot_data.push({sample: item.sample, expr: item.expr}); 
							});
						} else
							return false;
					});
					
					function calc_distribution(expr, dist_item) {
						if (scatter_distribution)
							return dist_item.distribution[Math.floor((transform(expr) - transform(dist_item.lowest)) / dist_item.interval)] / dist_item.max;
						else
							return 1;
					}
			
					var margin = {top: 20, right: 50, bottom: 100, left: 40},
						width = $(".right-area").width() - 40 - margin.left - margin.right,
						height = 600 - margin.top - margin.bottom;
					var entire_svg, svg, x, y, xAxis, yAxis;
					
					function draw() {
						svg.append("g")
							.attr("class", "x axis")
							.attr("transform", "translate(0," + height + ")")
							.call(xAxis)
							.selectAll("text")
								.attr("y", 6)
								.attr("font-size", "14px")
								.style("text-anchor", "start")
								.attr("transform", "rotate(30)");

						yAxisText = svg.append("g")
							.attr("class", "y axis")
							.call(yAxis);
						yAxisText.append("text")
							.attr("transform", "rotate(-90)")
							.attr("y", 6)
							.attr("dy", ".75em")
							.style("font-size", "16px")
							.style("text-anchor", "end")
							.text("Gene expression");
						yAxisText.append("text")
							.attr("x", 150)
							.attr("y", 6)
							.attr("dy", ".75em")
							.style("font-size", "24px")
							.style("text-anchor", "end")
							.text(gene_name);
						
						var line_prop = 0.6;
						var dx_left = x.rangeBand() * (1 - line_prop) / 2;
						var dx_right =  x.rangeBand() * (1 - (1 - line_prop) / 2);
						var line_stroke = '#ccc', line_stroke_width = '2px';
						var rect_stroke = '#ccc', rect_stroke_width = '2px';
						 
						var item=svg.selectAll(".normal.box").data(normal_data).enter();
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","normal box")
							.attr("x1", function(d) { return x(d.tissue) + dx_left; })
							.attr("y1", function(d) { return y(d.expr[0]); })
							.attr("x2", function(d) { return x(d.tissue) + dx_right; })
							.attr("y2", function(d) { return y(d.expr[0]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","normal box")
							.attr("x1", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y1", function(d) { return y(d.expr[0]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y2", function(d) { return y(d.expr[1]); });
						item.append("rect").style('stroke', rect_stroke).style('stroke-width', rect_stroke_width)
							.attr("class","normal box")
							.attr("x", function(d) { return x(d.tissue); })
							.attr("width", x.rangeBand())
							.attr("y", function(d) { return y(d.expr[3]); })
							.attr("height", function(d) { return y(d.expr[1]) - y(d.expr[3]); });
						item.append("line").style('stroke', '#000').style('stroke-width', '3px')
							.attr("class", "normal box")
							.attr("x1", function(d) { return x(d.tissue); })
							.attr("y1", function(d) { return y(d.expr[2]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand(); })
							.attr("y2", function(d) { return y(d.expr[2]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","normal box")
							.attr("x1", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y1", function(d) { return y(d.expr[3]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y2", function(d) { return y(d.expr[4]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","normal box")
							.attr("x1", function(d) { return x(d.tissue) + dx_left; })
							.attr("y1", function(d) { return y(d.expr[4]); })
							.attr("x2", function(d) { return x(d.tissue) + dx_right; })
							.attr("y2", function(d) { return y(d.expr[4]); });
							
						item=svg.selectAll(".cell-line.box").data(cell_line_data).enter();
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","cell-line box")
							.attr("x1", function(d) { return x(d.tissue) + dx_left; })
							.attr("y1", function(d) { return y(d.expr[0]); })
							.attr("x2", function(d) { return x(d.tissue) + dx_right; })
							.attr("y2", function(d) { return y(d.expr[0]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","cell-line box")
							.attr("x1", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y1", function(d) { return y(d.expr[0]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y2", function(d) { return y(d.expr[1]); });
						item.append("rect").style('stroke', rect_stroke).style('stroke-width', rect_stroke_width)
							.attr("class","cell-line box")
							.attr("x", function(d) { return x(d.tissue); })
							.attr("width", x.rangeBand())
							.attr("y", function(d) { return y(d.expr[3]); })
							.attr("height", function(d) { return y(d.expr[1]) - y(d.expr[3]); });
						item.append("line").style('stroke', '#000').style('stroke-width', '3px')
							.attr("class", "cell-line box")
							.attr("x1", function(d) { return x(d.tissue); })
							.attr("y1", function(d) { return y(d.expr[2]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand(); })
							.attr("y2", function(d) { return y(d.expr[2]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","cell-line box")
							.attr("x1", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y1", function(d) { return y(d.expr[3]); })
							.attr("x2", function(d) { return x(d.tissue) + x.rangeBand() / 2; })
							.attr("y2", function(d) { return y(d.expr[4]); });
						item.append("line").style('stroke', line_stroke).style('stroke-width', line_stroke_width)
							.attr("class","cell-line box")
							.attr("x1", function(d) { return x(d.tissue) + dx_left; })
							.attr("y1", function(d) { return y(d.expr[4]); })
							.attr("x2", function(d) { return x(d.tissue) + dx_right; })
							.attr("y2", function(d) { return y(d.expr[4]); });
						
						if(show_samples) {
							svg.selectAll(".normal.commonDot").data(normal_plot_data).enter()
							.append("circle")
								.attr("class", "normal commonDot")
								.attr("r","4")
								.attr("cx", function(d) { return x(d.tissue) + x.rangeBand() / 2 + x.rangeBand() * 0.8 * calc_distribution(d.expr, normal_distribution[d.tissue]) * (Math.random() - 0.5); })
								.attr("cy", function(d) { return y(d.expr); });
							
							svg.selectAll(".cell-line.commonDot").data(cell_line_plot_data).enter()
							.append("circle")
								.attr("class", "cell-line commonDot")
								.attr("r","4")
								.attr("cx", function(d) { return x(d.tissue) + x.rangeBand() / 2 + x.rangeBand() * 0.8 * calc_distribution(d.expr, cell_line_distribution[d.tissue]) * (Math.random() - 0.5); })
								.attr("cy", function(d) { return y(d.expr); });
						}
						
						item = svg.selectAll(".highlighted.highlightedDot").data(highlighted_cell_line_plot_data).enter();
						item.append("circle")
							.attr("class", "highlighted highlightedDot")
							.attr("r", "6")
							.attr("cx", function(d) { return x(d.tissue) + x.rangeBand() / 2 + x.rangeBand() * 0.2 * (Math.random() - 0.5); })
							.attr("cy", function(d) { return y(d.expr); });
						item.append("text")
							.attr("x", function(d) { return x(d.tissue) + x.rangeBand() / 2 + x.rangeBand() * 0.2 * (Math.random() - 0.5) - d.cell_line.length * 6 / 2; })
							.attr("y", function(d) { return y(d.expr) + 16; })
							.style("font-size", "12px")
							.text(function(d) { return d.cell_line; });
							
						svg.selectAll(".group.groupDot").data(group_plot_data).enter()
						.append("circle")
							.attr("class", "group groupDot")
							.attr("r","4")
							.attr("cx", function(d) { return x(d.group) + x.rangeBand() / 2 + x.rangeBand() * 0.2 * (Math.random() - 0.5); })
							.attr("cy", function(d) { return y(d.expr); });
							
						svg.selectAll(".custom.customDot").data(custom_plot_data).enter()
						.append("circle")
							.attr("class", "custom customDot")
							.attr("r","4")
							.attr("cx", function(d) { return x(custom_sample) + x.rangeBand() / 2 + x.rangeBand() * 0.2 * (Math.random() - 0.5); })
							.attr("cy", function(d) { return y(d.expr); });
					}
						
					function drawLinear() {
						x = d3.scale.ordinal()
							.rangeRoundBands([0, width], .3);

						y = d3.scale.linear()
							.range([height, 0]);

						xAxis = d3.svg.axis()
							.scale(x)
							.orient("bottom");
							
						yAxis = d3.svg.axis()
							.scale(y)
							.orient("left")
							.ticks(10);
						entire_svg = d3.select(".right-area").append("svg")
							.attr("width", width + margin.left + margin.right)
							.attr("height", height + margin.top + margin.bottom);
						svg = entire_svg.append("g")
							.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
						
						var tmp_array = normal_data.map(function(d) { return d.tissue; }).concat(cell_line_data.map(function(d) { return d.tissue; }));
						if (by_tissue) {
							for(var i = 0; i < tmp_array.length; ++ i)
								tmp_array[i] = tmp_array[i].replace(/\(NORMAL\)/,"(A)");
							tmp_array.sort();
							for(var i = 0; i < tmp_array.length; ++ i)
								tmp_array[i] = tmp_array[i].replace(/\(A\)/,"(NORMAL)");
						}
						if(custom_plot_data.length != 0)
							x.domain(tmp_array.concat(group_names).concat([custom_sample]));
						else
							x.domain(tmp_array.concat(group_names));
						var yMax = Math.max(Math.max.apply(Math, normal_data.map(function(d) { return d.expr[d.expr.length - 1]; })),
							Math.max.apply(Math, cell_line_data.map(function(d) { return d.expr[d.expr.length - 1]; })),
							Math.max.apply(Math, highlighted_cell_line_plot_data.map(function(d) { return d.expr; })),
							Math.max.apply(Math, group_plot_data.map(function(d) { return d.expr; })));
						if(custom_plot_data.length != 0)
							yMax = Math.max(yMax,
								Math.max.apply(Math, custom_plot_data.map(function(d) { return d.expr; })));
						y.domain([0, yMax]);

						draw();
					}
					
					function drawLog() {
						x = d3.scale.ordinal()
							.rangeRoundBands([0, width], .3);

						y = d3.scale.log()
							.range([height, 0]);

						xAxis = d3.svg.axis()
							.scale(x)
							.orient("bottom");
							
						yAxis = d3.svg.axis()
							.scale(y)
							.orient("left")
							.ticks(10);
						
						entire_svg = d3.select(".right-area").append("svg")
							.attr("width", width + margin.left + margin.right)
							.attr("height", height + margin.top + margin.bottom);
						svg = entire_svg.append("g")
							.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
						
						var tmp_array = normal_data.map(function(d) { return d.tissue; }).concat(cell_line_data.map(function(d) { return d.tissue; }));
						if (by_tissue) {
							for(var i = 0; i < tmp_array.length; ++ i)
								tmp_array[i] = tmp_array[i].replace(/\(NORMAL\)/,"(A)");
							tmp_array.sort();
							for(var i = 0; i < tmp_array.length; ++ i)
								tmp_array[i] = tmp_array[i].replace(/\(A\)/,"(NORMAL)");
						}
						if(custom_plot_data.length != 0)
							x.domain(tmp_array.concat(group_names).concat([custom_sample]));
						else
							x.domain(tmp_array.concat(group_names));
						var yMax = Math.max(Math.max.apply(Math, normal_data.map(function(d) { return d.expr[d.expr.length - 1]; })),
							Math.max.apply(Math, cell_line_data.map(function(d) { return d.expr[d.expr.length - 1]; })),
							Math.max.apply(Math, highlighted_cell_line_plot_data.map(function(d) { return d.expr; })),
							Math.max.apply(Math, group_plot_data.map(function(d) { return d.expr; })));
						var yMin;
						if(include_zero)
							yMin = Math.min(Math.min.apply(Math, normal_data.map(function(d) { return Math.min(d.expr[0], d.expr[5] == undefined ? high_limit : d.expr[5]); })),
								Math.min.apply(Math, cell_line_data.map(function(d) { return Math.min(d.expr[0], d.expr[5] == undefined ? high_limit : d.expr[5]); })),
								Math.min.apply(Math, highlighted_cell_line_plot_data.map(function(d) { return d.expr; })),
								Math.min.apply(Math, group_plot_data.map(function(d) { return d.expr; })));
						else
							yMin = Math.min(Math.min.apply(Math, normal_data.map(function(d) { return Math.min(d.expr[0], d.expr[6] == undefined ? high_limit : d.expr[6]); })),
								Math.min.apply(Math, cell_line_data.map(function(d) { return Math.min(d.expr[0], d.expr[6] == undefined ? high_limit : d.expr[6]); })),
								Math.min.apply(Math, highlighted_cell_line_plot_data.map(function(d) { return d.expr; })),
								Math.min.apply(Math, group_plot_data.map(function(d) { return d.expr; })));
						if(custom_plot_data.length != 0) {
							yMax = Math.max(yMax,
								Math.max.apply(Math, custom_plot_data.map(function(d) { return d.expr; })));
							yMin = Math.min(yMin,
								Math.min.apply(Math, custom_plot_data.map(function(d) { return d.expr; })));
						}				
						yMin =  Math.max(0.0001, yMin);
						y.domain([yMin, yMax]);

						draw();
					}	
						
					if(is_linear)	
						drawLinear();
					else
						drawLog();
					
					d3.selectAll(".box").on("mouseover", function(d) {
						d3.select("#boxInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						d3.select("#p5").text(d.expr[0]);
						d3.select("#p25").text(d.expr[1]);
						d3.select("#mediam").text(d.expr[2]);
						d3.select("#p75").text(d.expr[3]);
						d3.select("#p95").text(d.expr[4]);
						if(include_zero)
							$("#zeroValueAmount").hide();
						else {
							$("#zeroValueAmount").children("span").text(d.discard_num);
							$("#zeroValueAmount").show();
						}
							
						
					}).on("mouseout", function(d) {
						d3.select("#boxInf")
							.classed("hidden", true);
					});
					
					d3.selectAll("circle.normal").on("mouseover", function(d) {
						d3.select("#normalDotInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						d3.select("#normalTissueInf").text(d.tissue);
						d3.select("#normalExpressionInf").text(d.expr);
					}).on("mouseout", function(d) {
						d3.select("#normalDotInf")
							.classed("hidden", true);
					});
					
					d3.selectAll("circle.cell-line").on("mouseover", function(d) {
						d3.select("#cellLineDotInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						if(d.mutation == undefined)
							$('#cellLineDotInf').html('<p>Tissue: ' + d.tissue + '</p>'
								+ '<p>Cell line: ' + d.cell_line + '</p>'
								+ '<p>Expression: ' + d.expr + '</p>');
						else
							$('#cellLineDotInf').html('<p>Tissue: ' + d.tissue + '</p>'
							+ '<p>Cell line: ' + d.cell_line + '</p>'
							+ '<p>Expression: ' + d.expr + '</p>'
							+ '<p>Var reads: ' + d.var_reads + '</p>'
							+ '<p>Total reads: ' + d.total_reads + '</p>'
							+ '<p>Var freq: ' + d.var_freq + '</p>');
					}).on("mouseout", function(d) {
						d3.select("#cellLineDotInf")
							.classed("hidden", true);
					});
					
					d3.selectAll("circle.highlighted").on("mouseover", function(d) {
						d3.select("#highlightedDotInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						d3.select("#highlightedCellLineInf").text(d.cell_line);
						d3.select("#highlightedExpressionInf").text(d.expr);
					}).on("mouseout", function(d) {
						d3.select("#highlightedDotInf")
							.classed("hidden", true);
					});
					
					d3.selectAll("circle.group").on("mouseover", function(d) {
						d3.select("#groupDotInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						d3.select("#cellLineGroupInf").text(d.group);
						d3.select("#groupCellLineInf").text(d.cell_line);
						d3.select("#groupExpressionInf").text(d.expr);
					}).on("mouseout", function(d) {
						d3.select("#groupDotInf")
							.classed("hidden", true);
					});
					
					d3.selectAll("circle.custom").on("mouseover", function(d) {
						d3.select("#customDotInf")
							.classed("hidden", false)
							.style("left", d3.mouse(document.body)[0] + "px")
							.style("top", d3.mouse(document.body)[1] + "px");
						d3.select("#customSampleInf").text(d.sample);
						d3.select("#customExpressionInf").text(d.expr);
					}).on("mouseout", function(d) {
						d3.select("#customDotInf")
							.classed("hidden", true);
					});
					
					//$('rect.box').css('stroke', '#000').css('stroke-width', '2px');
					//$('rect.normal').css('fill', 'SteelBlue');
					d3.selectAll('rect.normal').style('fill', function(d) { return 'hsl(' + d.color + ',100%,70%)'; });
					//$('rect.cell-line').css('fill', 'DarkOrchid');
					d3.selectAll('rect.cell-line').style('fill', function(d) { return 'hsl(' + d.color + ',100%,60%)'; });
					$('circle.commonDot').css('stroke', '#000').css('stroke-width', '0.5px');
					$('circle.normal').css('opacity', '0.5').css('fill', 'gray');
					$('circle.cell-line').css('opacity', '0.5').css('fill', 'gray');
					$('circle.highlightedDot').css('stroke', '#000').css('stroke-width', '0.5px');
					$('circle.highlighted').css('fill', 'black');
					$('circle.groupDot').css('stroke', '#000').css('stroke-width', '0.5px');
					$('circle.group').css('opacity', '0.5').css('fill', 'gray');
					$('circle.customDot').css('stroke', '#000').css('stroke-width', '0.5px');
					$('circle.custom').css('opacity', '0.5').css('fill', 'red');
					//$('line.box').css('stroke', '#000').css('stroke-width', '2px');
					$('.axis').css('font', '10px arial');
					$('.axis path').css('fill', 'none').css('stroke', '#000').css('shape-rendering', 'crispEdges');
					$('.axis line').css('fill', 'none').css('stroke', '#000').css('shape-rendering', 'crispEdges');
					
					$('<li><a>' + gene_name + '</a></li>').appendTo('#download').click(function() {
						$('#tempDiv').children('svg').attr('width', width + margin.left + margin.right).attr('height', height + margin.top + margin.bottom).append(entire_svg.html());
						$('#pdfData').val($('#tempDiv').html());
						$('#tempDiv').children('svg').empty();
						//console.log($('#pdfData').val());
						$('#pdfForm').submit();
					});
					
					t_test_data[gene_name] = new Object();
					normal_plot_data.forEach(function(item) {
						if (t_test_data[gene_name][item.tissue] == undefined)
							t_test_data[gene_name][item.tissue] = [];
						t_test_data[gene_name][item.tissue].push(parseFloat(item.expr));
					});
					cell_line_plot_data.forEach(function(item) {
						if (t_test_data[gene_name][item.tissue] == undefined)
							t_test_data[gene_name][item.tissue] = [];
						t_test_data[gene_name][item.tissue].push(parseFloat(item.expr));
					});
					$('#tTestGene').siblings('ul').append('<li class="t-test-gene-item">' + gene_name + '</li>');
				}
			});
			
		</script>
	
	</head>

	<body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
		</button>
			<a class="navbar-brand">CEVIN</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a>Viewer</a></li>
				<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Download PDF<span class="caret"></span></a>
					<ul id="download"class="dropdown-menu">
					</ul>
				</li>
				<li id="tTest" data-toggle="modal" data-target="#tTestModal"><a>t test</a></li>
			</ul>
        </div>
      </div>
    </nav>
	<div class="alert alert-danger" role="alert" id="alert" style="display:none; position:fixed; top:50px; width:100%">
    </div>
	<div class="alert alert-success" role="alert" id="success" style="display:none; position:fixed; top:50px; width:100%">
     </div>
	<div class="left-panel">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Select gene(s) to display</h3>
			</div>
			<div class="panel-body" id="genePanel">
				<input type="text" id="gene" class="basic-input" data-provide="typeahead" autocomplete="off">
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Show normal tissue(s) types</h3>
			</div>
			<div class="panel-body" id="tissuePanel">
				<div class="input-group input-group-sm">
					<input type="text" id="tissue" class="form-control" data-provide="typeahead" autocomplete="off" >
					<span class="input-group-btn">
						<button class="btn btn-default" type="button"
							data-toggle="modal" data-target="#normalTissueModal"
							data-tooltip="tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to view normal tissues list.">
							<span class="glyphicon glyphicon-th-list"></span>
						</button>
						<button class="btn btn-default" type="button"
							data-toggle="modal" data-target="#dataSourceModal" data-url="http://www.gtexportal.org/home/gene/SLK" data-content="The normal tissues data were downloaded from the GTEx website at the Broad Institute."
							data-tooltip="tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to view data source.">
							<span class="glyphicon glyphicon-info-sign"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Show cell lines by tissue type</h3>
			</div>
			<div class="panel-body" id="cellLinePanel">
				<div class="input-group input-group-sm">
					<input type="text" id="cellLine" class="form-control" data-provide="typeahead" autocomplete="off" >
					<span class="input-group-btn">
						<button class="btn btn-default" type="button"
							data-toggle="modal" data-target="#cellLineTissueModal"
							data-tooltip="tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to view cell line tissues list.">
							<span class="glyphicon glyphicon-th-list"></span>
						</button>
						<button class="btn btn-default" type="button"
							data-toggle="modal" data-target="#dataSourceModal" data-url="http://www.ncbi.nlm.nih.gov/pubmed/25485619" data-content="The RNA-seq data and mutation data for 675 cancer cell lines is from the Nat Biotechnol. publication by Klijn et al."
							data-tooltip="tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to view data source.">
							<span class="glyphicon glyphicon-info-sign"></span>
						</button>
					</span>
				</div>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Highlighted cell lines</h3>
			</div>
			<div class="panel-body" id="highlightedCellLinePanel">
				<input type="text" id="highlightedCellLineText" class="form-control" data-provide="typeahead" autocomplete="off" >
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Custom groups of cell lines</h3>
			</div>
			<div class="panel-body" id="group">
				<button type="button" id="addAGroup" class="btn btn-sm btn-default">Add a group</button>
			</div>
		</div>
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">Select mutations (max=1)</h3>
			</div>
			<div class="panel-body" id="mutationPanel">
				<div class="input-group input-group-sm">
					<input type="text" id="mutationText" class="form-control" data-provide="typeahead" autocomplete="off" >
					<span class="input-group-btn">
						<button class="btn btn-default" type="button"
							data-toggle="modal" data-target="#dataSourceModal" data-url="http://cancer.sanger.ac.uk/cosmic" data-content="The data for the presence or absence of mutations in cancer samples data are taken from the WTSI COSMIC database."
							data-tooltip="tooltip" data-toggle="tooltip" data-placement="bottom" title="Click to view data source.">
							<span class="glyphicon glyphicon-info-sign"></span>
						</button>
					</span>
				</div>
				<span class="badge" id="mutationBadge" style="display:none"><span id="mutationGene"></span><i class="icon-white icon-remove-sign"></i></span>
				<div style="margin-top:5px"><span>Cosmic: </span>
					<div class="btn-group">
						<button type="button" id="cosmicText" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Any<span class="caret"></span></button>
						<ul id="cosmicMenu" class="dropdown-menu">
							<li><a>Any</a></li>
							<li><a>Not present</a></li>
							<li><a>Present</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Upload custom RNA-seq data file</h3>
			</div>
			<div class="panel-body" id="customPanel">
				<form method="post" enctype="multipart/form-data" action="javascript: upload();">
					<div class="div-upload"> 
						Select file
						<input type="file" name="fileName" id="fileName"/>
					</div>
					<div>
						<input type="submit" id="fileUpload" value="Confirm" class="btn btn-sm btn-default" style="width:110px"/>
					</div>
				</form>
				<button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#exampleFormatModal" style="width:110px; margin-top:5px">Example format</button>
			</div>
		</div>
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Display parameters</h3>
			</div>
			<div class="panel-body" id="viewPanel">
				<div style="width:80px; margin-left:auto; margin-right:auto; margin-top:10px; margin-bottom:10px">
					<input type="checkbox" id="scaleSelect" data-toggle="toggle" data-on="Linear" data-off="Log">
				</div>
				<div style="width:125px; margin-left:auto; margin-right:auto; margin-top:10px; margin-bottom:10px">
					<input type="checkbox" id="individualSelect" data-toggle="toggle" data-on="Box plot only" data-off="Show samples">
				</div>
				<div style="width:108px; margin-left:auto; margin-right:auto; margin-top:10px; margin-bottom:10px">
					<input type="checkbox" id="scatterSelect" data-toggle="toggle" data-on="Random" data-off="Distribution">
				</div>
				<!--div style="width:100px; margin-left:auto; margin-right:auto; margin-top:10px; margin-bottom:10px">
					<input type="checkbox" id="groupSelect" data-toggle="toggle" data-on="By default" data-off="By tissue">
				</div-->
				<div style="width:117px; margin-left:auto; margin-right:auto; margin-top:10px; margin-bottom:10px">
					<input type="checkbox" id="zeroValueSelect" data-toggle="toggle" data-on="Include zero" data-off="Discard zero">
				</div>
			</div>
		</div>
		<div style="width:80px; margin-left:auto; margin-right:auto">
			<button type="button" id="display" class="btn btn-lg btn-default">Display</button>
		</div>
	</div>
	<div class="right-area" >
	</div>
	<div class="modal fade" id="dataSourceModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Data source</h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary" id="goToSource">Go</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="exampleFormatModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Sample format</h4>
				</div>
				<div class="modal-body">
					<p>Users can upload more than one custom file. The custom file can contain several lines. Each line can contain several fields, which are separated by space or tab and the first line must be a header. Below is an example of the format:</p>
					<table class="table">
						<thead>
							<th>Gene</th><th>Sample 1</th><th>Sample 2</th><th>Sample 3</th><th>CSample 4</th><th>Sample 5</th>
						</thead>
						<tbody>
							<tr>
								<th>NOC2L</th><td>3.14159</td><td>2.71828</td><td>0.84553</td><td>2.84753</td><td>2.85009</td>
							</tr>
							<tr>
								<th>LOC100133331</th><td>0.84212</td><td>1.34675</td><td>0.84553</td><td>2.84753</td><td>2.85009</td>
							</tr>
							<tr>
								<th>WASH7P</th><td>0.77308</td><td>1.07044</td><td>1.28648</td><td>1.04333</td><td>0.70662</td>
							</tr>
							<tr>
								<th>KLHL17</th><td>8.71234</td><td>4.43567</td><td>8.45531</td><td>3.57432</td><td>4.24513</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="normalTissueModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Normal tissues</h4>
				</div>
				<div class="modal-body">
					<table class="table">
						<thead>
							<td id="allNormalTissue"><input type="checkbox"/>All</td>
							<td></td>
							<td></td>
						</thead>
						</tbody>
					<?php
						$con=mysql_connect(DB_HOST,DB_USER,DB_PASS);
						mysql_select_db(DB_NAME,$con);
						$result = mysql_query("select * from normal_tissue_list");
						$i = 0;
						echo '<tr>';
						while ($row=mysql_fetch_array($result))
						{
							++ $i;
							if ($i == 1)
								echo '<tr>';
							echo '<td><input type="checkbox"/>' . $row['tissue'] . '</td>';
							if ($i == 3)
							{
								echo '</tr>';
								$i = 0;
							}
						}
						if ($i != 0)
						{
							while ($i < 3)
							{
								++ $i;
								echo '<td></td>';
							}
							echo '</tr>';
						}
					?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary confirm">Confirm</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="cellLineTissueModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Cell line tissues</h4>
				</div>
				<div class="modal-body">
					<table class="table">
						<thead>
							<td id="allCellLineTissue"><input type="checkbox"/>All</td>
							<td></td>
							<td></td>
						</thead>
						</tbody>
					<?php
						$con=mysql_connect(DB_HOST,DB_USER,DB_PASS);
						mysql_select_db(DB_NAME,$con);
						$result = mysql_query("select * from cell_line_tissue_list");
						$i = 0;
						echo '<tr>';
						while ($row=mysql_fetch_array($result))
						{
							++ $i;
							if ($i == 1)
								echo '<tr>';
							echo '<td><input type="checkbox"/>' . $row['tissue'] . '</td>';
							if ($i == 3)
							{
								echo '</tr>';
								$i = 0;
							}
						}
						if ($i != 0)
						{
							while ($i < 3)
							{
								++ $i;
								echo '<td></td>';
							}
							echo '</tr>';
						}
					?>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary confirm">Confirm</button>
				</div>
			</div>
		</div>
	</div>
		<div class="modal fade" id="tTestModal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Cell line tissues</h4>
				</div>
				<div class="modal-body">
					<span>
						Gene:
						<div class="btn-group">
							<button type="button" id="tTestGene" class="btn btn-default t-test">Invalid</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu">
							</ul>
						</div>
						Group A:
						<div class="btn-group" style="width:244px">
							<button type="button" id="tTestGroup1" class="btn btn-default t-test">Invalid</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu">
							</ul>
						</div>
						Group B:
						<div class="btn-group" style="width:244px">
							<button type="button" id="tTestGroup2" class="btn btn-default t-test">Invalid</button>
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu">
							</ul>
						</div>
						<button type="button" id="tTestCalc" class="btn btn-primary confirm disabled">Calculate</button>
					</span>
					<div id="tTestResult" style="display:none;margin:10px">
						<p>The two-tailed P value equals <span id="pValue"></span>.</p>
						<p>By conventional criteria, this difference is considered to be <span id="significance"></span>.</p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div id="boxInf" class="hidden">
			<p>95<sup>th</sup> percentile: <span id="p95">0</span></p>
			<p>75<sup>th</sup> percentile: <span id="p75">0</span></p>
			<p>Mediam: <span id="mediam">0</span></p>
			<p>25<sup>th</sup> percentile: <span id="p25">0</span></p>
			<p>5<sup>th</sup> percentile: <span id="p5">0</span></p>
			<p id="zeroValueAmount">Zero values: <span></span></p>
	</div>
	<div id="normalDotInf" class="hidden">
		<p>Tissue: <span id="normalTissueInf"></span></p>
		<p>Expression: <span id="normalExpressionInf"></span></p>
	</div>
	<div id="cellLineDotInf" class="hidden">
		<!--p>Tissue: <span id="cellLineTissueInf"></span></p>
		<p>Cell line: <span id="cellLineInf"></span></p>
		<p>Expression: <span id="cellLineExpressionInf"></span></p-->
	</div>
	<div id="highlightedDotInf" class="hidden">
		<p>Cell line: <span id="highlightedCellLineInf"></span></p>
		<p>Expression: <span id="highlightedExpressionInf"></span></p>
	</div>
	<div id="groupDotInf" class="hidden">
		<p>Group: <span id="cellLineGroupInf"></span></p>
		<p>Cell line: <span id="groupCellLineInf"></span></p>
		<p>Expression: <span id="groupExpressionInf"></span></p>
	</div>
	<div id="customDotInf" class="hidden">
		<p>Custom data</p>
		<p>Sample: <span id="customSampleInf"></span></p>
		<p>Expression: <span id="customExpressionInf"></span></p>
	</div>
	<div id="tempDiv" style="display:none">
		<svg>
		</svg>
	</div>
	<form id="pdfForm" target="_blank" method="post" action="html2pdf.php">
		<input id="pdfData" type="hidden" name="data" value="">
	</form>
  </body>
</html>
