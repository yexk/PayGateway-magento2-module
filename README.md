## install 

Go to Magento2 root folder

Enter following command to install module:

`composer require pstk/paystack-magento2-module`

Wait while dependencies are updated.

- Enter following commands to enable module:

```
php bin/magento module:enable YeThird_PayGateway --clear-static-content
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```
