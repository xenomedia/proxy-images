# Proxy Images #
**Contributors:** caromanel  
**Tags:** images, attachments  
**Requires at least:** 3.7  
**Tested up to:** 4.7.2  
**Stable tag:** 0.1.0  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html  

Changes upload path to original site url

## Description ##
Changes images under wp-content/uploads to point to a different site.
If notification banner is enable it will show a banner in the bottom of the window showing the site url and the site where all images are pointing to.


## Installation ##

Using wp cli:

a) wp plugin install https://github.com/xenomedia/proxy-images/archive/master.zip --activate

b) wp option update pxy_settings '{"pxy_original_url":"https://www.originalurl.com/","pxy_banner":"show"}' --format=json

## Screenshots ##


## Changelog ##

### 1.0 ###
* Initial revision as part of Xeno Media projects.
