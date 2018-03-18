# Money Management System

PHP7, Slim, MongoDB, Angular 4, Bootstrap 4

## Overview

An expenses tracker that helps you to track income and expenses by analyzing monthly credit card statements

Let me introduce you:

* A parser for analyzing and categorizing data from financial statements (supported providers are Bank of America, Citizen and Discover)

* RESTful API for retrieving information

* A client for visualization personal balance and bills

## User Guide

1. How much did you spend and earn by years
![How much did you spend and earn by years](https://user-images.githubusercontent.com/31717889/30723523-ec386600-9f05-11e7-842b-044373bf948e.png)

2. What was your balance in a particular month 
![What was your balance in a particular month](https://user-images.githubusercontent.com/31717889/30723521-ec35bcde-9f05-11e7-9866-edb928253f4e.png)

  2.1. (plus additional visualization for averages by category)
  ![plus additional visualization for averages](https://user-images.githubusercontent.com/31717889/30723522-ec37187c-9f05-11e7-8a22-3e3f8adace06.png)

4. What was your statement in a particular month 
![What was a combined statement in a particular month](https://user-images.githubusercontent.com/31717889/30723524-ec38eb84-9f05-11e7-87bd-7e0a45da17ed.png)

## Install notes

### Server

* Install dependencies:

  ```
  $ php composer install
  $ mkdir logs && chmod 777 logs 
  ```

* Add the app config file (see an example `src/config/config.example.php`): 

  ```
  $ vi src/config/config.php
  ```

* Check out if any errors happened in the main app log file:

  ```
  $ tail -f app.log
  ```

* If you using the parser, put your financial statements to `/data`:

  ```
  $ mkdir data && chmod 777 data
  ```

* To run the parser see 'Utilities' section

* For local machines: don't forget to configure an nginx / Apache virtual host

* To redefine parser's grouping categories update `src/libs/Budget/Categorization/rules/credit_categ.php` (or create a local `credit_categ_local.php`)

### Client

* Install packages 

  ```
  cd client/
  npm install
  ```

* Make sure the `apiEndpoint` environment variable has desired value:
  
  ```
  cd client/
  vi /src/environments/environment.local.ts
  -- OR
  vi /src/environments/environment.prod.ts
  ```

  * Run the client: 

  ```
  cd client/ && ng serve --env=local
  ```

  * Open a local copy at `http://localhost:4200`

## Utilities

  **Main parser utilities**

   * Parse the dir full of statements:

    ```
    $ cd public && php cli.php /cli/parse-files GET
    ```

    * Parse a statment after a statement

    ```
    $ cd public && php cli.php /cli/parse-files GET file=*.CSV
    ```

  **Additional utils**

  ```
  $ cd public/
  $ php cli.php /cli/recategorize GET
  $ php cli.php /cli/output-titles GET
  $ php cli.php /cli/mock-file GET
  ```
  
   **Naming convention**

   * Citizen statements prefix is 'cit-'
   * Bank of America statements prefix is 'boa-'
   * Discover statments prefix is 'dis-'


## API Endpoints

  Note: Use GET method to retrieve data using the following properties, with required properties in bold:
  
  * Call `data-groupby` to get a sum that you spend/earn on a specified month
  
  
     Field | Description
     ------|------------
     **m** | month
     **y** | year
     cg | category
     type | type: debit = 1 or credit = -1]

     For example, `/api/data-groupby?m=1&y=2017&cg=rent`

  * Call `data-details` to get a list of all transactions that you made on a specified month
  
  
     Field | Description
     ------|------------
     **m** | month
     **y** | year

     For example, `/api/data-details?m=1&y=2017`

  * Call `data-tableby` to get a table of all sums by categories and/or years and/or months
  
  
     Field | Description
     ------|------------
     m | month
     y | year

     For example, `/api/data-tableby?y=2017`
    
  * Call `categories` to get a full list of categories

     For example, `/api/categories`    

