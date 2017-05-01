CREATE TABLE "dw_sitesInformations" (
	`ID`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`ID_sitesName`	INTEGER,
	`insertTime`	INTEGER,
	`latest`	INTEGER DEFAULT 1,
	`urls`	TEXT,
	`comment`	TEXT,
	`trustLevel`	INTEGER DEFAULT 0
);

CREATE TABLE `dw_sitesName` (
	`ID`	INTEGER PRIMARY KEY AUTOINCREMENT,
	`name`	TEXT,
	`urlName`	TEXT
);