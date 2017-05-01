# decodexWatcher API server

## What is this ?

At the end of 2016, Le Monde (french press group) released a notation list (the Decodex) for press websites. This list is often updated by the same group. It is officialy provided for the Decodex Firefox & Chrome addon.

The goal of this API is to monitor and get the modifications of the official list of the Decodex in order to help its users compare the evolution of the Decodex List.

The project is written in PHP. PHP 7.0 is recommended. Today, the list is stored in an Sqlite database.

## Use the API

The official instance of the API is available at https://api.decodexwatcher.communiquons.org/
You may find the schema of the API at https://swagger.decodexwatcher.communiquons.org/

The API is publicy avaible. The list is refreshed every 24 hours.