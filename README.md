## Esewa Payment Gateway Plugin for WHMCS

eSewa Payment Gateway as a payment method in your WHMCS Panel.

The eSewa payment plugin for WHMCS was created separate from the eSewa company. There are no connections between any of the plugin's creators and any of these two businesses.

## Features

- Order Invoice Payment
- Manual Invoice Payment

## Installation

Download the ZIP (or tar.gz) file from the releases [See the releases](https://github.com/surazdott/esewa-whmcs-module/releases)

Extract the zip file and paste the following files in WHMCS modules/gateways

### Folder Structure

```

├── modules
│   ├── gateways
|   |   |── callback
|   |   └────── esewa.php
|   |   |── esewa
|   |   └────── helpers.php
|   |   └────── init.php
|   |   └────── logo.png
|   |   └────── whmcs.json
|	├── esewa.php
├── .gitignore
└── README.md
```

- Go to the appropriate interface in the WHMCS Admin Area
- Search esewa payment gateway in the payment list and click on it
- Click on active button
- Click on manage button and update your merchant code
   
## Documentation
Check out the installation guide and configuration of [WHMCS Panel](https://help.whmcs.com/m/setup/l/1075240-configuring-your-first-payment-gateway)

## License

The eSewa WHMCS module is open-sourced software licensed under the [MIT license.](https://opensource.org/license/mit/)
