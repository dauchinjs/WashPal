# WashPal task
## Table of contents

* [Technologies](#technologies)
* [Setup](#setup)
* [Bonus questions](#bonus-questions)

## Technologies

Project is created with:

* PHP version: 7.4
* Composer version: 2.4.4

## Setup
1. Clone this repository `git clone https://github.com/dauchinjs/WashPal.git`
2. Install all dependencies: `composer install`
3. Rename the `.env.example` file to `.env` and add the server address (this is only for `typeTwo` project)
4. To run the project change directory to one of the folders `cd "folder name"` and run it using `php index.php` (works for both project types)

## Bonus questions
1.Is this approach secure? What can be done to make it more secure?

I think that both approaches are secure, but the second type would be more secure, because to get information I used
private functions, which are not available to the outside world.
To make it more secure I could put the authentication information in the .env file, and use the dotenv package to get
the information from the .env file.

2.Why are all time values in UTC?

Also known as Coordinated Universal Time, UTC is the primary time standard by which the world regulates clocks
and time, and is not adjusted for daylight saving time. Anyone who uses UTC is able to avoid the confusion of having to
deal with multiple time zones and for users it is easier to understand.

3.How many original Planck units are there?

There are 7 original Planck units: length, mass, time, electric current, thermodynamic temperature, amount of substance,
and luminous intensity.
