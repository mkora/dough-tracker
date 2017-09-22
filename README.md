# Money Management System

PHP7, Slim, MongoDB, Angular 4, Bootstrap 4

## Overview

Budgeting Web-Application has three parts:

* A parser for analyzing and categorizing data from financial statements (supported providers are Bank of America, Citizen and Discover)
* RESTful API for retrieving information
* A client for visualization personal balance and bills


## User Guide

What do you like to know about your money? For example, you can figure out the following questions:

1. How much did you spend and earn by years
![How much did you spend and earn by years](https://user-images.githubusercontent.com/31717889/30723523-ec386600-9f05-11e7-842b-044373bf948e.png)

2. What was your balance in a particular month 
![What was your balance in a particular month](https://user-images.githubusercontent.com/31717889/30723521-ec35bcde-9f05-11e7-9866-edb928253f4e.png)

3. (plus additional visualization for averages by category)
![plus additional visualization for averages](https://user-images.githubusercontent.com/31717889/30723522-ec37187c-9f05-11e7-8a22-3e3f8adace06.png)

4. What was a combined statement in a particular month 
![What was a combined statement in a particular month](https://user-images.githubusercontent.com/31717889/30723524-ec38eb84-9f05-11e7-87bd-7e0a45da17ed.png)

## Quick Start
### Server

* Install dependencies in your app directory, and create a config file or edit src/config/config.php:

  ```
  $ php composer install
  $ vi src/config/config.php
  ```
* Create `data` directory and download your financial statements there. Then, run a one of main parser utilities to fill out your DB.

  **Main parser utilities**

  ```
  $ cd public/
  all files
  $ php cli.php /cli/parse-files GET
  or one by one
  $ php cli.php /cli/parse-files GET file=*.CSV
  ```
  **Additional utils**

  ```
  $ cd public/
  $ php cli.php /cli/recategorize GET
  $ php cli.php /cli/output-titles GET
  $ php cli.php /cli/mock-file GET
  ```
  
* API Endpoints

  Use GET method to retrieve data using the following properties, with required properties in bold:
  
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

### Client

* Install packages 

  ```
  cd client/
  npm install
  ```

* Create or update a `environment[.prod].ts` file in `/src/environments/` to set up `apiEndpoint` to your Money Management Server.

