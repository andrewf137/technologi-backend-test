# Technologi backend test

## Requirements
* PHP >= 7.0.0
* composer

## Instructions

1. Checkout project:
    ```
    git clone https://github.com/andrewf137/technologi-backend-test.git
    ```
2. "cd" to project folder.
3. Run `composer install`.
3. Edit `<project-folder>/config/config.php` file.
4. Run `php init.php`.  
   This will:
   * create a database with parameters set in`<project-folder>/config/config.php` file
   * create "nodes" and "relations" tables
   * insert file structure into database
5. `index.php` file is in the root folder of the project.  
    It contains a simple form to perform queries as requested in the test instructions.
6. The file containing the "file structure" data is at the root of the project with name fileStructure.txt.  
   You can update this file and then run again `php int.php` to upload its content to the database.