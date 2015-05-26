#Introduction

Sample data consists of installation scripts, fixtures and media files. 
Installing sample data fills your database with a number of products of each type, price rules, CMS pages, banners and more.

#Deployment

To deploy sample data, use Composer:

1. In your composer.json, specify the following:
    - repository:
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

    - packages:
         ```
        {
            "require": {
                "magento/sample-data": "{version}"
            }
        }
        ```
2. From your Magento root directory, run composer update.

#Installing

Once deployed, the sample data can be installed using the Magento Installation Wizard (web installation) or using CLI (console installation).

###Web Installation

To install sample data, select the Use Sample Data checkbox [x] on the 4-th step "Customize Your Store"

###Console Installation

The steps required to install sample data are different depending on whether the Magento application itself is installed:

- If the Magento application is not installed, you can install it with sample data at once. Use the following code sample as an example:
```
php -f index.php install --base-url=http://localhost/magento2/ \
  --backend-frontname=admin \
  --db-host=localhost --db-name=magento --db-user=magento --db-password=magento \
  --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com \
  --admin-user=admin --admin-password=iamtheadmin --language=en_US \
  --currency=USD --timezone=America/Chicago
  --use-sample-data
```
- If the Magento application is already installed, to install the sample data use the following command:
```
php -f ./dev/tools/Magento/Tools/SampleData/install.php -- --admin_user=<admin> [--bootstrap="..."]
```

#Removing Sample Data

There is no special scripts that assist in uninstalling of Sample Data. 
To remove sample data, you must delete the database and re-install Magento with a new empty database
