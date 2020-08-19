DATABASE MODEL AND SCRIPT 
----database_configurations 

TEST RATES SCRIPT
###
INSERT INTO `rates`( `name`, `rate`, `from`, `to`, `is_enabled`, `date_added`)
VALUES('Advanced', '1000', '3', '7', '0', now()),( 'Premium', '1500', '7', '10', '0', now()),('basic', '800', '1', '3', '1', now() );

##
