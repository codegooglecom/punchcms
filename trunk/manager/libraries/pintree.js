var PinnableTree = function (options) {
	var defaults = {
					tree: $("#treeContainer"),
					cachedSize: null,
					leftWrapHeight: null,
					isPinned: false,
					scrollWidth: null,
					anchorId: null,
					initWidth: null,
					maxWidth: null,
					timer: {}
				},
		opts 	= $.extend({}, defaults, options),
		self	= this
		
	this.init = function () {
		// Load default values
		if (opts.parentHeight == null) {
			opts.parentHeight = opts.tree.parent().height()
		}
		if (opts.scrollWidth == null) {
			self.scrollWidth()
		}
		if (opts.maxWidth == null) {
			opts.maxWidth = opts.tree.parent().width() / 2
		}
		if (opts.cachedSize == null) {
			opts.cachedSize = {
				"width": opts.tree.width(),
				"height": opts.tree.height()
			}
		}
		
		// Load cookie values
		self.loadSavedValues()
		
		// Initiate ExpandableTree
		if (opts.isPinned !== null && opts.isPinned) {
			_addAnchor()
			self.setWidth(opts.initWidth, true)
			
			// Bind dHtmlXtree events
			if (typeof window._treeApi == "object") {
				var intCount = 0;
				opts.timer["_intCheck"] = setInterval(function () {
					if (window._treeLoaded || (intCount == 1000)) {
						clearInterval(opts.timer["_intCheck"])
						self.pin(true)
					}
					
					intCount++;
				}, 10)
			}
		} else {
			self.bindTreeEvents()
		}
		
		$(window).bind("resize", function () {
			opts.timer["PinnableTree__resize"] = setTimeout(function () {
				clearTimeout(opts.timer["PinnableTree__resize"])
				self.measure()
			}, 200)
		})
		
		
		window.pin = self
		return self
	}
	
	this.maxWidth = function () {
		opts.maxWidth = opts.tree.parent().width() / 2
		return opts.maxWidth
	}
	
	this.scrollWidth = function () {
		opts.scrollWidth = opts.tree.find(".containerTableStyle")[0].scrollWidth
		return opts.scrollWidth
	}
	
	this.measure = function (blnOnLoad) {
		setTimeout(function () {
			var intWidth = (opts.tree.width() < self.scrollWidth()) ? self.scrollWidth() : opts.tree.find(".containerTableStyle table").width() + 20
			var intWidth = (intWidth > self.maxWidth()) ? self.maxWidth() : intWidth
			
			_cookie.set("PinnableTree__initWidth", intWidth)
			self.setWidth(intWidth, blnOnLoad)
		},100)
	}
	
	this.unpin = function () {
		self.setWidth(opts.cachedSize.width)
		
		// Reset handler
		_treeApi.setOnOpenHandler(function () { return true })
	}
	
	this.pin = function (blnOnLoad) {
		if (window._treeApi) {
			_treeApi.setOnOpenHandler(function () {
				self.pin()
				
				return true
			})
		} else {
			throw Error("Could not set onOpenHandler on TreeAPI. TreeAPI not loaded.")
		}
		
		self.measure(blnOnLoad)
	}
	
	this.bindTreeEvents = function () {
		if (opts.tree.length > 0) {
			opts.tree
				.bind("mouseenter mouseleave", function (e) {
					self[(e.type == "mouseenter") ? "showAnchor" : "hideAnchor"]()
				})
		}
	}
	
	this.setWidth = function (intWidth, blnOnLoad) {
		if (blnOnLoad) {
			opts.tree.width(intWidth)
			opts.tree.next().css("margin-left", intWidth + 25)
		} else {
			opts.tree
				.animate({
					width: intWidth
				}, {
					duration: 200,
					queue: false
				})
				.next()
				.animate({
					marginLeft: intWidth + 25
				},{
					duration: 200,
					queue: false
				})
		}
	}
	
	this.showAnchor = function () {
		if (opts.pinId == null) {
			// Anchor not yet registered, create one.
			_addAnchor()
		} else {
			var $anchor = $(opts.pinId)
			
			$anchor
				.text((opts.isPinned) ? "Unpin" : "Pin")
				.show()
		}
	}
	
	this.hideAnchor = function () {
		if (opts.pinId !== null) {
			$(opts.pinId).hide()
		}
	}
	
	this.loadSavedValues = function () {
		// Load isPinned value
		var blnIsPinned = (_cookie.get("PinnableTree__isPinned") === "true")
		if (blnIsPinned !== null && typeof blnIsPinned == "boolean") {
			opts.isPinned = blnIsPinned
		}
		
		// Load initial width
		opts.initWidth = (parseInt(_cookie.get("PinnableTree__initWidth")) > 0) ? parseInt(_cookie.get("PinnableTree__initWidth")) : null
	}
	
	_addAnchor = function () {
		var $anchor = $("<a id='PinnableTree_pin'/>")
		
		$anchor
			.css({
				position: "absolute",
				right: "5px",
				top: "5px",
				cursor: "pointer"
			})
			.text(_getAnchorLabel())
			.appendTo(opts.tree)
			.bind("click", function () {
				_cookie.set("PinnableTree__isPinned", !opts.isPinned)
				self[(opts.isPinned) ? "unpin" : "pin"]()
				
				opts.isPinned = !opts.isPinned
				
				$(this)
					.text(_getAnchorLabel())
					.toggleClass("isPinned")
					
				return false
			})
		
		// Save ID's for later use
		opts.pinId = "#PinnableTree_pin"
		opts.expandId = "#PinnableTree_expand"
	}
	
	_getAnchorLabel = function () {
		return opts.isPinned ? "Unpin" : "Pin"
	}
	
	return self.init()
}
PinnableTree.prototype;