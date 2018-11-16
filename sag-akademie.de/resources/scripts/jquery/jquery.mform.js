/**
 * @author molle
 */


(function($) {
	
	$.extend({
		mForm: new function() {
			var self=this;
			/* caching object */
			this.mForm = {
				form: null,
				dynDiv: null, 
				buttonSelector: "#add",
				fieldCount: 0,
				actionLog: null
			};
			var mForm = this.mForm;
			
			function appendActionLog(action, id) {
				mForm.actionLog.append("<input type='hidden' name='"+action+"["+id+"]' value='"+id+"'/>");
			}
			
			
			this.construct = function(settings) {
				return this.each(function() {
					config = $.extend(this.config, {}, settings);
					mForm.form = $(this);
					mForm.dynDiv = mForm.form.find(".mFormContainer");
					mForm.actionLog = mForm.form.find(".mFormActionLog");
					
					/* datepicker */
					mForm.dynDiv.find(".dateInput").datepicker({
						showOn: 'button',
	 					buttonImage: '/css/theme/icons/calendar.png',
	  					buttonImageOnly: true,
	  					dateFormat: 'dd.mm.yy'
					});
					
					// pre existing entrys
					mForm.form.find(".dynFields").each(function() {
						var group = $(this); 
						group.find(".remove").click(function() {
							appendActionLog("remove",group.metadata().id);
							group.hide();
							group.remove();
						});
					});
				});
			};
			
			this.construct.cloneFrom = function (source) {
					mForm.form.find(mForm.buttonSelector).click(function() {
						mForm.fieldCount = mForm.fieldCount + 1; 
						var elements = $(source).clone();
						var id = "fields" + mForm.fieldCount;
						elements.attr("id", id);
						mForm.dynDiv.prepend(elements);
						
						var group = mForm.dynDiv.find("#" + id);
						group.html(group.html().replace(/#ID#/g, id ).replace(/templateFields/g, id));
										
						group.find(".remove").click(function () {
							group.hide();
							group.remove();
						});
						
						group.find(".dateInput").datepicker({
							showOn: 'button',
	 						buttonImage: '/css/theme/icons/calendar.png',
	  						buttonImageOnly: true,
	  						dateFormat: 'dd.mm.yy'
						});
						group.show();
					});	
			};
	 }
	});
	// extend plugin scope
	$.fn.extend({
		mForm: $.mForm.construct
	});
})(jQuery);