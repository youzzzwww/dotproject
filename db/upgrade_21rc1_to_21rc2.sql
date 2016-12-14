#
# $Id: upgrade_21rc1_to_21rc2.sql 4941 2007-05-21 08:47:12Z ajdonnison $
# 
# DO NOT USE THIS SCRIPT DIRECTLY - USE THE INSTALLER INSTEAD.
#
# All entries must be date stamped in the correct format.
#

#20070513
#removed jpLocale variable - use user locale instead.
DELETE FROM `config` WHERE config_name = 'jpLocale';