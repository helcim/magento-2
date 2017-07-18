# Helcim Payment Plugin for Magento 2 Community Edition

You can sign up for a Helcim account at https://www.helcim.com/

## Requirements

Requires Magento 2.x installation: http://devdocs.magento.com/

## Helcim Commerce Setup

In order to utilize the Helcim Commerce API, you must enter your Helcim Commerce account ID as well as an API token in the Magento plugin setup. For instructions on generating an API token with the correct permissions, please visit: https://www.helcim.com/support/article/627-helcim-commerce-api-enabling-api-access/

## Magento Plugin Installation

- Copy and paste the Helcim folder in the <your Magento2 install dir>/app/code Directory.
- Type the following instructions in your Magento2 server CLI:

Change your current directory to Magento 2 web root directory:
```bash
cd /<your Magento2 install dir>
```

Upgrades the Magento application, DB data, and schema:
```bash
php bin/magento setup:upgrade
```

Generates DI configuration and all missing classes that can be auto-generated:
```bash
php bin/magento setup:di:compile
```

## Magento Setup

- Login to your Magento2 web-administration
- Stores -> Configuration
- Sales -> Payment Methods
- Choose Helcim API under Other Payment Methods
- Enable Helcim API
- Enter your Helcim Commerce Account ID
- Enter your API Token
- Save Configuration

## Testing

Please visit https://www.helcim.com/ to create a developer sandbox account.

## SSL/TLS

Please note that the Helcim Commerce platforms requires Transport Layer Security (TLS) version 1.2 to process payments. Any older versions (TLS1.1 / TLS1.0 / SSLv3) will have connections rejected.

For more information on Helcim's API, visit: https://www.helcim.com/support/article/625-helcim-commerce-api-api-overview/