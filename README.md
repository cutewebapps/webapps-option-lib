webapps-option-lib
==================

Simple library for using database-saved options

USAGE
==================

To get property, simply call:

$value = Option_Hash::get( 'key' );
$value = Option_Hash::get( 'key', 'default-value' );

To set property:

Option_Hash::set( 'key', $value );

==================

Unlike wordpress options, so it is good to use on small set of options