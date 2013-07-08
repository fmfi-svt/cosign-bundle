Cosign bundle
=============

This bundle adds support for login using CoSign.

Installation
------------

1. Add the bundle as dependency using composer

  ```json
    {
      "require": {
          "svt/cosign-bundle": "@dev"
      }
      "repositories": [
          {
              "type": "git",
              "url": "https://github.com/fmfi-svt/cosign-bundle.git"
          }
      ]
    }
  ```

2. Enable in kernel

  ```php
  class AppKernel extends Kernel
  {
      public function registerBundles()
      {
          return array(
              // ...
              new SVT\CosignBundle\SVTCosignBundle(),
          ;
      }
      // ...
  }
  ```

3. Configure the firewall in `security.yml`:

  ```yaml
    security:
      firewalls:
        main:
          cosign:
            login_route: login # or whichever route you use for login
          anonymous: true # usually you want to allow anonymous access
          logout:
            success_handler: security.logout.success_handler.cosign
  ```

4. Set `cosign_logout_prefix parameter` in the config to your weblogin logout URL, ending in ?, e.g. `https://login.uniba.sk/logout.cgi?` for uniba.sk

  ```yaml
    parameters:
      cosign_logout_prefix: "https://weblogin.example.com/cosign/logout.cgi?"
  ```

5. Implement the login route. You may redirect the user back to page he was on or to homepage, profile page, etc. Note that the user may be already signed in usign cosign when he arrives to your site, in this case the login route will not be visited. For any actions necessary upon any login, use a login handler or a user provider, depending on your needs.
