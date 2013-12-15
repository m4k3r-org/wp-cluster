YUI.add("yuidoc-meta", function(Y) {
   Y.YUIDoc = { meta: {
    "classes": [
        "API",
        "Carrington",
        "Extended_Taxonomies",
        "Flawless.API",
        "Flawless.Asset",
        "Flawless.Branded_Login",
        "Flawless.BuddyPress",
        "Flawless.Content",
        "Flawless.Element",
        "Flawless.Flawless\\Shortcode",
        "Flawless.Management",
        "Flawless.Mobile",
        "Flawless.Navbars",
        "Flawless.Schema",
        "Flawless.Settings",
        "Flawless.Styles",
        "Flawless.Utility",
        "Flawless.Widget",
        "Legacy",
        "License",
        "Loader",
        "Log",
        "Maintenance",
        "Models",
        "Module",
        "Multisite",
        "SaaS",
        "Shortcode",
        "Shortcodes",
        "Template Methods",
        "Theme",
        "UI",
        "Utility",
        "Views",
        "flawless_wpp_extensions"
    ],
    "modules": [
        "API",
        "Branded Login",
        "Carrington",
        "Carrington Build",
        "Flawless",
        "Loader",
        "Log",
        "Maintenence",
        "Management",
        "Multisite",
        "SaaS",
        "Services_JSON",
        "Shortcodes",
        "Theme UI",
        "UI",
        "UsabilityDynamics",
        "Utility",
        "Views",
        "WP-Property",
        "carrington-build",
        "cfct_build",
        "default",
        "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttp:__github.com_crowdfavorite_wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp:__crowdfavorite.com\n\nReleased under the GPL license\nhttp:__www.opensource.org_licenses_gpl-license.php\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************",
        "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttps:__github.com_crowdfavorite_wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp:__crowdfavorite.com\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************"
    ],
    "allModules": [
        {
            "displayName": "API",
            "name": "API",
            "description": "API\n\nDescription: WPP API implementation"
        },
        {
            "displayName": "Branded Login",
            "name": "Branded Login",
            "description": "Name: Business Card\nVersion: 1.0\nDescription: Widgets for the Flawless theme.\nAuthor: Usability Dynamics, Inc.\nTheme Feature: header-business-card"
        },
        {
            "displayName": "Carrington",
            "name": "Carrington",
            "description": "Class Carrington"
        },
        {
            "displayName": "Carrington Build",
            "name": "Carrington Build",
            "description": "Responsive Design Classes"
        },
        {
            "displayName": "carrington-build",
            "name": "carrington-build"
        },
        {
            "displayName": "cfct_build",
            "name": "cfct_build",
            "description": "Standard return message class to help ensure\nconsistent handling of json return messages \nacross the system."
        },
        {
            "displayName": "default",
            "name": "default",
            "description": "Simple About text in the module-options"
        },
        {
            "displayName": "Flawless",
            "name": "Flawless",
            "description": "Attention General displays the attenion grabbing element on all standard pages.\n\n\nThis can be overridden in child themes with loop.php or\nattention-template.php, where 'template' is the context\nrequested by a template. For example, attention-blog-home.php would\nbe used if it exists and we ask for the attention with:\n<code>get_template_part( 'templates/attention', 'blog-home' );</code>"
        },
        {
            "displayName": "Loader",
            "name": "Loader",
            "description": "PHP Loader"
        },
        {
            "displayName": "Log",
            "name": "Log",
            "description": "Log\n\n-"
        },
        {
            "displayName": "Maintenence",
            "name": "Maintenence",
            "description": "Widgets for the Flawless theme."
        },
        {
            "displayName": "Management",
            "name": "Management",
            "description": "Name: Flawless Management\nDescription: The Management for the Flawless theme.\nAuthor: Usability Dynamics, Inc.\nVersion: 1.0.1\nCopyright 2010 - 2013 Usability Dynamics, Inc."
        },
        {
            "displayName": "Multisite",
            "name": "Multisite",
            "description": "MultiSite Capabilities\n\nDescription: Control Premium Features on Multisite"
        },
        {
            "displayName": "SaaS",
            "name": "SaaS",
            "description": "SaaS Functions\n\n- UD_API_Key / ud::api_key\n- UD_Site_UID / ud::site_uid\n- UD_Public_Key / ud::public_key\n- UD_Customer_Key / ud::customer_key"
        },
        {
            "displayName": "Services_JSON",
            "name": "Services_JSON",
            "description": "Converts to and from JSON format.\n\nJSON (JavaScript Object Notation) is a lightweight data-interchange\nformat. It is easy for humans to read and write. It is easy for machines\nto parse and generate. It is based on a subset of the JavaScript\nProgramming Language, Standard ECMA-262 3rd Edition - December 1999.\nThis feature can also be found in  Python. JSON is a text format that is\ncompletely language independent but uses conventions that are familiar\nto programmers of the C-family of languages, including C, C++, C#, Java,\nJavaScript, Perl, TCL, and many others. These properties make JSON an\nideal data-interchange language.\n\nThis package provides a simple encoder and decoder for JSON notation. It\nis intended for use with client-side Javascript applications that make\nuse of HTTPRequest to perform server communication functions - data can\nbe encoded into JSON notation for use in a client-side javascript, or\ndecoded from incoming Javascript requests. JSON format is native to\nJavascript, and can be directly eval()'ed with no further parsing\noverhead\n\nAll strings should be in ASCII or UTF-8 format!\n\nLICENSE: Redistribution and use in source and binary forms, with or\nwithout modification, are permitted provided that the following\nconditions are met: Redistributions of source code must retain the\nabove copyright notice, this list of conditions and the following\ndisclaimer. Redistributions in binary form must reproduce the above\ncopyright notice, this list of conditions and the following disclaimer\nin the documentation and/or other materials provided with the\ndistribution.\n\nTHIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED\nWARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF\nMERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN\nNO EVENT SHALL CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,\nINCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,\nBUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS\nOF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND\nON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR\nTORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE\nUSE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH\nDAMAGE."
        },
        {
            "displayName": "Shortcodes",
            "name": "Shortcodes",
            "description": "Name: Theme Shortcodes\nDescription: Shortcodes for the Flawless theme.\nAuthor: Usability Dynamics, Inc.\nVersion: 1.0\nCopyright 2010 - 2012 Usability Dynamics, Inc."
        },
        {
            "displayName": "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttp://github.com/crowdfavorite/wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp://crowdfavorite.com\n\nReleased under the GPL license\nhttp://www.opensource.org/licenses/gpl-license.php\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************",
            "name": "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttp:__github.com_crowdfavorite_wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp:__crowdfavorite.com\n\nReleased under the GPL license\nhttp:__www.opensource.org_licenses_gpl-license.php\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************"
        },
        {
            "displayName": "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttps://github.com/crowdfavorite/wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp://crowdfavorite.com\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************",
            "name": "taxonomy-landing\n\nThis file is part of Taxonomy Landing for WordPress\nhttps:__github.com_crowdfavorite_wp-taxonomy-landing\n\nCopyright (c) 2009-2012 Crowd Favorite, Ltd. All rights reserved.\nhttp:__crowdfavorite.com\n\n**********************************************************************\nThis program is distributed in the hope that it will be useful, but\nWITHOUT ANY WARRANTY; without even the implied warranty of\nMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.\n**********************************************************************"
        },
        {
            "displayName": "Theme UI",
            "name": "Theme UI",
            "description": "-\n\n-"
        },
        {
            "displayName": "UI",
            "name": "UI",
            "description": "Class UI"
        },
        {
            "displayName": "UsabilityDynamics",
            "name": "UsabilityDynamics",
            "description": "User Interface\n\nPorted over from WPP 2.0 class_ui.php"
        },
        {
            "displayName": "Utility",
            "name": "Utility",
            "description": "Utility Library."
        },
        {
            "displayName": "Views",
            "name": "Views",
            "description": "Views"
        },
        {
            "displayName": "WP-Property",
            "name": "WP-Property",
            "description": "The default page for property overview page.\n\nUsed when no WordPress page is setup to display overview via shortcode.\nWill be rendered as a 404 not-found, but still can display properties."
        }
    ]
} };
});