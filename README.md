## About Zip Codes

Restful service API to consult Zip Codes, technical aspects:

- Laravel Framework 9.35.1.
- PHP 8.0.19.
- MySQL Server 8.0.29 Homebrew.

## Components

### Model

Main model linked to database where previously was uploaded the info from the indicated source.

The main table hosts the info so field names have been named and normalized as required, they always have a prefix which indicates where the info belongs, e.g.:
- `d_asenta` from origin set as `settlement_name`
- `d_tipo_asenta` from origin set as `settlement_type`

Unused fields excluded by `hidden` protected property.

Attribute casts implemented by `casts` protected property from Laravel Model.

`groupValuesByPrefix` created to automatize the consultancy of the attributes in the same group by prefix, also cast strings to uppercase.

There are four accessors, tree to get the grouped info by calling a normal attribute and one more to implement the exception `settlemet_type`. E.g. you only have to call `$this->federal_entity` to get the array with federal info in it. 

Implemented for:
- `federal_entity`
- `municipality`
- `settlement`
- `settlemet_type`


`getByZipCode` does:
- Get the records with required zip code
- Abort and launch `HTTP 404 ERROR` if not found
- Group all the `settlements`
- Formats the output
- Return th info in the right format

### API Route
There is only one API route implemented, it's on `GET` method and the endpoint format is the requested:

`/api/zip-codes/{zip_code}`

##  ;)