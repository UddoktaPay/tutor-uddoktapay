# Tutor LMS - UddoktaPay Gateway

Integrate UddoktaPay payment gateway with Tutor LMS for accepting payments in Bangladesh.

## Description

The UddoktaPay Gateway add-on for Tutor LMS allows you to accept payments through various Bangladeshi payment methods including bKash, Nagad, Rocket, and more.

### Features
- Accept payments through multiple Bangladeshi payment methods
- Seamless integration with Tutor LMS
- Automatic payment verification
- Customizable payment button text
- Easy to configure

### Requirements
- WordPress 6.2 or higher
- PHP 7.4 or higher
- Tutor LMS plugin
- UddoktaPay merchant account

## Installation

1. Download [uddoktapay.zip](https://github.com/UddoktaPay/tutor-uddoktapay/releases/download/1.0.0/uddoktapay.zip)  from this repository
2. Go to WordPress Dashboard → Plugins → Add New
3. Click "Upload Plugin" at the top
4. Upload the `uddoktapay.zip` file
5. Click "Install Now"
6. Activate the plugin through the 'Plugins' screen in WordPress
7. Go to Tutor LMS → Settings → Payment Methods
8. Enter your UddoktaPay API credentials
9. Save the settings

## Development

### Setup
```bash
# Clone the repository
git clone https://github.com/UddoktaPay/tutor-uddoktapay.git

# Install dependencies
composer install

# Install development dependencies
composer install --dev
```

### Code Standards
The project follows WordPress coding standards. To check the code:

```bash
# Run PHPCS
composer run phpcs

# Fix automatically fixable issues
composer run phpcbf
```

## Support

For support, please email support@uddoktapay.com.

## Contributing

1. Fork the repository
2. Create a new branch for your feature
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT - see the [LICENSE](LICENSE) file for details.
