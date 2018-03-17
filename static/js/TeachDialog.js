(function($) {
	$.fn.TeachDialog = function(options) {
		var defaults = {
			modalId : null,
			animation : null,
			title : "<h3>系统信息</h3>",
			content : '<p>Content</p>',
			showCloseButton : true,
			showCloseButtonName : '关闭',
			CloseButtonAddFunc : function() {
			},
			otherButtons : [],
			otherButtonStyles : [],
			bootstrapModalOption : {
				backdrop : 'static'
			},
			largeSize : false,
			smallSize : false,
			dialogShow : function() {
			},
			dialogShown : function() {
			},
			dialogHide : function() {
			},
			dialogHidden : function() {
			},
			clickButton : function(sender, modal, index) {
			},
		};
		options = $.extend(defaults, options);
		var modalID = '';

		function random(a, b) {
			return Math.random() > 0.5 ? -1 : 1;
		}

		function getModalID() {
			return "TeachDialog-"
					+ [ '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', 'Q', 'q', 'W', 'w', 'E', 'e', 'R', 'r', 'T', 't', 'Y', 'y', 'U', 'u', 'I', 'i', 'O', 'o', 'P', 'p', 'A', 'a', 'S', 's', 'D', 'd', 'F', 'f', 'G', 'g', 'H', 'h', 'J', 'j', 'K', 'k', 'L', 'l', 'Z', 'z', 'X', 'x', 'C', 'c', 'V',
							'v', 'B', 'b', 'N', 'n', 'M', 'm' ].sort(random).join('').substring(5, 20);
		}

		$.fn.extend({
			closeDialog : function(modal) {
				var modalObj = modal;
				modalObj.modal('hide');
			}
		});

		return this
				.each(function() {
					var obj = $(this);
					if (options.modalID == null) {
						modalID = getModalID();
					} else {
						modalID = options.modalID
					}
					var tmpHtml = '<div class="modal" id="{ID}" role="dialog" tabindex="-1" aria-hidden="true"><div class="modal-dialog {LargeModal}"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button><h1 class="modal-title">{title}</h1></div><div class="modal-body">{body}</div><div class="modal-footer">{button}</div></div></div></div>';

					var buttonHtml = '<button class="btn modalclosebtn" data-dismiss="modal" aria-hidden="true">{CloseName}</button>';
					if (!options.showCloseButton && options.otherButtons.length > 0) {
						buttonHtml = '';
					}
					var size = "";
					if (options.largeSize) {
						size = "modal-lg";
					}
					if (options.smallSize) {
						size = "modal-sm";
					}
					if (options.largeSize && options.smallSize) {
						size = "";
					}
					var btnClass = 'cls-' + modalID;
					for (var i = 0; i < options.otherButtons.length; i++) {
						buttonHtml += '<button buttonIndex="' + i + '" class="' + btnClass + ' btn ' + options.otherButtonStyles[i] + '">' + options.otherButtons[i] + '</button>';
					}
					tmpHtml = tmpHtml.replace(/{LargeModal}/g, size).replace(/{ID}/g, modalID).replace(/{title}/g, options.title).replace(/{body}/g, options.content).replace(/{button}/g, buttonHtml).replace(/{CloseName}/g, options.showCloseButtonName);
					obj.append(tmpHtml);

					var modalObj = $('#' + modalID);
					$('.modalclosebtn').click(function() {
						options.CloseButtonAddFunc();
					})
					$('.' + btnClass).click(function() {
						var index = $(this).attr('buttonIndex');
						options.clickButton($(this), modalObj, index);
					});
					modalObj.on('show.bs.modal', function() {
						options.dialogShow();
					});
					modalObj.on('shown.bs.modal', function() {
						options.dialogShown();
					});
					modalObj.on('hide.bs.modal', function() {
						options.dialogHide();
					});
					modalObj.on('hidden.bs.modal', function() {
						options.dialogHidden();
						modalObj.remove();
					});
					modalObj.modal(options.bootstrapModalOption);
					if (options.animation == null) {
						modalObj.addClass($('#dialoganimation').html()).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
							$(this).removeClass($('#dialoganimation').html());
						})
					} else {
						modalObj.addClass(options.animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
							$(this).removeClass(options.animation);
						})

					}
				});

	};

	$.extend({
		TeachDialog : function(options) {
			$("body").TeachDialog(options);
		}
	});

})(jQuery);
