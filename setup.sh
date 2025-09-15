#!/bin/bash

#
# setup.sh
#
# description : generates the default installation
# version : 0.1

SRC_PATH=$(dirname $0)
CURRENT_PATH=$(pwd)
PROJECT_NAME=veltraj
DEST_PATH=/usr/share/$PROJECT_NAME
CONFIG_FILE=/usr/share/$PROJECT_NAME/include/config.ini
DEPENDENCIES=("apache2" "mariadb-server" "php" "php-mysql", "php-curl")
APACHE2_MODS=("rewrite")
SITE_PORT=8081
SQL_SCHEMAS=$(ls $SRC_PATH/schemas/schema-*.sql)
CLEANUP_FILES=("vhost.conf" "setup.sh", "composer.lock")

DBHOST=localhost
DBPORT=3306
DBUSER=veltraj
DBPASSWD=veltraj
DBDATABASE=veltraj

# Check if the current user is root
is_root(){
    if [ "$(id -u)" -ne 0 ]; then
        echo "This script must be executed with root privileges, retry with sudo"
        exit 1;
    fi
}

# For all DEPENDENCIES, launch install
install_dependencies(){
    for package in ${DEPENDENCIES[@]}; do
        echo "install package $package..."
        apt install -y $package
    done
}

# For all APACHE2_MODS, enable them
activate_apache_mods(){
    for mod in ${APACHE2_MODS[@]};do
        a2enmod $mod;
    done
}

# Check if a port is available, fallback to another if necessary
get_available_port(){
    local port_to_check=$1
    while netstat -tuln | grep -q ":$port_to_check"; do
        port_to_check=$((port_to_check + 1))
    done
    SITE_PORT=$port_to_check
    echo $port_to_check
}

# Configure Apache to listen on an available port
configure_apache_port(){
    local port_conf="/etc/apache2/ports.conf"
    local port=$(get_available_port $SITE_PORT)
    SITE_PORT=$port

    if ! grep -q "^Listen $port$" "$port_conf"; then
        echo "Adding 'Listen $port' to $port_conf"
        echo "Listen $port" >> "$port_conf"
    else
        echo "'Listen $port' is already present in $port_conf"
    fi

    echo "Apache will listen on port $port"
}

# Configure the virtual host with the selected port
configure_virtual_host(){
    local template_conf=$SRC_PATH/vhost.conf
    local vhost_conf=/etc/apache2/sites-available/$PROJECT_NAME.conf
    cp -rf $template_conf $vhost_conf
    sed -i "s/<VirtualHost 127.0.0.1:[0-9]*>/<VirtualHost 127.0.0.1:$SITE_PORT>/" $vhost_conf
    sed -i "s|DocumentRoot /usr/share/veltraj/public|DocumentRoot $DEST_PATH/public|" $vhost_conf
}


# Configure and import schemas
configure_mysql(){

    for schema in ${SQL_SCHEMAS[@]};do
        mysql < $schema
    done
}


# Change the password for mysql
change_mysql_password(){
    while true; do
        echo
        echo "New password for $PROJECT_NAME@$DBHOST : "
        read -s password
        echo "Retype the new password for $PROJECT_NAME@DBHOST : "
        read -s repassword
        echo 

        if [ "$password" == "$repassword" ]; then
            DBPASSWD=$password
            mysql -u root -e "ALTER USER '$DBUSER'@'$DBHOST' IDENTIFIED BY '$password'; FLUSH PRIVILEGES;"
            sed -i "/^\[database\]/,/^\[/{s/^passwd=.*/passwd=$DBPASSWD/}" "$CONFIG_FILE"
            echo "Password updated"
            break
        
        else
            echo "Passwords do not match. Try again."
        fi
    done
}

# Cleanup the project folder
cleanup(){
    for todel in ${CLEANUP_FILES[@]};do
        rm -rf $todel
    done
}

#
# ENTRY POINT
# 

# Need root privileges
is_root

# Goto project directory
cp -r $SRC_PATH $DEST_PATH

# Install dependencies
install_dependencies
activate_apache_mods

# Configure apache
configure_apache_port
configure_virtual_host
a2ensite $PROJECT_NAME
systemctl restart apache2.service

# write schemas
configure_mysql
change_mysql_password

# cleanup
cd $DEST_PATH
cleanup

# Regenerate composer dependencies
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-interaction

echo "You can access to the web interface by this address: http://127.0.0.1:$SITE_PORT"

# Restore the console where it was in the beginning
cd $CURRENT_PATH
