#Introduction

Magento sample data uses the responsive Luma theme to display a sample store, complete with products, categories, promotional price rules, CMS pages, banners, and so on. You can use the sample data to come up to speed quickly and see the power and flexibility of the Magento storefront.

Installing sample data is optional; you can install it before or after you install the Magento software.

#Deployment

##From Composer
To deploy sample data from Composer, use:

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
    
    where version is either an exact version or semantic version syntax.
2. From your Magento root directory, run composer update.

##From repository
To deploy sample data from repository, use:

1. Clone the sample data repository. For example, git clone [https://github.com/magento/magento2-sample-data.git](https://github.com/magento/magento2-sample-data.git)
2. Link the sample data repository with your Magento CE repository as follows: 
 ```
  php -f <sample-data-root>/dev/tools/build-sample-data.php -- --ce-source="path/to/magento/ce/edition"
 ```
3. Copy media files from `<sample-data-root>/media/*` to `<magento-root>/pub/media/*`

#Installation

Once deployed, the sample data can be installed using the Magento Setup Wizard (web installation) or using CLI (console installation).

###Web Installation

When installing the Magento application using the Magento Setup Wizard, you can choose to install the sample data at Step 4. Customize Your Store by selecting the *Use Sample Data* check box.

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
- If the Magento application is already installed, to install the sample data, enter the following commands in the order shown:
```
<path to Magento 2 bin dir>/magento setup:upgrade
<path to Magento 2 bin dir>/magento sampledata:install <your Magento administrator user name>
```

For example,
```
/var/www/magento2/bin magento setup:upgrade
/var/www/magento2/bin magento sampledata:install admin
```

#Removing Sample Data

There are no special scripts that assist in uninstalling of sample data. 
To remove sample data, you must delete the database and re-install Magento with a new empty database
