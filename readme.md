[alsaifgallery.com](https://alsaifgallery.com) (Magento 2).

## How to deploy the static content 
```              
bin/magento cache:clean
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US ar_SA
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme Mgs/supro \
	-f en_US ar_SA
bin/magento cache:clean
```