#
# Table structure for table 'tx_faster_cache'
#
CREATE TABLE tx_faster_cache (
	identifier varchar(100) DEFAULT '0' NOT NULL,
	url tinytext,
    extra text,

	PRIMARY KEY (identifier)
);
