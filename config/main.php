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
 * DEBUG : http://devweb.local/decodexList/decodexReader/assets/moduleDecodex/decodexUpdates.json
 * RELEASE (online) : http://www.lemonde.fr/webservice/decodex/updates
 */
$config->set("DecodexURL", "http://devweb.local/decodexList/decodexReader/assets/moduleDecodex/decodexUpdates.json");

/**
 * Tokens for a privilegied access
 */
$config->set("PriviliegedTokens", array(
    "token1"=>"xa23kRhv15TLM8l85VQeMRCqqNfdRqwW+sTzxOtv+uBag0LAU+st1QU7FdZezF6",
    "token2"=>"MbTXCsrdTUkVVbHQfFfqex2m86lcnVWEUABvEfXFFqDQImjz+OJDOcJQRMWwuJA",
    "token3"=>"TRLBgTQd+mTrxo3UYp4dkdW5R9MQqjG0JdlMkwrf+7SXW5Cxe4mNvfXNrMi2g1m",
));