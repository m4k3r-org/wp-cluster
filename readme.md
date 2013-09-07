### Description
* Flawless WordPress Theme By Usability Dynamics, Inc.
* Theme Homepage - http://usabilitydynamics.com/products/flawless/

### Notes
The theme has a default maximum width of 1090px, which is the default Flawless width, but can be adjusted under Appearance Settings.
On screens smaller than 1199px, the configured width is ignored, and the layout switches to a maximum width of 940px.
Below 979px, we assume a mobile devices is being used, and the layout expands to full width of the browser, converts all columns to full width and renders the mobile navbar.
Uses Twitter Bootstrap framework to handle the 12-column grid. By default, all blocks are fluid. Excluded components are: Hero Unit, Breadcrumbs, and Modals.

### Debugging
JavaScript function flawless.toggle_visual_debug(); can be called via console to render a grid helper.

### Theming
As far as layout are concerned, the basic structure goes like so: div.row-fluid > div.span[x] > div.cfct-module\
To stay consistent with automatically generated elements, add a .first and .last classes to the first and last div.span[x] in each row.
The Twitter Bootstrap scaffolding structure is followed closely with the core exception of our div.span[x] elements not having any margins.
To maintain relational widths, all spacing is handled by the inner .cfct-module elements.

### Using
Masonry is applied to all galleries by default, unless gallery has 'disable-masonry' class.
Masonry can be applied to rows by adding a 'enable-masonry' class to the row.