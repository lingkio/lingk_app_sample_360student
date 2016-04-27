# web-php-apache2-simple
A hello world PHP script

# Developer Workspace

[![Contribute](http://beta.codenvy.com/factory/resources/codenvy-contribute.svg)](http://beta.codenvy.com/f?id=5ayat0naxlljn3p2)

# Recipe

FROM [codenvy/php](https://hub.docker.com/r/codenvy/php/)

# Commands to run

| #       | Description           | Command  |
| :------------- |:-------------| :-----|
| 1      | Start Apache, tail logs | `sudo service apache2 start && sudo tail -f /var/log/apache2/access.log -f /var/log/apache2/error.log` |
| 2      | Stop Apache      |   `sudo service apache2 stop` |
| 3 | Restart Apache      |    `sudo service apache2 restart` |
| 3 | Composer           |    `composer install` |
# Preview URL

localhost:$mappedPort/$projectName

# Demos

[Listing Page](http://lingk360student.x10host.com/)

[Example 360 student view with complete data](http://lingk360student.x10host.com/#student/efb85d46ffd44204a00af3b7adbc4e75)
