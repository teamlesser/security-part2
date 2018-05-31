# Security Lab

This project is about making a secure Twitter clone. Users should be able to register, login, post messages, vote on messages, filter messages and reset their password on the site.

We will be working with a feature branch workflow, which means that we will be working in separate branches that are merged to master after a code review. We are using Kanban to structure the project workflow.

## Getting Started

To clone the project:
```
git clone git@bitbucket.org:group4securitylab/securitylab.git
```

If you are using the [Bitnami WAPP stack](https://bitnami.com/stack/wapp/installer) it is suggested that you clone the project to the path below, where "..." has to be substituted with your actual installation location. This is so that you can easily access your site locally through localhost (check that your server is running with the Bitnami WAPP Stack Manager Tool).

```
.../Bitnami/wappstack-7.1.13-0/apache2/htdocs
```

A directory will be created titled securitylab.
Move in to this directory with:

```
cd securitylab
```

To create a new branch from the master:
```
git checkout -b nameofnewbranch
```

To add/stage files:
```
git add nameoffile
```

To check status of current staging:
```
git status
```

To commit files:
```
git commit -m "your message"
```

To push branch/files to this repository:
```
git push origin nameofyourbranch
```

Merging to master should be done through the Bitbucket interface through a Pull Request.

### Prerequisites

You need to have a local testing server for your code. It is suggested that you use the [Bitnami WAPP stack](https://bitnami.com/stack/wapp/installer). It is easy to install. The directory where your securitylab-directory should be placed to be accessed is:

```
.../Bitnami/wappstack-7.1.13-0/apache2/htdocs
```

When your server is running (check through the Bitnami WAPP Stack Manager Tool) you can access your site through your browser with the address:

```
http://localhost/securitylab/
```

It is suggested that you develop with [PhpStorm](https://www.jetbrains.com/phpstorm/) (aqcuire a free student license) but this is not a must. Development can be done with any IDE as long as you are comfortable with it! 

If you have worked differently, feel free to make additions to this README.

## Contributing

Contributions should be done through separate branches which are merged to the master branch after code review by another member. Create a Pull Request when you feel that your feature is finished. When your contribution has been accepted, press the Merge button in your Pull Request.

## Authors

See the list of [members](https://bitbucket.org/group4securitylab/profile/members) for who participated in this project.

## Acknowledgments

* [README template by PurpleBooth](https://gist.github.com/PurpleBooth/109311bb0361f32d87a2)

