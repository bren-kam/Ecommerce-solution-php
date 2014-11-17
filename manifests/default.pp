##################################
# Grey Suit Retail Puppet Config #
##################################
# OS          : Ubuntu 12        #
# Database    : MySQL 5          #
# Web Server  : Apache 2         #
# PHP version : 5.4              #
##################################

# Vim
class { 'vim': }

# Set Timezone
class { 'timezone':
    timezone => 'America/Chicago',
}

# Puppi
class { 'puppi': }

# Apache setup
class { "apache":
  #  puppi        => true,
  #  puppi_helper => "myhelper",
}

apache::vhost { $fqdn :
  docroot => $docroot,
  server_name => $fqdn,
  serveraliases => $aliases,
  priority            => '',
  template            => 'apache/virtualhost/vhost.conf.erb',
}

apache::module { 'rewrite': }
apache::module { 'headers': }

# PHP Extensions
class {"php":}

php::module { ['xdebug', 'mysql', 'curl', 'gd', 'mcrypt']:
  notify => Service['apache2']
}

# MySQL Server
class { '::mysql::server':
  package_ensure  => present,
  root_password     => 'hPUWcA2w62WXX5C',
#  override_options  => { 'mysqld' => { 'default_time_zone' => 'America/Chicago' } },
}

# Needs to be Mysql 5.6: https://rtcamp.com/tutorials/mysql/mysql-5-6-ubuntu-12-04/

class { 'mysql::client':}

mysql::db { 'imaginer_system':
  user     => 'imaginer_admin',
  password => 'rbDxn6kkj2e4',
  host     => 'localhost',
  grant    => ['ALL'],
  charset => 'utf8',
}

#exec { "database_import":
#  timeout => 300,
#  command => "/bin/gzip -dc /vagrant/manifests/provision.sql.gz | /usr/bin/mysql -u root -phPUWcA2w62WXX5C",
#  creates => '/var/lib/mysql/imaginer_system',
#  require => Service['mysql'],
#}


# Git
#include git

# GrandCentr.al Setup
file { $docroot:
  ensure  => 'directory',
}