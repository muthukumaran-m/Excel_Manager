# Excel manager
A task to Import / Export Excel doc with user details

## Prerequisites
- Composer
- Laravel ^9
- XAMPP & MySQL
- PHP ^8

## Application setup
-   Clone this repo using ``` git clone https://github.com/muthukumaran-m/Excel_Manager.git ``` or directly download from the [Git repo](https://github.com/muthukumaran-m/Excel_Manager)

## API Setup
- Create one database as ``` pixel ```
- The API folder contains the Laravel app. Open the terminal inside this API folder. Then follow the steps mentioned below.
- Run ``` php artisan optimize ``` to clear all cache
- Install the required packages using ``` composer update ```
- Run the migration and seeders using ``` php artisan migrate:fresh --seed ```
- Start the php server using ``` php artisan serve --port 8001 ```

## Front end setup
- The UI folder contains the Angular app. Open the terminal inside this UI folder. Then follow the steps mentioned below.
- Install the required packages using ``` npm install ```
- Start the application using ``` ng serve ```
- Now use the ``` http://localhost:4200/ ``` to access the application

### Note
- I attached the sample Excel file for testing purpose
- Double the the table cell to edit the data
- Feel free to contact if any issues arises while doing the application setup **Email: kumaranpassion2work@outlook.in**

