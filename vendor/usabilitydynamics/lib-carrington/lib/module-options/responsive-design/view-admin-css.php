.cfct-responsive-list {
	border-top: 1px solid #cacaca;
	-moz-box-shadow: inset 0px 1px 1px 0px rgba(255, 255, 255, .5);
	-webkit-box-shadow: inset 0px 1px 1px 0px rgba(255, 255, 255, .5);
	box-shadow: inset 0px 1px 1px 0px rgba(255, 255, 255, .5);
	color: #000;
	padding: 6px 0 0 8px;
}

#cfct-build .cfct-responsive a.options-button {
	width: 24px;
	height: 23px;
}

#cfct-edit-module a.popover-active,
#cfct-build .cfct-responsive a.popover-active {
	background-position: 4px -96px;
	background-color: #1b1b1b;
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	-khtml-border-radius: 3px;
	border-radius: 3px;
}

#cfct-build .hidden-note {
	display:none;
}

#cfct-build .hidden-devices .hidden-note {
	display: block;
}

.cfct-responsive-classes li {
	cursor: pointer;
}

.cfct-responsive-classes li input {
	display: none;
}

.cfct-responsive-classes li img {
	border-radius: 6px;
}
.cfct-responsive-classes li img:hover {
	border: 1px solid #aaaaaa;
}

.cfct-popup-header .cfct-options-controls {
	text-shadow: none;
}

	.cfct-responsive-list li {
		float: left;
		margin: 0 6px 7px 0;
		cursor: pointer;
	}

	.cfct-responsive-list .cfct-il-icon {
		cursor: pointer;
		left: auto;
		height: 72px;
		padding: 0;
		position: relative;
		width: 72px;
	}

.cfct-responsive-classes {
	margin: 7px 0 0 0;
}

.cfct-responsive-classes input {
	display: none;
}

a.cfct-checkbox-mode {
	text-decoration: none;
	color: black;
}

.cfct-responsive-classes .cfct-checkbox-mode span,
.cfct-responsive-classes .cfct-checkbox-mode input {
	display: inline;
}

.cfct-responsive-icon-container {
	position: relative;
	display: block;
}

.cfct-responsive-disable-icon {
	position: absolute;
	background: none;
	display: none;
	top: 0;
	left: 0;
	width: 72px;
	height: 72px;
}

.cfct-responsive-disabled .cfct-responsive-disable-icon {
	display: block;
	background: url("<?php echo CFCT_BUILD_URL; ?>img/responsive/strikethrough.png") no-repeat 9px 11px;
}

.cfct-responsive-list a {
	display: block;
}

.cfct-responsive-disabled .cfct-il-icon,
.device-disabled .cfct-il-icon {
	background: #a5a4a4 url();
	border: 1px solid #8c8b8b;
	-moz-box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, .3), 0px 1px 1px 0px rgba(255, 255, 255, .7);
    -webkit-box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, .3), 0px 1px 1px 0px rgba(255, 255, 255, .7);
    box-shadow: inset 0px 1px 2px 0px rgba(0, 0, 0, .3), 0px 1px 1px 0px rgba(255, 255, 255, .7);
}
