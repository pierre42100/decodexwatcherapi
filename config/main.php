<?php
/**
 * Main configuration file
 *
 * @author Pierre HUBERT
 */

/**
 * The site mode : debug / release
 */
$config->set("site_mode", "debug");

/**
 * The URL where Decodex list can be retrieved
 *
 * DEBUG : http://devweb.local/decodexList/decodexReader/assets/moduleDecodex/11fev2017/decodexUpdates.json
 * RELEASE (online) : http://www.lemonde.fr/webservice/decodex/updates
 */
$config->set("decodexListURL", "http://devweb.local/decodexList/decodexReader/assets/moduleDecodex/30apr2017/decodexUpdates.json");

/**
 * Tokens for a privilegied access (Rest access)
 */
$config->set("priviliegedTokens", array(
    "token1"=>"xa23kRhv15TLM8l85VQeMRCqqNfdRqwW8sTzxOtv9uBag0LAUst1QU7FdZezF6",
    "token2"=>"MbTXCsrdTUkVVbHQfFfqex2m86lcnVWEUABvEfXFFqDQImjzOJDOcJQRMWwuJA",
    "token3"=>"TRLBgTQd4mTrxo3UYp4dkdW5R9MQqjG0JdlMkwrf7SXW5Cxe4mNvfXNrMi2g1m",
));

/**
 * Database filename (inside of data directory)
 */
$config->set("database_filename", "database.sqlite");

/**
 * Database prefix
 */
$config->set("database_prefix", "dw_");