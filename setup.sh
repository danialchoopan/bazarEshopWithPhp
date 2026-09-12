#!/bin/bash

# Quick Setup Script for Bazar Shop
# Supports both MySQL and SQLite

echo "🛍️  Bazar Shop - Quick Setup"
echo "============================="
echo ""

# Check if .env exists, if not create from example
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✅ .env file created. Please edit it with your database settings."
    echo ""
fi

# Ask user which database to use
echo "Choose database type:"
echo "1) MySQL/MariaDB"
echo "2) SQLite (easiest, no server needed)"
read -p "Enter choice [1-2] (default: 2): " db_choice
db_choice=${db_choice:-2}

if [ "$db_choice" = "1" ]; then
    echo ""
    echo "🔧 MySQL Setup"
    echo "--------------"
    read -p "Database name [em-bazar-shop-db]: " db_name
    db_name=${db_name:-em-bazar-shop-db}
    read -p "Database user [root]: " db_user
    db_user=${db_user:-root}
    read -sp "Database password: " db_pass
    echo ""
    
    # Update .env
    sed -i.bak "s/DB_DRIVER=.*/DB_DRIVER=mysql/" .env
    sed -i.bak "s/DB_DATABASE=.*/DB_DATABASE=$db_name/" .env
    sed -i.bak "s/DB_USERNAME=.*/DB_USERNAME=$db_user/" .env
    sed -i.bak "s|DB_PASSWORD=.*|DB_PASSWORD=$db_pass|" .env
    rm -f .env.bak
    
    # Create database and import SQL
    echo ""
    echo "📦 Creating MySQL database and importing schema..."
    mysql -u "$db_user" -p"$db_pass" -e "CREATE DATABASE IF NOT EXISTS \`$db_name\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    mysql -u "$db_user" -p"$db_pass" "$db_name" < em-bazar-shop-db.sql
    mysql -u "$db_user" -p"$db_pass" "$db_name" < database/migrations/001_add_modernization_tables.sql
    
    echo "✅ MySQL setup complete!"
    
else
    echo ""
    echo "🔧 SQLite Setup"
    echo "---------------"
    
    # Update .env for SQLite
    sed -i.bak "s/DB_DRIVER=.*/DB_DRIVER=sqlite/" .env
    sed -i.bak "s|DB_DATABASE=.*|DB_DATABASE=database/sqlite.db|" .env
    rm -f .env.bak
    
    # Create SQLite database
    echo "📦 Creating SQLite database..."
    sqlite3 database/sqlite.db < database/sqlite/schema.sql
    
    echo "✅ SQLite setup complete!"
    echo ""
    echo "📊 Database file: database/sqlite.db"
fi

echo ""
echo "✨ Setup Complete!"
echo "=================="
echo ""
echo "Next steps:"
echo "1. Make sure your web server points to the 'public/' directory"
echo "2. Visit: http://localhost/bazarEshopWithPhp/public/"
echo "3. Admin login: admin@bazar.local / admin123"
echo ""
echo "Happy coding! 🚀"
