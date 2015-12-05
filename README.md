# Magento_SMS
SMS Module for Magento (1.9 onward)

Installation steps

	1) Copy folder "Dhana" into "app/code/local" folder
	2) Copy Dhana_SMS.xml into "app/etc/modules" folder
	3) Create a database table "sms_api_provider" in your database manually. I will add the structure shortly
	4) Read "SMS Templates.xlsx" - guide to create new SMS templates. Note that if you add new variable names,
		then you will have to change relevant code in "Dhana/SMS/Model/Observer.php" file
		This is not a very elegant way of doing it.
		I will improve it to add variables from Admin side in future updates ;)

