/**
 * AI Contents Generator
 * 
 * @package JMAP::AIGENERATOR::administrator::components::com_jmap 
 * @subpackage js 
 * @author Joomla! Extensions Store
 * @copyright (C) 2021 Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
*/
(function($) {
	var AIContentGenerator = function () {
		/**
		 * Open first operation progress bar
		 * 
		 * @access private
		 * @return void 
		 */
		function openAIGeneratorProgress() {
			// Show first progress
			var firstProgress = '<div class="progress">' +
									'<div id="progressbar_aigenerator" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100">' +
										'<label class="visually-hidden">' + COM_JMAP_PROGRESSAIGENERATORTITLE + '</label>' +
									'</div>' +
								'</div>';
			
			// Build modal dialog
			var modalDialog =	'<div class="jmapmodal modal fade" id="aigenerator_process" tabindex="-1" role="dialog" aria-labelledby="progressModal" aria-hidden="true">' +
									'<div class="modal-dialog">' +
										'<div class="modal-content">' +
											'<div class="modal-header">' +
								        		'<h4 class="modal-title">' + COM_JMAP_PROGRESSAIGENERATORTITLE + '</h4>' +
								        		'<label class="closeaigenerator fas fa-times-circle"></label>' +
							        		'</div>' +
							        		'<div class="modal-body">' +
								        		'<p>' + firstProgress + '</p>' +
								        		'<p id="progressInfo1"></p>' +
							        		'</div>' +
							        		'<div class="modal-footer">' +
								        	'</div>' +
							        	'</div><!-- /.modal-content -->' +
						        	'</div><!-- /.modal-dialog -->' +
						        '</div>';
			// Inject elements into content body
			$('body').append(modalDialog);
			
			var modalOptions = {
					backdrop:'static'
				};
			
			let modalEl = document.querySelector('#aigenerator_process');
			let modalInstance = new bootstrap.Modal(modalEl, modalOptions);
			modalInstance.show();

			modalEl.addEventListener('shown.bs.modal', function(event) {
				$('#aigenerator_process div.modal-body').css({'width':'90%', 'margin':'auto'});
				$('#progressbar_aigenerator').css({'width':'100%'});
				// Inform user process initializing
				$('#progressInfo1').empty().append(COM_JMAP_PROGRESSAIGENERATORSUBTITLE);
			});
		};
		
		/**
		 * Register user events for interface controls
		 * 
		 * @access private
		 * @param Boolean initialize
		 * @return Void
		 */
		var addListeners = function(initialize) {
			// Append a dialog for the audit tool
			$('#jform_api label.radio:not(.active),#jform_removeimgs label.radio:not(.active),#generatebutton').on('click.aigenerator', function(jqEvent){
				if($(jqEvent.target).is('input') || $(jqEvent.target).is('a')) {
					$('#toolbar-cogs button').trigger('click');
				}
			});
			
			// Support for copy Clipoard buttons, new API and legacy API
			if(navigator.clipboard) {
				$(document).on('click', '#accordion_aigenerator_contents_results div.card-header.hasPopover', function(jqEvent){
					var context = $(this);
					var snippetTitle = $('h4', context).text().trim();
					if (window.event.ctrlKey) {
						var snippetDescription = context.next('div.card-body').html().trim();
					} else {
						var snippetDescription = context.next('div.card-body').text().trim();
					}
					
					navigator.clipboard.writeText(snippetTitle + ' - ' + snippetDescription)
					.then(function() {
						let tooltipInstance = new bootstrap.Tooltip($('h4',context).get(0),{
							trigger : 'click', 
							placement : 'bottom',
							title : COM_JMAP_AIGENERATOR_COPIED_CONTENT,
							template: '<div class="tooltip jmap_copy_tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
						});
						
						tooltipInstance.show();
						
						setTimeout(function(){
							tooltipInstance.dispose();
						}, 2000);
					})
					.catch(function(err) {
						var error = err;
					});
					return false;
				});
			}
			
			// Live event binding only once on initialize, avoid repeated handlers and executed callbacks
			if(initialize) {
				// Live event binding for close button AKA stop process
				$(document).on('click.aigenerator', 'label.closeaigenerator', function(jqEvent){
					let modalEl = document.querySelector('#aigenerator_process');
					if(modalEl) {
						bootstrap.Modal.getInstance(modalEl).hide();
					}
					window.stop();
				});
			}
		};
		
		/**
		 * Re-process images to ensure that at least a src is available
		 * 
		 * @access private
		 * @param Boolean initialize
		 * @return Void
		 */
		var reProcessImagesSrc = function(initialize) {
			var imgNodes = $('#contents_results div.card img');
			imgNodes.each(function(index, img){
				var imgNode = $(img);
				var hasASrc = img.hasAttribute('src') || img.hasAttribute('srcset');
				
				// The img is orphan, check if there is a data attribute to resume
				if(!hasASrc || imgNode.attr('src') == 'src') {
					var dataAttributes = ['data-src', 'data-original', 'data-lazyload', 'data-dt-lazy-src', 'data-lazy-src', 'data-noloadsrcset'];
					$.each(dataAttributes, function(index, dataAttribute){
						var dataValue = imgNode.attr(dataAttribute);
						if(dataValue) {
							if(dataAttribute != 'data-noloadsrcset') {
								imgNode.attr('src', dataValue);
							} else {
								imgNode.attr('srcset', dataValue);
							}
						}
					});
				}
			});
		};
		
		/**
		 * Public interface to the contents generator
		 * 
		 * @access public
		 * @return Void
		 */
		this.openProgressContentGeneration = function() {
			// Start first progress appending
			openAIGeneratorProgress();
		};
		
		/**
		 * Function dummy constructor
		 * 
		 * @access private
		 * @param String
		 *            contextSelector
		 * @method <<IIFE>>
		 * @return Void
		 */
		(function __construct() {
			// Add UI events
			addListeners.call(this, true);
			
			// Re-process images to ensure that at least a src is available
			reProcessImagesSrc();
		}).call(this);
		
	};
	
	// On DOM Ready
	$(function() {
		window.JMapAIContentGenerator = new AIContentGenerator();
	});
})(jQuery);