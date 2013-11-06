Nicks-Base-Theme
===========

Updated for DMS!

A LESS driven child theme template for PageLines. This is what I use to start each new theme with, and is currently how I reccommend to build Child Themes for PageLines. PageLines has its own PHP LESS compiler which compiles the LESS into CSS on the fly. This means all you need to worry about when theming is pecking out some LESS.

If you're working locally (which you should be), you can turn off the caching by adding `define('PL_LESS_DEV',true);` to your wp-config file. This prevents you from having to Save Options in order to re-build and re-cache the LESS. Do not run this on a production site, or face a nuclear meltdown.

LESS Directory
======

The /less directory thats been setup, provides a super clean and convienient way to theme. This directory, mirrors the /less directory found within PageLines. The LESS directory in PageLines contains all the LESS used throughout the framework, encapsulated into single files. The compiler then grabs these files and combines them into a single, virtual, compiled LESS file. If you copy a file from the parent /less directory, and you paste it into `/nicks-base-theme/less`, the compiler will skip over the parent, and run the child instead. This makes for super clean theming methods without a lot of overriding at all.

This also makes for quick theming. Inside the /less directory of this child theme you'll find `variables.less`. With this file you can easily change for example, the font size and line-height across the entire site without overriding. You can also list custom variables here to use across your child theme, and its sections. Mixins work in the same way. Copy `mixins.less` from the `pagelines/less`, paste it into the child themes /less directory, and add a csutom mixin. Now you can use that anywhere across your site. Any available LESS file in the parent can be pasted into the child. Variables is just one of them.

You'll also find the the style.less from out renamed `Altered Nav` section is missing. It's styles have been brought into the child theme `style.less` file. This is personal preference, and is the way I usually will build a child theme. I only use mixins and variables if I need to echo those styles into the sections. Otherwise I'll leave them in the childs `style.less` file.

Sections Directory
======

Your child themes sections go here. You can copy any of the parent theme sections, and paste them into here if you plan on changing or adding onto core sections. I reccommend changing the class name and deactivating the core section that you've cloned. Deactivating will ensure that sections LESS file doesn't get compiled. I've changed it  to `Altered Nav` as an example in the section provided.

Changelog
======

== 1.2 ==
* updated variables.less to match (caused issues with revslider props batman)

== 1.1 ==
* Updated for DMS

== 1.0.1 == 
* Added sample color picker running custom LESS var
* Added sample options panel
* Updated variables.less to sync with PageLines 2.4

== 1.0 == 
* Initial Release

