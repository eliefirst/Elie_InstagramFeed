# Elie Instagram Feed

Module Instagram Feed pour Magento 2.4.8 - Compatible Hyva Theme & PHP 8.4

**Développé par ElielWeb pour RedLine Paris**

## Features

- ✅ Grille asymétrique responsive
- ✅ Compatible Hyva Theme (Alpine.js + Tailwind CSS)
- ✅ Compatible PHP 8.4
- ✅ Configuration via Admin
- ✅ Cache intégré
- ✅ Instagram Graph API

## Installation
```bash
composer config repositories.elie-instagram vcs https://github.com/eliefirst/Elie_InstagramFeed.git
composer require eliefirst/instagram-feed:dev-main
php bin/magento module:enable Elie_InstagramFeed
php bin/magento setup:upgrade
php bin/magento cache:clean
```

## Configuration

**Admin Panel**: Stores → Configuration → ElielWeb Extensions → Instagram Feed

- Enable Instagram Feed: Yes
- Instagram Username: your_username
- Instagram Access Token: your_token
- Number of Posts: 6

## License

Copyright © 2025 RedLine. All rights reserved.
