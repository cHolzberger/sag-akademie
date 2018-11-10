/**
 * WARNING! THIS IS A GENERATED FILE, AND WILL BE RE-GENERATED EACH TIME THE
 * AJAXBRIDGE IS RUN.
 *
 * You should keep your javascript code inside this file as light as possible, 
 * and rather keep the body of your Ajax application in separate *.js files. 
 *
 * Do make a backup of your changes, before re-generating this file (AjaxBridge 
 * will display a warning message to you).
 *
 * Please refer to the built-in documentation inside the AjaxBridge application 
 * for help on using this file.
 */
 
 
/**
 * Application "FlexTable2.mxml"
 */

/**
 * The "FlexTable2" javascript namespace. All the functions/variables you
 * have selected under the "FlexTable2.mxml" in the tree will be
 * available as static members of this namespace object.
 */
FlexTable2 = {};


/**
 * Listen for the instantiation of the Flex application over the bridge
 */
FABridge.addInitializationCallback("b_FlexTable2", FlexTable2Ready);


/**
 * Hook here all the code that must run as soon as the "FlexTable2" class
 * finishes its instantiation over the bridge.
 *
 * For basic tasks, such as running a Flex method on the click of a javascript
 * button, chances are that both Ajax and Flex may well have loaded before the 
 * user actually clicks the button.
 *
 * However, using the "FlexTable2Ready()" is the safest way, as it will 
 * let Ajax know that involved Flex classes are available for use.
 */
function FlexTable2Ready() {

	// Initialize the "root" object. This represents the actual 
	// "FlexTable2.mxml" flex application.
	b_FlexTable2_root = FABridge["b_FlexTable2"].root();
	

	// Global variables in the "FlexTable2.mxml" application (converted 
	// to getters and setters)

	FlexTable2.getViewChooser = function () {
		return b_FlexTable2_root.getViewChooser();
	};


	FlexTable2.getGrid = function () {
		return b_FlexTable2_root.getGrid();
	};


	FlexTable2.getFadeOut = function () {
		return b_FlexTable2_root.getFadeOut();
	};


	FlexTable2.getViewStack = function () {
		return b_FlexTable2_root.getViewStack();
	};


	FlexTable2.getFadeIn = function () {
		return b_FlexTable2_root.getFadeIn();
	};


	FlexTable2.getViewGrid = function () {
		return b_FlexTable2_root.getViewGrid();
	};


	FlexTable2.getSuper = function () {
		return b_FlexTable2_root.getSuper();
	};


	FlexTable2.getThis = function () {
		return b_FlexTable2_root.getThis();
	};


	FlexTable2.getPageTitle = function () {
		return b_FlexTable2_root.getPageTitle();
	};


	FlexTable2.getPreloader = function () {
		return b_FlexTable2_root.getPreloader();
	};


	FlexTable2.getControlBarGroup = function () {
		return b_FlexTable2_root.getControlBarGroup();
	};


	FlexTable2.getScriptTimeLimit = function () {
		return b_FlexTable2_root.getScriptTimeLimit();
	};


	FlexTable2.getPreloaderChromeColor = function () {
		return b_FlexTable2_root.getPreloaderChromeColor();
	};


	FlexTable2.getFrameRate = function () {
		return b_FlexTable2_root.getFrameRate();
	};


	FlexTable2.getScriptRecursionLimit = function () {
		return b_FlexTable2_root.getScriptRecursionLimit();
	};


	FlexTable2.getUsePreloader = function () {
		return b_FlexTable2_root.getUsePreloader();
	};


	FlexTable2.getContentGroup = function () {
		return b_FlexTable2_root.getContentGroup();
	};


	// Global functions in the "FlexTable2.mxml" application

	FlexTable2.rightClick = function() {
		b_FlexTable2_root.rightClick();
	};

	FlexTable2.addRow = function(argString1, argString2, argString3, argBoolean, argArray) {
		b_FlexTable2_root.addRow(argString1, argString2, argString3, argBoolean, argArray);
	};

	FlexTable2.toggleColumnChooser = function() {
		b_FlexTable2_root.toggleColumnChooser();
	};

	FlexTable2.setData = function(argArray) {
		b_FlexTable2_root.setData(argArray);
	};

	FlexTable2.ds = function() {
		return b_FlexTable2_root.ds();
	};

	FlexTable2.unlockTable = function() {
		b_FlexTable2_root.unlockTable();
	};

	FlexTable2.clear = function() {
		b_FlexTable2_root.clear();
	};

	FlexTable2.saveData = function(argAdvancedDataGridEvent) {
		b_FlexTable2_root.saveData(argAdvancedDataGridEvent);
	};

	FlexTable2.lockTable = function() {
		b_FlexTable2_root.lockTable();
	};

	FlexTable2.init = function() {
		b_FlexTable2_root.init();
	};

	FlexTable2.onRollOver = function(argMouseEvent) {
		b_FlexTable2_root.onRollOver(argMouseEvent);
	};

	FlexTable2.toString = function() {
		return b_FlexTable2_root.toString();
	};

	FlexTable2.hasOwnProperty = function(arg) {
		return b_FlexTable2_root.hasOwnProperty(arg);
	};

	FlexTable2.isPrototypeOf = function(arg) {
		return b_FlexTable2_root.isPrototypeOf(arg);
	};

	FlexTable2.propertyIsEnumerable = function(arg) {
		return b_FlexTable2_root.propertyIsEnumerable(arg);
	};

	FlexTable2.Object = function() {
		return b_FlexTable2_root.Object();
	};

	FlexTable2.setPropertyIsEnumerable = function(argString, argBoolean) {
		b_FlexTable2_root.setPropertyIsEnumerable(argString, argBoolean);
	};

	FlexTable2.valueOf = function() {
		return b_FlexTable2_root.valueOf();
	};

	FlexTable2.willTrigger = function(argString) {
		return b_FlexTable2_root.willTrigger(argString);
	};

	FlexTable2.toString = function() {
		return b_FlexTable2_root.toString();
	};

	FlexTable2.removeEventListener = function(argString, argFunction, argBoolean) {
		b_FlexTable2_root.removeEventListener(argString, argFunction, argBoolean);
	};

	FlexTable2.EventDispatcher = function(argIEventDispatcher) {
		return b_FlexTable2_root.EventDispatcher(argIEventDispatcher);
	};

	FlexTable2.addEventListener = function(argString, argFunction, argBoolean1, argInt, argBoolean2) {
		b_FlexTable2_root.addEventListener(argString, argFunction, argBoolean1, argInt, argBoolean2);
	};

	FlexTable2.hasEventListener = function(argString) {
		return b_FlexTable2_root.hasEventListener(argString);
	};

	FlexTable2.dispatchEvent = function(argEvent) {
		return b_FlexTable2_root.dispatchEvent(argEvent);
	};

	FlexTable2.getDefaultButton = function() {
		return b_FlexTable2_root.getDefaultButton();
	};

	FlexTable2.setDefaultButton = function(argIFlexDisplayObject) {
		b_FlexTable2_root.setDefaultButton(argIFlexDisplayObject);
	};

	FlexTable2.SkinnableContainerBase = function() {
		return b_FlexTable2_root.SkinnableContainerBase();
	};

	FlexTable2.getWidth = function() {
		return b_FlexTable2_root.getWidth();
	};

	FlexTable2.setWidth = function(argNumber) {
		b_FlexTable2_root.setWidth(argNumber);
	};

	FlexTable2.getHeight = function() {
		return b_FlexTable2_root.getHeight();
	};

	FlexTable2.setHeight = function(argNumber) {
		b_FlexTable2_root.setHeight(argNumber);
	};

	FlexTable2.getRect = function(argDisplayObject) {
		return b_FlexTable2_root.getRect(argDisplayObject);
	};

	FlexTable2.getScale9Grid = function() {
		return b_FlexTable2_root.getScale9Grid();
	};

	FlexTable2.setScale9Grid = function(argRectangle) {
		b_FlexTable2_root.setScale9Grid(argRectangle);
	};

	FlexTable2.hitTestObject = function(argDisplayObject) {
		return b_FlexTable2_root.hitTestObject(argDisplayObject);
	};

	FlexTable2.getBounds = function(argDisplayObject) {
		return b_FlexTable2_root.getBounds(argDisplayObject);
	};

	FlexTable2.hitTestPoint = function(argNumber1, argNumber2, argBoolean) {
		return b_FlexTable2_root.hitTestPoint(argNumber1, argNumber2, argBoolean);
	};

	FlexTable2.getStage = function() {
		return b_FlexTable2_root.getStage();
	};

	FlexTable2.getParent = function() {
		return b_FlexTable2_root.getParent();
	};

	FlexTable2.localToGlobal = function(argPoint) {
		return b_FlexTable2_root.localToGlobal(argPoint);
	};

	FlexTable2.getLoaderInfo = function() {
		return b_FlexTable2_root.getLoaderInfo();
	};

	FlexTable2.getRotationZ = function() {
		return b_FlexTable2_root.getRotationZ();
	};

	FlexTable2.setRotationZ = function(argNumber) {
		b_FlexTable2_root.setRotationZ(argNumber);
	};

	FlexTable2.getRotationY = function() {
		return b_FlexTable2_root.getRotationY();
	};

	FlexTable2.setRotationY = function(argNumber) {
		b_FlexTable2_root.setRotationY(argNumber);
	};

	FlexTable2.getName = function() {
		return b_FlexTable2_root.getName();
	};

	FlexTable2.setName = function(argString) {
		b_FlexTable2_root.setName(argString);
	};

	FlexTable2.getRotationX = function() {
		return b_FlexTable2_root.getRotationX();
	};

	FlexTable2.setRotationX = function(argNumber) {
		b_FlexTable2_root.setRotationX(argNumber);
	};

	FlexTable2.getOpaqueBackground = function() {
		return b_FlexTable2_root.getOpaqueBackground();
	};

	FlexTable2.setOpaqueBackground = function(argObject) {
		b_FlexTable2_root.setOpaqueBackground(argObject);
	};

	FlexTable2.getCacheAsBitmap = function() {
		return b_FlexTable2_root.getCacheAsBitmap();
	};

	FlexTable2.setCacheAsBitmap = function(argBoolean) {
		b_FlexTable2_root.setCacheAsBitmap(argBoolean);
	};

	FlexTable2.getFilters = function() {
		return b_FlexTable2_root.getFilters();
	};

	FlexTable2.setFilters = function(argArray) {
		b_FlexTable2_root.setFilters(argArray);
	};

	FlexTable2.getAccessibilityProperties = function() {
		return b_FlexTable2_root.getAccessibilityProperties();
	};

	FlexTable2.setAccessibilityProperties = function(argAccessibilityProperties) {
		b_FlexTable2_root.setAccessibilityProperties(argAccessibilityProperties);
	};

	FlexTable2.getVisible = function() {
		return b_FlexTable2_root.getVisible();
	};

	FlexTable2.setVisible = function(argBoolean) {
		b_FlexTable2_root.setVisible(argBoolean);
	};

	FlexTable2.getRoot = function() {
		return b_FlexTable2_root.getRoot();
	};

	FlexTable2.setBlendShader = function(argShader) {
		b_FlexTable2_root.setBlendShader(argShader);
	};

	FlexTable2.getTransform = function() {
		return b_FlexTable2_root.getTransform();
	};

	FlexTable2.setTransform = function(argTransform) {
		b_FlexTable2_root.setTransform(argTransform);
	};

	FlexTable2.getRotation = function() {
		return b_FlexTable2_root.getRotation();
	};

	FlexTable2.setRotation = function(argNumber) {
		b_FlexTable2_root.setRotation(argNumber);
	};

	FlexTable2.getScaleZ = function() {
		return b_FlexTable2_root.getScaleZ();
	};

	FlexTable2.setScaleZ = function(argNumber) {
		b_FlexTable2_root.setScaleZ(argNumber);
	};

	FlexTable2.getScaleY = function() {
		return b_FlexTable2_root.getScaleY();
	};

	FlexTable2.setScaleY = function(argNumber) {
		b_FlexTable2_root.setScaleY(argNumber);
	};

	FlexTable2.getScaleX = function() {
		return b_FlexTable2_root.getScaleX();
	};

	FlexTable2.setScaleX = function(argNumber) {
		b_FlexTable2_root.setScaleX(argNumber);
	};

	FlexTable2.getMouseY = function() {
		return b_FlexTable2_root.getMouseY();
	};

	FlexTable2.getMouseX = function() {
		return b_FlexTable2_root.getMouseX();
	};

	FlexTable2.getZ = function() {
		return b_FlexTable2_root.getZ();
	};

	FlexTable2.setZ = function(argNumber) {
		b_FlexTable2_root.setZ(argNumber);
	};

	FlexTable2.getY = function() {
		return b_FlexTable2_root.getY();
	};

	FlexTable2.setY = function(argNumber) {
		b_FlexTable2_root.setY(argNumber);
	};

	FlexTable2.getX = function() {
		return b_FlexTable2_root.getX();
	};

	FlexTable2.setX = function(argNumber) {
		b_FlexTable2_root.setX(argNumber);
	};

	FlexTable2.local3DToGlobal = function(argVector3D) {
		return b_FlexTable2_root.local3DToGlobal(argVector3D);
	};

	FlexTable2.getMask = function() {
		return b_FlexTable2_root.getMask();
	};

	FlexTable2.setMask = function(argDisplayObject) {
		b_FlexTable2_root.setMask(argDisplayObject);
	};

	FlexTable2.DisplayObject = function() {
		return b_FlexTable2_root.DisplayObject();
	};

	FlexTable2.getAlpha = function() {
		return b_FlexTable2_root.getAlpha();
	};

	FlexTable2.setAlpha = function(argNumber) {
		b_FlexTable2_root.setAlpha(argNumber);
	};

	FlexTable2.getScrollRect = function() {
		return b_FlexTable2_root.getScrollRect();
	};

	FlexTable2.setScrollRect = function(argRectangle) {
		b_FlexTable2_root.setScrollRect(argRectangle);
	};

	FlexTable2.getBlendMode = function() {
		return b_FlexTable2_root.getBlendMode();
	};

	FlexTable2.setBlendMode = function(argString) {
		b_FlexTable2_root.setBlendMode(argString);
	};

	FlexTable2.globalToLocal3D = function(argPoint) {
		return b_FlexTable2_root.globalToLocal3D(argPoint);
	};

	FlexTable2.globalToLocal = function(argPoint) {
		return b_FlexTable2_root.globalToLocal(argPoint);
	};

	FlexTable2.getHitArea = function() {
		return b_FlexTable2_root.getHitArea();
	};

	FlexTable2.setHitArea = function(argSprite) {
		b_FlexTable2_root.setHitArea(argSprite);
	};

	FlexTable2.getDropTarget = function() {
		return b_FlexTable2_root.getDropTarget();
	};

	FlexTable2.Sprite = function() {
		return b_FlexTable2_root.Sprite();
	};

	FlexTable2.getUseHandCursor = function() {
		return b_FlexTable2_root.getUseHandCursor();
	};

	FlexTable2.setUseHandCursor = function(argBoolean) {
		b_FlexTable2_root.setUseHandCursor(argBoolean);
	};

	FlexTable2.stopDrag = function() {
		b_FlexTable2_root.stopDrag();
	};

	FlexTable2.startDrag = function(argBoolean, argRectangle) {
		b_FlexTable2_root.startDrag(argBoolean, argRectangle);
	};

	FlexTable2.getButtonMode = function() {
		return b_FlexTable2_root.getButtonMode();
	};

	FlexTable2.setButtonMode = function(argBoolean) {
		b_FlexTable2_root.setButtonMode(argBoolean);
	};

	FlexTable2.getSoundTransform = function() {
		return b_FlexTable2_root.getSoundTransform();
	};

	FlexTable2.setSoundTransform = function(argSoundTransform) {
		b_FlexTable2_root.setSoundTransform(argSoundTransform);
	};

	FlexTable2.getGraphics = function() {
		return b_FlexTable2_root.getGraphics();
	};

	FlexTable2.getTabEnabled = function() {
		return b_FlexTable2_root.getTabEnabled();
	};

	FlexTable2.setTabEnabled = function(argBoolean) {
		b_FlexTable2_root.setTabEnabled(argBoolean);
	};

	FlexTable2.getTabIndex = function() {
		return b_FlexTable2_root.getTabIndex();
	};

	FlexTable2.setTabIndex = function(argInt) {
		b_FlexTable2_root.setTabIndex(argInt);
	};

	FlexTable2.getAccessibilityImplementation = function() {
		return b_FlexTable2_root.getAccessibilityImplementation();
	};

	FlexTable2.setAccessibilityImplementation = function(argAccessibilityImplementation) {
		b_FlexTable2_root.setAccessibilityImplementation(argAccessibilityImplementation);
	};

	FlexTable2.getDoubleClickEnabled = function() {
		return b_FlexTable2_root.getDoubleClickEnabled();
	};

	FlexTable2.setDoubleClickEnabled = function(argBoolean) {
		b_FlexTable2_root.setDoubleClickEnabled(argBoolean);
	};

	FlexTable2.getMouseEnabled = function() {
		return b_FlexTable2_root.getMouseEnabled();
	};

	FlexTable2.setMouseEnabled = function(argBoolean) {
		b_FlexTable2_root.setMouseEnabled(argBoolean);
	};

	FlexTable2.InteractiveObject = function() {
		return b_FlexTable2_root.InteractiveObject();
	};

	FlexTable2.getFocusRect = function() {
		return b_FlexTable2_root.getFocusRect();
	};

	FlexTable2.setFocusRect = function(argObject) {
		b_FlexTable2_root.setFocusRect(argObject);
	};

	FlexTable2.getContextMenu = function() {
		return b_FlexTable2_root.getContextMenu();
	};

	FlexTable2.setContextMenu = function(argContextMenu) {
		b_FlexTable2_root.setContextMenu(argContextMenu);
	};

	FlexTable2.FlexSprite = function() {
		return b_FlexTable2_root.FlexSprite();
	};

	FlexTable2.toString = function() {
		return b_FlexTable2_root.toString();
	};

	FlexTable2.setPercentWidth = function(argNumber) {
		b_FlexTable2_root.setPercentWidth(argNumber);
	};

	FlexTable2.getUrl = function() {
		return b_FlexTable2_root.getUrl();
	};

	FlexTable2.initialize = function() {
		b_FlexTable2_root.initialize();
	};

	FlexTable2.getViewSourceURL = function() {
		return b_FlexTable2_root.getViewSourceURL();
	};

	FlexTable2.setViewSourceURL = function(argString) {
		b_FlexTable2_root.setViewSourceURL(argString);
	};

	FlexTable2.setTabIndex = function(argInt) {
		b_FlexTable2_root.setTabIndex(argInt);
	};

	FlexTable2.getColorCorrection = function() {
		return b_FlexTable2_root.getColorCorrection();
	};

	FlexTable2.setColorCorrection = function(argString) {
		b_FlexTable2_root.setColorCorrection(argString);
	};

	FlexTable2.setToolTip = function(argString) {
		b_FlexTable2_root.setToolTip(argString);
	};

	FlexTable2.getControlBarContent = function() {
		return b_FlexTable2_root.getControlBarContent();
	};

	FlexTable2.setControlBarContent = function(argArray) {
		b_FlexTable2_root.setControlBarContent(argArray);
	};

	FlexTable2.getId = function() {
		return b_FlexTable2_root.getId();
	};

	FlexTable2.Application = function() {
		return b_FlexTable2_root.Application();
	};

	FlexTable2.setPercentHeight = function(argNumber) {
		b_FlexTable2_root.setPercentHeight(argNumber);
	};

	FlexTable2.getControlBarVisible = function() {
		return b_FlexTable2_root.getControlBarVisible();
	};

	FlexTable2.setControlBarVisible = function(argBoolean) {
		b_FlexTable2_root.setControlBarVisible(argBoolean);
	};

	FlexTable2.getParameters = function() {
		return b_FlexTable2_root.getParameters();
	};

	FlexTable2.getControlBarLayout = function() {
		return b_FlexTable2_root.getControlBarLayout();
	};

	FlexTable2.setControlBarLayout = function(argLayoutBase) {
		b_FlexTable2_root.setControlBarLayout(argLayoutBase);
	};

	FlexTable2.addChildAt = function(argDisplayObject, argInt) {
		return b_FlexTable2_root.addChildAt(argDisplayObject, argInt);
	};

	FlexTable2.setErrorString = function(argString) {
		b_FlexTable2_root.setErrorString(argString);
	};

	FlexTable2.setMouseChildren = function(argBoolean) {
		b_FlexTable2_root.setMouseChildren(argBoolean);
	};

	FlexTable2.getSuggestedFocusSkinExclusions = function() {
		return b_FlexTable2_root.getSuggestedFocusSkinExclusions();
	};

	FlexTable2.invalidateSkinState = function() {
		b_FlexTable2_root.invalidateSkinState();
	};

	FlexTable2.styleChanged = function(argString) {
		b_FlexTable2_root.styleChanged(argString);
	};

	FlexTable2.setEnabled = function(argBoolean) {
		b_FlexTable2_root.setEnabled(argBoolean);
	};

	FlexTable2.removeChild = function(argDisplayObject) {
		return b_FlexTable2_root.removeChild(argDisplayObject);
	};

	FlexTable2.getBaselinePosition = function() {
		return b_FlexTable2_root.getBaselinePosition();
	};

	FlexTable2.SkinnableComponent = function() {
		return b_FlexTable2_root.SkinnableComponent();
	};

	FlexTable2.addChild = function(argDisplayObject) {
		return b_FlexTable2_root.addChild(argDisplayObject);
	};

	FlexTable2.setChildIndex = function(argDisplayObject, argInt) {
		b_FlexTable2_root.setChildIndex(argDisplayObject, argInt);
	};

	FlexTable2.getSkin = function() {
		return b_FlexTable2_root.getSkin();
	};

	FlexTable2.matchesCSSState = function(argString) {
		return b_FlexTable2_root.matchesCSSState(argString);
	};

	FlexTable2.setMouseEnabled = function(argBoolean) {
		b_FlexTable2_root.setMouseEnabled(argBoolean);
	};

	FlexTable2.removeChildAt = function(argInt) {
		return b_FlexTable2_root.removeChildAt(argInt);
	};

	FlexTable2.swapChildren = function(argDisplayObject1, argDisplayObject2) {
		b_FlexTable2_root.swapChildren(argDisplayObject1, argDisplayObject2);
	};

	FlexTable2.drawFocus = function(argBoolean) {
		b_FlexTable2_root.drawFocus(argBoolean);
	};

	FlexTable2.swapChildrenAt = function(argInt1, argInt2) {
		b_FlexTable2_root.swapChildrenAt(argInt1, argInt2);
	};

	FlexTable2.getChildIndex = function(argDisplayObject) {
		return b_FlexTable2_root.getChildIndex(argDisplayObject);
	};

	FlexTable2.getChildByName = function(argString) {
		return b_FlexTable2_root.getChildByName(argString);
	};

	FlexTable2.getNumChildren = function() {
		return b_FlexTable2_root.getNumChildren();
	};

	FlexTable2.setChildIndex = function(argDisplayObject, argInt) {
		b_FlexTable2_root.setChildIndex(argDisplayObject, argInt);
	};

	FlexTable2.getTabChildren = function() {
		return b_FlexTable2_root.getTabChildren();
	};

	FlexTable2.setTabChildren = function(argBoolean) {
		b_FlexTable2_root.setTabChildren(argBoolean);
	};

	FlexTable2.addChild = function(argDisplayObject) {
		return b_FlexTable2_root.addChild(argDisplayObject);
	};

	FlexTable2.swapChildren = function(argDisplayObject1, argDisplayObject2) {
		b_FlexTable2_root.swapChildren(argDisplayObject1, argDisplayObject2);
	};

	FlexTable2.removeChild = function(argDisplayObject) {
		return b_FlexTable2_root.removeChild(argDisplayObject);
	};

	FlexTable2.contains = function(argDisplayObject) {
		return b_FlexTable2_root.contains(argDisplayObject);
	};

	FlexTable2.removeChildAt = function(argInt) {
		return b_FlexTable2_root.removeChildAt(argInt);
	};

	FlexTable2.getTextSnapshot = function() {
		return b_FlexTable2_root.getTextSnapshot();
	};

	FlexTable2.swapChildrenAt = function(argInt1, argInt2) {
		b_FlexTable2_root.swapChildrenAt(argInt1, argInt2);
	};

	FlexTable2.getMouseChildren = function() {
		return b_FlexTable2_root.getMouseChildren();
	};

	FlexTable2.setMouseChildren = function(argBoolean) {
		b_FlexTable2_root.setMouseChildren(argBoolean);
	};

	FlexTable2.areInaccessibleObjectsUnderPoint = function(argPoint) {
		return b_FlexTable2_root.areInaccessibleObjectsUnderPoint(argPoint);
	};

	FlexTable2.DisplayObjectContainer = function() {
		return b_FlexTable2_root.DisplayObjectContainer();
	};

	FlexTable2.getChildAt = function(argInt) {
		return b_FlexTable2_root.getChildAt(argInt);
	};

	FlexTable2.getObjectsUnderPoint = function(argPoint) {
		return b_FlexTable2_root.getObjectsUnderPoint(argPoint);
	};

	FlexTable2.addChildAt = function(argDisplayObject, argInt) {
		return b_FlexTable2_root.addChildAt(argDisplayObject, argInt);
	};

	FlexTable2.getDepth = function() {
		return b_FlexTable2_root.getDepth();
	};

	FlexTable2.setDepth = function(argNumber) {
		b_FlexTable2_root.setDepth(argNumber);
	};

	FlexTable2.getLayoutMatrix = function() {
		return b_FlexTable2_root.getLayoutMatrix();
	};

	FlexTable2.regenerateStyleCache = function(argBoolean) {
		b_FlexTable2_root.regenerateStyleCache(argBoolean);
	};

	FlexTable2.setLayoutMatrix3D = function(argMatrix3D, argBoolean) {
		b_FlexTable2_root.setLayoutMatrix3D(argMatrix3D, argBoolean);
	};

	FlexTable2.transformPointToParent = function(argVector3D1, argVector3D2, argVector3D3) {
		b_FlexTable2_root.transformPointToParent(argVector3D1, argVector3D2, argVector3D3);
	};

	FlexTable2.initialize = function() {
		b_FlexTable2_root.initialize();
	};

	FlexTable2.getMinBoundsHeight = function(argBoolean) {
		return b_FlexTable2_root.getMinBoundsHeight(argBoolean);
	};

	FlexTable2.getTop = function() {
		return b_FlexTable2_root.getTop();
	};

	FlexTable2.setTop = function(argObject) {
		b_FlexTable2_root.setTop(argObject);
	};

	FlexTable2.getAutomationTabularData = function() {
		return b_FlexTable2_root.getAutomationTabularData();
	};

	FlexTable2.getScaleZ = function() {
		return b_FlexTable2_root.getScaleZ();
	};

	FlexTable2.setScaleZ = function(argNumber) {
		b_FlexTable2_root.setScaleZ(argNumber);
	};

	FlexTable2.getUid = function() {
		return b_FlexTable2_root.getUid();
	};

	FlexTable2.setUid = function(argString) {
		b_FlexTable2_root.setUid(argString);
	};

	FlexTable2.getScaleY = function() {
		return b_FlexTable2_root.getScaleY();
	};

	FlexTable2.setScaleY = function(argNumber) {
		b_FlexTable2_root.setScaleY(argNumber);
	};

	FlexTable2.getScaleX = function() {
		return b_FlexTable2_root.getScaleX();
	};

	FlexTable2.setScaleX = function(argNumber) {
		b_FlexTable2_root.setScaleX(argNumber);
	};

	FlexTable2.hasState = function(argString) {
		return b_FlexTable2_root.hasState(argString);
	};

	FlexTable2.getRepeaterItem = function(argInt) {
		return b_FlexTable2_root.getRepeaterItem(argInt);
	};

	FlexTable2.getStyleDeclaration = function() {
		return b_FlexTable2_root.getStyleDeclaration();
	};

	FlexTable2.setStyleDeclaration = function(argCSSStyleDeclaration) {
		b_FlexTable2_root.setStyleDeclaration(argCSSStyleDeclaration);
	};

	FlexTable2.getMaxWidth = function() {
		return b_FlexTable2_root.getMaxWidth();
	};

	FlexTable2.setMaxWidth = function(argNumber) {
		b_FlexTable2_root.setMaxWidth(argNumber);
	};

	FlexTable2.invalidateLayering = function() {
		b_FlexTable2_root.invalidateLayering();
	};

	FlexTable2.measureHTMLText = function(argString) {
		return b_FlexTable2_root.measureHTMLText(argString);
	};

	FlexTable2.getPreferredBoundsHeight = function(argBoolean) {
		return b_FlexTable2_root.getPreferredBoundsHeight(argBoolean);
	};

	FlexTable2.getSystemManager = function() {
		return b_FlexTable2_root.getSystemManager();
	};

	FlexTable2.setSystemManager = function(argISystemManager) {
		b_FlexTable2_root.setSystemManager(argISystemManager);
	};

	FlexTable2.getBoundsYAtSize = function(argNumber1, argNumber2, argBoolean) {
		return b_FlexTable2_root.getBoundsYAtSize(argNumber1, argNumber2, argBoolean);
	};

	FlexTable2.validateDisplayList = function() {
		b_FlexTable2_root.validateDisplayList();
	};

	FlexTable2.getMinWidth = function() {
		return b_FlexTable2_root.getMinWidth();
	};

	FlexTable2.setMinWidth = function(argNumber) {
		b_FlexTable2_root.setMinWidth(argNumber);
	};

	FlexTable2.matchesCSSType = function(argString) {
		return b_FlexTable2_root.matchesCSSType(argString);
	};

	FlexTable2.getExplicitOrMeasuredWidth = function() {
		return b_FlexTable2_root.getExplicitOrMeasuredWidth();
	};

	FlexTable2.getInitialized = function() {
		return b_FlexTable2_root.getInitialized();
	};

	FlexTable2.setInitialized = function(argBoolean) {
		b_FlexTable2_root.setInitialized(argBoolean);
	};

	FlexTable2.contentToGlobal = function(argPoint) {
		return b_FlexTable2_root.contentToGlobal(argPoint);
	};

	FlexTable2.getTransformZ = function() {
		return b_FlexTable2_root.getTransformZ();
	};

	FlexTable2.setTransformZ = function(argNumber) {
		b_FlexTable2_root.setTransformZ(argNumber);
	};

	FlexTable2.getTransformY = function() {
		return b_FlexTable2_root.getTransformY();
	};

	FlexTable2.setTransformY = function(argNumber) {
		b_FlexTable2_root.setTransformY(argNumber);
	};

	FlexTable2.getTransformX = function() {
		return b_FlexTable2_root.getTransformX();
	};

	FlexTable2.setTransformX = function(argNumber) {
		b_FlexTable2_root.setTransformX(argNumber);
	};

	FlexTable2.getTransform = function() {
		return b_FlexTable2_root.getTransform();
	};

	FlexTable2.setTransform = function(argTransform) {
		b_FlexTable2_root.setTransform(argTransform);
	};

	FlexTable2.getAutomationValue = function() {
		return b_FlexTable2_root.getAutomationValue();
	};

	FlexTable2.getExplicitHeight = function() {
		return b_FlexTable2_root.getExplicitHeight();
	};

	FlexTable2.setExplicitHeight = function(argNumber) {
		b_FlexTable2_root.setExplicitHeight(argNumber);
	};

	FlexTable2.executeBindings = function(argBoolean) {
		b_FlexTable2_root.executeBindings(argBoolean);
	};

	FlexTable2.getPercentWidth = function() {
		return b_FlexTable2_root.getPercentWidth();
	};

	FlexTable2.setPercentWidth = function(argNumber) {
		b_FlexTable2_root.setPercentWidth(argNumber);
	};

	FlexTable2.getModuleFactory = function() {
		return b_FlexTable2_root.getModuleFactory();
	};

	FlexTable2.setModuleFactory = function(argIFlexModuleFactory) {
		b_FlexTable2_root.setModuleFactory(argIFlexModuleFactory);
	};

	FlexTable2.getParentApplication = function() {
		return b_FlexTable2_root.getParentApplication();
	};

	FlexTable2.createAutomationIDPartWithRequiredProperties = function(argIAutomationObject, argArray) {
		return b_FlexTable2_root.createAutomationIDPartWithRequiredProperties(argIAutomationObject, argArray);
	};

	FlexTable2.drawRoundRect = function(argNumber1, argNumber2, argNumber3, argNumber4, argObject5, argObject6, argObject7, argObject8, argString, argArray, argObject9) {
		b_FlexTable2_root.drawRoundRect(argNumber1, argNumber2, argNumber3, argNumber4, argObject5, argObject6, argObject7, argObject8, argString, argArray, argObject9);
	};

	FlexTable2.resolveAutomationIDPart = function(argObject) {
		return b_FlexTable2_root.resolveAutomationIDPart(argObject);
	};

	FlexTable2.getHasFocusableChildren = function() {
		return b_FlexTable2_root.getHasFocusableChildren();
	};

	FlexTable2.setHasFocusableChildren = function(argBoolean) {
		b_FlexTable2_root.setHasFocusableChildren(argBoolean);
	};

	FlexTable2.setChildIndex = function(argDisplayObject, argInt) {
		b_FlexTable2_root.setChildIndex(argDisplayObject, argInt);
	};

	FlexTable2.getUpdateCompletePendingFlag = function() {
		return b_FlexTable2_root.getUpdateCompletePendingFlag();
	};

	FlexTable2.setUpdateCompletePendingFlag = function(argBoolean) {
		b_FlexTable2_root.setUpdateCompletePendingFlag(argBoolean);
	};

	FlexTable2.getProcessedDescriptors = function() {
		return b_FlexTable2_root.getProcessedDescriptors();
	};

	FlexTable2.setProcessedDescriptors = function(argBoolean) {
		b_FlexTable2_root.setProcessedDescriptors(argBoolean);
	};

	FlexTable2.getBottom = function() {
		return b_FlexTable2_root.getBottom();
	};

	FlexTable2.setBottom = function(argObject) {
		b_FlexTable2_root.setBottom(argObject);
	};

	FlexTable2.getStyleParent = function() {
		return b_FlexTable2_root.getStyleParent();
	};

	FlexTable2.getDoubleClickEnabled = function() {
		return b_FlexTable2_root.getDoubleClickEnabled();
	};

	FlexTable2.setDoubleClickEnabled = function(argBoolean) {
		b_FlexTable2_root.setDoubleClickEnabled(argBoolean);
	};

	FlexTable2.setActualSize = function(argNumber1, argNumber2) {
		b_FlexTable2_root.setActualSize(argNumber1, argNumber2);
	};

	FlexTable2.setLayoutMatrix = function(argMatrix, argBoolean) {
		b_FlexTable2_root.setLayoutMatrix(argMatrix, argBoolean);
	};

	FlexTable2.getTabFocusEnabled = function() {
		return b_FlexTable2_root.getTabFocusEnabled();
	};

	FlexTable2.setTabFocusEnabled = function(argBoolean) {
		b_FlexTable2_root.setTabFocusEnabled(argBoolean);
	};

	FlexTable2.getOwner = function() {
		return b_FlexTable2_root.getOwner();
	};

	FlexTable2.setOwner = function(argDisplayObjectContainer) {
		b_FlexTable2_root.setOwner(argDisplayObjectContainer);
	};

	FlexTable2.measureText = function(argString) {
		return b_FlexTable2_root.measureText(argString);
	};

	FlexTable2.getRepeaters = function() {
		return b_FlexTable2_root.getRepeaters();
	};

	FlexTable2.setRepeaters = function(argArray) {
		b_FlexTable2_root.setRepeaters(argArray);
	};

	FlexTable2.notifyStyleChangeInChildren = function(argString, argBoolean) {
		b_FlexTable2_root.notifyStyleChangeInChildren(argString, argBoolean);
	};

	FlexTable2.setStyle = function(argString, arg) {
		b_FlexTable2_root.setStyle(argString, arg);
	};

	FlexTable2.getFlexContextMenu = function() {
		return b_FlexTable2_root.getFlexContextMenu();
	};

	FlexTable2.setFlexContextMenu = function(argIFlexContextMenu) {
		b_FlexTable2_root.setFlexContextMenu(argIFlexContextMenu);
	};

	FlexTable2.createReferenceOnParentDocument = function(argIFlexDisplayObject) {
		b_FlexTable2_root.createReferenceOnParentDocument(argIFlexDisplayObject);
	};

	FlexTable2.getMouseFocusEnabled = function() {
		return b_FlexTable2_root.getMouseFocusEnabled();
	};

	FlexTable2.setMouseFocusEnabled = function(argBoolean) {
		b_FlexTable2_root.setMouseFocusEnabled(argBoolean);
	};

	FlexTable2.stopDrag = function() {
		b_FlexTable2_root.stopDrag();
	};

	FlexTable2.getHasLayoutMatrix3D = function() {
		return b_FlexTable2_root.getHasLayoutMatrix3D();
	};

	FlexTable2.localToContent = function(argPoint) {
		return b_FlexTable2_root.localToContent(argPoint);
	};

	FlexTable2.prepareToPrint = function(argIFlexDisplayObject) {
		return b_FlexTable2_root.prepareToPrint(argIFlexDisplayObject);
	};

	FlexTable2.endEffectsStarted = function() {
		b_FlexTable2_root.endEffectsStarted();
	};

	FlexTable2.getAccessibilityShortcut = function() {
		return b_FlexTable2_root.getAccessibilityShortcut();
	};

	FlexTable2.setAccessibilityShortcut = function(argString) {
		b_FlexTable2_root.setAccessibilityShortcut(argString);
	};

	FlexTable2.registerEffects = function(argArray) {
		b_FlexTable2_root.registerEffects(argArray);
	};

	FlexTable2.getAutomationOwner = function() {
		return b_FlexTable2_root.getAutomationOwner();
	};

	FlexTable2.getActiveEffects = function() {
		return b_FlexTable2_root.getActiveEffects();
	};

	FlexTable2.getFocusPane = function() {
		return b_FlexTable2_root.getFocusPane();
	};

	FlexTable2.setFocusPane = function(argSprite) {
		b_FlexTable2_root.setFocusPane(argSprite);
	};

	FlexTable2.getInheritingStyles = function() {
		return b_FlexTable2_root.getInheritingStyles();
	};

	FlexTable2.setInheritingStyles = function(argObject) {
		b_FlexTable2_root.setInheritingStyles(argObject);
	};

	FlexTable2.verticalGradientMatrix = function(argNumber1, argNumber2, argNumber3, argNumber4) {
		return b_FlexTable2_root.verticalGradientMatrix(argNumber1, argNumber2, argNumber3, argNumber4);
	};

	FlexTable2.getStyleManager = function() {
		return b_FlexTable2_root.getStyleManager();
	};

	FlexTable2.determineTextFormatFromStyles = function() {
		return b_FlexTable2_root.determineTextFormatFromStyles();
	};

	FlexTable2.getAutomationParent = function() {
		return b_FlexTable2_root.getAutomationParent();
	};

	FlexTable2.getMaxHeight = function() {
		return b_FlexTable2_root.getMaxHeight();
	};

	FlexTable2.setMaxHeight = function(argNumber) {
		b_FlexTable2_root.setMaxHeight(argNumber);
	};

	FlexTable2.getBaselinePosition = function() {
		return b_FlexTable2_root.getBaselinePosition();
	};

	FlexTable2.callLater = function(argFunction, argArray) {
		b_FlexTable2_root.callLater(argFunction, argArray);
	};

	FlexTable2.getAutomationEnabled = function() {
		return b_FlexTable2_root.getAutomationEnabled();
	};

	FlexTable2.hasFontContextChanged = function() {
		return b_FlexTable2_root.hasFontContextChanged();
	};

	FlexTable2.getPostLayoutTransformOffsets = function() {
		return b_FlexTable2_root.getPostLayoutTransformOffsets();
	};

	FlexTable2.setPostLayoutTransformOffsets = function(argTransformOffsets) {
		b_FlexTable2_root.setPostLayoutTransformOffsets(argTransformOffsets);
	};

	FlexTable2.getDescriptor = function() {
		return b_FlexTable2_root.getDescriptor();
	};

	FlexTable2.setDescriptor = function(argUIComponentDescriptor) {
		b_FlexTable2_root.setDescriptor(argUIComponentDescriptor);
	};

	FlexTable2.deleteReferenceOnParentDocument = function(argIFlexDisplayObject) {
		b_FlexTable2_root.deleteReferenceOnParentDocument(argIFlexDisplayObject);
	};

	FlexTable2.getLeft = function() {
		return b_FlexTable2_root.getLeft();
	};

	FlexTable2.setLeft = function(argObject) {
		b_FlexTable2_root.setLeft(argObject);
	};

	FlexTable2.getErrorString = function() {
		return b_FlexTable2_root.getErrorString();
	};

	FlexTable2.setErrorString = function(argString) {
		b_FlexTable2_root.setErrorString(argString);
	};

	FlexTable2.getWidth = function() {
		return b_FlexTable2_root.getWidth();
	};

	FlexTable2.setWidth = function(argNumber) {
		b_FlexTable2_root.setWidth(argNumber);
	};

	FlexTable2.getInstanceIndex = function() {
		return b_FlexTable2_root.getInstanceIndex();
	};

	FlexTable2.move = function(argNumber1, argNumber2) {
		b_FlexTable2_root.move(argNumber1, argNumber2);
	};

	FlexTable2.getClassStyleDeclarations = function() {
		return b_FlexTable2_root.getClassStyleDeclarations();
	};

	FlexTable2.initializeRepeaterArrays = function(argIRepeaterClient) {
		b_FlexTable2_root.initializeRepeaterArrays(argIRepeaterClient);
	};

	FlexTable2.getAutomationVisible = function() {
		return b_FlexTable2_root.getAutomationVisible();
	};

	FlexTable2.getExplicitMaxWidth = function() {
		return b_FlexTable2_root.getExplicitMaxWidth();
	};

	FlexTable2.setExplicitMaxWidth = function(argNumber) {
		b_FlexTable2_root.setExplicitMaxWidth(argNumber);
	};

	FlexTable2.getRotationZ = function() {
		return b_FlexTable2_root.getRotationZ();
	};

	FlexTable2.setRotationZ = function(argNumber) {
		b_FlexTable2_root.setRotationZ(argNumber);
	};

	FlexTable2.getRotationY = function() {
		return b_FlexTable2_root.getRotationY();
	};

	FlexTable2.setRotationY = function(argNumber) {
		b_FlexTable2_root.setRotationY(argNumber);
	};

	FlexTable2.getExplicitMinHeight = function() {
		return b_FlexTable2_root.getExplicitMinHeight();
	};

	FlexTable2.setExplicitMinHeight = function(argNumber) {
		b_FlexTable2_root.setExplicitMinHeight(argNumber);
	};

	FlexTable2.getRotationX = function() {
		return b_FlexTable2_root.getRotationX();
	};

	FlexTable2.setRotationX = function(argNumber) {
		b_FlexTable2_root.setRotationX(argNumber);
	};

	FlexTable2.clearStyle = function(argString) {
		b_FlexTable2_root.clearStyle(argString);
	};

	FlexTable2.invalidateProperties = function() {
		b_FlexTable2_root.invalidateProperties();
	};

	FlexTable2.setCacheHeuristic = function(argBoolean) {
		b_FlexTable2_root.setCacheHeuristic(argBoolean);
	};

	FlexTable2.getFilters = function() {
		return b_FlexTable2_root.getFilters();
	};

	FlexTable2.setFilters = function(argArray) {
		b_FlexTable2_root.setFilters(argArray);
	};

	FlexTable2.validateProperties = function() {
		b_FlexTable2_root.validateProperties();
	};

	FlexTable2.getBlendMode = function() {
		return b_FlexTable2_root.getBlendMode();
	};

	FlexTable2.setBlendMode = function(argString) {
		b_FlexTable2_root.setBlendMode(argString);
	};

	FlexTable2.getIncludeInLayout = function() {
		return b_FlexTable2_root.getIncludeInLayout();
	};

	FlexTable2.setIncludeInLayout = function(argBoolean) {
		b_FlexTable2_root.setIncludeInLayout(argBoolean);
	};

	FlexTable2.getRight = function() {
		return b_FlexTable2_root.getRight();
	};

	FlexTable2.setRight = function(argObject) {
		b_FlexTable2_root.setRight(argObject);
	};

	FlexTable2.addChildAt = function(argDisplayObject, argInt) {
		return b_FlexTable2_root.addChildAt(argDisplayObject, argInt);
	};

	FlexTable2.getAutomationName = function() {
		return b_FlexTable2_root.getAutomationName();
	};

	FlexTable2.setAutomationName = function(argString) {
		b_FlexTable2_root.setAutomationName(argString);
	};

	FlexTable2.getClassName = function() {
		return b_FlexTable2_root.getClassName();
	};

	FlexTable2.getNonInheritingStyles = function() {
		return b_FlexTable2_root.getNonInheritingStyles();
	};

	FlexTable2.setNonInheritingStyles = function(argObject) {
		b_FlexTable2_root.setNonInheritingStyles(argObject);
	};

	FlexTable2.getExplicitWidth = function() {
		return b_FlexTable2_root.getExplicitWidth();
	};

	FlexTable2.setExplicitWidth = function(argNumber) {
		b_FlexTable2_root.setExplicitWidth(argNumber);
	};

	FlexTable2.getMinHeight = function() {
		return b_FlexTable2_root.getMinHeight();
	};

	FlexTable2.setMinHeight = function(argNumber) {
		b_FlexTable2_root.setMinHeight(argNumber);
	};

	FlexTable2.getLayoutBoundsHeight = function(argBoolean) {
		return b_FlexTable2_root.getLayoutBoundsHeight(argBoolean);
	};

	FlexTable2.dispatchEvent = function(argEvent) {
		return b_FlexTable2_root.dispatchEvent(argEvent);
	};

	FlexTable2.getMaxBoundsWidth = function(argBoolean) {
		return b_FlexTable2_root.getMaxBoundsWidth(argBoolean);
	};

	FlexTable2.getIs3D = function() {
		return b_FlexTable2_root.getIs3D();
	};

	FlexTable2.getExplicitMinWidth = function() {
		return b_FlexTable2_root.getExplicitMinWidth();
	};

	FlexTable2.setExplicitMinWidth = function(argNumber) {
		b_FlexTable2_root.setExplicitMinWidth(argNumber);
	};

	FlexTable2.getStyle = function(argString) {
		return b_FlexTable2_root.getStyle(argString);
	};

	FlexTable2.getMouseY = function() {
		return b_FlexTable2_root.getMouseY();
	};

	FlexTable2.getMouseX = function() {
		return b_FlexTable2_root.getMouseX();
	};

	FlexTable2.getScreen = function() {
		return b_FlexTable2_root.getScreen();
	};

	FlexTable2.getExplicitOrMeasuredHeight = function() {
		return b_FlexTable2_root.getExplicitOrMeasuredHeight();
	};

	FlexTable2.getLayoutBoundsWidth = function(argBoolean) {
		return b_FlexTable2_root.getLayoutBoundsWidth(argBoolean);
	};

	FlexTable2.setFocus = function() {
		b_FlexTable2_root.setFocus();
	};

	FlexTable2.horizontalGradientMatrix = function(argNumber1, argNumber2, argNumber3, argNumber4) {
		return b_FlexTable2_root.horizontalGradientMatrix(argNumber1, argNumber2, argNumber3, argNumber4);
	};

	FlexTable2.setConstraintValue = function(argString, arg) {
		b_FlexTable2_root.setConstraintValue(argString, arg);
	};

	FlexTable2.getInstanceIndices = function() {
		return b_FlexTable2_root.getInstanceIndices();
	};

	FlexTable2.setInstanceIndices = function(argArray) {
		b_FlexTable2_root.setInstanceIndices(argArray);
	};

	FlexTable2.getRepeaterIndices = function() {
		return b_FlexTable2_root.getRepeaterIndices();
	};

	FlexTable2.setRepeaterIndices = function(argArray) {
		b_FlexTable2_root.setRepeaterIndices(argArray);
	};

	FlexTable2.getTweeningProperties = function() {
		return b_FlexTable2_root.getTweeningProperties();
	};

	FlexTable2.setTweeningProperties = function(argArray) {
		b_FlexTable2_root.setTweeningProperties(argArray);
	};

	FlexTable2.getCachePolicy = function() {
		return b_FlexTable2_root.getCachePolicy();
	};

	FlexTable2.setCachePolicy = function(argString) {
		b_FlexTable2_root.setCachePolicy(argString);
	};

	FlexTable2.addChild = function(argDisplayObject) {
		return b_FlexTable2_root.addChild(argDisplayObject);
	};

	FlexTable2.invalidateSize = function() {
		b_FlexTable2_root.invalidateSize();
	};

	FlexTable2.setVisible = function(argBoolean1, argBoolean2) {
		b_FlexTable2_root.setVisible(argBoolean1, argBoolean2);
	};

	FlexTable2.getBoundsXAtSize = function(argNumber1, argNumber2, argBoolean) {
		return b_FlexTable2_root.getBoundsXAtSize(argNumber1, argNumber2, argBoolean);
	};

	FlexTable2.parentChanged = function(argDisplayObjectContainer) {
		b_FlexTable2_root.parentChanged(argDisplayObjectContainer);
	};

	FlexTable2.transformAround = function(argVector3D1, argVector3D2, argVector3D3, argVector3D4, argVector3D5, argVector3D6, argVector3D7, argBoolean) {
		b_FlexTable2_root.transformAround(argVector3D1, argVector3D2, argVector3D3, argVector3D4, argVector3D5, argVector3D6, argVector3D7, argBoolean);
	};

	FlexTable2.getMeasuredHeight = function() {
		return b_FlexTable2_root.getMeasuredHeight();
	};

	FlexTable2.setMeasuredHeight = function(argNumber) {
		b_FlexTable2_root.setMeasuredHeight(argNumber);
	};

	FlexTable2.getMaintainProjectionCenter = function() {
		return b_FlexTable2_root.getMaintainProjectionCenter();
	};

	FlexTable2.setMaintainProjectionCenter = function(argBoolean) {
		b_FlexTable2_root.setMaintainProjectionCenter(argBoolean);
	};

	FlexTable2.getAutomationChildren = function() {
		return b_FlexTable2_root.getAutomationChildren();
	};

	FlexTable2.removeChild = function(argDisplayObject) {
		return b_FlexTable2_root.removeChild(argDisplayObject);
	};

	FlexTable2.matchesCSSState = function(argString) {
		return b_FlexTable2_root.matchesCSSState(argString);
	};

	FlexTable2.validateNow = function() {
		b_FlexTable2_root.validateNow();
	};

	FlexTable2.invalidateDisplayList = function() {
		b_FlexTable2_root.invalidateDisplayList();
	};

	FlexTable2.getMeasuredWidth = function() {
		return b_FlexTable2_root.getMeasuredWidth();
	};

	FlexTable2.setMeasuredWidth = function(argNumber) {
		b_FlexTable2_root.setMeasuredWidth(argNumber);
	};

	FlexTable2.setLayoutBoundsPosition = function(argNumber1, argNumber2, argBoolean) {
		b_FlexTable2_root.setLayoutBoundsPosition(argNumber1, argNumber2, argBoolean);
	};

	FlexTable2.getAutomationChildAt = function(argInt) {
		return b_FlexTable2_root.getAutomationChildAt(argInt);
	};

	FlexTable2.getPercentHeight = function() {
		return b_FlexTable2_root.getPercentHeight();
	};

	FlexTable2.setPercentHeight = function(argNumber) {
		b_FlexTable2_root.setPercentHeight(argNumber);
	};

	FlexTable2.getIsPopUp = function() {
		return b_FlexTable2_root.getIsPopUp();
	};

	FlexTable2.setIsPopUp = function(argBoolean) {
		b_FlexTable2_root.setIsPopUp(argBoolean);
	};

	FlexTable2.setLayoutBoundsSize = function(argNumber1, argNumber2, argBoolean) {
		b_FlexTable2_root.setLayoutBoundsSize(argNumber1, argNumber2, argBoolean);
	};

	FlexTable2.getId = function() {
		return b_FlexTable2_root.getId();
	};

	FlexTable2.setId = function(argString) {
		b_FlexTable2_root.setId(argString);
	};

	FlexTable2.getStyleName = function() {
		return b_FlexTable2_root.getStyleName();
	};

	FlexTable2.setStyleName = function(argObject) {
		b_FlexTable2_root.setStyleName(argObject);
	};

	FlexTable2.globalToContent = function(argPoint) {
		return b_FlexTable2_root.globalToContent(argPoint);
	};

	FlexTable2.getIsDocument = function() {
		return b_FlexTable2_root.getIsDocument();
	};

	FlexTable2.setCacheAsBitmap = function(argBoolean) {
		b_FlexTable2_root.setCacheAsBitmap(argBoolean);
	};

	FlexTable2.getAccessibilityName = function() {
		return b_FlexTable2_root.getAccessibilityName();
	};

	FlexTable2.setAccessibilityName = function(argString) {
		b_FlexTable2_root.setAccessibilityName(argString);
	};

	FlexTable2.getRepeaterIndex = function() {
		return b_FlexTable2_root.getRepeaterIndex();
	};

	FlexTable2.getParent = function() {
		return b_FlexTable2_root.getParent();
	};

	FlexTable2.getRepeater = function() {
		return b_FlexTable2_root.getRepeater();
	};

	FlexTable2.getMeasuredMinHeight = function() {
		return b_FlexTable2_root.getMeasuredMinHeight();
	};

	FlexTable2.setMeasuredMinHeight = function(argNumber) {
		b_FlexTable2_root.setMeasuredMinHeight(argNumber);
	};

	FlexTable2.getVisibleRect = function(argDisplayObject) {
		return b_FlexTable2_root.getVisibleRect(argDisplayObject);
	};

	FlexTable2.getPreferredBoundsWidth = function(argBoolean) {
		return b_FlexTable2_root.getPreferredBoundsWidth(argBoolean);
	};

	FlexTable2.getFocusManager = function() {
		return b_FlexTable2_root.getFocusManager();
	};

	FlexTable2.setFocusManager = function(argIFocusManager) {
		b_FlexTable2_root.setFocusManager(argIFocusManager);
	};

	FlexTable2.getVerticalCenter = function() {
		return b_FlexTable2_root.getVerticalCenter();
	};

	FlexTable2.setVerticalCenter = function(argObject) {
		b_FlexTable2_root.setVerticalCenter(argObject);
	};

	FlexTable2.effectStarted = function(argIEffectInstance) {
		b_FlexTable2_root.effectStarted(argIEffectInstance);
	};

	FlexTable2.UIComponent = function() {
		return b_FlexTable2_root.UIComponent();
	};

	FlexTable2.getDocument = function() {
		return b_FlexTable2_root.getDocument();
	};

	FlexTable2.setDocument = function(argObject) {
		b_FlexTable2_root.setDocument(argObject);
	};

	FlexTable2.getFocus = function() {
		return b_FlexTable2_root.getFocus();
	};

	FlexTable2.validationResultHandler = function(argValidationResultEvent) {
		b_FlexTable2_root.validationResultHandler(argValidationResultEvent);
	};

	FlexTable2.setCurrentState = function(argString, argBoolean) {
		b_FlexTable2_root.setCurrentState(argString, argBoolean);
	};

	FlexTable2.getTransitions = function() {
		return b_FlexTable2_root.getTransitions();
	};

	FlexTable2.setTransitions = function(argArray) {
		b_FlexTable2_root.setTransitions(argArray);
	};

	FlexTable2.finishPrint = function(argObject, argIFlexDisplayObject) {
		b_FlexTable2_root.finishPrint(argObject, argIFlexDisplayObject);
	};

	FlexTable2.contentToLocal = function(argPoint) {
		return b_FlexTable2_root.contentToLocal(argPoint);
	};

	FlexTable2.validateSize = function(argBoolean) {
		b_FlexTable2_root.validateSize(argBoolean);
	};

	FlexTable2.getHorizontalCenter = function() {
		return b_FlexTable2_root.getHorizontalCenter();
	};

	FlexTable2.setHorizontalCenter = function(argObject) {
		b_FlexTable2_root.setHorizontalCenter(argObject);
	};

	FlexTable2.getEnabled = function() {
		return b_FlexTable2_root.getEnabled();
	};

	FlexTable2.setEnabled = function(argBoolean) {
		b_FlexTable2_root.setEnabled(argBoolean);
	};

	FlexTable2.getNestLevel = function() {
		return b_FlexTable2_root.getNestLevel();
	};

	FlexTable2.setNestLevel = function(argInt) {
		b_FlexTable2_root.setNestLevel(argInt);
	};

	FlexTable2.getCursorManager = function() {
		return b_FlexTable2_root.getCursorManager();
	};

	FlexTable2.getStates = function() {
		return b_FlexTable2_root.getStates();
	};

	FlexTable2.setStates = function(argArray) {
		b_FlexTable2_root.setStates(argArray);
	};

	FlexTable2.getValidationSubField = function() {
		return b_FlexTable2_root.getValidationSubField();
	};

	FlexTable2.setValidationSubField = function(argString) {
		b_FlexTable2_root.setValidationSubField(argString);
	};

	FlexTable2.getAlpha = function() {
		return b_FlexTable2_root.getAlpha();
	};

	FlexTable2.setAlpha = function(argNumber) {
		b_FlexTable2_root.setAlpha(argNumber);
	};

	FlexTable2.styleChanged = function(argString) {
		b_FlexTable2_root.styleChanged(argString);
	};

	FlexTable2.getMinBoundsWidth = function(argBoolean) {
		return b_FlexTable2_root.getMinBoundsWidth(argBoolean);
	};

	FlexTable2.getVisible = function() {
		return b_FlexTable2_root.getVisible();
	};

	FlexTable2.setVisible = function(argBoolean) {
		b_FlexTable2_root.setVisible(argBoolean);
	};

	FlexTable2.getHeight = function() {
		return b_FlexTable2_root.getHeight();
	};

	FlexTable2.setHeight = function(argNumber) {
		b_FlexTable2_root.setHeight(argNumber);
	};

	FlexTable2.setLayoutMatrix3D = function(argMatrix3D) {
		b_FlexTable2_root.setLayoutMatrix3D(argMatrix3D);
	};

	FlexTable2.getZ = function() {
		return b_FlexTable2_root.getZ();
	};

	FlexTable2.setZ = function(argNumber) {
		b_FlexTable2_root.setZ(argNumber);
	};

	FlexTable2.removeChildAt = function(argInt) {
		return b_FlexTable2_root.removeChildAt(argInt);
	};

	FlexTable2.getY = function() {
		return b_FlexTable2_root.getY();
	};

	FlexTable2.setY = function(argNumber) {
		b_FlexTable2_root.setY(argNumber);
	};

	FlexTable2.getX = function() {
		return b_FlexTable2_root.getX();
	};

	FlexTable2.setX = function(argNumber) {
		b_FlexTable2_root.setX(argNumber);
	};

	FlexTable2.getAutomationDelegate = function() {
		return b_FlexTable2_root.getAutomationDelegate();
	};

	FlexTable2.setAutomationDelegate = function(argObject) {
		b_FlexTable2_root.setAutomationDelegate(argObject);
	};

	FlexTable2.invalidateLayoutDirection = function() {
		b_FlexTable2_root.invalidateLayoutDirection();
	};

	FlexTable2.replayAutomatableEvent = function(argEvent) {
		return b_FlexTable2_root.replayAutomatableEvent(argEvent);
	};

	FlexTable2.getConstraintValue = function(argString) {
		return b_FlexTable2_root.getConstraintValue(argString);
	};

	FlexTable2.getBaseline = function() {
		return b_FlexTable2_root.getBaseline();
	};

	FlexTable2.setBaseline = function(argObject) {
		b_FlexTable2_root.setBaseline(argObject);
	};

	FlexTable2.getAccessibilityEnabled = function() {
		return b_FlexTable2_root.getAccessibilityEnabled();
	};

	FlexTable2.setAccessibilityEnabled = function(argBoolean) {
		b_FlexTable2_root.setAccessibilityEnabled(argBoolean);
	};

	FlexTable2.getMeasuredMinWidth = function() {
		return b_FlexTable2_root.getMeasuredMinWidth();
	};

	FlexTable2.setMeasuredMinWidth = function(argNumber) {
		b_FlexTable2_root.setMeasuredMinWidth(argNumber);
	};

	FlexTable2.getMaxBoundsHeight = function(argBoolean) {
		return b_FlexTable2_root.getMaxBoundsHeight(argBoolean);
	};

	FlexTable2.getToolTip = function() {
		return b_FlexTable2_root.getToolTip();
	};

	FlexTable2.setToolTip = function(argString) {
		b_FlexTable2_root.setToolTip(argString);
	};

	FlexTable2.getNumAutomationChildren = function() {
		return b_FlexTable2_root.getNumAutomationChildren();
	};

	FlexTable2.getParentDocument = function() {
		return b_FlexTable2_root.getParentDocument();
	};

	FlexTable2.stylesInitialized = function() {
		b_FlexTable2_root.stylesInitialized();
	};

	FlexTable2.getLayoutBoundsY = function(argBoolean) {
		return b_FlexTable2_root.getLayoutBoundsY(argBoolean);
	};

	FlexTable2.getLayoutBoundsX = function(argBoolean) {
		return b_FlexTable2_root.getLayoutBoundsX(argBoolean);
	};

	FlexTable2.effectFinished = function(argIEffectInstance) {
		b_FlexTable2_root.effectFinished(argIEffectInstance);
	};

	FlexTable2.getContentMouseY = function() {
		return b_FlexTable2_root.getContentMouseY();
	};

	FlexTable2.getContentMouseX = function() {
		return b_FlexTable2_root.getContentMouseX();
	};

	FlexTable2.getDesignLayer = function() {
		return b_FlexTable2_root.getDesignLayer();
	};

	FlexTable2.setDesignLayer = function(argDesignLayer) {
		b_FlexTable2_root.setDesignLayer(argDesignLayer);
	};

	FlexTable2.getExplicitMaxHeight = function() {
		return b_FlexTable2_root.getExplicitMaxHeight();
	};

	FlexTable2.setExplicitMaxHeight = function(argNumber) {
		b_FlexTable2_root.setExplicitMaxHeight(argNumber);
	};

	FlexTable2.getRotation = function() {
		return b_FlexTable2_root.getRotation();
	};

	FlexTable2.setRotation = function(argNumber) {
		b_FlexTable2_root.setRotation(argNumber);
	};

	FlexTable2.createAutomationIDPart = function(argIAutomationObject) {
		return b_FlexTable2_root.createAutomationIDPart(argIAutomationObject);
	};

	FlexTable2.getAccessibilityDescription = function() {
		return b_FlexTable2_root.getAccessibilityDescription();
	};

	FlexTable2.setAccessibilityDescription = function(argString) {
		b_FlexTable2_root.setAccessibilityDescription(argString);
	};

	FlexTable2.getCurrentState = function() {
		return b_FlexTable2_root.getCurrentState();
	};

	FlexTable2.setCurrentState = function(argString) {
		b_FlexTable2_root.setCurrentState(argString);
	};

	FlexTable2.owns = function(argDisplayObject) {
		return b_FlexTable2_root.owns(argDisplayObject);
	};

	FlexTable2.getShowInAutomationHierarchy = function() {
		return b_FlexTable2_root.getShowInAutomationHierarchy();
	};

	FlexTable2.setShowInAutomationHierarchy = function(argBoolean) {
		b_FlexTable2_root.setShowInAutomationHierarchy(argBoolean);
	};

	FlexTable2.getLayoutMatrix3D = function() {
		return b_FlexTable2_root.getLayoutMatrix3D();
	};

	FlexTable2.getLayoutDirection = function() {
		return b_FlexTable2_root.getLayoutDirection();
	};

	FlexTable2.setLayoutDirection = function(argString) {
		b_FlexTable2_root.setLayoutDirection(argString);
	};

	FlexTable2.drawFocus = function(argBoolean) {
		b_FlexTable2_root.drawFocus(argBoolean);
	};

	FlexTable2.getFocusEnabled = function() {
		return b_FlexTable2_root.getFocusEnabled();
	};

	FlexTable2.setFocusEnabled = function(argBoolean) {
		b_FlexTable2_root.setFocusEnabled(argBoolean);
	};

	FlexTable2.setMxmlContent = function(argArray) {
		b_FlexTable2_root.setMxmlContent(argArray);
	};

	FlexTable2.createDeferredContent = function() {
		b_FlexTable2_root.createDeferredContent();
	};

	FlexTable2.setElementIndex = function(argIVisualElement, argInt) {
		b_FlexTable2_root.setElementIndex(argIVisualElement, argInt);
	};

	FlexTable2.swapElements = function(argIVisualElement1, argIVisualElement2) {
		b_FlexTable2_root.swapElements(argIVisualElement1, argIVisualElement2);
	};

	FlexTable2.getAutoLayout = function() {
		return b_FlexTable2_root.getAutoLayout();
	};

	FlexTable2.setAutoLayout = function(argBoolean) {
		b_FlexTable2_root.setAutoLayout(argBoolean);
	};

	FlexTable2.setModuleFactory = function(argIFlexModuleFactory) {
		b_FlexTable2_root.setModuleFactory(argIFlexModuleFactory);
	};

	FlexTable2.getCreationPolicy = function() {
		return b_FlexTable2_root.getCreationPolicy();
	};

	FlexTable2.setCreationPolicy = function(argString) {
		b_FlexTable2_root.setCreationPolicy(argString);
	};

	FlexTable2.removeElement = function(argIVisualElement) {
		return b_FlexTable2_root.removeElement(argIVisualElement);
	};

	FlexTable2.getNumElements = function() {
		return b_FlexTable2_root.getNumElements();
	};

	FlexTable2.addElementAt = function(argIVisualElement, argInt) {
		return b_FlexTable2_root.addElementAt(argIVisualElement, argInt);
	};

	FlexTable2.swapElementsAt = function(argInt1, argInt2) {
		b_FlexTable2_root.swapElementsAt(argInt1, argInt2);
	};

	FlexTable2.SkinnableContainer = function() {
		return b_FlexTable2_root.SkinnableContainer();
	};

	FlexTable2.addElement = function(argIVisualElement) {
		return b_FlexTable2_root.addElement(argIVisualElement);
	};

	FlexTable2.getDeferredContentCreated = function() {
		return b_FlexTable2_root.getDeferredContentCreated();
	};

	FlexTable2.setMxmlContentFactory = function(argIDeferredInstance) {
		b_FlexTable2_root.setMxmlContentFactory(argIDeferredInstance);
	};

	FlexTable2.removeAllElements = function() {
		b_FlexTable2_root.removeAllElements();
	};

	FlexTable2.getLayout = function() {
		return b_FlexTable2_root.getLayout();
	};

	FlexTable2.setLayout = function(argLayoutBase) {
		b_FlexTable2_root.setLayout(argLayoutBase);
	};

	FlexTable2.getElementAt = function(argInt) {
		return b_FlexTable2_root.getElementAt(argInt);
	};

	FlexTable2.removeElementAt = function(argInt) {
		return b_FlexTable2_root.removeElementAt(argInt);
	};

	FlexTable2.getElementIndex = function(argIVisualElement) {
		return b_FlexTable2_root.getElementIndex(argIVisualElement);
	};

}
