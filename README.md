#Introduction

Sample Data is a data set that represents Magento features. It consists of installation scripts, fixtures and media files.
After its installation on Magento instance an admin user will receive a number of products of any kind, price rules, CMS pages, banners and more.

#Deployment

Deployment of Sample Data can be performed using composer.

###Using Composer
To deploy Sample Data using composer, please, specify sample data package in your composer.json

1. Specify repository
```
{
    "repositories": [
        {
            "type": "composer",
            "url": "http://packages.magento.com/"
        }
    ],
}
```
2. Specify packages
```
{
    "require": {
        "magento/sample-data": "{version}"
    }
}
```
3. Run composer update from your Magento root directory

#Installing

Being once deployed the Sample Data is available for installation through the Magento Installation Wizard or using CLI.

###Web Installation

To install Sample Data user should choose checkbox [v] "Use Sample Data".

###Console Installation

There are two ways
 - Magento has not been installed yet
 - Magento has already been installed

####Magento has not been installed yet

Specify additional parameter --use-sample-data
Example
```
php -f index.php install --base-url=http://localhost/magento2/ \
  --backend-frontname=admin \
  --db-host=localhost --db-name=magento --db-user=magento --db-password=magento \
  --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com \
  --admin-user=admin --admin-password=iamtheadmin --language=en_US \
  --currency=USD --timezone=America/Chicago
  --use-sample-data
```

####Magento has already been installed

Run Sample Data command in Magento CLI

```
php -f ./dev/tools/Magento/Tools/SampleData/install.php -- --admin_user=<admin> [--bootstrap="..."]
```

#Uninstalling

There is no special scripts that assist in uninstalling of Sample Data. All installed data should be removed manually.
