# Lingk Sample App - 360 degree view of the a student
A PHP project that utilizes a number of Lingk APIs to create a high quality view of a student.

Some of the data feeds used by the app include:
- Student Demographics
- Student Enrollment
- Student Sections
- Section Instructors
- Student Program
- Sections
- Programs
- Learning Events

# Developer Workspace

[![Contribute](http://beta.codenvy.com/factory/resources/codenvy-contribute.svg)](http://beta.codenvy.com/f?id=1zt24oncb3h9enby)

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
